<?php

namespace App\Services;

use App\Jobs\CheckReservationConfirmation;
use App\Jobs\HandleInvalidPresenceConfirmation;
use App\Mail\ReservationSummaryMail;
use App\Mail\TeamReservationConfirmationMail;
use App\Models\DetailReservation;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ReservationService
{
    protected $allowedNetwork;

    public function __construct()
    {
        $this->allowedNetwork = '192.168.40.192';
    }

    public function createReservation($request)
    {
        $emailsToSend = []; // Untuk menyimpan email yang akan dikirim
        $emailsAlreadyReserved = [];

        try {
            DB::beginTransaction();

            $data = $request->only(['date', 'purpose', 'type', 'email_mhs', 'time_slot', 'table_id']);
            $currentUser = auth()->user(); // Ambil user yang sedang login

            if ($currentUser->is_reserve) {
                throw new \Exception('Anda hanya dapat melakukan reservasi satu kali per hari.');
            }

            // Update is_reserve menjadi true untuk user yang sedang login
            $this->updateIsReserve($currentUser, false);

            // Buat reservasi baru
            $reservation = Reservation::create([
                'table_id' => $data['table_id'],
                'code' => $this->generateReservationCode(),
                'status' => 'Terverifikasi',
                'type' => $data['type'],
                'purpose' => $data['purpose'],
                'time_slot' => $data['time_slot'],
                'date' => $data['date'],
                'expires_at' => Carbon::now()->addMinutes(5),
            ]);

            // Buat detail reservasi untuk user pembuat
            $this->createReservationDetails($reservation, $currentUser->id, 'Terverifikasi');

            if ($data['type'] === 'Kelompok') {
                $reservation->update(['status' => 'Menunggu']);

                foreach ($data['email_mhs'] as $email) {
                    $user = $this->findUserByEmail($email);

                    $existingDetail = DetailReservation::where('reservation_id', $reservation->id)
                        ->where('user_id', $user->id)
                        ->first();

                    if (!$existingDetail) {
                        $this->createReservationDetails($reservation, $user->id, 'Menunggu');
                        $emailsToSend[] = $email; // Tambahkan email ke daftar pengiriman

                        if (!$user->is_reserve) {
                            Log::info("User {$email} added to reservation {$reservation->code} and is_reserve {$reservation->is_reserve}");
                            $this->updateIsReserve($user, false);
                        } else {
                            $emailsAlreadyReserved[] = $email;
                        }
                    }
                }

                // Jika ada email yang sudah melakukan reservasi, lempar exception dengan daftar email
                if (!empty($emailsAlreadyReserved)) {
                    throw new \Exception('Maaf, User dengan email berikut sudah melakukan reservasi hari ini: ' . implode(', ', $emailsAlreadyReserved));
                }
            } else {
                $emailsToSend[] = $currentUser->email; // Tambahkan email user individu
            }

            // Update kursi yang tersedia
            $this->updateAvailableSeat($data['table_id'], $reservation, false);

            // Kirim job untuk memeriksa konfirmasi jika kelompok
            if ($reservation->type === 'Kelompok') {
                CheckReservationConfirmation::dispatch($reservation, $data['table_id'])->delay($reservation->expires_at);
            }

            DB::commit();

            // Kirim email setelah proses commit berhasil
            foreach ($emailsToSend as $email) {
                if ($reservation->type === 'Kelompok') {
                    $this->sendConfirmationEmail($email, $reservation);
                } else {
                    $this->sendReservationSummary($reservation);
                }
            }

            return [
                'code' => $reservation->code,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error creating reservation: ' . $e->getMessage());
        }
    }

    // Update status konfirmasi team
    public function updateConfirmTeam($reservationId, $userId, $request)
    {
        if (!URL::hasValidSignature($request)) {
            return view('confirmation', [
                'status' => 'error',
                'message' => 'Maaf, tautan konfirmasi ini sudah kedaluwarsa. Silakan melakukan reservasi ulang.',
            ]);
        }

        $detailReservation = DetailReservation::where('reservation_id', $reservationId)->where('user_id', $userId)->first();

        if (!$detailReservation) {
            return view('confirmation', [
                'status' => 'error',
                'message' => 'Reservasi tidak ditemukan atau sudah dikonfirmasi.',
            ]);
        }

        if ($detailReservation->status !== 'Terverifikasi') {
            $detailReservation->update(['status' => 'Terverifikasi']);

            $unconfirmedCount = DetailReservation::where('reservation_id', $reservationId)->where('status', 'Menunggu')->count();

            $reservation = Reservation::find($reservationId);

            if ($unconfirmedCount == 0) {
                $reservation->update(['status' => 'Terverifikasi']);
                $this->sendReservationSummary($reservation);
            }

            return view('confirmation', [
                'status' => 'success',
                'message' => 'Terima kasih! Konfirmasi Anda berhasil.',
            ]);
        }

        return view('confirmation', [
            'status' => 'error',
            'message' => 'Reservasi ini sudah dikonfirmasi sebelumnya.',
        ]);
    }

    public function updateConfirmPresence($request)
    {
        $reservationCode = $request->query('code');
        $ip = $request->query('ip');

        // Cek apakah IP pengguna diizinkan
        if (!$this->isAllowedNetwork($ip)) {
            Log::info("IP {$ip} tidak diizinkan untuk mengkonfirmasi reservasi.");
            throw new \Exception('Pastikan konfirmasi menggunakan jaringan di H6 ya!');
        }

        $reservation = Reservation::where('code', $reservationCode)->first();
        if (!$reservation) {
            throw new ModelNotFoundException("Reservasi dengan kode {$reservationCode} tidak ditemukan.");
        }
        if ($reservation->status !== 'Terverifikasi') {
            throw new ModelNotFoundException("Reservasi dengan kode {$reservationCode} belum terverifikasi.");
        }

        // Validasi apakah di rentang waktu 1 jam
        if ($this->isReservationInViolationTime($reservation)) {
            $reservation->update(['status' => 'Pelanggaran']);
            DetailReservation::where('reservation_id', $reservation->id)->update(['status' => 'Pelanggaran']);

            $detailReservations = DetailReservation::where('reservation_id', $reservation->id)->get();
            $userPenaltyCount = null;

            foreach ($detailReservations as $detail) {
                $user = User::find($detail->user_id);
                if ($user) {
                    $user->increment('penalty_count');
                    $userPenaltyCount = $user->penalty_count; // Simpan jumlah pelanggaran terakhir
                }
            }

            $maxViolations = 3; // Batas maksimum pelanggaran
            $remainingChances = $maxViolations - $userPenaltyCount;

            throw new \Exception("Konfirmasi Anda sudah diterima! 🎉 Namun, karena Anda melakukan konfirmasi lebih dari 1 jam setelah waktu yang diizinkan, reservasi ini tercatat sebagai pelanggaran. Anda tetap bisa menggunakan meja yang telah dipesan. 😊\n\n" . "⚠️ *Peringatan*: Anda memiliki total $maxViolations kesempatan pelanggaran. Saat ini, Anda sudah memiliki $userPenaltyCount pelanggaran. " . ($remainingChances > 0 ? "Sisa kesempatan Anda tinggal $remainingChances kali lagi. Yuk, lebih disiplin ke depannya! 💪" : 'Ini adalah pelanggaran terakhir Anda. Selanjutnya, Anda mungkin akan kehilangan hak reservasi. 😟'));
        }

        $reservation->update(['status' => 'Terkonfirmasi']);
        DetailReservation::where('reservation_id', $reservation->id)->update(['status' => 'Terkonfirmasi']);
    }

    public function getReservationByCode($code)
    {
        // Cari reservasi berdasarkan kode
        $reservation = Reservation::with('details')->where('code', $code)->first();

        if (!$reservation) {
            return null;
        }

        // Hitung waktu mundur jika ada `expires_at`
        $remainingTime = null;
        if ($reservation->expires_at) {
            $now = Carbon::now();
            $expiresAt = Carbon::parse($reservation->expires_at);
            if ($now->lessThan($expiresAt)) {
                $remainingTime = $expiresAt->diffForHumans($now, true);
            }
        }

        // Format respons data
        return [
            'code' => $reservation->code,
            'status' => $reservation->status,
            'type' => $reservation->type,
            'purpose' => $reservation->purpose,
            'time_slot' => $reservation->time_slot,
            'date' => $reservation->date,
            'expires_at' => $reservation->expires_at,
            'remaining_time' => $remainingTime,
            'details' => $reservation->details->map(function ($detail) {
                return [
                    'user_id' => $detail->user_id,
                    'status' => $detail->status,
                ];
            }),
        ];
    }

    protected function createReservationDetails($reservation, $userId, $status)
    {
        DetailReservation::create([
            'user_id' => $userId,
            'reservation_id' => $reservation->id,
            'status' => $status,
        ]);
    }

    protected function sendConfirmationEmail($email, $reservation)
    {
        $user = $this->findUserByEmail($email);

        Mail::to($email)->send(new TeamReservationConfirmationMail($user, $reservation));
    }

    protected function sendReservationSummary($reservation)
    {
        $reservation->detailReservations->each(function ($detail) use ($reservation) {
            Mail::to($detail->user->email_mhs)->send(new ReservationSummaryMail($reservation));
        });
    }

    protected function findUserByEmail($email)
    {
        $user = User::where('email_mhs', $email)->first();

        if ($user) {
            return $user;
        }

        throw new ModelNotFoundException("User with email {$email} not found.");
    }

    private function generateReservationCode()
    {
        $timestamp = date('YmdHis');
        $randomNumber = rand(1000, 9999);
        $uniquePart = substr(md5(uniqid($timestamp, true)), 0, 6);

        $code = 'RSV' . $timestamp . $randomNumber . $uniquePart;
        return strtoupper($code);
    }

    private function isAllowedNetwork($ip)
    {
        return $ip === $this->allowedNetwork;
    }

    public function updateAvailableSeat($tableId, $reservation, $rollback = true)
    {
        $table = Table::find($tableId);
        if (!$table) {
            throw new \Exception("Table with ID {$tableId} not found.");
        }

        // Hitung total anggota, termasuk pembuat reservasi
        $totalPeople =
            $reservation->type === 'Kelompok'
                ? DetailReservation::where('reservation_id', $reservation->id)->count() // Semua anggota
                : 1; // Jika individu, hanya 1 orang

        // Jika rollback, tambahkan kembali seat
        $adjustment = $rollback ? $totalPeople : -$totalPeople;

        $newAvailableSeat = $table->available_seats + $adjustment;
        Log::info("New available seat for table ID {$tableId}: {$newAvailableSeat}");

        if ($newAvailableSeat < 0) {
            throw new \Exception("Not enough seats available for table ID {$tableId}. Required: {$totalPeople}, Available: {$table->available_seats}.");
        }

        // Set is_available based on newAvailableSeat and rollback condition
        if ($newAvailableSeat === 0) {
            $table->is_available = false;
        } elseif ($rollback && $newAvailableSeat > 0) {
            $table->is_available = true;
        }

        // Update the table with new available seats and availability status
        $table->update(['available_seats' => $newAvailableSeat, 'is_available' => $table->is_available]);
    }

    public function updateIsReserve($user, $rollback = false)
    {
        // Pastikan parameter $user adalah instance model User
        if (!$user instanceof User) {
            $user = User::find($user); // Cari user jika hanya berupa ID
        }

        if (!$user) {
            throw new \Exception('User tidak ditemukan.');
        }

        // Jika rollback, set is_reserve menjadi false
        $isReserve = $rollback ? false : true;

        // Update user dengan nilai baru
        $user->update(['is_reserve' => $isReserve]);
    }

    private function isReservationInViolationTime($reservation)
    {
        $now = Carbon::now();
        $reservationDate = Carbon::parse($reservation->date);
        $reservationStartTime = Carbon::createFromFormat('H:i', explode('-', $reservation->time_slot)[0]);

        // Gabungkan tanggal reservasi dengan waktu mulai
        $reservationStartDateTime = Carbon::create(
            $reservationDate->year,
            $reservationDate->month,
            $reservationDate->day,
            $reservationStartTime->hour,
            $reservationStartTime->minute
        );

        $oneHourBefore = $reservationStartDateTime->copy()->subHour();

        Log::info("Now: {$now}, Reservation start datetime: {$reservationStartDateTime}, One hour before: {$oneHourBefore}");

        // Periksa apakah reservasi hari ini dan waktu sekarang sudah melewati 1 jam sebelum waktu mulai
        return $now->greaterThanOrEqualTo($oneHourBefore) &&
               $reservation->date == $now->toDateString() &&
               $reservation->status == 'Terverifikasi';
    }

    private function isSeatAvailable(int $tableId, string $timeSlot, string $date): bool
    {
        // Cek apakah sudah ada reservasi dengan status selain dibatalkan di waktu tersebut
        $existingReservations = Reservation::where('table_id', $tableId)
            ->where('time_slot', $timeSlot)
            ->where('date', $date)
            ->whereNotIn('status', ['Dibatalkan', 'Selesai']) // Status yang tidak menghalangi ketersediaan
            ->exists();

        return !$existingReservations; // Tersedia jika tidak ada reservasi
    }
}
