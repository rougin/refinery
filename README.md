# Refinery

[![Latest Stable Version](https://poser.pugx.org/rougin/refinery/v/stable)](https://packagist.org/packages/rougin/refinery) [![Total Downloads](https://poser.pugx.org/rougin/refinery/downloads)](https://packagist.org/packages/rougin/refinery) [![Latest Unstable Version](https://poser.pugx.org/rougin/refinery/v/unstable)](https://packagist.org/packages/rougin/refinery) [![License](https://poser.pugx.org/rougin/refinery/license)](https://packagist.org/packages/rougin/refinery) [![endorse](https://api.coderwall.com/rougin/endorsecount.png)](https://coderwall.com/rougin)

A command line interface for [Migrations Class](http://www.codeigniter.com/user_guide/libraries/migration.html) in [CodeIgniter](http://www.codeigniter.com/)

# Installation

Install ```Refinery``` via [Composer](https://getcomposer.org):

```$ composer require rougin/refinery```

# Examples

### Keywords

```Refinery``` also provides a *ready-to-eat* migration based on the following keywords below:

* ```create_(table)_table```
* ```add_(column)_in_(table)_table```
* ```modify_(column)_in_(table)_table```
* ```delete_(column)_in_(table)_table```

### Creating a table named "user"

```bash
$ php vendor/bin/refinery create create_user_table
"20150607123241_create_user_table.php" has been created.
```

**20150607123241_create_user_table.php**

```php
class Migration_create_user_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field('id');
        $this->dbforge->create_table('user');
    }

    public function down()
    {
        $this->dbforge->drop_table('user');
    }

}
```

### Adding column named "name" in "user" table

```bash
$ php vendor/bin/refinery create add_name_in_user_table
"20150607123510_add_name_in_user_table.php" has been created.
```

**20150607123510_add_name_in_user_table.php**

```php
class Migration_add_name_in_user_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('user', array(
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'auto_increment' => FALSE,
                'null' => FALSE,
                'unsigned' => FALSE
            )
        ));
    }

    public function down()
    {
        $this->dbforge->drop_column('user', 'name');
    }

}
```

### Migrating all files in ```application/migrations``` directory

```bash
$ php vendor/bin/refinery migrate
"20150607123241_create_user_table" has been migrated to the database.
"20150607123510_add_name_in_user_table" has been migrated to the database.
```

### You can also revert back if you want

```bash
$ php vendor/bin/refinery rollback
Database is reverted back to version 20150607123241. (20150607123241_create_user_table)
```

### Or reset them back

```bash
$ php vendor/bin/refinery reset
Database has been resetted.
```

# Commands

#### ```migrate```

#### Description:

Migrates the database

#### ```rollback```

#### Description:

Returns to a previous/specified migration

#### Arguments:

* ```version``` - Specified version of the migration

#### ```reset```

#### Description:

Resets all migrations

#### ```create```

#### Description:

Creates a new migration file

#### Arguments:

* ```name``` - Name of the migration file

#### Options:

**NOTE**: The following options below are only available when you use the ```add_```, ```delete_```, or ```modify_``` keywords

* ```--from-database``` - Generates a migration file that is based from the database

* ```--sequential``` - Generates a migration file with a sequential identifier

    **NOTE**: If you really want to use the sequential identifier, just change your ```$config['migration_type']``` in ```application/config/migration.php```

* ```--type=[LENGTH]``` - Data type of the column

* ```--length=[LENGTH]``` - Length of the column

* ```--auto_increment``` - Generates an "AUTO_INCREMENT" flag on the column

* ```--default``` - Generates a default value in the column definition

* ```--null``` - Generates a "NULL" value in the column definition

* ```--primary``` - Generates a "PRIMARY" value in the column definition

* ```--unsigned``` - Generates an "UNSIGNED" value in the column definition