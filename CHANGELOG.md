# Changelog

## 1.3.0 (March 27, 2020)
Partial rewrite after the discovery of the `limit` API param. Everything should work as before, in fact it should work better!

### Added
- **InstagramBasicDisplayApiConfig.module** Added `limit` config option.
- **InstagramBasicDisplayApi.module** `getMedia()` added `children` option. If set to `false` carousel album children will not be requested/returned.
- **InstagramBasicDisplayApi.module** `getMedia()` added `json` option. If set to `true` a JSON string is returned. The default is equivalent to `$config->ajax` as generally when an AJAX request is made, JSON is the required response.

### Changed
- **InstagramBasicDisplayApi.module** `count` option for `getMedia()` changed name to `limit` and this now is implemented in the request.
- **InstagramBasicDisplayApi.module** Cache now only operates on `apiRequest()`.

### Fixed
- **InstagramBasicDisplayApi.module** `getMedia()` now removes items without a `media_url`.
- **InstagramBasicDisplayApi.module** `filterMedia()` now checks if a caption is present before trying to parse tags.

### Removed
- **InstagramBasicDisplayApi.module** `getCacheKey()` and `getCacheTime()` and moved their logic into `apiRequest()` as this is now the only place where cache is used.


## 1.2.1 (March 21, 2020)

### Added
- **README.md** `$instagram = $modules->get("InstagramBasicDisplayApi");` added to examples for clarity.
- **README.md** `getMedia()` example added.

### Removed
- **InstagramBasicDisplayApi.module** `getEndpoint()` and `urlGraph` constant - This is now handled in `apiRequest()` directly.
- **InstagramBasicDisplayApi.module** `refreshAccessToken()` - This is now handled in `apiRequest()` directly.


## 1.2.0 (March 12, 2020)
Module recoded to use the User Token Generator instead of OAuth.

## 1.0.1 (February 25, 2020)

### Fixed
- **InstagramBasicDisplayApi.module** `getMedia()` - `CAROUSEL_ALBUM` children now returned on all items of this type regardless of the type option.
