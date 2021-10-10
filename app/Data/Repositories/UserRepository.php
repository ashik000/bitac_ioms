<?php


namespace App\Data\Repositories;


use App\Data\Models\Role;
use App\Data\Models\User;

class UserRepository
{

    public function findByEmail(string $usernameOrEmail) {
        return User::where('email', '=', $usernameOrEmail)
                    ->first();
    }

    public function createUser($name, $email, $password) {
        $user = new \App\Data\Models\User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->remember_token = bin2hex(random_bytes(32));
        $user->save();

        $adminRole = Role::where('name', 'admin')->first();
        \App\Data\Models\UserPermission::insert([
            'user_id' => $user->id,
            'role_id' => $adminRole->id
        ]);
    }
}
