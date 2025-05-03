<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Aliziodev\GmailUnique\Traits\HasNormalizedEmail;

class TestUser extends Model
{
    use HasNormalizedEmail;

    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;
}
