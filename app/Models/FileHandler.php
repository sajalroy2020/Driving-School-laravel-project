<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class FileHandler extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'file_type',
        'storage_type',
        'original_name',
        'file_name',
        'user_id',
        'path',
        'extension',
        'size',
        'external_link',
    ];

    public function upload($to, $file, $name = NULL, $id = NULL)
    {

        try {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            if ($name == '') {
                $file_name = rand(000,999).time() . '.' . $extension;
            } else {
                $file_name = $name . '-' . time() . '.' . $extension;
            }
            $file_name = str_replace(' ', '_', $file_name);

//            Storage::put('uploads/' . $to . '/' . $file_name, file_get_contents($file->getRealPath()));

            Storage::disk(config('app.STORAGE_DRIVER'))
                ->put('uploads/' . $to . '/' . $file_name, file_get_contents($file->getRealPath()));


            $fileManager = (is_null($id)) ? new self() : self::find($id);
            $fileManager = is_null($fileManager) ?  new self() : $fileManager;
            $fileManager->file_type = $file->getMimeType();
            $fileManager->storage_type = config('filesystems.default');
            $fileManager->original_name = $originalName;
            $fileManager->file_name = $file_name;
            $fileManager->path = 'uploads/' . $to.'/'.$file_name;
            $fileManager->extension = $extension;
            $fileManager->size = $size;
            $fileManager->save();
            return $fileManager;

        } catch (\Exception $e) {
            return NULL;
        }
    }

    public function removeFile()
    {
        if (Storage::disk(config('app.STORAGE_DRIVER'))->exists($this->path)) {
            Storage::disk(config('app.STORAGE_DRIVER'))->delete($this->path);
            return 100;
        }
        return 200;
    }
}
