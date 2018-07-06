<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_name_in_users_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('users', array(
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'auto_increment' => FALSE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));
    }

    public function down()
    {
        $this->dbforge->drop_column('users', 'name');
    }

}
