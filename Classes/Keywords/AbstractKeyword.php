<?php

namespace LiquidLight\Shortcodes\Keywords;

abstract class AbstractKeyword
{
	/**
	 * content
	 *
	 * The page response as a string
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * attributes
	 *
	 * A list of allowed attributes - everything else gets removed
	 *
	 * @var array
	 */
	protected $attributes = [];

	public function __construct(string $content)
	{
		$this->content = $content;
	}

	abstract public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	);

	public function removeAlienAttributes(&$attributes): void
	{
		foreach ($attributes as $key => $value) {
			if (!in_array($key, $this->attributes) && $key !== 'value') {
				unset($attributes[$key]);
			}
		}
	}
}
