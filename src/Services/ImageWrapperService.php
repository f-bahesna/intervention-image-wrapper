<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Services;

use Fbahesna\InterventionImageWrapper\Traits\ImageOptimization;
use Fbahesna\InterventionImageWrapper\Traits\ImageModifiers;
use Fbahesna\InterventionImageWrapper\Traits\ImageUploader;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

/**
 * @author frada <fbahezna@gmail.com>
 */
class ImageWrapperService
{
    use ImageOptimization, ImageModifiers, ImageUploader;
    protected ImageManager $manager;
    protected array $config;

    protected $image;
    protected string $format = 'jpg';
    protected int $quality;
    protected string $disk;
    protected string $tmpDir;

    public function __construct(array $config)
    {
        $this->config = $config;

        $driver = $this->config['intervention']['driver']
                ? new GdDriver()
                : new ImagickDriver();

        $this->manager = new ImageManager($driver);

        $this->disk = $config['disk'];
        $this->quality = $config['quality'] ?? 85;
        $this->tmpDir = $config['tmp_dir'] ?? sys_get_temp_dir();
    }

    public function load(string $path): self
    {
        $this->image = $this->manager->read($path);
        $this->format = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }
}