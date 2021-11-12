<?php

namespace LiquidLight\Shortcodes\Keywords;

class InstagramKeyword extends AbstractKeyword
{
	protected $attributes = [
	];

	public function processShortcode(
		string $keyword,
		string $value,
		array $attributes,
		string $match
	) {
		return '<div class="shortcode photo instagram"><blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="' . $value . '" data-instgrm-version="14"></blockquote><script async src="//www.instagram.com/embed.js"></script></div>';
	}
}
