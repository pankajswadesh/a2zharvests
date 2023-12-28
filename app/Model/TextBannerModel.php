<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TextBannerModel extends Model
{
    protected $table = 'text_banner';
    public $timestamps = true;
    protected $fillable = array('title','description', 'status');
    protected $visible = array('id','title', 'description', 'status');
}
