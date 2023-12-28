<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model 
{

    protected $table = 'categories';
    public $timestamps = true;
    protected $fillable = array('parent_id', 'category_name', 'category_image', 'url','in_home','priority', 'status');
    protected $visible = array('id','parent_id', 'category_name', 'category_image', 'url','in_home','priority', 'status');

    public function product()
    {
        return $this->hasMany('App\Model\ProductModel', 'category_id');
    }

    public function category(){
        return $this->belongsTo('App\Model\CategoryModel', 'parent_id');
    }

}
