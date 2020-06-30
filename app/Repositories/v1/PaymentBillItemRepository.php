<?php

namespace App\Repositories\v1;

use App\Models\PasswordResetModel;
use App\Models\PaymentBillItemModel;
use App\Models\PaymentBillModel;
use App\Models\ServiceModel;
use App\Repositories\v1\Base\BaseRepository;

class PaymentBillItemRepository extends BaseRepository
{
    const QUERY_FIELDS = [
        'ADMIN' => [

        ],
        'EXPERT' => [

        ],
        'USER' => [

        ]
    ];

    public function __construct()
    {

    }

    /**
     * Формируем запрос со всеми полями
     * @return \Illuminate\Database\Query\Builder
     */
    public function generateFullQuery()
    {
        $query = PaymentBillItemModel::whereNull('payment_bill_items.deleted_at');
        $query->select([
            'payment_bill_items.*',
        ]);

        $query->join('payment_bills', function ($join) {
            $join->on('payment_bills.id', '=', 'payment_bill_items.payment_bill_id');
            $join->whereNull('payment_bills.deleted_at');
        });

        return $query;
    }

    public function generateClearQuery()
    {
        $query = PaymentBillItemModel::whereNull('payment_bill_items.deleted_at');
        $query->select([
            'payment_bill_items.*',
        ]);


        return $query;
    }

    // @TODO AmoCRM
    public function createNullableModel($service_id, $count = 1)
    {
        $model_payment_bill_item = new PaymentBillItemModel();
        $model_payment_bill_item->count = $count;
        $model_payment_bill_item->price = 0;
        $model_payment_bill_item->full_price = 0;
        $model_payment_bill_item->total_sum_by_item = 0;
        $model_payment_bill_item->total_sum_full_price_by_item = 0;
        $model_payment_bill_item->service_id = $service_id;

        $model_payment_bill_item->status = 'pending';

        $model_payment_bill_item->create_request_date = date('Y-m-d H:i:s');

        return $model_payment_bill_item;
    }

    public function getItemToTransactions($arQuery = []){
        $query = PaymentBillItemModel::whereNull('payment_bill_items.deleted_at');

        $query
            ->join('order_items','payment_bill_items.order_item_id','=','order_items.id')
            ->join('orders','order_items.order_id','=','orders.id')
            ->join('services','payment_bill_items.service_id','=','services.id');
        $query->select([
            'payment_bill_items.id as payment_bill_items_id',
            'payment_bill_items.count as payment_bill_items_count',
            'payment_bill_items.total_sum_by_item as payment_bill_items_total_sum_by_item',
            'payment_bill_items.date_payed as payment_bill_items_date_payed',
            'orders.id as orders_id',
            'order_items.id as order_items_id',
            'order_items.status_processing as order_items_status_processing',
            'orders.user_id as orders_user_id',
            'services.id as services_id',
            'services.user_id as services_user_id',
        ]);

        $result = $this->addQueryParam($arQuery,$query);

        return $result;
    }

}
