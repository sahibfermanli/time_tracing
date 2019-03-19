<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $table = 'countries';
    protected $fillable = ['code', 'country', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
