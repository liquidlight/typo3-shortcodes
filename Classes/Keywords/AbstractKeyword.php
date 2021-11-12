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

	public function __construct(Response $response, string $body)
	{
		$this->response = $response;
		$this->body = $body;
	}

	abstract public function processShortcode(
		string $value,
		array $attributes,
		string $match
	);
}