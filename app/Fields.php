<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fields extends Model
{
    protected $table = 'fields';
    protected $fillable = ['start_time', 'end_time', 'deleted', 'deleted_at', 'deleted_by', 'created_by'];
}
