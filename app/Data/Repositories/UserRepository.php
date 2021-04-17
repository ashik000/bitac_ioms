<?php


namespace App\Data\Repositories;


use App\Data\Models\User;

class UserRepository
{

    public function findByEmail(string $usernameOrEmail) {
        return User::where('email', '=', $usernameOrEmail)
                    ->first();
    }
}
