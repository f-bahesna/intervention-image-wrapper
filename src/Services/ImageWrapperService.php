<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Services;

use Fbahesna\InterventionImageWrapper\Traits\ImageOptimization;
use Fbahesna\InterventionImageWrapper\Traits\ImageModifiers;
use Fbahesna\InterventionImageWrapper\Traits\ImageUploader;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * @author frada <fbahezna@gmail.com>
 */
class ImageWrapperService
{
    use ImageOptimization, ImageModifiers, ImageUploader;
    protected ImageManager $manager;
    protected array $config;
    protected ImageInterface $image;
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

    /***
     * @param mixed $file
     * @return ImageWrapperService
     */
    public function load(mixed $file): self
    {
        $path = $this->assertFileLoader($file);
        $this->image = $this->manager->read($path);
        $this->format = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    private function assertFileLoader($source): mixed
    {
        if($source instanceof UploadedFile) {
            $source = $source->getRealPath();
        } elseif(is_string($source)) {
            if(!file_exists($source)) {
                throw new InvalidArgumentException('File does not exist');
            }
        } else {
            throw new InvalidArgumentException('File must be an instance of UploadedFile or a string');
        }

        return $source;
    }
}