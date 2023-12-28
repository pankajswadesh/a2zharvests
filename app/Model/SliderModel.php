<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SliderModel extends Model
{
    protected $table = 'sliders';
    public $timestamps = true;
    protected $fillable = array('slider_image', 'status');
    protected $visible = array('id','slider_image',  'status');


}
