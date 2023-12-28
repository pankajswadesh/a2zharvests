<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CashbackSettingsModel extends Model
{
    protected $table = 'cashback_settings';
    public $timestamps = true;
    protected $fillable = array('min_amount', 'cashback_percent', 'cashback_upto','status');
    protected $visible = array('id','min_amount', 'cashback_percent','cashback_upto', 'status');
}
