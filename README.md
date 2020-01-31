# Instagram Basic Display API
Instagram Basic Display API is an HTTP-based API that apps can use to get an Instagram user's profile, images, videos, and albums.

This is currently a proof-of-concept in Alpha. Please do not use on production sites.

## Methods

**getUserProfile(**_string_ **$username)** - Get an authenticated user's profile information
- https://developers.facebook.com/docs/instagram-basic-display-api/reference/user

**getUserMedia(**_string|int_ **$username)** - Get a list of Media on the User.
- https://developers.facebook.com/docs/instagram-basic-display-api/guides/getting-profiles-and-media

**getAuthorizationCodeUri()** - Get the Authorization Code URI.
- https://developers.facebook.com/docs/instagram-basic-display-api/reference/oauth-authorize

**getRedirectUri(**_bool_ **$httpUrl)** - Get the Redirect URI.
- If `$httpUrl` is set to `true` the full URI is returned.
