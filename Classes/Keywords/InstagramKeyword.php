<?php

namespace LiquidLight\Shortcodes\Keywords;

class InstagramKeyword extends AbstractKeyword
{
	protected $attributes = [
		'url',
		'code'
	];

	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {
		$parameters['url'] = isset($attributes['code']) ? ('https://www.instagram.com/p/' . $attributes['code']) :
			($attributes['url'] ?: $attributes['value']);

		if ($post = file_get_contents('https://api.instagram.com/oembed/?' . http_build_query($parameters))) {
			if ($post = @json_decode($post, true)) {
				return '<div class="shortcode photo instagram">' . $post['html'] . '</div>';
			}
		}
	}
}
