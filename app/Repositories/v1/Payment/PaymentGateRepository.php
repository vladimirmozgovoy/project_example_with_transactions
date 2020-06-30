<?php

namespace App\Repositories\v1\Payment;

use App\Helpers\Http\PaginateHelper;
use App\Models\OrderModel;
use App\Repositories\v1\Base\BaseRepository;

class PaymentGateRepository extends BaseRepository
{
    public function __construct()
    {

    }

    /**
     * Формируем запрос со всеми полями
     * @return \Illuminate\Database\Query\Builder
     */
    public function generateFullQuery()
    {
        $query = OrderModel::whereNull('orders.deleted_at');
        $query->select([
            'orders.id as orders_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.global_number as orders_global_number',
            'orders.uuid as orders_uuid',
            'orders.created_at as orders_created_at',
            'orders.status as orders_status',

            'order_items.status as order_items_status',
            'order_items.id as order_items_id',
            'order_items.total_sum_by_item as order_items_total_sum_by_item',
            'order_items.total_sum_full_price_by_item as order_items_total_sum_full_price_by_item',
            'order_items.price as order_items_price',
            'order_items.full_price as order_items_full_price',
            'order_items.count as order_items_count',
            'order_items.currency as order_items_currency',
            'order_items.service_id as order_items_service_id',
            'order_items.order_id as order_items_order_id',

            'users.id as users_id',
            'users.first_name as users_first_name',
            'users.last_name as users_last_name',
            'users.second_name as users_second_name',

            'services.name as services_name',
            'services.description as services_description',
            'services.price_text as services_price_text',

            'payment_bills.id as payment_bills_id',
            'payment_bills.order_id as payment_bills_order_id',
            'payment_bills.total_sum as payment_bills_total_sum',
            'payment_bills.number as payment_bills_number',
            'payment_bills.status as payment_bills_status',
            'payment_bills.confirmation_url as payment_bills_confirmation_url',

            'payment_bills.create_request_date as payment_bills_create_request_date',
            'payment_bills.payed_date as payment_bills_payed_date',

            'payment_bill_items.id as payment_bill_items_id',
            'payment_bill_items.status as payment_bill_items_status',
            'payment_bill_items.total_sum_by_item as payment_bill_items_total_sum_by_item',
            'payment_bill_items.total_sum_full_price_by_item as payment_bill_items_total_sum_full_price_by_item',
            'payment_bill_items.price as payment_bill_items_price',
            'payment_bill_items.full_price as payment_bill_items_full_price',
            'payment_bill_items.count as payment_bill_items_count',
            'payment_bill_items.currency as payment_bill_items_currency',
            'payment_bill_items.service_id as payment_bill_items_service_id',
            'payment_bill_items.order_item_id as payment_bill_items_order_item_id',
        ]);
        $query->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'orders.user_id');
            $join->whereNull('users.deleted_at');
        });

        $query->join('order_items', function ($join) {
            $join->on('order_items.order_id', '=', 'orders.id');
            $join->whereNull('order_items.deleted_at');
        });

        $query->join('payment_bills', function ($join) {
            $join->on('payment_bills.order_id', '=', 'orders.id');
            $join->whereNull('payment_bills.deleted_at');
        });

        $query->join('payment_bill_items', function ($join) {
            $join->on('payment_bill_items.payment_bill_id', '=', 'payment_bills.id');
            $join->on('payment_bill_items.order_item_id', '=', 'order_items.id');
            $join->whereNull('payment_bill_items.deleted_at');
        });

        $query->leftJoin('services', function ($join) {
            $join->on('services.id', '=', 'order_items.service_id');
            $join->whereNull('services.deleted_at');
        });

        /*
        $query->leftJoin('categories', function ($join) {
            $join->on('categories.id', '=', 'goods.category_id');
            $join->whereNull('categories.deleted_at');
        });
        $query->leftJoin('goods_units', function ($join) {
            $join->on('goods_units.id', '=', 'goods.goods_unit_id');
            $join->whereNull('goods_units.deleted_at');
        });
        */

        return $query;
    }

    /**
     * Формируем запрос со всеми полями
     * @return \Illuminate\Database\Query\Builder
     */
    public function generateFullQueryEmptyPaymentBill()
    {
        $query = OrderModel::whereNull('orders.deleted_at');
        $query->select([
            'orders.id as orders_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.global_number as orders_global_number',
            'orders.uuid as orders_uuid',
            'orders.created_at as orders_created_at',
            'orders.status as orders_status',

            'order_items.status as order_items_status',
            'order_items.id as order_items_id',
            'order_items.total_sum_by_item as order_items_total_sum_by_item',
            'order_items.total_sum_full_price_by_item as order_items_total_sum_full_price_by_item',
            'order_items.price as order_items_price',
            'order_items.full_price as order_items_full_price',
            'order_items.count as order_items_count',
            'order_items.currency as order_items_currency',
            'order_items.service_id as order_items_service_id',
            'order_items.order_id as order_items_order_id',

            'users.id as users_id',
            'users.first_name as users_first_name',
            'users.last_name as users_last_name',
            'users.second_name as users_second_name',

            'services.name as services_name',
            'services.description as services_description',
            'services.price_text as services_price_text',

            'payment_bills.id as payment_bills_id',
            'payment_bills.order_id as payment_bills_order_id',
            'payment_bills.total_sum as payment_bills_total_sum',
            'payment_bills.number as payment_bills_number',
            'payment_bills.status as payment_bills_status',
            'payment_bills.confirmation_url as payment_bills_confirmation_url',

            'payment_bills.create_request_date as payment_bills_create_request_date',
            'payment_bills.payed_date as payment_bills_payed_date',

            'payment_bill_items.id as payment_bill_items_id',
            'payment_bill_items.status as payment_bill_items_status',
            'payment_bill_items.total_sum_by_item as payment_bill_items_total_sum_by_item',
            'payment_bill_items.total_sum_full_price_by_item as payment_bill_items_total_sum_full_price_by_item',
            'payment_bill_items.price as payment_bill_items_price',
            'payment_bill_items.full_price as payment_bill_items_full_price',
            'payment_bill_items.count as payment_bill_items_count',
            'payment_bill_items.currency as payment_bill_items_currency',
            'payment_bill_items.service_id as payment_bill_items_service_id',
            'payment_bill_items.order_item_id as payment_bill_items_order_item_id',
        ]);
        $query->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'orders.user_id');
            $join->whereNull('users.deleted_at');
        });

        $query->join('order_items', function ($join) {
            $join->on('order_items.order_id', '=', 'orders.id');
            $join->whereNull('order_items.deleted_at');
        });

        $query->leftJoin('payment_bills', function ($join) {
            $join->on('payment_bills.order_id', '=', 'orders.id');
            $join->whereNull('payment_bills.deleted_at');
        });

        $query->leftJoin('payment_bill_items', function ($join) {
            $join->on('payment_bill_items.payment_bill_id', '=', 'payment_bills.id');
            $join->on('payment_bill_items.order_item_id', '=', 'order_items.id');
            $join->whereNull('payment_bill_items.deleted_at');
        });

        $query->leftJoin('services', function ($join) {
            $join->on('services.id', '=', 'order_items.service_id');
            $join->whereNull('services.deleted_at');
        });

        /*
        $query->leftJoin('categories', function ($join) {
            $join->on('categories.id', '=', 'goods.category_id');
            $join->whereNull('categories.deleted_at');
        });
        $query->leftJoin('goods_units', function ($join) {
            $join->on('goods_units.id', '=', 'goods.goods_unit_id');
            $join->whereNull('goods_units.deleted_at');
        });
        */

        return $query;
    }

    public function generateClearQuery()
    {
        $query = OrderModel::whereNull('orders.deleted_at');
        $query->select([
            'orders.*',
        ]);

        /*
        $query->leftJoin('categories', function ($join) {
            $join->on('categories.id', '=', 'goods.category_id');
            $join->whereNull('categories.deleted_at');
        });
        $query->leftJoin('goods_units', function ($join) {
            $join->on('goods_units.id', '=', 'goods.goods_unit_id');
            $join->whereNull('goods_units.deleted_at');
        });
        */

        return $query;
    }

    public function getWithPagination($arQuery){

        $query = $this->generateClearQuery();
        $query = $this->addQueryParam($arQuery, $query);

        $model = $query->paginate(PaginateHelper::COUNT_PAGINATE);
        $model__to_array = $model->toArray();

        $data = [];
        $data['items'] = $model__to_array['data'];
        $data['pagination']['current_page'] = $model__to_array['current_page'];
        $data['pagination']['total_item_count'] = $model__to_array['total'];
        $data['pagination']['last_page'] = $model__to_array['last_page'];
        $data['pagination']['per_page'] = $model__to_array['per_page'];
        $data['pagination']['from'] = $model__to_array['from'];
        $data['pagination']['to'] = $model__to_array['to'];
        $data['pagination']['prev_page'] = $model__to_array['prev_page_url'];
        $data['pagination']['next_page'] = $model__to_array['next_page_url'];
        
        return $data;
    }
    
    public function getWithEmptyPaymentBill($arQuery = ['count' => 9999])
    {
        $query = $this->generateFullQueryEmptyPaymentBill();
        $query = $this->addQueryParam($arQuery, $query);

        $result = $query->get();

        return $result;
    }
}
