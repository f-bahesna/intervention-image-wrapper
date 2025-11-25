<?php
declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraBaseTestCase;
use Fbahesna\InterventionImageWrapper\Facades\ImageWrapper;
use Fbahesna\InterventionImageWrapper\ImageWrapperServiceProvider;

/**
 * @author frada <fbahezna@gmail.com>
 */
class TestCase extends OrchestraBaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ImageWrapperServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'ImageWrapper' => ImageWrapper::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Fix: set storage path manually (no storagePath() needed)
        $storage = sys_get_temp_dir() . '/imagewrap_tests';
        $app->instance('path.storage', $storage);

        // Configure filesystem
        $app['config']->set('filesystems.default', 'local');
        $app['config']->set('filesystems.disks.local', [
            'driver' => 'local',
            'root'   => $storage, // use manual storage path
        ]);

        // Package config
        $app['config']->set('imagewrapper.disk', 'local');
        $app['config']->set('imagewrapper.quality', 85);
    }
}