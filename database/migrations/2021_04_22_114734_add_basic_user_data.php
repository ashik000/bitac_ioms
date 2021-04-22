<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBasicUserData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = new \App\Data\Models\User();
        $user->name = 'Inovace Admin';
        $user->email = 'admin@inovacetech.com';
        $user->password = bcrypt('inovacedevice');
        $user->remember_token = bin2hex(random_bytes(32));
        $user->save();

        //todo move the role names to a constant file
        $adminRole = new \App\Data\Models\Role();
        $adminRole->name = 'admin';
        $adminRole->save();

        $managerRole = new \App\Data\Models\Role();
        $managerRole->name = 'manager';
        $managerRole->save();

        $operatorRole = new \App\Data\Models\Role();
        $operatorRole->name = 'operator';
        $operatorRole->save();

        \App\Data\Models\UserPermission::insert([
            'user_id' => $user->id,
            'role_id' => $adminRole->id
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::table('users')->truncate();
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
        \Illuminate\Support\Facades\DB::table('user_permissions')->truncate();
    }
}
