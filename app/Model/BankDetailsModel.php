<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BankDetailsModel extends Model 
{

    protected $table = 'bank_details';
    public $timestamps = true;
    protected $fillable = array('user_id', 'holder_name', 'account_no', 'branch_name', 'ifsc_code');
    protected $visible = array('user_id', 'holder_name', 'account_no', 'branch_name', 'ifsc_code');

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}