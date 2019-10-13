# Reports Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.0.7 - 2019-10-13

> {warning} The permissions has been changed, so existing users/user groups with limited permissions need to be reset to the new permissions.

### Added
- Added export targets dropdown in reports index

### Fixed
- Fixed parsing of Twig code in subject of email targets
- Fixed faulty logic in user permissions that caused issues when users had some but not other permissions ([#3](https://github.com/superbigco/craft-reports/issues/3))

## 1.0.6 - 2019-08-12

### Added
- Added plugin settings
- Added plugin title override

## Changed
- Changed queue job to use target name instead of generic title

### Fixed
- Fixed running when having multiple reports

## 1.0.5 - 2019-03-08

### Added
- Added console command for listing report targets

### Changed
- Report targets is now handled via queue jobs

### Fixed
- Fixed highlight of subnav link

## 1.0.4 - 2019-03-07

### Added
- Added console command for running report targets
- Added list of connected report targets when editing a report

## 1.0.3 - 2019-03-06

### Fixed
- Fixed double table creation in install migration that caused SQL errors.

## 1.0.2 - 2019-03-06

### Fixed
- Fixed SQL error when creating new report

## 1.0.1 - 2019-03-06

### Changed
- Changed `league/csv` dependency to not crash with other plugins

### Fixed
- Fixed error when creating new report

## 1.0.0 - 2019-03-05
### Added
- Initial release
