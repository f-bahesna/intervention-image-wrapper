<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Traits;

use Illuminate\Support\Str;

/**
 * @author frada <fbahezna@gmail.com>
 */
trait ImageOptimization
{
    /*** Intervention v3 optimization happens on save() */
    public function optimize(): static
    {
        $tmpFile = tempnam($this->tmpDir, 'imgwrap_');
        $this->image->save($tmpFile);

        $this->image = $this->manager->read($tmpFile);

        return $this;
    }

    protected function tempFilename(string $ext): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . 'imagewrap_' .
            Str::random(12). '.' . $ext;
    }
}