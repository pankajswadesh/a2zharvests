<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliveryProfileModel extends Model
{
    protected $table='delivery_profile';
    protected $fillable = [
        'user_id','address','vehicle_details', 'adhaar_card', 'pan_card','driving_lisence','blue_book','vehicle_type','adv_amnt'
    ];
    protected $visable = [
        'id','user_id','address','vehicle_details', 'adhaar_card', 'pan_card','driving_lisence','blue_book','vehicle_type','wallet_balance'
    ];

}
