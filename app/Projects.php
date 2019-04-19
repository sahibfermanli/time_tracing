<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $table = 'projects';
    protected $fillable = ['project', 'description', 'payment', 'total_payment', 'currency_id', 'payment_type', 'time', 'client_id', 'client_role_id', 'project_manager_id', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
    // payment types:
    // 1: fix
    // 2: fix + hourly rate
    // 3: hourly rate
    // 4: monthly
}
