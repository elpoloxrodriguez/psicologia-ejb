<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'interview_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('patient_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('interviews');
    }

    public function down()
    {
        $this->forge->dropTable('interviews');
    }
}
