<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentBillModel extends Model
{
    protected $table = 'payment_bills';

    protected $fillable = [
        'name',
        'description',
    ];
}
