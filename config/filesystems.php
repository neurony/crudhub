<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */
    'disks' => [

        'media' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media'),
            'url' => env('APP_URL').'/storage/media',
            'visibility' => 'public',
            'throw' => false,
        ],

        'media_unassigned' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media-unassigned'),
            'url' => env('APP_URL').'/storage/media-unassigned',
            'visibility' => 'public',
            'throw' => false,
        ],

    ],
];
