<?php

namespace App\Repositories\v1;

use App\Models\OrderItemsModel;
use App\Models\OrdersModel;
use App\Repositories\v1\Base\BaseRepository;
use App\Services\v1\Permission\PermissionsService;

class OrderItemsRepository extends BaseRepository
{
    const QUERY_FIELDS = [
        'ADMIN' => [
            'orders.id as orders_id',
            'orders.user_id as orders_user_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.uuid as orders_uuid',
            'orders.status as orders_status',

            'order_items.id as order_items_id',

            'order_items.total_sum_by_item as order_items_total_sum_by_item',
            'order_items.price as order_items_price',
            'order_items.count as order_items_count',
            'order_items.currency as order_items_currency',
            'order_items.service_id as order_items_service_id',
            'order_items.order_id as order_items_order_id',

            'services.id as services_id',
            'services.user_id as services_user_id',
            'services.name as services_name',
            'services.description as services_description',
            'services.price_text as services_price_text',
        ],
        'EXPERT' => [
            'orders.id as orders_id',
            'orders.user_id as orders_user_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.uuid as orders_uuid',
            'orders.status as orders_status',

            'order_items.id as order_items_id',
            'order_items.total_sum_by_item as order_items_total_sum_by_item',
            'order_items.price as order_items_price',
            'order_items.count as order_items_count',
            'order_items.currency as order_items_currency',
            'order_items.service_id as order_items_service_id',
            'order_items.order_id as order_items_order_id',

            'services.id as services_id',
            'services.user_id as services_user_id',
            'services.name as services_name',
            'services.user_id as services_user_id',
            'services.description as services_description',
            'services.price_text as services_price_text',
        ],
        'USER' => [
            'orders.id as orders_id',
            'orders.user_id as orders_user_id',
            'orders.total_sum as orders_total_sum',
            'orders.number as orders_number',
            'orders.uuid as orders_uuid',
            'orders.status as orders_status',

            'order_items.id as order_items_id',
            'order_items.total_sum_by_item as order_items_total_sum_by_item',
            'order_items.price as order_items_price',
            'order_items.count as order_items_count',
            'order_items.currency as order_items_currency',
            'order_items.service_id as order_items_service_id',
            'order_items.order_id as order_items_order_id',

            'services.id as services_id',
            'services.user_id as services_user_id',
            'services.name as services_name',
            'services.description as services_description',
            'services.price_text as services_price_text',
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
        $query = OrderItemsModel::whereNull('order_items.deleted_at');

        $query->leftJoin('orders', function ($join) {
            $join->on('orders.id', '=', 'order_items.order_id');
            $join->whereNull('orders.deleted_at');
        });
        $query->leftJoin('services', function ($join) {
            $join->on('services.id', '=', 'order_items.service_id');
            $join->whereNull('services.deleted_at');
        });


        $query_select_fields = PermissionsService::getQueryFields(OrderItemsRepository::QUERY_FIELDS);
        $query->select($query_select_fields);

        return $query;
    }

    public function generateClearQuery()
    {
        $query = OrderItemsModel::whereNull('order_items.deleted_at');
        $query->select([
            'order_items.id as order_items_id',
            'order_items.total_sum_by_item as order_items_total_sum_by_item',
            'order_items.price as order_items_price',
            'order_items.count as order_items_count',
            'order_items.currency as order_items_currency',
            'order_items.service_id as order_items_service_id',
            'order_items.order_id as order_items_order_id',
            'order_items.additional_fields as order_items_additional_fields',
            'order_items.default_fields as order_items_default_fields',
            'order_items.payment_method as order_items_payment_method',
            'order_items.month_installment as order_items_month_installment',
            'order_items.debt_sum as order_items_debt_sum',
            'order_items.created_at as order_items_created_at',
            ]);


        return $query;
    }

}
