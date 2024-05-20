<?php

return [

    /*
    |
    | The disk on which to store added files and derived images by default.
    | Choose one of the disks you've configured in config/filesystems.php.
    |
    | IMPORTANT!!!
    | This setting overwrites the "media-library.disk_name" config option.
    |
    */
    'disk_name' => env('MEDIA_DISK', 'media'),

    /*
    |
    | The fully qualified class name of the media model.
    |
    | IMPORTANT!!!
    | This setting overwrites the "media-library.media_model" config option.
    |
    */
    'media_model' => \Zbiller\Crudhub\Models\Media::class,

];
