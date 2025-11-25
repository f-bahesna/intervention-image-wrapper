<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @author frada <fbahezna@gmail.com>
 */
class ImageWrapper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'imagewrapper';
    }
}