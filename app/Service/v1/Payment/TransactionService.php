<?php


namespace App\Service\v1\Payment;

use App\Helpers\Errors\ErrorMessages;
use App\Helpers\General\ResponseObject;
use App\Models\OrderModel;
use App\Models\PaymentBillItemModel;
use App\Models\PaymentBillModel;
use App\Models\TransactionsModel;
use App\Models\UserModel;
use App\Repositories\v1\OrderItemsRepository;
use App\Repositories\v1\OrdersRepository;
use App\Repositories\v1\PaymentBillItemRepository;
use App\Repositories\v1\PaymentBillRepository;
use App\Repositories\v1\TransactionsRepository;
use App\Repositories\v1\UsersRepository;
use App\Service\v1\BaseService;
use App\StaticClasses\ProfileStatic;
use DB;
use PHPUnit\Util\RegularExpressionTest;


class TransactionService extends BaseService
{

    private $payment_bill_items_repo;
    private $order_item_repo;
    private $transaction_repo;

    public function __construct()
    {
        $this->payment_bill_items_repo = new PaymentBillItemRepository();
        $this->order_item_repo = new OrderItemsRepository();
        $this->transaction_repo = new TransactionsRepository();
    }


    public function getAllTransactions()
    {
        $response = new ResponseObject();
        $params['where']['flag_completed'] = true;
        $params['where']['user_id'] = ProfileStatic::$user_id;

        $transaction_model = $this->transaction_repo->get($params);
        $response->setIsSuccess('SUCCESS',['items'=> $transaction_model]);

        return $response;
    }

    public function validateTransactions($data, $required = true)
    {
        $pattern = ['id' => ['integer']];
        return $this->validator($pattern, $data, $required);
    }






    public function createTransactionsByPaymentBillItemId($payment_bill_item_id)
    {
        $response = new ResponseObject();

       // Проверка на сущестование счета

        $payment_bill_items_model = $this->payment_bill_items_repo->getItemToTransactions(['where' => ['payment_bill_items.id' => $payment_bill_item_id]]);

        $payment_bill_items_model = $payment_bill_items_model->first();

        if($payment_bill_items_model === null){
            $response->setIsError('PAYMENT_BILL_ITEM_NOT_FOUND', [], [ErrorMessages::getMessageByCode('PAYMENT_BILL_ITEM_NOT_FOUND', [$id])]);
            return $response;
        }


        // проверка счета на оплату

        if (!$payment_bill_items_model->payment_bill_items_date_payed == null) {
            $response->setIsError('PAYMENT_BILL_ITEM_ALREADY_PAYMENT', [], [ErrorMessages::getMessageByCode('PAYMENT_BILL_ITEM_ALREADY_PAYMENT')]);
            return $response;
        }

        //проверка заказа  , что он не завершен
        $prove_finished = $this->proveOnFinishedOrderItem($payment_bill_items_model);
        if (!$prove_finished->success) {
            return $prove_finished;
        }

        //проверка заказа  , что он подтвержен экспертом
        $prove_accepted = $this->proveOnAcceptedOrderItem($payment_bill_items_model);
        if (!$prove_accepted->success) {
            return $prove_accepted;
        }

        // проверка баланса пользователя на создание транзакции
        $prove_balance = $this->proveOnBalanceUserByPaymentBillItems($payment_bill_items_model);
        if (!$prove_balance->success) {
            return $prove_balance;
        }

        DB::beginTransaction();
        $save = $this->saveAllItems($payment_bill_items_model, $payment_bill_item_id);

        if (!$save) {
            $response->setIsError('ERROR_ON_CREATE_TRANSACTION_BY_PAYMENT_BILL_ITEM', [], [ErrorMessages::getMessageByCode('ERROR_ON_CREATE_TRANSACTION_BY_PAYMENT_BILL_ITEM', [$payment_bill_item_id])]);
            DB::rollback();
            return $response;
        }
        DB::commit();
        $response->setIsSuccess('SUCCESS');
        return $response;
    }





    public function proveOnFinishedOrderItem($payment_bill_item_model)
    {
        $response = new ResponseObject();
        if ($payment_bill_item_model->order_items_status_processing == 'FINISHED') {
            $response->setIsError('ORDER_ITEM_ALREADY_FINISHED', [], [ErrorMessages::getMessageByCode('ORDER_ITEM_ALREADY_FINISHED')]);
            return $response;
        }

        $response->setIsSuccess('SUCCESS');
        return $response;
    }

    public function proveOnAcceptedOrderItem($payment_bill_item_model)
    {
        $response = new ResponseObject();
        if ($payment_bill_item_model->order_items_status_processing != 'ACCEPTED') {
            $response->setIsError('ORDER_ITEM_NOT_ACCEPTED', [], [ErrorMessages::getMessageByCode('ORDER_ITEM_NOT_ACCEPTED')]);
            return $response;
        }

        $response->setIsSuccess('SUCCESS');
        return $response;
    }

