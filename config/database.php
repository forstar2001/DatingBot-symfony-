<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'homestead'),
            'username' => env('DB_USERNAME', 'homestead'),
            'password' => env('DB_PASSWORD', 'secret'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    //table prefix for dictionaries
    'dictionary_prefix' => env('DICTIONARY_TABLES_PREFIX','dictionary_'),
    'profile_detail_value_type_name_string' => env('PROFILE_DETAIL_VALUE_TYPE_STRING', 'string'),
    'profile_detail_value_type_name_integer' => env('PROFILE_DETAIL_VALUE_TYPE_INTEGER', 'integer'),
    'profile_detail_value_type_name_decimal' => env('PROFILE_DETAIL_VALUE_TYPE_DECIMAL', 'decimal'),
    'profile_detail_value_type_name_datetime' => env('PROFILE_DETAIL_VALUE_TYPE_DATETIME', 'datetime'),
    'profile_detail_value_type_name_time' => env('PROFILE_DETAIL_VALUE_TYPE_TIME', 'time'),
    'profile_detail_value_type_name_image' => env('PROFILE_DETAIL_VALUE_TYPE_IMAGE', 'image'),
    'profile_detail_value_type_name_document' => env('PROFILE_DETAIL_VALUE_TYPE_DOCUMENT', 'document'),
    'profile_detail_value_type_name_dictionary_single' => env('PROFILE_DETAIL_VALUE_TYPE_DICTIONARY_SINGLE', 'Single dictionary value'),
    'profile_detail_value_type_name_dictionary_multiple' => env('PROFILE_DETAIL_VALUE_TYPE_DICTIONARY_MULTIPLE', 'Multiple dictionary value'),
    'profile_detail_value_type_name_dictionary_country' => env('PROFILE_DETAIL_VALUE_TYPE_DICTIONARY_COUNTRY', 'Country'),
    'profile_detail_value_type_name_dictionary_city' => env('PROFILE_DETAIL_VALUE_TYPE_DICTIONARY_CITY', 'City'),
    'profile_detail_value_type_name_dictionary_region' => env('PROFILE_DETAIL_VALUE_TYPE_DICTIONARY_REGION', 'Region'),

    //bot profile details key parameters
    //'profile_detail_country_id' => env('PROFILE_DETAIL_COUNTRY_ID', ),
    //'profile_detail_region_id' => env('PROFILE_DETAIL_REGION_ID', ),
    'profile_detail_city_id' => env('PROFILE_DETAIL_CITY_ID', 49),
    'profile_detail_datetime_id' => env('PROFILE_DETAIL_DATETIME_ID', 77),
    'profile_detail_age_id' => env('PROFILE_DETAIL_AGE_ID', 54),
    'profile_detail_photo_id' => env('PROFILE_DETAIL_PHOTO_ID', 76),
    'profile_detail_realname_id' => env('PROFILE_DETAIL_REALNAME_ID', 53),

    'template_typing_id' => env('TEMPLATE_TYPING_ID', 19),
    'template_online_id' => env('TEMPLATE_ONLINE_ID', 20),
    'template_matched_with_id' => env('TEMPLATE_MATCHED_WITH_ID', 21),
    'template_type_message_id' => env('TEMPLATE_TYPE_MESSAGE_ID', 22),
    'template_offline_id' => env('TEMPLATE_OFFLINE_ID', 32),

    'template_category_default_message_id' => env('TEMPLATE_CATEGORY_DEFAULT_MESSAGE_ID', 2),

    'scenario_type_write_first_id' => env('SCENARIO_TYPE_WRITE_FIRST_ID', 2),
    
    'source_profile_status_active_id' => env('SOURCE_PROFILE_STATUS_ACTIVE_ID', 1),

    'frontend_profiles_result_amount' => env('FRONTEND_PROFILES_RESULT_AMOUNT', 100),
    'frontend_default_country_id' => env('FRONTEND_DEFAULT_COUNTRY_ID', 3),
    'frontend_default_drop_link' => env('FRONTEND_DEFAULT_DROP_LINK', 'https://melixas.com/#/adv/COW860'),

    'frontend_enabled_countries' => env('FRONTEND_ENABLED_COUNTRIES', [3,7]),

    'default_scenario_id' => env('DEFAULT_SCENARIO_ID', 10),

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
