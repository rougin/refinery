<?php

use Rougin\Refinery\Migration;

class Migration_add_name_in_users_table extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        $data = array('name' => array());

        $data['name']['type'] = 'VARCHAR';
        $data['name']['constraint'] = 100;
        $data['name']['auto_increment'] = false;
        $data['name']['default'] = null;
        $data['name']['null'] = true;
        $data['name']['unsigned'] = false;

        $this->dbforge->add_column('users', $data);
    }

    /**
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_column('users', 'name');
    }
}
