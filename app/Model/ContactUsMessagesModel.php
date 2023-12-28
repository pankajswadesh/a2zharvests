<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ContactUsMessagesModel extends Model
{
    protected $table='contact_messages';
    protected $fillable = [
       'name','email', 'phone','subject','message'
    ];
    protected $visable = [
        'id','name','email', 'phone','subject','message'
    ];
}
