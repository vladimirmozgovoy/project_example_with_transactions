<?php
/**
 * Created by PhpStorm.
 * User: XpoHo
 * Date: 13.08.2019
 * Time: 16:26
 */

namespace App\Helpers\General;

use App\Helpers\Errors\CustomErrorLogger;

class ResponseObject
{
    public $success;
    public $result_code;
    public $data;
    public $messages;

    function __construct()
    {
        $this->success = false;
        $this->result_code = 'NONE';
        $this->data = [];
        $this->messages = [];
    }

    // SET
    public function setData($data_ar, $expect_keys = ['success'])
    {
        foreach ($data_ar as $key => $item) {

            if (!in_array((string)$key, $expect_keys)) {
                $this->data[$key] = $item;
            }
        }

    }
    public function setMessages($messages_ar){
        foreach ($messages_ar as $key => $item) {
            $this->messages[$key] = $item;
        }
    }
    //
    public function setIsSuccess($result_code = 'OK', $data_ar = [], $messages_ar = []){
        $this->success = true;
        $this->result_code = $result_code;
        if(!empty($data_ar)){
            $this->setData($data_ar);
        }

        if(!empty($messages_ar)){
            $this->setMessages($messages_ar);
        }
    }

    public function setIsError($result_code = 'HAS_ERROR', $data_ar = [], $messages_ar = []){
        $this->success = false;
        $this->result_code = $result_code;

        if(!empty($data_ar)){
            $this->setData($data_ar);
        }

        if(!empty($messages_ar)){
            $this->setMessages($messages_ar);
        }
        CustomErrorLogger::Log('500', ['error' => $result_code, 'data' => \request()->all(), 'messages' => $messages_ar]);
    }

    // GET
    public function getDataKey($key){
        $result = null;
        if(key_exists($key, $this->data)){
            $result = $this->data[$key];
        }

        return $result;
    }

}
