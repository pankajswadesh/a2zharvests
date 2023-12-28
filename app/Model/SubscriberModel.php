<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubscriberModel extends Model
{

    protected $table = 'newsletter_subscriber';
    public $timestamps = true;
    protected $fillable = array('email');
    protected $visible = array('id','email');
}