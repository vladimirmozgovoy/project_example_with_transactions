<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentBillItemModel extends Model
{
    protected $table = 'payment_bill_items';

    protected $fillable = [
        'name',
    ];
}
