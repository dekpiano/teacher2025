<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistoricalScoreAccess extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'score_access_id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'teacher_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'register_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'is_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'granted_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'granted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'revoked_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'revoked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('score_access_id', true);
        $this->forge->addUniqueKey(['teacher_id', 'register_year'], 'teacher_period_unique');
        $this->forge->addKey('register_year');
        $this->forge->createTable('tb_register_score_access', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_register_score_access', true);
    }
}
