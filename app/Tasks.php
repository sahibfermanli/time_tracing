<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $table = 'tasks';
    protected $fillable = ['task', 'project_id', 'deadline', 'payment', 'total_payment', 'currency_id', 'payment_type', 'time', 'act_time', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
    // payment types:
    // 1: fix
    // 2: fix + hourly rate
    // 3: hourly rate
    // 4: monthly
}
