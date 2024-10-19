# Refinery

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Total Downloads][ico-downloads]][link-downloads]

`Refinery` is a console-based package of [Migrations Class](https://www.codeigniter.com/userguide3/libraries/migration.html) for the [Codeigniter 3](https://codeigniter.com/userguide3). It uses the [Describe](https://roug.in/describe/) package for retrieving the database tables for creating database migrations.

## Installation

From an existing `Codeigniter 3` project, the `Refinery` package can be installed through [Composer](https://getcomposer.org/):

``` bash
$ composer require rougin/refinery --dev
```

``` json
// ciacme/composer.json

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

Then configure the project's database connectivity settings:

``` php
// ciacme/application/config/database.php

// ...

$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => '',
    'password' => '',
    'database' => '',
    'dbdriver' => 'mysqli',
    
    // ...
);
```

> [!NOTE]
> Although using database connection is not required in using `Refinery`, this is only applicable when creating migration files based on an existing database. Please see [Creating from database](https://github.com/rougin/refinery?tab=readme-ov-file#creating-from-database) below for its usage.

## Basic Usage

### Creating migration files

To create a new database migration, kindly run the `create` command:

``` bash
$ vendor/bin/refinery create create_users_table
[PASS] "20241019044009_create_users_table.php" successfully created!
```

``` php
// ciacme/application/migrations/20241019044009_create_users_table.php

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
> * The `Migration` class under `Refinery` is directly extended on `CI_Migration`.
> * The created file will be in `migrations` directory under `application`. If it does not exists, `Refinery` will automatically create the specified directory.

When creating database migrations, `Refinery` will try to guess the expected output of `up` and `down` methods of a migration file based on its name (e.g., `add_name_in_users_table`):

```bash
$ vendor/bin/refinery create add_name_in_users_table
"20241019044035_add_name_in_users_table.php" has been created.
```

``` php
// ciacme/application/migrations/20241019044035_add_name_in_users_table.php

use Rougin\Refinery\Migration;

class Migration_add_name_in_users_table extends Migration
{
    /**
     * @return void
     */
    public function up()
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

    /**
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_column('users', 'name');
    }
}
```

Please see the accepted keywords below when creating database migration files:

| Keyword  | Description                                    | Example                      |
|----------|------------------------------------------------|------------------------------|
| `add`    | Adds new column to a table                     | `add_name_in_users_table`    |
| `create` | Creates new table with `id` as the primary key | `create_users_table`         |
| `delete` | Deletes a column from a table                  | `delete_name_in_users_table` |
| `modify` | Updates a column of a table                    | `modify_name_in_users_table` |

### Running the migrations

Kindly use the `migrate` command to use the files for database migrations:

``` bash
$ vendor/bin/refinery migrate
[INFO] Migrating "create_users_table"...
[PASS] "create_users_table" migrated!
[INFO] Migrating "add_name_in_users_table"...
[PASS] "add_name_in_users_table" migrated!
```

When running this command, the target timestamp (`--target`) will always be the latest file in the `migrations` directory if not specified (e.g., `add_name_in_users_table`). Use the `--target` option to migrate to a specific version:

``` bash
$ vendor/bin/refinery migrate --target=20241019044009
[INFO] Migrating "create_users_table"...
[PASS] "create_users_table" migrated!
```

To rollback a database, kindly use the `rollback` command:

``` bash
$ vendor/bin/refinery rollback
[INFO] Rolling back "add_name_in_users_table"...
[PASS] "add_name_in_users_table" rolled back!
```

> [!NOTE]
> Without a `--target` option, the `rollback` will only revert to its previous version (e.g., `create_users_table`).

To reset back the database schema to version `0`, the `reset` command can be used:

``` bash
$ vendor/bin/refinery migrate
[INFO] Migrating "add_name_in_users_table"...
[PASS] "add_name_in_users_table" migrated!

$ vendor/bin/refinery reset
[INFO] Rolling back "add_name_in_users_table"...
[PASS] "add_name_in_users_table" rolled back!
[INFO] Rolling back "create_users_table"...
[PASS] "create_users_table" rolled back!
```

## Creating from database

`Refinery` also allows to create a database migration based on the existing database table. Prior in creating its database migration, kindly ensure that the specified table already exists in the database schema:

``` sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

After checking the database table exists, run the same `create` command with the `--from-database` option:

``` bash
$ vendor/bin/refinery create create_users_table --from-database
"20241019044729_create_users_table.php" has been created.
```

``` php
// ciacme/application/migrations/20241019044729_create_users_table.php

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

        $data = array('name' => array());
        $data['name']['type'] = 'varchar';
        $data['name']['auto_increment'] = false;
        $data['name']['constraint'] = 100;
        $data['name']['default'] = null;
        $data['name']['null'] = true;
        $data['name']['unsigned'] = false;
        $this->dbforge->add_field($data);

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
> The `--from-database` option only exists when creating files under the `create_*_table` prefix.

## Creating sequential migrations

By default, `Refinery` uses a timestamp prefix as its numbering style when creating migration files. To change it to a sequential numbering, kindly update the value of `migration_type` in the `config/migration.php` to `sequential`:

``` php
// ciacme/application/config/migration.php

/*
|--------------------------------------------------------------------------
| Migration Type
|--------------------------------------------------------------------------
|
| Migration file names may be based on a sequential identifier or on
| a timestamp. Options are:
|
|   'sequential' = Sequential migration naming (001_add_blog.php)
|   'timestamp'  = Timestamp migration naming (20121031104401_add_blog.php)
|                  Use timestamp format YYYYMMDDHHIISS.
|
| Note: If this configuration value is missing the Migration library
|       defaults to 'sequential' for backward compatibility with CI2.
|
*/
$config['migration_type'] = 'sequential';
```

Then run the `create` command to generate a migration file in sequential migration:

``` bash
$ vendor/bin/refinery create create_users_table
[PASS] "001_create_users_table.php" successfully created!
```

Alternatively, the `--sequential` option can also be added in the `create` command to update the said configuration:

``` bash
$ vendor/bin/refinery create add_name_in_users_table --sequential
[PASS] "002_add_name_in_users_table.php" successfully created!
```

> [!NOTE]
> When using the `--sequential` option, the `migration_type` in the `config/migration.php` is also set as `sequential`.

## Using `refinery.yml`

`Refinery` currently works out of the box after the configuration based on `Installation`. However, using a `refinery.yml` can be used for complex setups like specifying the new application path:

``` yaml
# refinery.yml

app_path: %%CURRENT_DIRECTORY%%
```

To create a `refinery.yml`, simply run the `initialize` command:

``` bash
$ vendor/bin/refinery initialize
[PASS] "refinery.yml" added successfully!
```

> [!NOTE]
> `%%CURRENT_DIRECTORY%%` is a placeholder variable which is the current directory of `refinery.yml`.

### `app_path`

This property specifies the `application` directory. It may updated to any directory (e.g., `ciacme/application`, `ciacme/config`, etc.) as long it can detect the `config/config.php` file from the defined directory:

``` yaml
# refinery.yml

app_path: %%CURRENT_DIRECTORY%%/Sample

# ...
```

> [!NOTE]
> `Refinery` will try to check the path specified in `app_path` if it is a valid `Codeigniter 3` project. Then it will perform another check if the `application` directory exists or if the `config` directory can be accessed directly from the directory defined in `app_path`.

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