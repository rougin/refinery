# Changelog

All notable changes to `Refinery` will be documented in this file.

## [0.4.0](https://github.com/rougin/refinery/compare/v0.3.0...v0.4.0) - Unreleased

### Added
- `Parser` for determining commands to be used

### Changed
- Based all functions to `CI_Migration` class
- Separated core functionality from commands
- Messages in displaying all commmand outputs
- `auto_increment` option to `auto-increment` in `create` command
- Code coverage provider to `Codecov`
- Code documentation by `php-cs-fixer`
- Improved code quality by `phpstan`
- Reworked entire code structure
- Workflow provider to `Github Actions`

### Removed
- `sequential` option in `create` command

## [0.3.0](https://github.com/rougin/refinery/compare/v0.2.1...v0.3.0) - 2017-01-07

### Added
- Exceptions for specific errors

### Fixed
- Guessing the keywords from user's input

### Changed
- Improve code quality

## [0.2.1](https://github.com/rougin/refinery/compare/v0.2.0...v0.2.1) - 2016-09-07

### Added
- StyleCI for conforming code to PSR standards

### Changed
- Versions of several libraries in `composer.json`

## [0.2.0](https://github.com/rougin/refinery/compare/v0.1.6...v0.2.0) - 2016-04-14

### Added
- Tests

## [0.1.6](https://github.com/rougin/refinery/compare/v0.1.5...v0.1.6) - 2016-03-05

### Fixed
- Forgot code `$this->codeigniter = get_instance();` in `AbstractCommand`

## [0.1.5](https://github.com/rougin/refinery/compare/v0.1.4...v0.1.5) - 2016-03-03

### Changed
- From `Rougin\SparkPlug\SparkPlug` to CodeIgniter's `get_instance()`

### Fixed
- Issue in getting CodeIgniter's instance

## [0.1.4](https://github.com/rougin/refinery/compare/v0.1.3...v0.1.4) - 2015-10-23

### Changed
- Code structure
- Extensibility

## [0.1.3](https://github.com/rougin/refinery/compare/v0.1.2...v0.1.3) - 2015-09-18

### Added
- Migration to [`rougin/blueprint`](https://github.com/rougin/blueprint)

### Fixed
- Commands
- Simplified code structure

## [0.1.2](https://github.com/rougin/refinery/compare/v0.1.1...v0.1.2) - 2015-09-18

### Added
- [`rougin/spark-plug`](https://github.com/rougin/spark-plug) as a dependency

### Fixed
- Based functions from [`rougin/describe`](https://github.com/rougin/describe)'

## [0.1.1](https://github.com/rougin/refinery/compare/v0.1.0...v0.1.1) - 2015-06-07

### Fixed
- Include [`symfony/console`](http://symfony.com/doc/current/components/console/introduction.html) as a dependency

## 0.1.0 - 2015-06-25

### Added
- `Refinery` library