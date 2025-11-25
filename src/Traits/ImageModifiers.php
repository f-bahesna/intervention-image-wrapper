<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * @author frada <fbahezna@gmail.com>
 */
trait ImageModifiers
{
    public function resize(?int $width, ?int $height = null): self
    {
        $this->image->resize($width, $height);
        return $this;
    }

    public function fit(?int $width, ?int $height = null): self
    {
        $this->image = $this->image->scaleDown($width, $height);
        return $this;
    }

    public function crop(int $width, int $height, int $x = 0, int $y = 0): static
    {
        $this->image = $this->image->crop($width, $height, $x, $y);
        return $this;
    }

    public function rotate(float $angle = 90): static
    {
        $this->image->rotate($angle);
        return $this;
    }

    public function blur(int $amount = 5): static
    {
        $this->image->blur($amount);
        return $this;
    }

    public function delete(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? ($this->config['disk'] ?? 'public');;
        return Storage::disk($disk)->delete($path);
    }
}