<?php

namespace LiquidLight\Shortcodes\Keywords;

class SpotifyKeyword extends AbstractKeyword
{
	protected $attributes = [
		'theme', // Can be 0 or 1
		'height', // Should be 380 or 0
		'width',
	];

	public function processShortcode(
		string $keyword,
		string $value,
		array $attributes,
		string $match
	) {
		// Remove any extra get params
		$value = strtok($value, '?');

		// Make the URL an "embed" URL
		if(strpos($value, 'open.spotify.com/embed') === false) {
			$value = str_replace('open.spotify.com/', 'open.spotify.com/embed/', $value);
		}

		// Height can only be 380 or 80
		if(isset($attributes['height']) && !in_array((int)$attributes['height'], [80, 380])) {
			$attributes['height'] = 380;
		}

		return sprintf(
			'<div class="shortcode audio spotify"><iframe src="%s?%s" width="%s" height="%s" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe></div>',
			$value,
			(isset($attributes['theme']) && !(bool)$attributes['theme'] ? 'theme=0' : ''),
			($attributes['width'] ?? '100%'),
			($attributes['height'] ?? 380)
		);
	}
}
