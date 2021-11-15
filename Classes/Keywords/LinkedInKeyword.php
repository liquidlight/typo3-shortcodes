<?php

namespace LiquidLight\Shortcodes\Keywords;

class LinkedInKeyword extends AbstractKeyword
{
	protected $attributes = [
		'width',
		'height',
	];

	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {
		$value = $attributes['value'] ?: false;

		if (strripos($value, 'linkedin.com') !== false) {
			$url = explode('/', trim($value, '/'));
			$value = end($url);
		}

		return sprintf(
			'<div class="shortcode linkedin post"><iframe src="https://www.linkedin.com/embed/feed/update/%s" width="%s" height="%s" frameborder="0" allowfullscreen="" title="Embedded post"></iframe></div>',
			$value,
			($attributes['width'] ?? '100%'),
			($attributes['height'] ?? 600)
		);
	}
}
