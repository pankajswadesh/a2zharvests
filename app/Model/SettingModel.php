<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SettingModel extends Model
{
    protected $table = 'setting';
    public $timestamps = true;
    protected $fillable = array('key', 'value');
    protected $visible = array('id','key', 'value');

}
