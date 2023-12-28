<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FaqModel extends Model
{
    protected $table = 'faq';
    public $timestamps = true;
    protected $fillable = array('question','answer', 'status');
    protected $visible = array('id','question','answer', 'status');
}
