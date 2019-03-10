<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NonBillableCodes extends Model
{
    protected $table = 'non_billable_codes';
    protected $fillable = ['title', 'created_by', 'deleted', 'deleted_at', 'deleted_by'];
}
