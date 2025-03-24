<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    "default" => env("FILESYSTEM_DISK", "local"),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    "disks" => [
        "local" => [
            "driver" => "local",
            "root" => storage_path("app/private"),
            "serve" => true,
            "throw" => false,
        ],

        "public" => [
            "driver" => "local",
            "root" => storage_path("app/public"),
            "url" => env("APP_URL") . "/storage",
            "visibility" => "public",
            "throw" => false,
        ],

        "s3" => [
            "driver" => "s3",
            "key" => env("AWS_ACCESS_KEY_ID"),
            "secret" => env("AWS_SECRET_ACCESS_KEY"),
            "region" => env("AWS_DEFAULT_REGION"),
            "bucket" => env("AWS_BUCKET"),
            "url" => env("AWS_URL"),
            "endpoint" => env("AWS_ENDPOINT"),
            "use_path_style_endpoint" => env(
                "AWS_USE_PATH_STYLE_ENDPOINT",
                false
            ),
            "throw" => false,
        ],

        "private" => [
            "driver" => "local",
            "root" => storage_path("app/private"),
            "visibility" => "private",
        ],

        "tws3" => [
            "driver" => "s3", // Этот параметр остается неизменным
            "key" => env("TW_ACCESS_KEY_ID"), // Указываем переменную окружения для ключа доступа
            "secret" => env("TW_SECRET_ACCESS_KEY"), // Указываем переменную окружения для секретного ключа
            "region" => env("TW_DEFAULT_REGION"), // Регион Timeweb Cloud
            "bucket" => env("TW_BUCKET"), // Имя бакета
            "url" => env("TW_URL"), // URL для доступа к бакету
            "endpoint" => env("TW_ENDPOINT"), // Кастомный endpoint для Timeweb S3
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    "links" => [
        public_path("storage") => storage_path("app/public"),
    ],
];
