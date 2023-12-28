<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductImageModel extends Model 
{

    protected $table = 'product_images';
    public $timestamps = true;
    protected $fillable = array('product_id', 'image');
    protected $visible = array('id','product_id', 'image');



    public function product()
    {
        return $this->belongsTo('App\Model\ProductModel', 'product_id');
    }

}
