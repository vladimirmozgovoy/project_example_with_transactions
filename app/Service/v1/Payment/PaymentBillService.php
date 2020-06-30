<?php


namespace App\Service\v1\Payment;


use App\Helpers\Errors\ErrorMessages;
use App\Helpers\General\ResponseObject;
use App\Models\PaymentBillItemModel;
use App\Repositories\v1\OrderItemRepository;
use App\Repositories\v1\PaymentBillItemRepository;
use App\Repositories\v1\PaymentBillRepository;
use App\Service\v1\BaseService;
use DB;


class PaymentBillService extends  BaseService
{

    /**
     * @var PaymentBillItemRepository
     */
    private $payment_bill_item_repo;

    public function __construct()
    {

    }

    public function preparePaymentBillItems($model_order_items_map)
    {
        $model_payment_bill_items = [];

        foreach ($model_order_items_map as $item_map) {
            $model_payment_bill_item = new PaymentBillItemModel();
            $model_payment_bill_item->count = $item_map['model']['count'];
            $model_payment_bill_item->price =  $item_map['sum'];
            $model_payment_bill_item->full_price = $item_map['sum'];
            $model_payment_bill_item->total_sum_by_item = $item_map['sum'];
            $model_payment_bill_item->total_sum_full_price_by_item = $item_map['sum'];
            $model_payment_bill_item->service_id = $item_map['model']['service_id'];
            $model_payment_bill_item->order_item_id = $item_map['model']['id'];

            if ($item_map['model']['currency'] != null) {
                $model_payment_bill_item->currency = $item_map['model']['currency'];
            }

            $model_payment_bill_item->status = 'pending';
            $model_payment_bill_item->create_request_date = date('Y-m-d H:i:s');
            $model_payment_bill_items[] = $model_payment_bill_item;
        }

        return $model_payment_bill_items;
    }





    public function proveOnSumPaymentBillService($order_item_model, $sum)
    {
        $response = new ResponseObject();

        $payment_bill_item_repo = new PaymentBillItemRepository();
        $payment_bill_item_model = $payment_bill_item_repo->getClear(['where'=>['order_item_id' => $order_item_model['id']]]);

        $sum_order_item = $order_item_model['total_sum_by_item'];

        $payment_bill_sum = 0;
        $between = $sum_order_item;
        if(count($payment_bill_item_model) > 0){
            foreach ($payment_bill_item_model as $item){
                $payment_bill_sum = $item['total_sum_by_item']+$payment_bill_sum;
            }

        }

        $between = $between - $payment_bill_sum;
        if($sum > $between){
            $response->setIsError('CREATE_PAYMENT_BILL_ITEM_SUM_EXCEED_DEBT_SUM', [], [ErrorMessages::getMessageByCode('CREATE_PAYMENT_BILL_ITEM_SUM_EXCEED_DEBT_SUM', [$sum, $order_item_model['id'], $between])]);
            return $response;
        }

        $response->setIsSuccess('SUCCESS');
        return $response;
    }


    public function getPaymentBillById($id){
        $response = new ResponseObject();
        $payment_bill_repo =  new PaymentBillRepository();
        $payment_bill =  $payment_bill_repo->getSingleClear(['where'=> ['payment_bills.id'=>$id]]);
        $response->setIsSuccess('SUCCESS',['item'=> $payment_bill]);
        return $response;

    }


    public function proveOrderItems($data)
    {
        $response = new ResponseObject();

        //
        $order_items_ids = [];
        foreach ($data as $item){
            $order_items_ids[] = $item['order_item_id'];
        }

        //Проверка на существование order_items
        $order_item_repo = new OrderItemRepository();
        $model_order_items_list = $order_item_repo->getClear(['whereIn'=>['order_items.id'=>$order_items_ids]]);

        if(count($order_items_ids) != count($model_order_items_list)){
            $response->setIsError('ORDER_ITEMS_NOT_EXIST_IN_LIST', [], [ErrorMessages::getMessageByCode('ORDER_ITEMS_NOT_EXIST_IN_LIST')]);
            return $response;
        }

        // проверка на сумму
        foreach ($data as $item){
            foreach ($model_order_items_list as $model_order_item) {

                if($model_order_item['id'] == $item['order_item_id']){
                    $prove_on_sum = $this->proveOnSumPaymentBillService($model_order_item, $item['sum']);
                    if(!$prove_on_sum->success){
                        return $prove_on_sum;
                    }
                }
            }
        }

        $response->setIsSuccess('SUCCESS');
        return $response;
    }


