# Shortcodes

Wordpress style shortcodes for embedding external content in TYPO3.

## Usage

Our of the box, the extension comes with plenty of shortcodes to get you started, including Youtube, Vimeo, Facebook, Twitter and plenty of others.

To use the shortcodes, you can use a shorthand syntax or follow the Wordpress, HTML inspired method.

More details below as to the exact usage for each tag, but as an example, the Youtube one could be used like one of the following:

**⚠️ If using a full URL, it is worth leaving a space between the end of the link and the closing square bracket - this prevents the `]` being used in the link**

### Shorthand colon syntax

```
[youtube: https://www.youtube.com/watch?v=JrFFN9lag2w ]
```

### Shorthand equals syntax

```
[youtube=https://www.youtube.com/watch?v=JrFFN9lag2w ]
```

### Wordpress Long-form syntax

```
[youtube url="https://www.youtube.com/watch?v=JrFFN9lag2w" ]
```

## Creating your own keyword

In the land of shortcodes, keyword is the "service" used to trigger the shortcode (e.g. `youtube` in the examples above).

Registering your own keyword requires a new class which extends `LiquidLight\Shortcodes\Keywords\AbstractKeyword`.

An example of how to extend and use can be found in the Classes/Keywords folder - anything but the `AbstractKeyword` class can be copied and used.

Once created, you can add it via the `ext_localconf.php` where `new` is the name of the keyword.

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['shortcodes']['processShortcode']['new'] =
		\Vendor\Ext\Keywords\NewKeyword::class;
```

Using this method allows you to overwrite existing keywords if you wish to alter their output. The only requirement is that you return a string.

## Existing Shortcodes

### Facebook

`[facebook]`

Copy the URL of a facebook post or video

```
[facebook=https://www.facebook.com/20531316728/posts/10154009990506729/ ]
```

**Properties:**

- url
- width
- height

**Defaults:**

```
[facebook=LINK width="500"]
```

### Iframe

`[iframe]`

Allows a generic iframe to be rendered with the passed in URL

```
[iframe: https://typo3.com/ ]
```

**Properties:**

- url
- width
- height
- allowfullscreen
- allow
- frameBorder


### Instagram

`[instagram]`

Embeds an Instagram post

```
[instagram=https://www.instagram.com/p/CWI-FeDs-us/ ]
```

### LinkedIn

`[linkedin]`

Embeds an Linked post - ensure the url has `urn:li:activity` or similar in it.

For example:

```
[linkedin=https://www.linkedin.com/feed/update/urn:li:activity:6856570271759949825/ ]
```

**Properties:**

- height
- width

**Defaults:**

```
[linkedin=LINK width="100%" height="600"]
```

### Soundcloud

`[soundcloud]`

Shows a Soundcloud player for a track or artists - can have the size & colour customised. Use the URL

For example:

```
[soundcloud=https://soundcloud.com/cbschmidt/seodriven-331 ]
```

**Properties:**

- url
- maxwidth
- maxheight - can be 166 or 450
- color
- auto_play
- show_comments

### Spotify

`[spotify]`

Go to Spotify and click on what you want to embed - song, artist, playlist etc.

Click the 3 dots -> Share -> Copy Link

```
[spotify=https://open.spotify.com/track/3gdewACMIVMEWVbyb8O9sY?si=145df8aede6a4b04 ]
```

**Properties:**

- height - can be 80 or 380
- theme - can be 1 or 0 (disables the coloured background)

**Defaults:**

```
[spotify=LINK height="380" theme="1"]
```

### Twitter

`[twitter | tweet]`

Copy the URL (or the status code) of a tweet

```
[twitter=https://twitter.com/Interior/status/463440424141459456 ]
```

**Properties:**

Any properties are passed through to the [oembed-api](https://developer.twitter.com/en/docs/twitter-for-websites/timelines/guides/oembed-api), so the list & defaults can be found there

```
[tweet=https://twitter.com/Interior/status/463440424141459456 theme="light"]
```

### Video

`[video]`

The video element outputs an `<iframe>` with the src of that passed in. It allows an arbitrary URL to a video to be passed in and it will wrap it in a `<div>` for responsive styling purposes.

```
[video=https://www.liquidlight.co.uk/path/to/video ]
```

**Properties**

- code - optional if using the shorthand syntax
- url - optional if using the shorthand syntax
- width
- height
- ratio - video ratio in a colon format (e.g. `ratio="4:3"`) - defaults to `16:9` - rendered as a `data-ratio` attribute for styling

### Vimeo

`[vimeo]`

Renders a Vimeo iframe embed. Can take a full URL or code.

```
[vimeo=]
```

See [Video](#video) for properties and use.

### Youtube

`[youtube]`

Renders a Youtube iframe embed. Can take a full URL or code.

```
[youtube=https://www.youtube.com/watch?v=JrFFN9lag2w ]
```

See [Video](#video) for properties and use.
