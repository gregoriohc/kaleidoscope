<?php

namespace Gregoriohc\Kaleidoscope\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Gregoriohc\Kaleidoscope\Fractalizable;

class User extends Model
{
    use Fractalizable;

    protected $fillable = ['name', 'email'];
}