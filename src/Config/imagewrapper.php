<?php

/**
 * @author frada <fbahezna@gmail.com>
 */

return [
    'disk' => env('IMAGEWRAP_DISK', 'public'),

    'quality' => env('IMAGEWRAP_QUALITY', 85),

    'tmp_dir' => env('IMAGEWRAP_TMP', sys_get_temp_dir()),

    'intervention' => [
        'driver' => env('IMAGEWRAP_DRIVER', 'gd'), // or 'imagick'
    ],
];
