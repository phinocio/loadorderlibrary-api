# Load Order Library API

# Table of Contents

<!-- TOC depthto:1 -->

- [v1.1.1](#v111)
- [v1.1.0](#v110)
- [v1.0.1](#v101)
- [v1.0.0](#v100)

<!-- /TOC -->

# v1.1.1
> 2022-01-31

## Fixed
- Fixed wrong added/missing for list comparison items

# v1.1.0
> 2022-01-26

## Added
- Implemented the comparison feature for the API. Said feature is a bit simpler than the current LoL one, but will provide better info for the front-end to then display how it wants

## Fixed
- Files are now lowercased on upload this will fix an issue where if the file name was different (`Skyrim.ini` vs `skyrim.ini`) for example, the hash would be different despite the contents being the same

## Internals
- Composer
	- Updated with `composer update`

# v1.0.1
> 2021-08-31

## Added
- Added backups, list expiring, and other things from main site

# v1.0.0
> 2021-07-10

## Added
- Initial release