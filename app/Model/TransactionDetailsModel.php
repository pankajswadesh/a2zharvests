<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransactionDetailsModel extends Model
{
    protected $table = 'transaction_details';
    public $timestamps = true;
    protected $fillable = array('user_id','refer_for_id', 'transaction_id','amount','transaction_type','status');
    protected $visible = array('id','user_id','refer_for_id','transaction_id','amount', 'transaction_type', 'status');
}
