<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BrandModel extends Model 
{

    protected $table = 'brands';
    public $timestamps = true;
    protected $fillable = array('brand_name', 'status', 'url');
    protected $visible = array('id','brand_name', 'status', 'url');

    public function product()
    {
        return $this->hasMany('App\Model\ProductModel', 'brand_id');
    }

}