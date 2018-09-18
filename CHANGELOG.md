# Changelog

## [Unreleased]
### Added
- `theme` command.  This will install a theme from a git url or if no url is set, it will present the options 

### Changed
- General code cleanup and consolidation

## [0.7.3] - 2018-09-16
### Added
- `--vagrant` option to the `new` command.  If this is set, it will run `vagrant up` when installation is complete.

## [0.7.2] - 2018-09-16
### Notes
This update is to support the newest version of the framework which branches the views out of the main framework repository in support of a "theme" system.  Nothing has changed in the themes except their location.  The `new` command will automatically install them for you when it creates your project.  Please see the [README](https://github.com/dappur/dapp#new) to learn more.

### Changed
- Project creation now supports installing themes automatically, since the themes have been separated from the main framework.

### Removed
- Migrate commands.  Theres no point as they were just passthrough commands for phinx.

## [0.7.1] - 2018-09-15
### Added
- App class generation command: `app`
- Middleware class generation command: `middleware`
- Twig Extension class generation command: `twigex`

### Changed
- Updated README
- Updated command hints and instructions

## [0.7.0] - 2018-09-15
### Added
- New readme with usage instructions.

### Changed
- Updated existing commands to work with v3.0 of the Framework.
- Disabled the `setup` command until it can be rewritten for v3.0.

### Removed
- Removed redundant `Controller.php` classes
- Legacy code that was unused


[Unreleased]: https://github.com/dappur/dapp/compare/v0.7.3...HEAD
[0.7.3]: https://github.com/dappur/dapp/compare/v0.7.2...v0.7.3
[0.7.2]: https://github.com/dappur/dapp/compare/v0.7.1...v0.7.2
[0.7.1]: https://github.com/dappur/dapp/compare/v0.7.0...v0.7.1
[0.7.0]: https://github.com/dappur/dapp/tree/v0.7.0