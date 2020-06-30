<?php

namespace App\Repositories\v1;

use App\Models\OrdersModel;
use App\Repositories\v1\Base\BaseRepository;
use App\Services\v1\Permission\PermissionsService;
use DB;

class OrdersRepository extends BaseRepository
{
    const QUERY_FIELDS = [
        'ADMIN' => [
            'orders.id as orders_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.uuid as orders_uuid',
            'orders.created_at as orders_created_at',

        ],
        'EXPERT' => [
            'orders.id as orders_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.global_number as orders_global_number',
            'orders.uuid as orders_uuid',
            'orders.created_at as orders_created_at',


        ],
        'USER' => [
            'orders.id as orders_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.uuid as orders_uuid',
            'orders.created_at as orders_created_at',

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
        $query = OrdersModel::whereNull('orders.deleted_at');


        $query_select_fields = PermissionsService::getQueryFields(OrdersRepository::QUERY_FIELDS);
        $query->select($query_select_fields);

        return $query;
    }

    public function generateClearQuery()
    {
        $query = OrdersModel::whereNull('orders.deleted_at');
        $query->select([
            'orders.*',
        ]);

        return $query;
    }

    public function getOrdersWithPaymentBills($arQuery = []){
        $query = OrdersModel::whereNull('orders.deleted_at');
        $query = $query->whereNull('orders.date_payed');
        $query = $query->where('total_debt_sum', '>', '0');
        $query = $query->join('payment_bills', function ($join) {
           $join->on('payment_bills.order_id', '=', 'orders.id');
            $join->whereNull('payment_bills.deleted_at');
        });
        $query->select(
            [
            'orders.id as orders_id',
            'orders.total_sum as orders_total_sum',
            'orders.currency as orders_currency',
            'orders.status as orders_status',
            'orders.number as orders_number',
            'orders.user_id as orders_user_id',
            'orders.date_payed as orders_date_payed',
            'orders.total_debt_sum as orders_total_debt_sum',
            'orders.created_at as orders_created_at',
            'payment_bills.id as payment_bills_id',
            'payment_bills.total_sum as payment_bills_total_sum',
            'payment_bills.currency as payment_bills_currency',
            'payment_bills.payment_method_type as payment_bills_payment_method_type',
            'payment_bills.status as payment_bills_status',
            'payment_bills.name as payment_bills_name',
            'payment_bills.number as payment_bills_number',
            'payment_bills.description as payment_bills_description',
            'payment_bills.payment_system_crc_1 as payment_bills_payment_system_crc_1',
            'payment_bills.confirmation_url as payment_bills_confirmation_url',
            'payment_bills.order_id as payment_bills_order_id',
            'payment_bills.payed_date as payment_bills_payed_date',
            'payment_bills.created_at as payment_bills_created_at',
            ]
        );
     $query = $this->addQueryParam($arQuery,$query);
    return $query;
    }
    public function getOrdersPaymentBillsItems($arQuery = []){
        $query = OrdersModel::whereNull('orders.deleted_at');
        $query = $query->whereNull('orders.date_payed');
        $query = $query->where('total_debt_sum', '>', '0');
        $query = $query->join('order_items', function ($join) {
            $join->on('order_items.order_id', '=', 'orders.id');
            $join->whereNull('order_items.deleted_at');
            $join->where('order_items.month_installment','>',0);
        });

        $query = $query->join('payment_bills', function ($join) {
            $join->on('payment_bills.order_id', '=', 'orders.id');
            $join->whereNull('payment_bills.deleted_at');
        });
        $query = $query->join('payment_bill_items', function ($join) {
            $join->on('payment_bill_items.payment_bill_id', '=', 'payment_bills.id');
            $join->whereNull('payment_bill_items.deleted_at');
        });
        $query->select(
            [

              'payment_bill_items.*'
            ]
        );
        $query = $this->addQueryParam($arQuery,$query);
        return $query;
    }


}
