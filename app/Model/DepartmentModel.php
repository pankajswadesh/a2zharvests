<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DepartmentModel extends Model 
{

    protected $table = 'departments';
    public $timestamps = true;
    protected $fillable = array('dept_name', 'status');
    protected $visible = array('id','dept_name', 'status');

}