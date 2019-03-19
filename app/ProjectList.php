<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectList extends Model
{
    protected $table = 'project_list';
    protected $fillable = ['project', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
