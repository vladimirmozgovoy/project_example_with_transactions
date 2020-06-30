<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionsModel extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'initiator',
        'user_id',
        'type',
        'sum',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'flag_completed',
        'order_item_id',
    ];


}
