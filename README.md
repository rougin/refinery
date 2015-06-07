# Refinery

[![Latest Stable Version](https://poser.pugx.org/rougin/refinery/v/stable)](https://packagist.org/packages/rougin/refinery) [![Total Downloads](https://poser.pugx.org/rougin/refinery/downloads)](https://packagist.org/packages/rougin/refinery) [![Latest Unstable Version](https://poser.pugx.org/rougin/refinery/v/unstable)](https://packagist.org/packages/rougin/refinery) [![License](https://poser.pugx.org/rougin/refinery/license)](https://packagist.org/packages/rougin/refinery) [![endorse](https://api.coderwall.com/rougin/endorsecount.png)](https://coderwall.com/rougin)

A command line interface for [Migrations Class](http://www.codeigniter.com/user_guide/libraries/migration.html) in [CodeIgniter](http://www.codeigniter.com/)

# Installation

Install ```Refinery``` via [Composer](https://getcomposer.org):

```$ composer require rougin/refinery```

# Examples

### Creating a table named "user"

```bash
$ php vendor/bin/refinery create:migration create_user_table
"20150607123241_create_user_table.php" has been created.
```

**20150607123241_create_user_table.php**

```php
class Migration_create_user_table extends CI_Migration {

	/**
	 * Run the migrations
	 */
	public function up()
	{
		$this->dbforge->add_field('id');
		$this->dbforge->create_table('user');
	}

	/**
	 * Reverse the migrations
	 */
	public function down()
	{
		$this->dbforge->drop_table('user');
	}

}
```

### Adding column named "name" in "user" table

```bash
$ php vendor/bin/refinery create:migration add_name_in_user_table
"20150607123510_add_name_in_user_table.php" has been created.
```

**20150607123510_add_name_in_user_table.php**

```php
class Migration_add_name_in_user_table extends CI_Migration {

	/**
	 * Run the migrations
	 */
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

	/**
	 * Reverse the migrations
	 */
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
$ php vendor/bin/refinery migrate --revert=1
Database is reverted back to version 20150607123241. (20150607123241_create_user_table)
```

### Or reset them back

```bash
$ php vendor/bin/refinery migrate:reset
Database has been resetted.
```

# Commands

#### ```migrate```

#### Description:

Migrate the database

#### Arguments:

* ```version``` (**Optional**) - Migrate to a specified version of the database

#### Options:

* ```--revert``` - Number of times to revert from the list of migrations

	**NOTE**: If an error occurs about URI, just include an equal sign ```=``` in ```$config['permitted_uri_chars']``` on ```application/config.php```

#### ```migrate:reset```

#### Description:

Rollback all migrations back to start

#### ```create:migration```

#### Description:

Create a new migration file

#### Arguments:

* ```name``` - Name of the migration file

#### Options:

**NOTE**: The following options below are only available when you use the ```add_```, ```delete_```, or ```modify_``` text

* ```--revert``` - Number of times to revert from the list of migrations

* ```--sequential``` - Generates a migration file with a sequential identifier

	**NOTE**: If you really want to use the sequential identifier, just change your ```$config['migration_type']``` in ```application/config/migration.php```

* ```--type=[LENGTH]``` - Data type of the column

* ```--length=[LENGTH]``` - Length of the column

* ```--auto_increment``` - Generates an "AUTO_INCREMENT" flag on the column

* ```--default``` - Generates a default value in the column definition

* ```--null``` - Generates a "NULL" value in the column definition

* ```--primary``` - Generates a "PRIMARY" value in the column definition

* ```--unsigned``` - Generates an "UNSIGNED" value in the column definition