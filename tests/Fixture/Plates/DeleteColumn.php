<?php

use Rougin\Refinery\Migration;

class Migration_remove_name_in_users_table extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        $this->dbforge->drop_column('users', 'name');
    }

    /**
     * @return void
     */
    public function down()
    {
        $data = array('name' => array());
        $data['name']['type'] = 'varchar';
        $data['name']['auto_increment'] = false;
        $data['name']['constraint'] = 100;
        $data['name']['default'] = null;
        $data['name']['null'] = true;
        $data['name']['unsigned'] = false;
        $this->dbforge->add_column('users', $data);
    }
}
