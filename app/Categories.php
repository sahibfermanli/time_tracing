<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';
    protected $fillable = ['category', 'up_category', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