    public function proveOnBalanceUserByPaymentBillItems($payment_bill_items_model)
    {
        $response = new ResponseObject();
        $user_repo = new UserRepository();

        $user_model = $user_repo->getSingleClear(['where' => ['users.id' => $payment_bill_items_model->orders_user_id]]);
        $balance_user = floatval($user_model['balance']);

        if ($payment_bill_items_model->payment_bill_items_total_sum_by_item > $balance_user) {
            $response->setIsError('USER_BALANCE_NOT_ENOUGH_VALUE', [], [ErrorMessages::getMessageByCode('USER_BALANCE_NOT_ENOUGH_VALUE')]);
            return $response;
        }
        $response->setIsSuccess('SUCCESS');
        return $response;
    }

    public function saveAllItems($payment_bill_items_model, $payment_bill_item_id)
    {
        $user_from_id = $payment_bill_items_model->orders_user_id;

        $save_transactions = $this->saveOnlyTransaction($payment_bill_items_model, $user_from_id);

        $order_item_id = $payment_bill_items_model->order_items_id;
        $order_item_model = $this->order_item_repo->getSingleClear(['where' => ['id' => $order_item_id]]);


        if (!$save_transactions) {
            return $save_transactions;
        }

        $change_balance_user = $this->baseChangeBalanceUsers($user_from_id, $payment_bill_items_model->payment_bill_items_total_sum_by_item, 'DEBIT');
        if (!$change_balance_user) {
            return $change_balance_user;
        }


        $change_debt_order_item = $this->changeDebtOrderItem($payment_bill_items_model,$order_item_model);

        if (!$change_debt_order_item) {
            return $change_debt_order_item;
        }

        $payment_bill_item_id = intval($payment_bill_item_id);
        $set_payment_date_for_pbi = $this->setDatePaymentForPaymentBillItem($payment_bill_item_id,date('Y-m-d H:i:s'));
        if (!$set_payment_date_for_pbi) {
            return $set_payment_date_for_pbi;
        }

        $set_payment_date_for_oi = $this->setDatePaymentForOrderItem($order_item_model);
        if (!$set_payment_date_for_oi) {
            return $set_payment_date_for_oi;
        }

        return true;
    }

    public function saveOnlyTransaction($payment_bill_items_model, $user_from_id)
    {
        $transactions_model = new TransactionsModel();

        $transactions = $this->prepareTransactions($payment_bill_items_model, $user_from_id);

        $save_transactions = $transactions_model->insert($transactions);
        return $save_transactions;
    }

    public function prepareTransactions($payment_bill_item_model, $user_from_id)
    {


        $transactions = [];
        $initiator = json_encode(['payment_bill_item'=> $payment_bill_item_model->payment_bill_items_id]);

        $transactions[] =
            [
                'initiator' => $initiator,
                'user_id' => $user_from_id,
                'type' => 'DEBIT',
                'sum' => $payment_bill_item_model->payment_bill_items_total_sum_by_item,
                'order_item_id' => $payment_bill_item_model->order_items_id,
                'status' => true,
                'flag_completed' => true,
                'created_at' => date('Y-m-d H:i:s'),

            ];
        $transactions[] =
            [
                'initiator' => $initiator,
                'user_id' => $payment_bill_item_model->services_user_id,
                'type' => 'REPLENISHMENT',
                'sum' => $payment_bill_item_model->payment_bill_items_total_sum_by_item,
                'order_item_id' => $payment_bill_item_model->order_items_id,
                'status' => true,
                'flag_completed' => false,
                'created_at' => date('Y-m-d H:i:s'),
            ];

        return $transactions;
    }

    public function changeDebtOrderItem($payment_bill_items_model,$order_item_model,$type = 'MINUS')
    {

        switch ($type){
            case  'PLUS' :   $order_item_model->debt_sum += $payment_bill_items_model->payment_bill_items_total_sum_by_item;
            case  'MINUS' :   $order_item_model->debt_sum -= $payment_bill_items_model->payment_bill_items_total_sum_by_item;
        }

        $update = $order_item_model->save();

        if (!$update) {

            return $update;
        }

        return true;
    }

    public function setDatePaymentForPaymentBillItem($payment_bill_item_id,$date )
    {
        $payment_bill_items_model = PaymentBillItemModel::where('id', '=', $payment_bill_item_id)->first();

        $payment_bill_items_model->date_payed = $date;
        $save = $payment_bill_items_model->save();

        return $save;
    }

    public function setDatePaymentForOrderItem($order_item_model,$type = null)
    {

        if($order_item_model === null){
           return false;
        }
         $save = true;

        if ($order_item_model->debt_sum == 0 and $type === null) {
            $order_item_model->date_payed = date('Y-m-d H:i:s');
            $save = $order_item_model->save();
        }
        if($type == 'CANCEL'){
            $order_item_model->date_payed = null;
            $save = $order_item_model->save();
        }
        return $save;
    }
}
