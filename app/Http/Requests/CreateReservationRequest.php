<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'purpose' => 'required|in:' . implode(',', Reservation::PURPOSE_OPTIONS),
            'type' =>  'required|in:' . implode(',', Reservation::TYPE_OPTIONS),
            'email_mhs' => [
                'required',
                'array',
            ],
            'email_mhs.*' => [
                'email',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@mhs.dinus.ac.id')) {
                        $fail('Email harus menggunakan domain @mhs.dinus.ac.id.');
                    }

                    $localPart = substr($value, 0, strrpos($value, '@'));
                    if (strlen($localPart) != 12) {
                        $fail('Bagian sebelum @ harus memiliki tepat 12 karakter.');
                    }
                },
            ],
            'time_slot' => 'required|in:' . implode(',', Reservation::TIME_SLOT_OPTIONS),
            'table_id' => 'required|exists:tables,id',
        ];
    }
}
