<?php

namespace LiquidLight\Shortcodes\Keywords;

class VimeoKeyword extends AbstractKeyword
{
	public function processShortcode(
		string $value,
		array $attributes,
		string $match
	) {
		preg_match('/vimeo\.com\/([0-9]{1,10})/', $value, $matches);

		return '<div class="video vimeo"><iframe src="https://player.vimeo.com/video/' . (count($matches) ? trim($matches[1]) : $value) . '" allowfullscreen></iframe></div>';
	}
}
