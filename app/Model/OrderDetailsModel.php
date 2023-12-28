<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDetailsModel extends Model 
{

    protected $table = 'order_details';
    public $timestamps = true;
    protected $fillable = array('id','order_id', 'supplier_id', 'delivery_id', 'payment_method', 'product_id', 'product_name', 'price', 'gross_price', 'qty','supplier_quantity', 'unit', 'tax_value', 'tax', 'discount_value', 'discount','status','tentative_delivery_date','tentative_customer_delivery_date','star','review','delivery_tips');
    protected $visible = array('id','order_id', 'supplier_id', 'delivery_id', 'payment_method', 'product_id', 'product_name', 'price', 'gross_price', 'qty','supplier_quantity', 'unit', 'tax_value', 'tax', 'discount_value', 'discount','status','tentative_delivery_date','tentative_customer_delivery_date','star','review','delivery_tips');

    public function order()
    {
        return $this->belongsTo('App\Model\OrderModel', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\User', 'supplier_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Model\ProductModel', 'product_id');
    }

    public function delivery()
    {
        return $this->belongsTo('App\User', 'delivery_id');
    }

}