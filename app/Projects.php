<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $table = 'projects';
    protected $fillable = ['project', 'description', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
