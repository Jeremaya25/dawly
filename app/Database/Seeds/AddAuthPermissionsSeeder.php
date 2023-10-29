<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddAuthPermissionsSeeder extends Seeder
{
    public function run()
    {
        $authorize = $auth = service('authorization');

        $authorize->createPermission('urls.manage', 'Allows a user to create, edit, and delete urls');
        $authorize->createPermission('urls.add', 'Allows a user to create urls');
        $authorize->createPermission('urls.update', 'Allows a user to edit urls');
        $authorize->createPermission('urls.delete', 'Allows a user to delete urls');

        $authorize->createPermission('files.manage', 'Allows a user to create, edit, and delete files');

        $authorize->createPermission('users.manage', 'Allows a user to create, edit, and delete users');
        # Add permissions to Administradors
        $authorize->addPermissionToGroup('urls.manage', 'administradors');
        $authorize->addPermissionToGroup('users.manage', 'administradors');
        $authorize->addPermissionToGroup('files.manage', 'administradors');
        
        # Add permissions to usuaris
        $authorize->addPermissionToGroup('urls.add', 'usuaris');

        # Add permissions to usuaris-extra
        $authorize->addPermissionToGroup('urls.add', 'usuaris-extra');
        $authorize->addPermissionToGroup('files.manage', 'usuaris-extra');
    }
}
