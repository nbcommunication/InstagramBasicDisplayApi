# Instagram Basic Display API
Instagram Basic Display API is an HTTP-based API that apps can use to get an Instagram user's profile, images, videos, and albums.

## Installation
Prior to installation, please follow the instructions on how to set up a Facebook application for the Instagram Basic Display API
1. Download the [zip file](https://github.com/nbcommunication/InstagramBasicDisplayApi/archive/master.zip) at Github or clone the repo into your `site/modules` directory.
2. If you downloaded the zip file, extract it in your `sites/modules` directory.
3. In your admin, go to Modules > Refresh, then Modules > New, then click on the Install button for this module.
4. Add your Instagram App ID and App Secret to the module configuration and **Save**.
5. In your Facebook application settings, add the Redirect URI and **Save**.
6. Send the Authorization Code URI to the Instagram User you wish to authenticate.
7. Once they have authorized the application, you should see them listed under Authorized Accounts.

## Creating a Facebook Application for the Instagram Basic Display API
Coming Soon!

## Methods

**getProfile(**_string_ **$username)** - Get an authorized user's profile information
- https://developers.facebook.com/docs/instagram-basic-display-api/reference/user

**getMedia(**_string|int_ **$username)** - Get a list of Media on the User.
- https://developers.facebook.com/docs/instagram-basic-display-api/guides/getting-profiles-and-media

**getDefaultUser()** - Get the default (first) authorized user
- Returns the username.

**getRedirectUri()** - Get the Redirect URI.
- This must be added to your application before authorization.

**getAuthUri()** - Get the Authorization Code URI.
- https://developers.facebook.com/docs/instagram-basic-display-api/reference/oauth-authorize


## Backwards Compatibility with InstagramFeed
Coming soon!

**getRecentMedia(**_string|int_ **$username)** - Get a list of Media on the User.
- Alias of `getMedia()` to provide backwards compatibility with [Instagram Feed](https://modules.processwire.com/modules/instagram-feed/).
