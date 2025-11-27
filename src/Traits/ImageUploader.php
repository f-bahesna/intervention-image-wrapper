<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @author frada <fbahezna@gmail.com>
 */
trait ImageUploader
{
    /*
     |--------------------------------------------------------------------------
     | store
     |--------------------------------------------------------------------------
     | Save image into path you provide
     | Make directory if not exists
     | Last method you call it
     */
    public function store(string $path): string
    {
        $dir = dirname($path);
        if(!Storage::disk($this->disk)->exists($dir)) {
            Storage::disk($this->disk)->makeDirectory($dir);
        }

        $encoded = $this->image->encode();
        $binary = $encoded->toString();

        Storage::disk($this->disk)->put($path, $binary);

        // return path if supported (S3, OSS, etc)
        if (method_exists(Storage::disk($this->disk), 'url')) {
            return Storage::disk($this->disk)->url($path);
        }

        return $path;
    }

    /*
     |--------------------------------------------------------------------------
     | upload
     |--------------------------------------------------------------------------
     |  Handle image upload: load the file, apply edits, then save it.
     */
    public function upload(
        UploadedFile $file,
        string $dir = 'images',
        ?string $name = null,
        array $options = []
    ): string {
        $this->load($file->getRealPath());

        if(isset($options['resize'])){
            [$w, $h] = $options['resize'];
            $this->resize($w, $h);
        }

        if(isset($options['fit'])){
            [$w, $h] = $options['fit'];
            $this->resize($w, $h);
        }

        $filename = $name ?? uniqid() . "." . $this->format;

        return $this->store(trim($dir, '/') . '/' . $filename);
    }
}