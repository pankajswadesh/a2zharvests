<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentModel extends Model 
{

    protected $table = 'payments';
    public $timestamps = true;
    protected $fillable = array('id','payment_method', 'payment_status', 'payment_date_time');
    protected $visible = array('id','payment_method', 'payment_status', 'payment_date_time');

}