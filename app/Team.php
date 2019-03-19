<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'team';
    protected $fillable = ['project_id', 'user_id', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