    public function validatorPaymentBillItems($data)
    {
        $pattern = [
            'order_item_id' => ['integer'],
            'sum' => ['numeric'],
        ];
        $validator = $this->validator($pattern, $data, true);
        return $validator;
    }

    public function validationPaymentBillItems($data)
    {
        $response = new ResponseObject();

        foreach ($data as $item){
            // проверяем что переданы правильные параметры
            if(!is_array($item)){
                $response->setIsError('PAYMENT_BILL_ITEM_CREATE_NOT_VALID_PARAM', [], [ErrorMessages::getMessageByCode('PAYMENT_BILL_ITEM_CREATE_NOT_VALID_PARAM')]);
                return $response;
            }

            $validator = $this->validatorPaymentBillItems($item);
            if(!$validator->success){
                return  $validator;
            }
        }

        $prove_orders_items = $this->proveOrderItems($data);
        if(!$prove_orders_items->success){
            return $prove_orders_items;
        }

        $response->setIsSuccess('SUCCESS');
        return $response;
    }



    public function  prepareMapOrderItem($model_order_items,$data)
    {
        //prepare map
        $order_items_map = [];
        foreach ($model_order_items as $order_items){
            foreach ($data as $data_items){
                if($order_items['id'] == $data_items['order_item_id']){
                    $order_items_map[] =
                    [
                        'model' => $order_items,
                        'order_item_id' => $order_items['id'],
                        'order_total_sum_by_item' => floatval($order_items->total_sum_by_item),
                        'sum' =>$data_items['sum']
                    ];
                }
            }
        }

        return $order_items_map;
    }



    public function prepareSavePaymentBillItems($data)
    {
        $validation = $this->validationPaymentBillItems($data);

        if(!$validation->success){
            return $validation;
        }

        $result = true;
        $model_order_items_ids = [];

        foreach ($data as $items){
            $model_order_items_ids[] = $items['order_item_id'];
        }

        $order_repo_items= new OrderItemRepository();

        $model_order_items = $order_repo_items->getClear(['whereIn'=>['order_items.id'=> $model_order_items_ids]]);
        $order_items_map = $this->prepareMapOrderItem($model_order_items,$data);

        // payment_bill_items
        $model_payment_bill_items = $this->preparePaymentBillItems($order_items_map);

        foreach ( $model_payment_bill_items as $item_save){
            if (!$item_save->save()) {
                $result = false;
            }
        }
        return $result;
    }

    public function savePaymentBillItems($data)
    {
        $response = new ResponseObject();

        DB::beginTransaction();

        // убираем запросы, которые создают счета с нулевыми суммами
        $data__parsed = [];
        foreach ($data as $data_item) {
            if($data_item['sum'] > 0){
                $data__parsed[] = $data_item;
            }
        }

        $prepare_save_payment_bill_items = $this->prepareSavePaymentBillItems($data__parsed);

        if(!$prepare_save_payment_bill_items) {
            DB::rollback();
            $response->setIsError('PAYMENT_BILL_ITEMS_NOT_SAVED', [], [ErrorMessages::getMessageByCode('PAYMENT_BILL_ITEMS_NOT_SAVED')]);
        }

        DB::commit();
        $response->setIsSuccess('SUCCESS_CREATE_PAYMENT_BILL_ITEMS', ['payment bill items are created success. created items count='.count($data__parsed)], ['payment bill items are created success. count='.count($data__parsed)]);
        return $response;
    }

}
