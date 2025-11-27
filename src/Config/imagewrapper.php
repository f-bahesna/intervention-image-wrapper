<?php

/**
 * @author frada <fbahezna@gmail.com>
 */

return [
    'disk' => env('IMAGE_WRAPPER_DISK', 'public'),

    'quality' => env('IMAGE_WRAPPER_QUALITY', 85),

    'tmp_dir' => env('IMAGE_WRAPPER_TMP', sys_get_temp_dir()),

    'intervention' => [
        'driver' => env('IMAGE_WRAPPER_DRIVER', 'gd'), // or 'imagick'
    ],
];
