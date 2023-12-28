<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WebInfoModel extends Model
{
    protected $table='web_info';
    protected $fillable = ['id','key','value'];
    protected $visable = ['id','key','value'];
    public $timestamps=true;
}
