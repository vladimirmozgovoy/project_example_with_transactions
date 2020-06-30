<?php
/**
 * Created by PhpStorm.
 * User: XpoHo
 * Date: 21.10.2019
 * Time: 20:06
 */

namespace App\Http\Controllers\API\v1\Main;

use App\Http\Controllers\API\v1\Base\BaseController;
use App\Service\v1\Payment\TransactionService;
use App\Services\v1\CourseService;
use App\Services\v1\LessonsService;
use App\Services\v1\PasswordRecoveryService;

use App\Services\v1\ServicesService;
use App\Services\v1\TestService;
use Illuminate\Http\Request;

class TransactionController extends BaseController
{
    // создаем переменные
    protected $test_service;
    /**
     * @var TransactionService
     */
    private $transaction_service;

    function __construct()
    {
        // инициализируем сервис
        $this->transaction_service = new TransactionService();
    }


    public function create(Request $request)
    {
        $data = $request->post();
        $validate = $this->transaction_service->validateTransactions($data);
        if (!$validate->success) {
            return $this->sendError($validate->result_code, $validate->messages);
        }

        $create = $this->transaction_service->createTransactionsByPaymentBillItemId($data['id']);
        if ($create->success === true) {
            return $this->sendResponse($create->result_code, $create->data);
        }
        else {
            return $this->sendError($create->result_code, $create->messages);
        }
    }


    public function getAll(){
        $transactions =  $this->transaction_service->getAllTransactions();
        if($transactions->success === true){
            return $this->sendResponse($transactions->result_code, $transactions->data);
        }
        else {
            return $this->sendError($transactions->result_code,$transactions->messages);
        }
    }


}
