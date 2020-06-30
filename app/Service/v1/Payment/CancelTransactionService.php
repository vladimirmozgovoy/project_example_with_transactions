<?php


namespace App\Service\v1\Payment;

use App\Helpers\Errors\ErrorMessages;
use App\Helpers\General\ResponseObject;
use App\Http\Controllers\API\v1\Main\UserController;
use App\Models\OrderModel;
use App\Models\PaymentBillItemModel;
use App\Models\PaymentBillModel;
use App\Models\TransactionsModel;
use App\Models\UserModel;
use App\Repositories\v1\OrderItemRepository;
use App\Repositories\v1\OrderRepository;
use App\Repositories\v1\PaymentBillItemRepository;
use App\Repositories\v1\PaymentBillRepository;
use App\Repositories\v1\TransactionsRepository;
use App\Repositories\v1\UserRepository;
use App\Service\v1\BaseService;
use App\StaticClasses\ProfileStatic;
use DB;
use PHPUnit\Util\RegularExpressionTest;


class CancelTransactionService extends TransactionService
{

    private $payment_bill_items_repo;
    private $order_item_repo;
    private $transaction_repo;

    public function __construct()
    {
        $this->payment_bill_items_repo = new PaymentBillItemRepository();
        $this->order_item_repo = new OrderItemRepository();
        $this->transaction_repo = new TransactionsRepository();
    }


    public function prepareCancelTransactions($transactions)
    {
        $initiator['admin'] = ProfileStatic::$user_id;
        $initiator = json_encode($initiator);
        $transactions_ids = [];

        foreach ($transactions as $items) {
            $transactions_ids[] = $items->id;

            $type = null;

            $user_id = $items->user_id;

            $transactions_base = [

                'user_id' => $user_id,
                'initiator' => $initiator,
                'sum' => $items->sum,
                'order_item_id' => $items->order_item_id,
                'status' => true,
                'flag_completed' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            switch ($items->type) {

                case 'DEBIT':
                    $type = 'REPLENISHMENT';
                    break;
                case 'REPLENISHMENT':
                    $type = 'DEBIT';
                    break;

            }

            $transactions_base['type'] = $type;

            $new_transactions[] = $transactions_base;

        }
        $result['new_transactions'] = $new_transactions;
        $result['transactions_ids'] = $transactions_ids;


        return $result;
    }


    public function changeBalancesUsers($transactions)
    {

        foreach ($transactions as $items) {
            $change = $this->baseChangeBalanceUsers($items['user_id'], $items['sum'], $items['type']);
            if (!$change) {
                return $change;
            }
        }
        return true;
    }

    public function saveTransactionsAndChangeBalanceWithDate($transactions,$payment_bill_items_model,$transaction_ids)
    {
        $save_transactions = TransactionsModel::insert($transactions);

        $order_item_id = $payment_bill_items_model->order_items_id;
        $order_item_model = $this->order_item_repo->getSingleClear(['where' => ['id' => $order_item_id]]);
        $set_cancel_transactions = $this->setCancelTransactions($transaction_ids);
        if(!$set_cancel_transactions){

        return false;

        }


        if (!$save_transactions) {

            return false;

        }

        $change_balance = $this->changeBalancesUsers($transactions);

        if (!$change_balance) {

            return false;

        }

        $change_debt_order_item = $this->changeDebtOrderItem($payment_bill_items_model,$order_item_model,'PLUS');

        if (!$change_debt_order_item) {

            return false;

        }
        $clear_payment_date_for_payment_bill_item = $this->setDatePaymentForPaymentBillItem($payment_bill_items_model->payment_bill_items_id,null);

        if(!$clear_payment_date_for_payment_bill_item){
            return false;
        }

        $clear_payment_date_for_order_item = $this->setDatePaymentForOrderItem($order_item_model,'CANCEL');

        if(!$clear_payment_date_for_order_item){
            return false;
        }

        return true;
    }


    public function setCancelTransactions($transactions_ids){
       $update =  TransactionsModel::whereIn('id',$transactions_ids)->update(['cancel'=> true]);
       if(!$update){
           return false;
       }
       else{
           return true;
       }
    }

    public function baseCancelTransactionByPaymentBillItemId($payment_bill_item_id){
        $response = new ResponseObject();

        $payment_bill_items_model = $this->payment_bill_items_repo->getItemToTransactions(['where' => ['payment_bill_items.id' => $payment_bill_item_id]]);

        $payment_bill_items_model = $payment_bill_items_model->first();

        // Проверка на сущестование счета

        if($payment_bill_items_model === null){
            $response->setIsError('PAYMENT_BILL_ITEM_NOT_FOUND', [], [ErrorMessages::getMessageByCode('PAYMENT_BILL_ITEM_NOT_FOUND')]);
            return $response;
        }

        // проверка счета  на  оплату

        if ($payment_bill_items_model->payment_bill_items_date_payed === null) {
            $response->setIsError('PAYMENT_BILL_ITEM_NOT_ALREADY_PAYMENT', [], [ErrorMessages::getMessageByCode('PAYMENT_BILL_ITEM_NOT_ALREADY_PAYMENT')]);
            return $response;
        }

        $params['where']['initiator->payment_bill_item'] = $payment_bill_item_id;
        $params['where']['cancel'] = false;
        $params['where']['flag_completed'] = true;

        $transactions = $this->transaction_repo->get($params);

        if(count($transactions) != 2){
            $response->setIsError('TRANSACTION_NON_UNFREEZE', [], [ErrorMessages::getMessageByCode('TRANSACTION_NON_UNFREEZE')]);

            return $response;
        }

        $result_prepare = $this->prepareCancelTransactions($transactions);


        $transactions = $result_prepare['new_transactions'];
        $transaction_ids = $result_prepare['transactions_ids'];
        $save = $this->saveTransactionsAndChangeBalanceWithDate($transactions,$payment_bill_items_model,$transaction_ids);

        if (!$save){
            $response->setIsError('NOT_CANCEL_TRANSACTIONS', [], ErrorMessages::getMessageByCode('NOT_CANCEL_TRANSACTIONS'));

        }
        else{
            $response->setIsSuccess('SUCCESS');
        }
        return $response;

    }

    public function cancelTransactionsByPaymentBillItemId($payment_bill_item_id)
    {

        $response = new ResponseObject();

        $cancel = $this->baseCancelTransactionByPaymentBillItemId($payment_bill_item_id);

        DB::beginTransaction();

        if (!$cancel->success) {
            DB::rollback();
            $response->setIsError('NOT_CANCEL_TRANSACTIONS', [], ErrorMessages::getMessageByCode('NOT_CANCEL_TRANSACTIONS'));
        } else {
            DB::commit();
            $response->setIsSuccess('SUCCESS', []);

        }
        return $response;
    }

    public function cancelTransactionsByOrderItemId($order_item_id){
        $response = new ResponseObject();


       $payment_bill_items_model = $this->payment_bill_items_repo->get(['where'=>['order_item_id'=> $order_item_id]]);
       foreach ($payment_bill_items_model as $items){

          DB::beginTransaction();

           $cancel = $this->baseCancelTransactionByPaymentBillItemId($items->id);

          if(!$cancel->success){
              DB::rollback();
             return $cancel;
          }
       }
       DB::commit();
       $response->setIsSuccess('SUCCESS');
       return $response;
    }


}
