<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DiscountModel extends Model 
{

    protected $table = 'discounts';
    public $timestamps = true;
    protected $fillable = array('discount_name', 'status');
    protected $visible = array('id','discount_name', 'status');

}