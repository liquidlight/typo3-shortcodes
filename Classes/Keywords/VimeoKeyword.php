<?php

namespace LiquidLight\Shortcodes\Keywords;

class VimeoKeyword extends VideoKeyword
{
	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {
		$value = isset($attributes['code']) && $attributes['code'] ?
			$attributes['code'] : (
				isset($attributes['url']) && $attributes['url'] ?
					$attributes['url'] : (
						isset($attributes['value']) && $attributes['value'] ?
							$attributes['value'] :
							false
					)
			);

		/**
		 * Vimeo URLs come in many different fashions - see link below for regex in action
		 *
		 * https://regex101.com/r/5zMM1k/1
		 */
		preg_match('/vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|)(\d+)(?:|\/\?)\/?([\d|\w]*)\/?/', $value, $matches);


		$code = $value;
		if (count($matches)) {
			// If it is unlisted, then append the hash
			$code = trim($matches[2]) . (isset($matches[3]) && !is_null($matches[3]) ? '?h=' . trim($matches[3]) : '');
		}

		return sprintf(
			'<div class="shortcode video vimeo" data-ratio="%s"><iframe src="https://player.vimeo.com/video/%s" title="%s" %s allowfullscreen></iframe></div>',
			$this->getRatio($attributes),
			$code,
			$this->getTitle($attributes),
			(
				(isset($attributes['width']) ? 'width="' . $attributes['width'] . '" ' : '') .
				(isset($attributes['height']) ? 'height="' . $attributes['height'] . '" ' : '') .
				(isset($attributes['loading']) ? 'loading="' . $attributes['loading'] . '" ' : 'loading="lazy" ')
			)
		);
	}
}
