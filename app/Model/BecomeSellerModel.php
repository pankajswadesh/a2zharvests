<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BecomeSellerModel extends Model
{
    protected $table='become_seller_request';
    protected $fillable = [
       'name','email', 'phone','business_name','business_description'
    ];
    protected $visable = [
        'id','name','email', 'phone','business_name','business_description'
    ];
}
