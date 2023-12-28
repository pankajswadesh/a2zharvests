<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model 
{

    protected $table = 'carts';
    public $timestamps = true;
    protected $fillable = array('id','user_id', 'supplier_id', 'product_id', 'quantity', 'price','unit', 'discount', 'tax', 'datetime');
    protected $visible = array('id','user_id', 'supplier_id', 'product_id', 'quantity', 'price','unit', 'discount', 'tax', 'datetime');

    public function product()
    {
        return $this->belongsTo('App\Model\ProductModel', 'product_id');
    }
    public function supplier_product()
    {
        return $this->belongsTo('App\Model\SupplierProductModel', 'product_id','product_id')->where('status','Active');
    }
    public function active_supplier_product()
    {
        return $this->belongsTo('App\Model\SupplierProductModel', 'product_id','product_id')->where('status','Active');
    }
    public function is_supplier_active()
    {
        return $this->belongsTo('App\User', 'supplier_id')->where('status','Active');
    }

}