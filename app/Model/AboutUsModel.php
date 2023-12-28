<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AboutUsModel extends Model
{
    protected $table='about_us';
    protected $fillable = [
        'image', 'title', 'description',
    ];
    protected $visable = [
        'id','image', 'title', 'description',
    ];
    public $timestamps = true;
}
