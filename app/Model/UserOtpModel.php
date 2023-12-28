<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserOtpModel extends Model
{
    protected $table='user_otp';
    protected $fillable = ['id','token','otp'];
    protected $visable = ['id','token','otp'];
}
