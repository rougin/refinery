# Refinery

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Generates "ready-to-eat" migrations for [CodeIgniter](http://www.codeigniter.com/). An extension and a command line interface for [Migrations Class](http://www.codeigniter.com/user_guide/libraries/migration.html).

## Install

Via Composer

``` bash
$ composer require rougin/refinery
```

## Usage

### Creating a table

``` bash
$ php vendor/bin/refinery create create_user_table
"20150607123241_create_user_table.php" has been created.
```

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

**NOTE**: Use `--from-database` option if you want to create a migration of a table from a database.

### Creating a column inside a table

```bash
$ php vendor/bin/refinery create add_name_in_user_table
"20150607123510_add_name_in_user_table.php" has been created.
```

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

### Migrate, rollback and reset

```bash
$ php vendor/bin/refinery migrate
"20150607123241_create_user_table" has been migrated to the database.
"20150607123510_add_name_in_user_table" has been migrated to the database.
```

```bash
$ php vendor/bin/refinery rollback
Database is reverted back to version 20150607123241. (20150607123241_create_user_table)
```

**NOTE**: You can also specify the version you want to rollback on using the `--version` option. (e.g: `--version=20150607123241`)

```bash
$ php vendor/bin/refinery reset
Database has been resetted.
```

## Change Log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email rougingutib@gmail.com instead of using the issue tracker.

## Credits

- [Rougin Royce Gutib][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/rougin/refinery.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/rougin/refinery/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/rougin/refinery.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/rougin/refinery.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rougin/refinery.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/rougin/refinery
[link-travis]: https://travis-ci.org/rougin/refinery
[link-scrutinizer]: https://scrutinizer-ci.com/g/rougin/refinery/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/rougin/refinery
[link-downloads]: https://packagist.org/packages/rougin/refinery
[link-author]: https://github.com/rougin
[link-contributors]: ../../contributors
