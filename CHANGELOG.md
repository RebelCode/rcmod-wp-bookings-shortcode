# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.1-alpha6] - 2018-12-06
### Changed
- Updated usage of service transformer to use the one provided by the bookings UI module.

## [0.1-alpha5] - 2018-10-30
### Changed
- Using the services entity manager to retrieve services.

## [0.1-alpha4] - 2018-09-13
### Fixed
- Fixed missing support for "Week Starts On" setting.

## [0.1-alpha3] - 2018-07-14
### Added
- Support for wizard color setting.

## [0.1-alpha2] - 2018-07-13
### Changed
- Moved responsibility of rendering styles and scripts away from this module. 
- Using wizard block for rendering application instead of events.
- Preselected service information for first wizard step is loaded synchronously during request which improves UX.
- Using separate events for filtering shortcode params (one for filtering and one for transforming).

## [0.1-alpha1] - 2018-05-21
Initial version.
