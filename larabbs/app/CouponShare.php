<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponShare extends Model
{
    protected $table = 'tb_coupon_shares';
    protected $fillable = [
        'mid',
        'account',
        'coupon_link'
    ];
}
