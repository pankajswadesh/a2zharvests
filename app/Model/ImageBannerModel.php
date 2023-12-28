<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImageBannerModel extends Model
{
    protected $table = 'image_banner';
    public $timestamps = true;
    protected $fillable = array('image','type', 'status');
    protected $visible = array('id','image', 'type','status');
}
