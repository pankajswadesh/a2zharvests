<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RecentSearchesModel extends Model
{
    protected $table = 'recent_searches';
    public $timestamps = true;
    protected $fillable = array('search_query');
    protected $visible = array('id','search_query');
}
