<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],


        // CUSTOM
        'single_registration_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/registration_error/error.log'),
            'level' => 'debug',
        ],
        'single_email_send_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/email_send_error/error.log'),
            'level' => 'debug',
        ],
        'single_payment_request' => [
            'driver' => 'daily',
            'path' => storage_path('logs/payment_request/error.log'),
            'level' => 'debug',
        ],
        'single_validate_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/validate_error/error.log'),
            'level' => 'debug',
        ],
        'single_task_send_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/single_task_send_error/error.log'),
            'level' => 'debug',
        ],
        'single_401' => [
            'driver' => 'daily',
            'path' => storage_path('logs/401/error.log'),
            'level' => 'debug',
        ],
        'single_403' => [
            'driver' => 'daily',
            'path' => storage_path('logs/403/error.log'),
            'level' => 'debug',
        ],
        'single_404' => [
            'driver' => 'daily',
            'path' => storage_path('logs/404/error.log'),
            'level' => 'debug',
        ],
        'single_423' => [
            'driver' => 'daily',
            'path' => storage_path('logs/423/error.log'),
            'level' => 'debug',
        ],
        'single_500' => [
            'driver' => 'daily',
            'path' => storage_path('logs/500/error.log'),
            'level' => 'debug',
        ],

        'single_task_fetch_info' => [
            'driver' => 'daily',
            'path' => storage_path('logs/single_task_fetch/info.log'),
            'level' => 'debug',
        ],
        'single_task_fetch_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/single_task_fetch/error.log'),
            'level' => 'debug',
        ],
    ],

];
