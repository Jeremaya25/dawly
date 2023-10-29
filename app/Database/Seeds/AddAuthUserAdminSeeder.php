<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;
use App\Entities\User;

class AddAuthUserAdminSeeder extends Seeder
{
    public function run()
    {
        $authorize = $auth = service('authorization');
        $users = model(UserModel::class);

        $row = [
            'active'   => 1,
            'password' => '1234',
            'username' => 'admin',
            'email' => 'admin@me.local',
            'name' => 'Admin',
            'surname' => 'Admin',
            'alias' => 'admin',
        ];

        $user = new User($row);
        $userId = $users->insert($user);
        $authorize->addUserToGroup($userId, 'administradors');
        $authorize->addUserToGroup($userId, 'usuaris');
    }
}
