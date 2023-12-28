<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShopDetailsModel extends Model 
{

    protected $table = 'shop_details';
    public $timestamps = true;
    protected $fillable = array('user_id', 'business_name', 'business_id', 'gst_no', 'fsssi_no', 'start_time', 'end_time', 'alt_phone_no');
    protected $visible = array('user_id', 'business_name', 'business_id', 'gst_no', 'fsssi_no', 'start_time', 'end_time', 'alt_phone_no');

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}