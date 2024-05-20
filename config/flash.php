<?php

return [

    /*
    |
    | Whether to throw the actual exception or to show the pretty error message.
    |
    | Set to "true" to throw the actual exception.
    | This is useful while in development, so you can easily see the error.
    |
    */
    'throw_errors' => env('FLASH_THROW_ERRORS', false),

    /*
    |
    | Whether to log the actual exception while still showing the pretty error message.
    |
    | Set to "true" to log the actual error.
    | This is useful while in production, so you have a way of debugging the exact issue.
    |
    */
    'log_errors' => env('FLASH_LOG_ERRORS', true),

];
