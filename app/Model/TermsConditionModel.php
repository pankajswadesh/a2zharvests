<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TermsConditionModel extends Model
{
    protected $table = 'terms_condition';
    public $timestamps = true;
    protected $fillable = array('contents');
    protected $visible = array('id','contents');
}
