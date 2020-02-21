# Instagram Basic Display API
Instagram Basic Display API is an HTTP-based API that apps can use to get an Instagram user's profile, images, videos, and albums.

This is currently a proof-of-concept in Alpha. Please do not use on production sites.

## Methods

**getProfile(**_string_ **$username)** - Get an authorized user's profile information
- https://developers.facebook.com/docs/instagram-basic-display-api/reference/user

**getMedia(**_string|int_ **$username)** - Get a list of Media on the User. //$options
- https://developers.facebook.com/docs/instagram-basic-display-api/guides/getting-profiles-and-media

**getDefaultUser()** - Get the default (first) authorized user
- Returns the username.

**getRedirectUri()** - Get the Redirect URI.
- This must be added to your application before authorization.

**getAuthUri()** - Get the Authorization Code URI.
- https://developers.facebook.com/docs/instagram-basic-display-api/reference/oauth-authorize


## Backwards Compatibility with InstagramFeed

@todo 
**getRecentMedia(**_string|int_ **$username)** - Get a list of Media on the User.
- Alias of `getMedia()` to provide backwards compatibility with [Instagram Feed](https://modules.processwire.com/modules/instagram-feed/).
