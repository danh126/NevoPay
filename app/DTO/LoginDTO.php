<?php

namespace App\DTO;

use App\Models\User;

class LoginDTO
{
    public function __construct(
        public readonly User $user,
        public readonly string $accessToken,
        public readonly string $tokenType = 'Bearer',
    ){}
}
