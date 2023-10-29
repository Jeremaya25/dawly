<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Install extends Seeder
{
    public function run()
    {
        // Execute all seeders
        $this->call('AddAuthGroupsSeeder');
        $this->call('AddAuthPermissionsSeeder');
        $this->call('AddAuthUserAdminSeeder');
    }
}
