<?php

namespace LiquidLight\Shortcodes\Keywords;

class VideoKeyword extends AbstractKeyword
{
	protected $attributes = [
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
		return sprintf(
			'<div class="shortcode video" data-ratio="%s"><iframe src="%s" %s allowfullscreen></iframe></div>',
			$this->getRatio($attributes),
			$attributes['value'],
			(
				(isset($attributes['width']) ? 'width="' . $attributes['width'] . '" ' : '') .
				(isset($attributes['height']) ? 'height="' . $attributes['height'] . '" ' : '') .
				(isset($attributes['loading']) ? 'loading="' . $attributes['loading'] . '" ' : 'loading="lazy" ')
			)
		);
	}

	/**
	 * getRatio
	 *
	 * Get the ratio of a video from a passed in width & height
	 *
	 * @param  array $attributes
	 * @return string
	 */
	protected function getRatio(array $attributes): string
	{
		// Set a default ratio
		$ratio = '16:9';

		// Return existing ratio if set - create a standard separated by colon
		if (isset($attributes['ratio'])) {
			return str_replace('/', ':', $attributes['ratio']);
		}

		// Return default if we don't have width and height
		if (!isset($attributes['width']) || !isset($attributes['height'])) {
			return $ratio;
		}

		// Strip any non-numeric characters (e.g. px, rem)
		$width = preg_replace('/[^0-9]/', '', $attributes['width']);
		$height = preg_replace('/[^0-9]/', '', $attributes['height']);

		// Return default if we don't have numeric widths & heights
		if (!(bool)$width || !(bool)$height) {
			return $ratio;
		}

		// Calculate ratio
		for ($i = $height; $i > 1; $i--) {
			if (($width % $i) == 0 && ($height % $i) == 0) {
				$width = $width / $i;
				$height = $height / $i;
			}
		}

		return sprintf('%s:%s', $width, $height);
	}
}
