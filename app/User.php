<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'username', 'password', 'role_id', 'level_id', 'send_mail', 'deleted', 'deleted_at', 'deleted_by', 'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function deleted_value() {
        return $this->deleted;
    }

    public function send_mail() {
        return $this->send_mail;
    }

    public function username() {
        return $this->username();
    }

    public function role() {
        $role = $this->role_id;

        return $role;
    }
}
