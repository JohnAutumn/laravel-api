<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = array('barcode', 'name', 'offer', 'image_id', 'start_date', 'finish_date');
}
