<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThirdParties extends Model
{
    protected $table = "third_parties";
    protected $fillable = ['project_id', 'client_id', 'role_id', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
