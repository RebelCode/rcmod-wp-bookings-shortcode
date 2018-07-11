# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD
### Changed
- Moved responsibility of rendering styles and scripts away from this module. 
- Using wizard block for rendering application instead of events.
- Preselected service information for first wizard step is loaded synchronously during request which improves UX.
- Using separate events for filtering shortcode params (one for filtering and one for transforming).

## [0.1-alpha1] - 2018-05-21
Initial version.
