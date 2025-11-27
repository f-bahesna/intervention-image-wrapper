<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Traits;

/**
 * @author frada <fbahezna@gmail.com>
 */
trait ImageOptimization
{
    /*** Intervention v3 optimization happens on save() */
    public function optimize(?int $percent = 80): static
    {
        $tmpFile = tempnam($this->tmpDir, 'imgwrap_');

        $this->image->save($tmpFile, quality: $percent);

        $this->image = $this->manager->read($tmpFile);

        return $this;
    }
}