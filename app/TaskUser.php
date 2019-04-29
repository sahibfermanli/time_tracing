<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    protected $table = 'task_user';
    protected $fillable = ['user_id', 'task_id', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
