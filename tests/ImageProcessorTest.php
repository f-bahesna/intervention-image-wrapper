<?php
declare(strict_types=1);

namespace Tests;

use Fbahesna\InterventionImageWrapper\Facades\ImageWrapper;
use Fbahesna\InterventionImageWrapper\Services\ImageWrapperService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @author frada <fbahezna@gmail.com>
 */
class ImageProcessorTest extends TestCase
{
    /*** @test */
    public function test_can_process_an_image()
    {
        parent::setUp();

        $storagePath = __DIR__ . '/output';
        if(!is_dir($storagePath)){
            mkdir($storagePath, 0777, true);
        }

        config()->set('imagewrapper.disk', 'local');
        config()->set('filesystems.disks.local',
            [
                'driver' => 'local',
                'root' => $storagePath,
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

        $this->assertFileExists($storagePath.'/'.$output);

        fwrite(STDOUT, "Output file is at: {$storagePath}/{$output}\n");
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

        fwrite(STDOUT, "[TEST] Successfully resizing image from ${originalSize} bytes to ${optimizedSize} bytes\n");
    }

    private function wrapperService()
    {
        return $this->app->make(ImageWrapperService::class);
    }
}