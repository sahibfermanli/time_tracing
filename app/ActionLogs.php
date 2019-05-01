<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionLogs extends Model
{
    protected $table = 'action_logs';
    protected $fillable = ['user_id', 'action', 'table', 'row_id'];
}
