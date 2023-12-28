<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'referal_code','user_name','api_token', 'email','phone','location','password','latitude','longitude','image_url','status','wallet_amount','vendor_commision','refered_by','is_default_delivery','device_id','star','parent_id','available_distance'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function findOrFail($id)
    {
    }

    public function supplier_product()
    {
        return $this->hasMany('App\Model\SupplierProductModel', 'user_id');
    }

    public function shop_details()
    {
        return $this->hasOne('App\Model\ShopDetailsModel', 'user_id');
    }

    public function bank_details()
    {
        return $this->hasOne('App\Model\BankDetailsModel', 'user_id');
    }
    public function mySuppliers()
    {
        return $this->hasMany('App\User', 'parent_id');
    }

}
