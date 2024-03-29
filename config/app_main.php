<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ссылки с https
    |--------------------------------------------------------------------------
    |
    */
    'secure_url' => env('MAIN_SECURE_URL', false),

    /*
    |--------------------------------------------------------------------------
    | Название домена
    |--------------------------------------------------------------------------
    |
    */
    'domain_name' => env('MAIN_DOMAIN_NAME', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Ссылка для редиректа в CRM
    |--------------------------------------------------------------------------
    |
    */
    'crm_redirect_uri' => env('MAIN_CRM_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | Отладка
    |--------------------------------------------------------------------------
    |
    | memory_usege_log - запись в лог количества потребляемой памяти
    | database_query_log - запись в лог всех запросов к бд
    |
    */
    'debug' => [
        'memory_usage_log' => env('MAIN_DEBUG_MEMORY_USAGE_LOG', false),
        'database_query_log' => env('MAIN_DEBUG_DATABASE_QUERY_LOG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Состояние системы.
    |--------------------------------------------------------------------------
    |
    */
    'is_off_system' => env('MAIN_IS_OFF_SYSTEM', false),

];
