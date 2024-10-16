<?php

use Rougin\Refinery\Migration;

class Migration_delete_users_table extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        $this->dbforge->drop_table('users');
    }

    /**
     * @return void
     */
    public function down()
    {
        $data = array('id' => array());
        $data['id']['type'] = 'integer';
        $data['id']['auto_increment'] = true;
        $data['id']['constraint'] = 10;
        $this->dbforge->add_field($data);
        $this->dbforge->add_key('id', true);

        $this->dbforge->create_table('users');
    }
}
