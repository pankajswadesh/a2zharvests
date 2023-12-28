<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model 
{

    protected $table = 'orders';
    public $timestamps = true;
    protected $fillable = array('id','order_id','user_id', 'payment_id', 'shipping_id', 'transaction_id', 'total_amount','delivery_charge', 'gross_amount','use_wallet','wallet_amount', 'total_discount', 'total_tax','applied_promo_code','promo_discount','cashback_amount','cashback_status', 'datetime','user_delivery_date','user_delivery_time','status','tracking_id');
    protected $visible = array('id','order_id','user_id', 'payment_id', 'shipping_id', 'transaction_id','total_amount','delivery_charge','gross_amount','use_wallet','wallet_amount','total_discount', 'total_tax','applied_promo_code','promo_discount','cashback_amount','cashback_status', 'datetime','user_delivery_date','user_delivery_time','status','tracking_id');

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo('App\Model\PaymentModel', 'payment_id');
    }

    public function order_details()
    {
        return $this->hasMany('App\Model\OrderDetailsModel', 'order_id');
    }

    public function shipping()
    {
        return $this->belongsTo('App\Model\ShippingModel', 'shipping_id');
    }



}
