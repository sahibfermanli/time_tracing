<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
    protected $table = 'currencies';
    protected $fillable = ['currency', 'deleted', 'deleted_at', 'deleted_by', 'created_by'];
}
