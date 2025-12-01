<?php
declare(strict_types=1);

namespace Tests;

use Fbahesna\InterventionImageWrapper\Facades\ImageWrapper;
use Fbahesna\InterventionImageWrapper\Services\ImageWrapperService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use \InvalidArgumentException;

/**
 * @author frada <fbahezna@gmail.com>
 */
class ImageProcessorTest extends TestCase
{
    private string $storagePath = './output';

    private string $baseImage = 'tests/images/horse.jpg';
    /*** @test */
    public function test_can_process_an_image()
    {
        parent::setUp();

        if(!is_dir($this->storagePath)){
            mkdir($this->storagePath, 0777, true);
        }

        config()->set('imagewrapper.disk', 'local');
        config()->set('filesystems.disks.local',
            [
                'driver' => 'local',
                'root' => $this->storagePath,
            ]
        );

        fwrite(STDOUT, "[TEST] Start processing image\n");

        $file = __DIR__.'/images/horse.jpg';
        $output = uniqid().'.jpg';

        ImageWrapper::load($file)
            ->resize(400, 400)
            ->optimize()
            ->store($output);

        fwrite(STDOUT, "[TEST] Finished processing image\n");

        $this->assertFileExists($this->storagePath.'/'.$output);

        fwrite(STDOUT, "Output file is at: {$this->storagePath}/{$output}\n");
    }

    public function test_it_can_resize_image()
    {
        Storage::fake('local');

        $image = UploadedFile::fake()->image('horse.jpg', 4000, 4000);

        $originalPath = __DIR__.'/images/horse.jpg';
        Storage::disk('local')->put($originalPath, file_get_contents($image->getRealPath()));

        $originalSize = Storage::disk('local')->size($originalPath);

        $optimizedPath = __DIR__.'/optimized/horse.jpg';

        fwrite(STDOUT, "[TEST] Start resizing image\n");

        $this->wrapperService()
            ->load($image)
            ->resize(700, 700)
            ->store($optimizedPath);

        $optimizedSize = Storage::disk('local')->size($optimizedPath);

        $this->assertLessThan(
            $originalSize,
            $optimizedSize,
            "Optimized should reduce the image size."
        );

        fwrite(STDOUT, "[TEST] Successfully resizing image from " .$originalSize. " bytes to ".$optimizedSize." bytes\n");
    }

    public function test_it_can_detects_corrupted_image()
    {
        Storage::fake('public');

        $corrupted = UploadedFile::fake()->image('corrupted.jpg', 4000, 4000);

        file_put_contents($corrupted->getRealPath(), 'not-a-real-image-binary');

        $this->expectException(InvalidArgumentException::class);

        ImageWrapper::load($corrupted)->resize(300, 300);
    }

    public function test_it_can_rejects_invalid_mime()
    {
        $file = UploadedFile::fake()->create('fake.pdf', 12, 'application/pdf');
//        $file = UploadedFile::fake()->image('corrupted.jpg', 4000, 4000);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported image type');

        ImageWrapper::load($file);
    }

    private function wrapperService()
    {
        return $this->app->make(ImageWrapperService::class);
    }
}