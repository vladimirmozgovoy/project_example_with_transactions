<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplenishmentModel extends Model
{
    protected $table = 'request_to_replenishment_balance';

    protected $fillable = [
        'mrh_pass1',
        'mrh_pass2',
        'description',
        'is_test',
        'crc',
        'url',
        'confirm_crc_1',
        'confirm_crc_2',
        'sum',
        'inv_id',
        'signature_value',
        'user_id',
    ];


}
