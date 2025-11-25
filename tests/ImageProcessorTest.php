<?php
declare(strict_types=1);

namespace Tests;

use Fbahesna\InterventionImageWrapper\Facades\ImageWrapper;

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
}