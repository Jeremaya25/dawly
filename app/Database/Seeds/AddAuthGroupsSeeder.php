<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddAuthGroupsSeeder extends Seeder
{
    public function run()
    {
        $authorize = $auth = service('authorization');
        $authorize->createGroup('administradors', 'Usuaris administradors del sistema');
        $authorize->createGroup('usuaris-extra', 'Usuaris amb accés a fitxers');
        $authorize->createGroup('usuaris','Usuaris generals');
    }
}
