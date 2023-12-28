<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ContactUsModel extends Model
{
    protected $table='contact_us';
    protected $fillable = [
        'email', 'phone', 'address',
    ];
    protected $visable = [
        'id','email', 'phone', 'address',
    ];
}
