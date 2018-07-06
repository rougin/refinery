<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_users_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field('id');

        $this->dbforge->create_table('users');
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
    }

}
