# Refinery

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Total Downloads][ico-downloads]][link-downloads]

`Refinery` is a console-based package of [Migrations Class](https://www.codeigniter.com/userguide3/libraries/migration.html) for the [Codeigniter 3](https://codeigniter.com/userguide3). It uses the [Describe](https://roug.in/describe/) package for retrieving the database tables and as the basis for code generation.

## Installation

From an existing `Codeigniter 3` project, the `Refinery` package can be installed through [Composer](https://getcomposer.org/):

``` bash
$ composer require rougin/refinery --dev
```

``` json
// acme/composer.json

{
  // ...

  "require-dev":
  {
    "mikey179/vfsstream": "1.6.*",
    "phpunit/phpunit": "4.* || 5.* || 9.*",
    "rougin/refinery": "~0.4"
  }
}
```

## Basic Usage

To create a new database migration, kindly run the `create` command:

``` bash
$ vendor/bin/refinery create create_users_table
[PASS] "20241017173347_create_users_table.php" successfully created!
```

``` php
// application/migrations/20241017173347_create_users_table.php

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
```

> [!NOTE]
> The `Migration` class under `Refinery` is directly based on `CI_Migration`. The only difference is the said class provides improved code documentation for the `dbforge` utility.

To create a database migration from an existing database table, run the same `create` command with the `--from-database` option:

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

The `Refinery` package will try to guess the expected output of `up` and `down` methods of a migration file based on its provided name (e.g., `add_name_in_users_table`):

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

### Available keywords

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

**NOTE**: You can also specify the version you want to rollback on using the `--target` option. (e.g: `--target=20180621090905`)

```bash
$ vendor/bin/refinery rollback --target 20180621090905
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