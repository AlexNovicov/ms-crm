<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Controller settings
    |--------------------------------------------------------------------------
    */
    'route' => [
        'domain' => null,
        'path' => '/admin/skcWdsDdms/alt-log',
        'middleware' => ['web', /*'admin_access:admin_log'*/],
    ],

   /*
   |--------------------------------------------------------------------------
   | Logging settings
   |--------------------------------------------------------------------------
   */
    'logging' => [

        'default' => [
            'driver' => 'daily',
            'level' => 'debug',
            'max_files' => 3,
        ],

        'custom_log' => [

            //'log_name' => [
            //    'driver' => 'daily',
            //    'level' => 'debug',
            //    'max_files' => 3,
            //],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Grouping
    |--------------------------------------------------------------------------
    */
    'grouping' => [
        [
            'system_name' => 'error',
            'pattern' => 'error_*',
            'name' => 'Ошибки',
        ],
        [
            'system_name' => 'warning',
            'pattern' => 'warning_*',
            'name' => 'Предупреждения',
        ],
        [
            'system_name' => 'info',
            'pattern' => 'info_*',
            'name' => 'Информация',
        ],
    ],

   /*
   |--------------------------------------------------------------------------
   | View settings
   |--------------------------------------------------------------------------
   */
    'view' => [
        'back_url' => '/admin',
        'log_date_format' => 'HH:mm:ss DD.MM.YYYY',
    ],

    /*
    |--------------------------------------------------------------------------
    | File settings
    |--------------------------------------------------------------------------
    */
    'alt_logs_path' => storage_path('app/alt_logs/'),
    'max_file_size' => 1048576 * 50,

];
