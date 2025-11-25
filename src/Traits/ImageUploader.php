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
    public function store(string $path): string
    {
        $tempPath = $this->tmpDir. '/'. uniqid('img_', true) . '.' . $this->format;

        $this->image->save($tempPath, quality: $this->quality);

        $stream = fopen($tempPath, 'r');
        Storage::disk($this->disk)->put($path, $stream);
        fclose($stream);

        @unlink($tempPath);

        return $path;
    }

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