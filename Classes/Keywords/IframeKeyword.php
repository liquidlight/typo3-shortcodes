<?php

namespace LiquidLight\Shortcodes\Keywords;

class IframeKeyword extends AbstractKeyword
{
	protected $attributes = [
		'src',
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
		$src = $attributes['value'];
		unset($attributes['value']);

		if (!$src && isset($attributes['src']) && $attributes['src']) {
			$src = $attributes['src'];
			unset($attributes['src']);
		}

		$properties = [];
		foreach ($attributes as $key => $value) {
			$properties[] = sprintf('%s="%s"', $key, $value);
		}

		return sprintf(
			'<div class="shortcode iframe"><iframe src="%s" %s></iframe></div>',
			$src,
			implode(' ', $properties)
		);
	}
}
