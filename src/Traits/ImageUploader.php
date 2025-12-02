<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

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
        $disk = Storage::disk($this->disk);

        if(!$disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $disk->put($path, $this->encode($ext));

        // return path if supported (S3, OSS, etc)
        if (method_exists($disk, 'url')) {
            return $disk->url($path);
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

    /**
     * The new encoding rules for intervention image V3
     * jpeg2000 uses modern wavelet based compression that can be both lossy and lossless.
     */
    private function encode($ext): string
    {
        $this->validateEncode($ext);

        return match ($ext) {
            'jpg', 'jpeg'   => $this->image->toJpeg($this->quality)->toString(),
            'png'           => $this->image->toPng()->toString(),
            'webp'          => $this->image->toWebp($this->quality)->toString(),
            'jpeg2000'      => $this->image->toJpeg2000($this->quality)->toString(),
            default         => $this->image->toJpeg($this->quality)->toString(),
        };
    }

    private function validateEncode($ext): void
    {
        if(!in_array($ext, $this->allowedExtensions)) {
            throw new InvalidArgumentException('Unsupported output format [$ext]');
        }
    }
}