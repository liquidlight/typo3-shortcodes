<?php

namespace LiquidLight\Shortcodes\Keywords;

/**
 * SoundcloudKeyword
 *
 * Docs: https://developers.soundcloud.com/docs/oembed#introduction
 */
class SoundcloudKeyword extends AbstractKeyword
{
	protected $attributes = [
		'url',
		'maxwidth',
		'maxheight', // 166 or 450
		'color',
		'auto_play',
		'show_comments',
	];

	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {
		$parameters = array_merge($attributes, $parameters);
		$parameters = [
			'format' => 'json',
			'url' => isset($attributes['url']) && $attributes['url'] ? $attributes['url'] : $attributes['value'],
		];

		if ($track = file_get_contents('https://soundcloud.com/oembed?' . http_build_query($parameters))) {
			if ($track = @json_decode($track, true)) {
				return '<div class="shortcode audio soundcloud">' . $track['html'] . '</div>';
			}
		}
	}
}
