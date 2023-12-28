<?php
namespace App;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{

    protected $table = 'permissions';
    protected $fillable = ['group_name','name','display_name','description'];
    protected $visible = ['id','group_name','name','display_name','description'];
    public $timestamps = true;

}