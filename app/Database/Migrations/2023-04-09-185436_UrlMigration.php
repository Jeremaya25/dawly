<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UrlMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'route'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'url'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'shortname'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'description'          => [
                    'type'           => 'TEXT',
                    'null'           => true,
            ],
            'active'          => [
                    'type'           => 'TINYINT',
                    'constraint'     => 1,
                    'default'        => 1,
                    'null'           => false,
            ],
            'user_id'          => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'null'           => true,
            ],
            'expires'          => [
                    'type'           => 'DATETIME',
                    'null'           => true,
                    'default'        => null,
            ],
            'created_at'      =>  [
                    'type'         =>  'DATETIME',
                    'null'         =>  true,
                    'default'    =>  null,
            ],
        ]);
        $this->forge->addPrimaryKey('route');
        $this->forge->addKey('user_id');

        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');

        $this->forge->createTable('url', true);
    }

    public function down()
    {
        $this->forge->dropTable('url');
    }
}
