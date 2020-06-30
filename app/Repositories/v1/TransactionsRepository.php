<?php

namespace App\Repositories\v1;

use App\Models\OrderModel;
use App\Models\TaskModel;
use App\Models\TransactionsModel;
use App\Repositories\v1\Base\BaseRepository;

class TransactionsRepository extends BaseRepository
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
        $query = TransactionsModel::whereNull('transactions.deleted_at');
        $query->select([
            'transactions.*',
        ]);


        return $query;
    }

    public function generateClearQuery()
    {
        $query = TransactionsModel::whereNull('transactions.deleted_at');
        $query->select([
            'transactions.*',
        ]);
        return $query;
    }

}
