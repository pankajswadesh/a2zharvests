<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TaxModel extends Model 
{

    protected $table = 'taxs';
    public $timestamps = true;
    protected $fillable = array('tax_name', 'is_inclusive', 'tax_value', 'status');
    protected $visible = array('id','tax_name', 'is_inclusive', 'tax_value', 'status');

    public function tax_value()
    {
        return $this->hasMany('App\Model\TaxValueModel', 'tax_id');
    }

}