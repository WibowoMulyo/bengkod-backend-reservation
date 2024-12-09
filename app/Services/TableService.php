<?php

namespace App\Services;

use App\Models\Table;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class TableService
{
    public function getAllTable()
    {
        $tables = Table::all();

        foreach($tables as $table){
            if ($table->thumbnail) {
                $table->thumbnail = url("storage/thumbnails/{$table->thumbnail}");
            }
        }
        return $table;
    }

    public function getTableById($id)
    {
        $table = Table::findOrFail($id);

        if (!$table) {
            throw new ModelNotFoundException("Table with ID $id not found.");
        }

        if ($table->thumbnail) {
            $table->thumbnail = url("storage/thumbnails/{$table->thumbnail}");
        }
        return $table;
    }

    public function createTable(array $data)
    {
        if ($data['thumbnail']) {
            $thumbnailPath = $data['thumbnail']->store('public/thumbnails');
            $data['thumbnail'] = basename($thumbnailPath);
        }

        Table::create($data);
        return (object)[];
    }

    public function updateTable($id, array $data)
    {
        $table = $this->getTableById($id);
        
        if(isset($data['thumbnail'])){
            if ($table->thumbnail) {
                Storage::delete('public/thumbnails/' . $table->thumbnail);
            }
            
            $thumbnailPath = $data['thumbnail']->store('public/thumbnails');
            $data['thumbnail'] = basename($thumbnailPath);
        }

        $table->update($data);
        return (object)[];
    }

    public function destroy($id)
    {
        $table = $this->getTableById($id);
        $table->delete();
    }
}
