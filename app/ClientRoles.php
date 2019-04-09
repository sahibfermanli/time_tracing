<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientRoles extends Model
{
    protected $table = 'client_roles';
    protected $fillable = ['role', 'description', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
