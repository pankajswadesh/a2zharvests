<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SeoDataModel extends Model
{
    protected $table = 'seo_data';
    public $timestamps = true;
    protected $fillable = array('title', 'description','keywords','page_name','page_slug');
    protected $visible = array('id','title', 'description','keywords','page_name','page_slug');
}
