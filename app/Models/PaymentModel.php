<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentModel extends Model
{
    const PAYMENT_TYPE = ['INSTALLMENT','PREPAYMENT','WITHOUT_PREPAYMENT'];
    const PAYMENT_CONST = 'WITHOUT_PREPAYMENT';
}
