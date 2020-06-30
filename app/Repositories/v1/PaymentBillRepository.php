<?php

namespace App\Repositories\v1;

use App\Models\PasswordResetModel;
use App\Models\PaymentBillModel;
use App\Models\ServiceModel;
use App\Repositories\v1\Base\BaseRepository;
use App\Services\v1\Permission\PermissionService;

class PaymentBillRepository extends BaseRepository
{
    const QUERY_FIELDS = [
        'ADMIN' => [
            'payment_bills.id as payment_bills_id',
            'payment_bills.total_sum as payment_bills_total_sum',
            'payment_bills.currency  as payment_bills_currency',
            'payment_bill_items.id as payment_bill_items_id',
            'payment_bill_items.total_sum_by_item as payment_bill_total_sum_by_item',
            'services.id as service_id',
            'services.user_id as service_user_id',
            'orders.user_id as orders_user_id',
            'orders.id as orders_id'
        ],
        'EXPERT' => [
            'payment_bills.id as payment_bills_id',
            'payment_bills.total_sum as payment_bills_total_sum',
            'payment_bills.currency  as payment_bills_currency',
            'payment_bill_items.id as payment_bill_items_id',
            'payment_bill_items.total_sum_by_item as payment_bill_total_sum_by_item',
            'services.id as service_id',
            'services.user_id as service_user_id',
            'orders.user_id as orders_user_id',
            'orders.id as orders_id'
        ],
        'USER' => [
            'payment_bills.id as payment_bills_id',
            'payment_bills.total_sum as payment_bills_total_sum',
            'payment_bills.currency  as payment_bills_currency',
            'payment_bill_items.id as payment_bill_items_id',
            'payment_bill_items.total_sum_by_item as payment_bill_total_sum_by_item',
            'services.id as service_id',
            'services.user_id as service_user_id',
            'orders.user_id as orders_user_id',
            'orders.id as orders_id'
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
        $query = PaymentBillModel::whereNull('payment_bills.deleted_at');

        $query->join('payment_bill_items','payment_bills.id','=','payment_bill_items.payment_bill_id');
        $query->join('services','payment_bill_items.service_id','=','services.id');
        $query->join('orders','payment_bills.order_id','=','orders.id');

        $query->join('orders', function ($join) {
            $join->on('orders.id', '=', 'payment_bills.order_id');
            $join->whereNull('orders.deleted_at');
        });

        $query_select_fields = PermissionService::getQueryFields(PaymentBillRepository::QUERY_FIELDS);
        $query->select($query_select_fields);

        return $query;
    }


    public function generateClearQuery()
    {
        $query = PaymentBillModel::whereNull('payment_bills.deleted_at');
        $query->select([
            'payment_bills.*',
        ]);

        return $query;
    }





}
