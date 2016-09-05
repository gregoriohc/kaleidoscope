<?php

namespace Gregoriohc\Kaleidoscope\Tests\Transformers;

use Gregoriohc\Kaleidoscope\Tests\Models\User;
use League\Fractal;

class CustomUserTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'      => (int) $user->id,
            'name'    => $user->name,
        ];
    }
}
