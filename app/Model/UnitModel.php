<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UnitModel extends Model 
{

    protected $table = 'units';
    public $timestamps = true;
    protected $fillable = array('unit_name', 'status');
    protected $visible = array('id','unit_name', 'status');

}