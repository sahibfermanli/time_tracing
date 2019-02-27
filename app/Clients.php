<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $table = 'clients';
    protected $fillable = ['name', 'director', 'category_id', 'email', 'phone', 'address', 'zipcode', 'voen', 'account_no', 'bank_name', 'bank_voen', 'bank_code', 'bank_m_n', 'bank_swift', 'contract_no', 'contract_date', 'deleted', 'deleted_at', 'deleted_by', 'created_by'];
}
