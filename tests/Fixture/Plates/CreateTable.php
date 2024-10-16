<?php

use Rougin\Refinery\Migration;

class Migration_create_users_table extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        $data = array('id' => array());
        $data['id']['type'] = 'integer';
        $data['id']['auto_increment'] = true;
        $data['id']['constraint'] = 10;
        $this->dbforge->add_field($data);
        $this->dbforge->add_key('id', true);

        $this->dbforge->create_table('users');
    }

    /**
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}
