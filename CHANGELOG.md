# Load Order Library API

# Table of Contents

<!-- TOC depthto:1 -->

- [v0.1.0](#v100)
- [Subheading definitions](#subheading-definitions)

<!-- /TOC -->

# v0.1.1
> 2021-03-10

## Added
- Added `LoadOrdersTest.php` for feature testing load orders
- Added `LoadOrderController.php` for responding to routes defined in `api.php`
- Added `get`, `post`, and `destroy` routes to `api.php` for handling requests
- Added `GamesTableSeeder` to seeder, in addition to a factory generating 5 load orders
- Added factories for `Game` and `LoadOrder` for testing
- Added `UploadServiceTest` to test file uploading
- Created `UploadService` to handle file uploading and file name generation
- Added `CreateSlug` and `ValidFiles` helpers
- Created custom `ValidFilename` rule
- Added `uploads` driver to `filesystems.php`

## Fixed

## Changed

## Removed
- Removed example tests

# v0.1.0 
> 2021-03-10

- Initial commit getting things ready to work on api

# Subheading definitions

## Added
Used for additions that did not already exist.

## Fixed
Used for fixes to existing things that don't function as intended. 

Example: in [v1.2.2](#v122) I listed changing the decimals to be 2 spaces as a fix as that was the intended result but I forgot to implement that. Whereas in [v1.2.4](#v124) I listed the change as a change instead, as it was already working as intended and I decided to change it to 2 decimal places.

## Changed
Used for updates/changes to existing things that doesn't fall under fixes. (Eg: adding headings to changelog, or changing the color of an element).

## Removed
Used for indicating things that were removed and not changed into something else. Like removing commenting code in a file, full functions, or entire files.

## Internals
Used for updates to NPM/Composer dependencies, whether updated, added, or removed.

## Closed
Used to link to closed Github issues, if applicable.