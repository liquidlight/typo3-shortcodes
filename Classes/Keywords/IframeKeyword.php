<?php

namespace LiquidLight\Shortcodes\Keywords;

class IframeKeyword extends AbstractKeyword
{
	protected $attributes = [
		'allow',
		'allowfullscreen',
		'frameBorder',
		'height',
		'ratio',
		'src',
		'width',
	];

	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {

		$src = array_key_exists('value', $attributes) ? $attributes['value'] : '';
		unset($attributes['value']);

		if (!$src && isset($attributes['src']) && $attributes['src']) {
			$src = $attributes['src'];
			unset($attributes['src']);
		}

		$ratio = $this->getRatio($attributes);
		unset($attributes['ratio']);

		$properties = [];
		foreach ($attributes as $key => $value) {
			$properties[] = sprintf('%s="%s"', $key, $value);
		}

		return sprintf(
			'<div class="shortcode iframe" %s><iframe src="%s" title="%s" %s></iframe></div>',
			$ratio ? sprintf('data-ratio="%s"', $ratio) : '',
			$src,
			$this->getTitle($attributes),
			implode(' ', $properties)
		);
	}

	/**
	 * getRatio
	 *
	 * Get the ratio of a an iframe from a passed in width & height
	 *
	 * @param  array $attributes
	 * @return string
	 */
	protected function getRatio(array $attributes, $defaultRatio = null): string
	{

		// Return existing ratio if set - create a standard separated by colon
		if (isset($attributes['ratio'])) {
			return str_replace('/', ':', $attributes['ratio']);
		}

		// Return default if we don't have width and height
		if ($defaultRatio && (!isset($attributes['width']) || !isset($attributes['height']))) {
			return $defaultRatio;
		}

		// Strip any non-numeric characters (e.g. px, rem)
		$width = preg_replace('/[^0-9]/', '', $attributes['width'] ?? null);
		$height = preg_replace('/[^0-9]/', '', $attributes['height'] ?? null);

		// Return default if we don't have numeric widths & heights
		if (!(bool)$width || !(bool)$height) {
			return $defaultRatio ?? false;
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
