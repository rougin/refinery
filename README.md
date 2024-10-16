# Refinery

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Total Downloads][ico-downloads]][link-downloads]

Refinery is an extension and a command line interface of [Migrations Class](https://www.codeigniter.com/user_guide/libraries/migration.html) for the [Codeigniter](https://codeigniter.com/) framework. It uses the [Describe](https://roug.in/describe/) library for retrieving the database tables and as the basis for code generation.

## Installation

Install `Refinery` through [Composer](https://getcomposer.org/):

``` bash
$ composer require rougin/refinery
```

## Basic Usage

### Creating a table

``` bash
$ vendor/bin/refinery create create_users_table
"20180621090905_create_users_table.php" has been created.
```

``` php
// application/migrations/20180621090905_create_users_table.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_users_table extends CI_Migration
{
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
```

Use `--from-database` option to create a migration from an existing database table.

``` sql
CREATE TABLE IF NOT EXISTS `user` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `age` int(2) NOT NULL,
    `gender` varchar(10) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
```

``` bash
$ vendor/bin/refinery create create_users_table --from-database
"20180621090905_create_users_table.php" has been created.
```

``` php
// application/migrations/20180621090905_create_users_table.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_users_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('users', array(
            'gender' => array(
                'type' => 'string',
                'constraint' => 10,
                'auto_increment' => FALSE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->add_column('users', array(
            'age' => array(
                'type' => 'integer',
                'constraint' => 2,
                'auto_increment' => FALSE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->add_column('users', array(
            'name' => array(
                'type' => 'string',
                'constraint' => 200,
                'auto_increment' => FALSE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->add_column('users', array(
            'id' => array(
                'type' => 'integer',
                'constraint' => 10,
                'auto_increment' => TRUE,
                'default' => '',
                'null' => FALSE,
                'unsigned' => FALSE
            ),
        ));

        $this->dbforge->create_table('users');
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}
```

### Creating a column inside a table

```bash
$ vendor/bin/refinery create add_name_in_users_table
"20180621090953_add_name_in_users_table.php" has been created.
```

``` php
// application/migrations/20180621090953_add_name_in_users_table.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_name_in_users_table extends CI_Migration
{
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
```

#### Available keywords

| Command | Description |
| ------- | ----------- |
| `create` | creates new table (`create_users_table`) |
| `add` | adds new column for a specific table (`add_username_in_users_table`) |
| `delete` | deletes specified column from table (`delete_created_at_in_users_table`) |
| `modify` | updates the specified column from table (`modify_name_in_users_table`) |

### Migrate, rollback and reset

```bash
$ vendor/bin/refinery migrate
Migrating: 20180621090905_create_users_table
Migrated:  20180621090905_create_users_table
Migrating: 20180621090953_add_name_in_users_table
Migrated:  20180621090953_add_name_in_users_table
```

```bash
$ vendor/bin/refinery rollback
Rolling back: 20180621090953_add_name_in_users_table
Rolled back:  20180621090953_add_name_in_users_table
```

**NOTE**: You can also specify the version you want to rollback on using the `--version` option. (e.g: `--version=20180621090905`)

```bash
$ vendor/bin/refinery rollback 20180621090905
Rolling back: 20180621090905_create_users_table
Rolled back:  20180621090905_create_users_table
```

```bash
$ vendor/bin/refinery reset
Rolling back: 20180621090953_add_name_in_users_table
Rolled back:  20180621090953_add_name_in_users_table
Rolling back: 20180621090905_create_users_table
Rolled back:  20180621090905_create_users_table
```

## Changelog

Please see [CHANGELOG][link-changelog] for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Credits

- [All contributors][link-contributors]

## License

The MIT License (MIT). Please see [LICENSE][link-license] for more information.

[ico-build]: https://img.shields.io/github/actions/workflow/status/rougin/refinery/build.yml?style=flat-square
[ico-coverage]: https://img.shields.io/codecov/c/github/rougin/refinery?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rougin/refinery.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/rougin/refinery.svg?style=flat-square

[link-build]: https://github.com/rougin/refinery/actions
[link-changelog]: https://github.com/rougin/refinery/blob/master/CHANGELOG.md
[link-contributors]: https://github.com/rougin/refinery/contributors
[link-coverage]: https://app.codecov.io/gh/rougin/refinery
[link-downloads]: https://packagist.org/packages/rougin/refinery
[link-license]: https://github.com/rougin/refinery/blob/master/LICENSE.md
[link-packagist]: https://packagist.org/packages/rougin/refinery
[link-upgrading]: https://github.com/rougin/refinery/blob/master/UPGRADING.md