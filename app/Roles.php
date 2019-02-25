<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';
    protected $fillable = ['role', 'description', 'deleted', 'deleted_at', 'deleted_by', 'created_by'];
}
