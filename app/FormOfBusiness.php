<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormOfBusiness extends Model
{
    protected $table = 'form_of_business';
    protected $fillable = ['title', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
