<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserLoginHistoryModel extends Model
{
    protected $table='user_login_history';
    protected $fillable = ['id','user_id','login_time','logout_time'];
    protected $visable = ['id','user_id','login_time','logout_time'];
}
