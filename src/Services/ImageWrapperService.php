<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Services;

use Fbahesna\InterventionImageWrapper\Traits\ImageOptimization;
use Fbahesna\InterventionImageWrapper\Traits\ImageModifiers;
use Fbahesna\InterventionImageWrapper\Traits\ImageUploader;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use InvalidArgumentException;
use Symfony\Component\Mime\MimeTypes;

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

        $driver = $this->config['intervention']['driver'] === 'gd'
                ? new GdDriver()
                : new ImagickDriver();

        $this->manager = new ImageManager($driver);

        $this->disk = $config['disk'];
        $this->quality = $this->sanitizeInt($config['quality'], 85);
        $this->tmpDir = $config['tmp_dir'] ?? sys_get_temp_dir();
    }

    /***
     * @param mixed $file
     * @return ImageWrapperService
     */
    public function load(mixed $file): self
    {
        $path = $this->assertFileLoader($file);

        $this->validateExtension($file);

        $this->ensureImageIsReadable($path);

        $this->format = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

        return $this;
    }

    public function driver()
    {
        return $this->driver();
    }

    /**
     * @param UploadedFile|string $file
     * Validate extension
     */
    private function validateExtension(UploadedFile|string $file): void
    {
        $mime = $file instanceof UploadedFile ? $file->getMimeType() : MimeTypes::getDefault()->guessMimeType($file);

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if(!in_array($mime, $allowed, true)) {
            throw new InvalidArgumentException("Unsupported image type: {$mime}");
        }
    }

    /**
     * check for corrupted image or assign corrected image
     */
    private function ensureImageIsReadable(mixed $path): void
    {
        try {
            $this->image = $this->manager->read($path);
        }catch (DecoderException $exception){
            throw new InvalidArgumentException("Corrupted or unreadable image file.", 0, $exception);
        }
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
            throw new InvalidArgumentException('File must be an instance of UploadedFile or a string path');
        }

        return $source;
    }

    private function sanitizeInt($value, ?int $default): int
    {
        return is_numeric($value) ? (int) $value : $default;
    }
}