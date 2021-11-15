<?php

namespace LiquidLight\Shortcodes\Keywords;

class IframeKeyword extends AbstractKeyword
{
	protected $attributes = [
		'width',
		'height',
		'allowfullscreen',
		'allow',
		'frameBorder',
	];

	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {
		$value = $attributes['value'];
		unset($attributes['value']);

		$properties = [];
		foreach ($attributes as $key => $value) {
			$properties[] = sprintf('%s="%s"', $key, $value);
		}

		return sprintf(
			'<div class="shortcode iframe"><iframe src="%s" %s></iframe></div>',
			$value,
			implode(' ', $properties)
		);
	}
}
