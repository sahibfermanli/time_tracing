<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $table = 'projects';
    protected $fillable = ['project', 'description', 'client_id', 'client_role_id', 'project_manager_id', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
