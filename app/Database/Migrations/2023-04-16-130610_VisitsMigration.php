<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VisitsMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'url_route'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'ip'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'created_at'      =>  [
                    'type'         =>  'DATETIME',
                    'null'         =>  true,
                    'default'    =>  null,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('url_route');
        $this->forge->addForeignKey('url_route', 'url', 'route', 'CASCADE', 'CASCADE');
        $this->forge->createTable('visits');
    }

    public function down()
    {
        $this->forge->dropTable('visits');
    }
}
