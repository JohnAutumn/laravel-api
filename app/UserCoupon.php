<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $table = 'user_coupons';
    protected $fillable = array('barcode', 'login', 'active');
}
