# Changelog

## 1.2.1 (March 21, 2020)

### Added
- **README.md** `$instagram = $modules->get("InstagramBasicDisplayApi");` added to examples for clarity.
- **README.md** `getMedia()` example added.

### Fixed
...

### Changed
...

### Removed
- **InstagramBasicDisplayApi.module** `getEndpoint()` and `urlGraph` constant - This is now handled in `apiRequest()` directly.
- **InstagramBasicDisplayApi.module** `refreshAccessToken()` - This is now handled in `apiRequest()` directly.


## 1.2.0 (March 12, 2020)
Module recoded to use the User Token Generator instead of OAuth.

## 1.0.1 (February 25, 2020)

### Fixed
- **InstagramBasicDisplayApi.module** `getMedia()` - `CAROUSEL_ALBUM` children now returned on all items of this type regardless of the type option.
