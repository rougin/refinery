<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * {{ description }}
 * 
 * @package  CodeIgniter
 * @category Migrations
 */
class Migration_{{ name }} extends CI_Migration {

    /**
     * Runs the migration
     *
     * @return void
     */
    public function up()
    {
{% for column in columns if command == 'delete' %}
        $this->dbforge->drop_column('{{ table }}', '{{ column.field }}');
{% endfor %}
{% for column in columns if command == 'add' or command == 'modify' %}
        $this->dbforge->{{ command }}_column('{{ table }}', array(
            '{{ column.field }}' => array(
                'type' => '{{ dataTypes[column.dataType] }}',
                'constraint' => '{{ column.length }}',
                'auto_increment' => {{ column.isAutoIncrement ? 'TRUE' : 'FALSE' }},
                'default' => '{{ column.defaultValue }}',
                'null' => {{ column.isNull ? 'TRUE' : 'FALSE' }},
                'unsigned' => {{ column.isUnsigned ? 'TRUE' : 'FALSE' }}
            )
        ));

{% if column.isPrimary %}
        $this->dbforge->add_key('{{ column.field }}', TRUE);

{% endif %}
{% endfor %}
{% if command == 'create' %}
        $this->dbforge->add_field('id');
        $this->dbforge->create_table('{{ table }}');
{% endif %}
    }

    /**
     * Reverses the migration
     *
     * @return void
     */
    public function down()
    {
{% for column in columns if command == 'add' %}
        $this->dbforge->drop_column('{{ table }}', '{{ column.field }}');
{% endfor %}
{% for column in columns if command == 'delete' %}
        $this->dbforge->{{ command }}_column('{{ table }}', array(
            '{{ column.field }}' => array(
                'type' => '{{ dataTypes[column.dataType] }}',
                'constraint' => '{{ column.length }}',
                'auto_increment' => {{ column.isAutoIncrement ? 'TRUE' : 'FALSE' }},
                'default' => '{{ column.defaultValue }}',
                'null' => {{ column.isNull ? 'TRUE' : 'FALSE' }},
                'unsigned' => {{ column.isUnsigned ? 'TRUE' : 'FALSE' }}
            )
        ));

{% if column.isPrimary %}
        $this->dbforge->add_key('{{ column.field }}', TRUE);
{% endif %}
{% endfor %}
{% for column in defaultColumns %}
        $this->dbforge->{{ command }}_column('{{ table }}', array(
            '{{ column.field }}' => array(
                'type' => '{{ dataTypes[column.dataType] }}',
                'constraint' => '{{ column.length }}',
                'auto_increment' => {{ column.isAutoIncrement ? 'TRUE' : 'FALSE' }},
                'default' => '{{ column.defaultValue }}',
                'null' => {{ column.isNull ? 'TRUE' : 'FALSE' }},
                'unsigned' => {{ column.isUnsigned ? 'TRUE' : 'FALSE' }}
            )
        ));

{% if column.isPrimary %}
        $this->dbforge->add_key('{{ column.field }}', TRUE);

{% endif %}
{% endfor %}
{% if command == 'create' %}
        $this->dbforge->drop_table('{{ table }}');
{% endif %}
    }

}