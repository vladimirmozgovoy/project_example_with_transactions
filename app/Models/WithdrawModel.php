<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawModel extends Model
{
    protected $table = 'request_to_withdraw_balance';

    protected $fillable = [
        'card_number',
        'sum',
        'flag_close',
        'admin_email',
        'user_id',
    ];


}
