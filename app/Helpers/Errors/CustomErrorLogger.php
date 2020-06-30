<?php
/**
 * Created by PhpStorm.
 * User: XpoHo
 * Date: 05.12.2019
 */

namespace App\Helpers\Errors;

use Illuminate\Support\Facades\Log;

class CustomErrorLogger
{
    const ERROR_DRIVER = [
        'REGISTRATION_ERROR' => 'single_registration_error',
        'EMAIL_SEND_ERROR' => 'single_email_send_error',
        'PAYMENT_REQUEST' => 'single_payment_request',
        'VALIDATE_ERROR' => 'single_validate_error',
        'TASK_SEND_ERROR' => 'single_task_send_error',
        '401' => 'single_401',
        '403' => 'single_403',
        '404' => 'single_404',
        '423' => 'single_423',
        '500' => 'single_500',

        'TASK_FETCH_INFO' => 'single_task_fetch_info',
        'TASK_FETCH_ERROR' => 'single_task_fetch_error',
    ];

    static public function Log($code, $data)
    {
        if(key_exists($code, CustomErrorLogger::ERROR_DRIVER))
        {
            $additional_data = [
                'url' => \request()->fullUrl()
            ];

            $result_data = array_merge($additional_data, $data);

            Log::channel(CustomErrorLogger::ERROR_DRIVER[$code])->debug($code, $result_data);
            Log::channel(CustomErrorLogger::ERROR_DRIVER[$code])->debug(PHP_EOL.'|=================================|'.PHP_EOL);
        }
    }
}