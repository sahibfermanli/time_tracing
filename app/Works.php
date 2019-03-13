<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Works extends Model
{
    protected $table = 'works';
    protected $fillable = ['user_id', 'task_id', 'field_id', 'work', 'same_work', 'color', 'deleted', 'deleted_at', 'deleted_by', 'completed', 'completed_at'];
}
