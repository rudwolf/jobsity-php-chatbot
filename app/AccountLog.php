<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountLog extends Model
{
    //
    protected $fillable = ['email','log_entry'];
}
