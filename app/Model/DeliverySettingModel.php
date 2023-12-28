<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliverySettingModel extends Model
{
    protected $table='delivery_setting';
    protected $fillable = [
        'max_amount', 'delivery_charge'
    ];
    protected $visable = [
        'id','max_amount', 'delivery_charge'
    ];
}
