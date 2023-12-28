<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ParameterModel extends Model
{
    protected $table='parameter';
    protected $fillable = [
        'PARAM', 'PREFIX', 'SUFFIX', 'STNO','NUMBERPAD','DVDR',
    ];
    protected $visable = [
        'PARAM', 'PREFIX', 'SUFFIX', 'STNO','NUMBERPAD','DVDR',
    ];
}
