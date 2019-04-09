<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $table = 'tasks';
    protected $fillable = ['task', 'project_id', 'user_id', 'user_date', 'deadline', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
