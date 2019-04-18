<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLevels extends Model
{
    protected $table = 'user_levels';
    protected $fillable = ['level', 'description', 'percentage', 'hourly_rate', 'currency_id', 'deleted', 'deleted_at', 'deleted_by', 'created_by'];
}
