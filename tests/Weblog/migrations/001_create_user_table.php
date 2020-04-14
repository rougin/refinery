<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_user_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('user', array(
            'gender' => array(
                'type' => 'VARCHAR',
                'constraint' => 10,
                'auto_increment' => FALSE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->add_column('user', array(
            'age' => array(
                'type' => 'INTEGER',
                'constraint' => 2,
                'auto_increment' => FALSE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->add_column('user', array(
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 200,
                'auto_increment' => FALSE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->add_column('user', array(
            'id' => array(
                'type' => 'INTEGER',
                'constraint' => 10,
                'auto_increment' => TRUE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->create_table('user');
    }

    public function down()
    {
        $this->dbforge->drop_table('user');
    }

}
