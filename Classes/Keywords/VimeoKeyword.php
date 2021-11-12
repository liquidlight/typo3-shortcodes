<?php

namespace LiquidLight\Shortcodes\Keywords;

class VimeoKeyword extends VideoKeyword
{
	public function processShortcode(
		string $keyword,
		string $value,
		array $attributes,
		string $match
	) {
		preg_match('/vimeo\.com\/([0-9]{1,10})/', $value, $matches);

		return sprintf(
			'<div class="shortcode video vimeo" data-ratio="%s"><iframe src="https://player.vimeo.com/video/%s" %s allowfullscreen></iframe></div>',
			$this->getRatio($attributes),
			(count($matches) ? trim($matches[1]) : $value),
			(
				(isset($attributes['width']) ? 'width="' . $attributes['width'] . '" ' : '') .
				(isset($attributes['height']) ? 'height="' . $attributes['height'] . '" ' : '')
			)
		);
	}
}
