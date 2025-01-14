<?php

namespace LiquidLight\Shortcodes\Keywords;

class VideoKeyword extends IframeKeyword
{
	protected $attributes = [
		'src',
		'code',
		'height',
		'loading',
		'ratio',
		'url',
		'width',
	];

	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {
		$shortcode = parent::processShortcode($keyword, $attributes, $match);

		return str_replace('class="shortcode iframe"', 'class="shortcode video"', $shortcode);
	}

	protected function getRatio(array $attributes, $defaultRatio = null): string
	{
		return parent::getRatio($attributes, '16:9');
	}
}
