<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * @author frada <fbahezna@gmail.com>
 */
trait ImageModifiers
{
    /*
    |--------------------------------------------------------------------------
    | Resize
    |--------------------------------------------------------------------------
    | Force image into the exact width and height you provide
    | Image may become stretched
    */
    public function resize(?int $width, ?int $height = null): self
    {
        $this->image->resize($width, $height);
        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | fit
    |--------------------------------------------------------------------------
    | Ensure image never exceeds the given width and height
    | Edited by Aspect Ratio
    */
    public function fit(?int $width, ?int $height = null): self
    {
        $this->image->cover($width, $height);
        return $this;
    }

    /*
     |--------------------------------------------------------------------------
     | rotate
     |--------------------------------------------------------------------------
     | Rotate image by given angle (default: 90 degrees)
     */
    public function crop(int $width, int $height, int $x = 0, int $y = 0): static
    {
        $this->image->crop($width, $height, $x, $y);
        return $this;
    }

    /*
     |--------------------------------------------------------------------------
     | rotate
     |--------------------------------------------------------------------------
     | Rotate image by given angle (default: 90 degrees)
     */
    public function rotate(float $angle = 90): static
    {
        $this->image->rotate($angle);
        return $this;
    }

    /*
     |--------------------------------------------------------------------------
     | blur
     |--------------------------------------------------------------------------
     | Apply gaussian blur effect to the image
     */
    public function blur(int $amount = 5): static
    {
        $this->image->blur($amount);
        return $this;
    }

    /*
     |--------------------------------------------------------------------------
     | brightness
     |--------------------------------------------------------------------------
     | Adjust image brightness (positive = brighter, negative = darker)
     */
    public function brightness(?int $level = 30): static
    {
        $this->image->brightness($level);
        return $this;
    }

    /*
     |--------------------------------------------------------------------------
     | delete
     |--------------------------------------------------------------------------
     | Delete stored image from disk
     */
    public function delete(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? ($this->config['disk'] ?? 'public');;
        return Storage::disk($disk)->delete($path);
    }
}