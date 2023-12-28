<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TaxValueModel extends Model 
{

    protected $table = 'tax_values';
    public $timestamps = true;
    protected $fillable = array('tax_id', 'ledger_name', 'value', 'status');
    protected $visible = array('id','tax_id', 'ledger_name', 'value', 'status');


    public function tax()
    {
        return $this->belongsTo('App\Model\TaxModel', 'tax_id');
    }

}