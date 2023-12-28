<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SupplierProductModel extends Model 
{

    protected $table = 'supplier_products';
    public $timestamps = true;
    protected $fillable = array('user_id', 'product_id', 'quantity', 'price', 'discount_id','discount_value', 'status');
    protected $visible = array('id','user_id', 'product_id', 'quantity', 'price', 'discount_id', 'discount_value','status');

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Model\ProductModel', 'product_id');
    }

    public function discount()
    {
        return $this->belongsTo('App\Model\DiscountModel', 'discount_id');
    }





}