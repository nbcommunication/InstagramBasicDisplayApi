# Instagram Basic Display API
Instagram Basic Display API is an HTTP-based API that Facebook apps can use to get an Instagram user's profile, images, videos, and albums. This module provides an easy way to access that data.

## Requirements
* ProcessWire >= 2.7
* A Facebook Developer account
* Access to an Instagram user account

## Creating a Facebook App

These instructions will assist you in creating a Facebook App for the Instagram Basic Display API.

The app you will create uses the [*User Token Generator*](https://developers.facebook.com/docs/instagram-basic-display-api/overview#user-token-generator) for authentication - it does not need to be submitted for App Review. However, this does mean that you need to be able to login to the Instagram account for approval. If setting up for a client, suggest arranging a time when they can change the password temporarily to allow you to access the account and authenticate the app.

### Create a New Facebook App
1. Login to your account at [https://developers.facebook.com/](https://developers.facebook.com/).
2. *My Apps > Create App*.
3. Give your app a *Display Name*. This cannot contain certain reserved words such as `Instagram`, `IG`, `Insta` or `Facebook`. I recommend using something like `Image Feed - ProcessWire Website`.
4. Add a *Contact Email* if not already populated.
5. Click **Create App ID** and complete the Security Check if required.
6. You will be prompted to *Add a Product*. Find *Instagram* and click **Set Up**.

### Configure the Facebook App
You should now see *Instagram* listed in *Products* in the left sidebar. Before continuing with the Basic Display setup, you need to configure the app itself.

#### Settings > Basic
1. Scroll to the bottom of this page and click **Add Platform**.
2. Select **Website**.
3. Enter the URL of the site the module will be used on. If you intend to use this module on multiple sites, use your company's own website.
4. **Save Changes**

You can add any other information to this screen that you wish, such as an *App Icon*.

#### Settings > Advanced (Optional)
1. *Security > Server IP Whitelist* - Whitelist any server IPs here.
2. *Business Manager* - If you have a Business Manager account, select it here to assign ownership of the app.

### Basic Display Configuration
Now your Facebook app is ready, you can configure it to access the Instagram Basic Display API.

#### Create an Instagram Basic Display App
*Instagram > Basic Display*, click **Create New App** then **Create App**.

The app requires some settings:
* *Client OAuth Settings* - Add `https://{your-website.com}/{your-admin-url}/InstagramBasicDisplayApi` as a *Valid OAuth Redirect URI*.
* *Deauthorize Callback URL* - Set this to `https://{your-website.com}/{your-admin-url}/InstagramBasicDisplayApi?action=deauthorize`.
* *Data Deletion Request URL* - Set this to `https://{your-website.com}/{your-admin-url}/InstagramBasicDisplayApi?action=delete`.

The Redirect URI should never be called by the API. However, if a user does deauthorize your app, or request deletion, the module is setup to handle these requests and notify you, so it makes sense to set actual URLs here.

#### Add the Instagram user account
1. *Roles > Instagram Testers*.
2. Click **Add Instagram Testers**.
3. Enter the username of the account to be added and click it when it appears.
4. Click **Submit**.
5. Login to the Instagram Account through [instagram.com](https://instagram.com/).
6. *Settings > Apps and Websites*. On the website, click the username to go to the profile, then click the "cog" icon.
7. Click on **Tester Invites** then click **Accept** for the app you have created.
8. Back in the App Dashboard, go to *Instagram > Basic Display > User Token Generator*.
9. You should now see the user account here. Click on **Generate Token**.
10. Log in to the Instagram Account again if necessary or click the continue button.
11. **Authorize** the app.
12. Check *I Understand* then copy the token that has been generated. Save it somewhere as you will need it to configure the module.

These instructions were current as of March 2020. If you notice any changes to how the Facebook Developer platform operates, please let me know on the [Support Forum](https://processwire.com/talk/topic/23028-instagrambasicdisplayapi/).

## Module Configuration
After you have installed this module:
1. *Modules > Configure > InstagramBasicDisplayApi*.
2. Add the username and generated token to *Add an Instagram User Account*.
3. Click **Submit**.
4. If successful, you should now see the account information in *Authorized Accounts*.

You can add as many accounts as you wish, as long as they have a token generated in your app.

The access token will be renewed automatically within the week prior to expiry.

## Methods

#### **getProfile(**_string_ **$username)**
Get a user's profile information.

This method returns an associative array with the following fields/keys:
- `username`
- `id`
- `account_type`
- `media_count`

```php
$instagram = $modules->get('InstagramBasicDisplayApi');

// Get the profile data of the default (first) user
$profile = $instagram->getProfile();

// Get the profile data of a specified user
$profile = $instagram->getProfile('username');

// Display the profile information
if(count($profile)) {
    $info = '';
    foreach($profile as $key => $value) {
        $info .= "<li>$key: $value</li>";
    }
    echo "<ul>$info</ul>";
}
```

#### **getImages(**_string_ **$username**, _int_ **$limit)**
Get a list of Images for a user.

This method returns a `WireArray` of `WireData` objects each with the following properties:
* `id` - The Media's ID.
* `type` - The Media's type. Can be *IMAGE*, *VIDEO* or *CAROUSEL_ALBUM*.
* `alt` - The Media's caption text. Not returnable for Media in albums.
* `description` - Alias of `alt`.
* `src` - The Media's URL.
* `url` - Alias of `src`.
* `tags` - An array of hashtags.
* `created` - The Media's publish date as a unix timestamp.
* `createdStr` - The Media's publish date in ISO 8601 format.
* `link` - The Media's permanent URL.
* `href` - Alias of `link`.

```php
$instagram = $modules->get('InstagramBasicDisplayApi');

// Get images from the default user
$images = $instagram->getImages(); // Returns all images found in the first request

// Get 10 images from the default user
$images = $instagram->getImages(10);

// Get images from a specified user
$images = $instagram->getImages('username'); // Returns all images found in the first request

// Get 8 images from a specified user
$images = $instagram->getImages('username', 8);

// Render the images
echo "<ul>" .
	$images->each("<li>" .
		"<a href='{href}'>" .
			"<img src='{src}' alt='{alt}'>" .
		"</a>" .
	"</li>") .
"</ul>";
```

The main image of a carousel album and "poster" of a video is also returned.

#### **getCarouselAlbum(**_string_ **$username)**
Get the most recent Carousel Album for a user.

This method returns a `WireData` object with the same properties as `getImage()`. It also has an additional `children` property which contains a `WireArray` of the album's images.

```php
$instagram = $modules->get('InstagramBasicDisplayApi');

// Get the most recent album from the default user
$album = $instagram->getCarouselAlbum();

// Get the most recent album from a specified user
$album = $instagram->getCarouselAlbum('username');

// Render the album
if(isset($album)) {
	if(!$album->children) return '';
	echo '<ul>' .
		$album->children->each('<li>' .
			'<a href="{href}">' .
				'<img src="{src}" alt="{alt}">' .
			'</a>' .
		'</li>') .
	'</ul>';
}
```

#### **getCarouselAlbums(**_string_ **$username**, _int_ **$limit)**
Get a list of Carousel Albums for a user.

This method returns a `WireArray` of `WireData` objects each with the same properties as `getImage()`. Each item also has an additional `children` property which contains a `WireArray` of the album's images.

This method should be used with care, as many API calls may need to be made to find the carousel albums requested. It is recommended to only use this if the Instagram user posts carousel albums frequently.

```php
$instagram = $modules->get('InstagramBasicDisplayApi');

// Get albums from the default user
$albums = $instagram->getCarouselAlbums(); // 4 returned if found

// Get 2 albums from the default user
$albums = $instagram->getCarouselAlbums(2);

// Get albums from a specified user
$albums = $instagram->getCarouselAlbums('username'); // 4 returned if found

// Get 3 albums from a specified user
$albums = $instagram->getCarouselAlbums('username', 3);

// Render the albums
if($albums->count()) {
	echo '<ul>' .
		$instagram->getCarouselAlbums()->each(function($album) {
			if(!$album->children) return '';
			return '<li>' .
				'<ul>' .
					$album->children->each('<li>' .
						'<a href="{href}">' .
							'<img src="{src}" alt="{alt}">' .
						'</a>' .
					'</li>') .
				'</ul>' .
			'</li>';
		}) .
	'</ul>';
}
```

#### **getVideo(**_string_ **$username)**
Get the most recent Video for a user.

This method returns a `WireData` object with the same properties as `getImage()`. There is also an additional property for this media type:
- `poster` - The Media's thumbnail image URL.

```php
$instagram = $modules->get('InstagramBasicDisplayApi');

// Get the most recent video from the default user
$video = $instagram->getVideo();

// Get the most recent video from a specified user
$video = $instagram->getVideo('username');

// Render the video
if(isset($video)) {

	echo '<video ' .
		"src='$video->src' " .
		"poster='$video->poster' " .
		'type="video/mp4" ' .
		'controls ' .
		'playsinline' .
	'></video>';

	if($video->description) {
		echo "<p>$video->description</p>";
	}
}
```

#### **getVideos(**_string_ **$username**, _int_ **$limit)**
Get a list of Videos for a user.

This method returns a `WireArray` of `WireData` objects each with the same properties as `getImage()`. There is also an additional property for this media type:
- `poster` - The Media's thumbnail image URL.

This method should be used with care, as many API calls may need to be made to find the videos requested. It is recommended to only use this if the Instagram user posts videos frequently.

```php
$instagram = $modules->get('InstagramBasicDisplayApi');

// Get videos from the default user
$videos = $instagram->getVideos(); // 4 returned if found

// Get 2 videos from the default user
$videos = $instagram->getVideos(2);

// Get videos from a specified user
$videos = $instagram->getVideos('username'); // 4 returned if found

// Get 3 videos from a specified user
$videos = $instagram->getVideos('username', 3);

// Render the videos
if($videos->count()) {
	echo '<ul>' .
		$videos->each('<li>' .
			'<video ' .
				'src="{src}" ' .
				'poster="{poster}" ' .
				'type="video/mp4" ' .
				'controls ' .
				'playsinline' .
			'></video>' .
		'</li>') .
	'</ul>';
}
```

#### **getMedia(**_string_ **$username**, _int_ **$limit)**
Get a list of Media for a user.

The following example demonstrates how this can be used to create a multi-media gallery using the [UIkit](https://getuikit.com) [Grid](https://getuikit.com/docs/grid), [Cover](https://getuikit.com/docs/cover) and [Lightbox](https://getuikit.com/docs/lightbox) components:

```php
// Function for rendering items
function renderInstagramItem($src, $alt, $href = null) {
	if(is_null($href)) $href = $src;
	return "<a href='$href' data-caption='$alt' class='uk-display-block uk-cover-container'" . ($src !== $href ? " data-poster='$src'" : '') . ">" .
		"<canvas width='640' height='640'></canvas>" .
		"<img src='$src' alt='$alt' data-uk-img data-uk-cover>" .
	"</a>";
}

// Get the module
$instagram = $modules->get('InstagramBasicDisplayApi');

// Get the 16 most recent items and render them based on type
$items = [];
foreach($instagram->getMedia(16) as $item) {
	switch($item->type) {
		case 'VIDEO':
			$items[] = renderInstagramItem($item->poster, $item->alt, $item->src);
			break;
		case 'CAROUSEL_ALBUM':
			// If 4 or greater items, display a grid of the first 4 images
			// Otherwise display the main image (no break, moves to default)
			if($item->children->count() >= 4) {
				$out = '';
				$i = 0;
				foreach($item->children as $child) {
					$out .= "<div" . ($i++ < 4 ? '' : " class='uk-hidden'") . ">" . // Hides items after the 4th one
						renderInstagramItem($child->src, $item->alt) .
					"</div>";
				}
				$items[] = "<div class='uk-grid-collapse uk-child-width-1-2' data-uk-grid>$out</div>";
				break;
			}
		default: // IMAGE
			$items[] = renderInstagramItem($item->src, $item->alt);
			break;
	}
}

// Render the items as a grid
echo "<div class='uk-grid-small uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@l' data-uk-grid data-uk-lightbox>";
foreach($items as $item) {
	echo "<div>$item</div>";
}
echo "</div>";
```

## Pagination
Lazy loading of items can be achieved using `getMedia()`. The following example shows how to return batches of items using AJAX requests:

#### PHP
```php
$instagram = $modules->get('InstagramBasicDisplayApi'); // The pagination cursor is reset if the request is not AJAX, e.g. when the page is loaded.

if($config->ajax) {
	header('Content-Type: application/json');
	echo $instagram->getMedia(); // ['json' => true] is inferred by $config->ajax
	die();
}

echo "<div id='instagram' class='uk-grid-small uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@l' data-uk-grid data-uk-lightbox data-uk-scrollspy='" . json_encode([
	'target' => '> div',
	'cls' => 'uk-animation-slide-bottom-small',
	'delay' => 128,
]) . "'></div>";

```

#### Javascript
```javascript
var instagram = {

	$el: {}, // Where the items go
	$loading: {}, // The loading spinner
	total: 0, // The total number of items

	init: function() {

		this.$el = UIkit.util.$("#instagram");
		if(!this.$el) return;

		// Add the spinner
		UIkit.util.after(this.$el, "<div id='instagram-loading' class='uk-text-center uk-margin-top uk-hidden'><span data-uk-spinner></span></div>");
		this.$loading = UIkit.util.$("#instagram-loading");

		// Get the first batch of items
		this.get();
	},

	get: function() {

		var this$1 = this;

		// Show spinner
		UIkit.util.removeClass(this$1.$loading, "uk-hidden");

		// Request
		UIkit.util.ajax(window.location.href, {
			method: "GET",
			headers: {"X-Requested-With": "XMLHttpRequest"},
			responseType: "json"
		}).then(function(xhr) {

			// Hide spinner
			UIkit.util.addClass(this$1.$loading, "uk-hidden");

			var data = xhr.response;
			if(!UIkit.util.isArray(data) || !data.length) return; // If no items do not render

			var items = [];
			data.forEach(function(item) {

				switch(item.type) {
					case "VIDEO":
						items.push(this$1.renderItem(item.poster, item.alt, item.src));
						break;
					case "CAROUSEL_ALBUM":
						// If 4 or greater items, display a grid of the first 4 images with the rest hidden
						// Otherwise display the main image (no break, moves to default)
						if(item.children.length >= 4) {
							var out = "";
							for(var i = 0; i < item.children.length; i++) {
								out += "<div" + (i < 4 ? "" : " class='uk-hidden'") + ">" +
									this$1.renderItem(item.children[i].src, item.alt) +
								"</div>";
							}
							items.push("<div class='uk-grid-collapse uk-child-width-1-2' data-uk-grid>" + out + "</div>");
							break;
						}
					default: // IMAGE
						items.push(this$1.renderItem(item.src, item.alt));
						break;
				}
			});

			var count = items.length;
			if(count) {

				// Wrap all items with a div
				var out = "";
				for(var i = 0; i < count; i++) {
					out += "<div id='instagram-item-" + (this$1.total + i) + "'>" +
						items[i] +
					"</div>";
				}

				// Append items to the container
				UIkit.util.append(this$1.$el, out);

				// Attach scrollspy listener on last item of second last row
				if(count > 5) {
					UIkit.util.on("#instagram-item-" + (this$1.total + count - 6), "inview", function(e) {
						this$1.get();
					});
				}

				// Update total
				this$1.total = this$1.total + count;
			}
		}, function(e) {
			UIkit.util.addClass(this$1.$loading, "uk-hidden");
			console.log(e); // ERROR
		})
	},

	renderItem: function(src, alt, href) {
		if(href === void 0) href = src;
		return "<a href='" + href + "' data-caption='" + alt + "' class='uk-display-block uk-cover-container'" +
			(src !== href ? " data-poster='" + src + "'" : "") + ">" +
			"<canvas width='640' height='640'></canvas>" +
			"<img src='" + src + "' alt='" + alt + "' data-uk-img data-uk-cover>" +
		"</a>";
	}
};

UIkit.util.ready(function() {
	instagram.init();
});
```
The javascript example uses [UIkit's Javascript Utilities](https://github.com/uikit/uikit-site/blob/feature/js-utils/docs/pages/javascript-utilities.md) but the same effect is achievable using plain javascript or any common library like jQuery.

## Backwards Compatibility with Instagram Feed
The primary reason for this module's development was to retain functionality on existing websites that use the [Instagram Feed](https://modules.processwire.com/modules/instagram-feed/) module, which uses a now deprecated API.

To assist with upgrading, this module replicates some methods provided by Instagram Feed:

#### **getRecentMedia(**_string_ **$username)**
Get the most recent media published by a user.

This is probably the most commonly used method from [InstagramFeed](https://modules.processwire.com/modules/instagram-feed/). You should not need to change your method call, however some of the values returned by the deprecated API are no longer present and are returned as `null` instead.

#### **getRecentMediaByTag(**_string_ **$tag**, _string_ **$username)**
Get a list of recently tagged media.

Instagram Basic Display API does not provide a way to search media by tag. This implementation will keep calling the API until it has enough matching items to return, or all media items have been retrieved.

Using this method is therefore **not recommended** as it is likely to slow response times and could possibly exhaust resource limits.

#### **setImageCount(**_int_ **$imageCount)**
Set the image count.

#### **getRecentComments(**_array_ **$media)**
Get recent comments. Returns a blank array as comments cannot be accessed by the Instagram Basic Display API.

#### **getUserIdByUsername(**_string_ **$username)**
Get the user's ID from their username.

### Upgrading to Instagram Basic Display Api
For a basic implementation, where all that Instagram Feed was requesting was the latest _n_ images, switching to use this module should only require installation and setup, then changing the module call:

```php
// Previously...
$instagram = $modules->get('InstagramFeed');
$images = $instagram->getRecentMedia();

// Now...
$instagram = $modules->get('InstagramBasicDisplayApi');
$images = $instagram->getRecentMedia();
```

If you had changed the default image count in the module config, you may also need to call `setImageCount()` to retain this number:
```php
// Images to return had been set to 8 in the InstagramFeed config
$instagram = $modules->get('InstagramBasicDisplayApi');
$images = $instagram->setImageCount(8)->getRecentMedia();
```

If you were getting images by tag, this also should work, but as noted above this can require numerous calls to the API, so should be used only if necessary and thoroughly tested to ensure there is no major impact on response times etc:
```php
$instagram = $modules->get('InstagramBasicDisplayApi');
$images = $instagram->getRecentMediaByTag('tagname');
```

As the Basic Display API differs from the deprecated one, there will be differences in what data is returned, but for the most part this should only be some values returning as `null` where they had a value previously such as `user_has_liked` or `users_in_photo`.

You should test thoroughly before deploying the upgrade.

## Notes
* By default, 24 media items are returned in each API call. You can change this in the module config. Up to 10K items can be returned at once, but it is not recommended!
* For methods related to InstagramFeed, e.g. `getRecentMedia()`, 4 items are returned by default, as this is the default in this module.
* Examples shown above may not work on older ProcessWire versions.
