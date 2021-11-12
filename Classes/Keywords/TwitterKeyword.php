<?php

namespace LiquidLight\Shortcodes\Keywords;

/**
 * TwitterKeyword
 *
 * Uses Embed API
 * https://developer.twitter.com/en/docs/twitter-for-websites/timelines/guides/oembed-api
 */
class TwitterKeyword extends VideoKeyword
{
	protected $attributes = [
		'theme',
		'widget_type',
		'limit',
		'maxwidth',
		'maxheight',
		'omit_script',
		'lang',
		'related',
		'border_color',
		'chrome',
		'aria_polite',
		'dnt',
	];

	public function processShortcode(
		string $value,
		array $attributes,
		string $match
	) {
		// Set dnt off by default
		$parameters = [
			'dnt' => true,
		];

		// Merge passed in parameters
		$parameters = array_merge($attributes, $parameters);

		// Checks if it is an ID or URL passed in
		if (strpos($value, 'twitter.com') === false) {
			$parameters['id'] = $value;
		} else {
			$parameters['url'] = $value;
		}

		// Get the tweet
		if ($tweet = file_get_contents('https://api.twitter.com/1.1/statuses/oembed.json?' . http_build_query($parameters))) {
			if ($tweet = @json_decode($tweet, true)) {
				return '<div class="shortcode twitter">' . $tweet['html'] . '</div>';
			}
		}
	}
}
