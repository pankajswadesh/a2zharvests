<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PromocodesModel extends Model
{
    protected $table = 'promo_codes';
    public $timestamps = true;
    protected $fillable = array('promo_code', 'min_amount', 'discount_percent','for','discount_upto','status');
    protected $visible = array('id','promo_code', 'min_amount', 'discount_percent','for','discount_upto','status');
}
