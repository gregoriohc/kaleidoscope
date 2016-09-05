<?php

namespace Gregoriohc\Kaleidoscope\Tests\Models;

use Gregoriohc\Kaleidoscope\Fractalizable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Fractalizable;

    protected $fillable = ['name', 'email'];
}
