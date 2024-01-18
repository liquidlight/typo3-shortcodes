<?php

namespace LiquidLight\Shortcodes\Keywords;

use TYPO3\CMS\Core\Http\Response;

abstract class AbstractKeyword
{
	/**
	 * response
	 *
	 * The middleware reposnse object
	 */
	protected $response;

	/**
	 * body
	 *
	 * The page response as a string
	 *
	 * @var string
	 */
	protected $body;

	/**
	 * attributes
	 *
	 * A list of allowed attributes - everything else gets removed
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * globalAttributes
	 *
	 * A list of attributes allowed on all shortcodes
	 *
	 * @var array
	 */
	private $globalAttributes = [];

	public function __construct(Response $response, string $body)
	{
		$this->response = $response;
		$this->body = $body;
	}

	abstract public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	);

	/**
	 * removeAlienAttributes
	 *
	 * Remove any attributes which have been passed in which shouldn't have.
	 *
	 * @param mixed $attributes
	 *
	 * @return void
	 */
	public function removeAlienAttributes(&$attributes): void
	{
		$allowed = array_merge($this->globalAttributes, $this->attributes);

		foreach ($attributes as $key => $value) {
			if (!in_array($key, $allowed) && $key !== 'value') {
				unset($attributes[$key]);
			}
		}
	}
}
