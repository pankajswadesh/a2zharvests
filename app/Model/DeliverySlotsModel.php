<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliverySlotsModel extends Model
{
    protected $table='delivery_slots';
    protected $fillable = [
        'slot_name', 'status'
    ];
    protected $visable = [
        'id','slot_name', 'status'
    ];
    public $timestamps = true;
}
