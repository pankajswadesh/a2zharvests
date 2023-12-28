<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model 
{

    protected $table = 'products';
    public $timestamps = true;
    protected $fillable = array('category_id', 'sub_category_id','brand_id', 'product_name', 'print_name', 'product_image', 'product_description', 'product_company', 'unit_id', 'department_id', 'tax_id', 'url');
    protected $visible = array('id','category_id','sub_category_id', 'brand_id', 'product_name', 'print_name', 'product_image', 'product_description', 'product_company', 'unit_id', 'department_id', 'tax_id', 'url');

    public function category()
    {
        return $this->belongsTo('App\Model\CategoryModel', 'category_id');
    }
    public function sub_category()
    {
        return $this->belongsTo('App\Model\CategoryModel', 'sub_category_id');
    }

    public function images()
    {
        return $this->hasMany('App\Model\ProductImageModel', 'product_id','product_id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Model\BrandModel', 'brand_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Model\UnitModel', 'unit_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Model\DepartmentModel', 'department_id');
    }

    public function tax()
    {
        return $this->belongsTo('App\Model\TaxModel', 'tax_id');
    }

}
