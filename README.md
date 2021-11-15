# Shortcodes

Wordpress style shortcodes for embedding external content in TYPO3.

## Usage

Our of the box, the extension comes with plenty of shortcodes to get you started, including Youtube, Vimeo, Facebook, Twitter and plenty of others.

To use the shortcodes, you can use a shorthand syntax or follow the Wordpress, HTML inspired method.

More details below as to the exact usage for each tag, but as an example, the Youtube one could be used like one of the following:

** ⚠️ If using a full URL, it is worth leaving a space between the end of the link and the closing square bracket - this prevents the `]` being used in the link**

### Shorthand colon syntax

```
[youtube: https://www.youtube.com/watch?v=JrFFN9lag2w ]
```

### Shorthand equals syntax

```
[youtube = https://www.youtube.com/watch?v=JrFFN9lag2w ]
```

### Wordpress Long-form syntax

```
[youtube url="https://www.youtube.com/watch?v=JrFFN9lag2w" ]
```

### Existing Shortcodes

#### Facebook

`[facebook]`

Copy the URL of a facebook post or video

```
[facebook: https://www.facebook.com/20531316728/posts/10154009990506729/ ]
```

**Properties:**

- width
- height

**Defaults:**

```
[facebook: LINK, width = 500]
```

#### Iframe

`[iframe]`

Allows a generic iframe to be rendered with the passed in URL

```
[iframe: https://typo3.com/ ]
```

**Properties:**

- width
- height
- allowfullscreen
- allow
- frameBorder


#### Instagram

`[instagram]`

Embeds an Instagram post

```
[instagram: https://www.instagram.com/p/CWI-FeDs-us/ ]
```

#### LinkedIn

`[linkedin]`

Embeds an Linked post - ensure the url has `urn:li:activity` or similar in it.

For example:

```
[linkedin: https://www.linkedin.com/feed/update/urn:li:activity:6856570271759949825/ ]
```

**Properties:**

- height
- width

**Defaults:**

```
[linkedin: LINK, width = 100%, height = 600]
```

#### Spotify

`[spotify]`

Go to Spotify and click on what you want to embed - song, artist, playlist etc.

Click the 3 dots -> Share -> Copy Link

```
[spotify: https://open.spotify.com/track/3gdewACMIVMEWVbyb8O9sY?si=145df8aede6a4b04 ]
```

**Properties:**

- height - can be 80 or 380
- theme - can be 1 or 0 (disables the coloured background)

**Defaults:**

```
[spotify: LINK, height = 380, theme = 1]
```

#### Twitter

`[twitter | tweet]`

Copy the URL (or the status code) of a tweet

```
[twitter: https://twitter.com/Interior/status/463440424141459456 ]
```

**Properties:**

Any properties are passed through to the [oembed-api](https://developer.twitter.com/en/docs/twitter-for-websites/timelines/guides/oembed-api), so the list & defaults can be found there

```
[tweet: https://twitter.com/Interior/status/463440424141459456, theme=light]
```

#### Video

#### Vimeo

#### Youtube
