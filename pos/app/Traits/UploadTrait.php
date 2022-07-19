<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    public function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public_folder', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Carbon::now()->toDateString() . str_random(15);

        $file = $uploadedFile->storeAs($folder, $name . '.' . $uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }

    public function deleteOne($folder = null, $disk = 'public_folder', $filename = null)
    {
        Storage::disk($disk)->delete($folder . $filename);
    }

    public function deleteOneFullPath($fullFilePath = null, $disk = 'public_folder')
    {
        Storage::disk($disk)->delete($fullFilePath);
    }
}
