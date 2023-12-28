<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShippingModel extends Model 
{

    protected $table = 'shipping';
    public $timestamps = true;
    protected $fillable = array('id','user_id', 'name', 'email', 'phone_no', 'address', 'latitude', 'longitude', 'pincode', 'landmark','city','state');
    protected $visible = array('id','user_id', 'name', 'email', 'phone_no', 'address', 'latitude', 'longitude', 'pincode', 'landmark','city','state');

}