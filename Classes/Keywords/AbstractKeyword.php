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
	 * title
	 *
	 * The title of the Keyword (used in HTML attiritbues)
	 */
	protected $title;

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
	private $globalAttributes = [
		'title',
	];

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

	protected function getTitle($attributes)
	{
		// Has a specific title been set?
		if (isset($attributes['title']) && (strlen($attributes['title']) > 0)) {
			return $attributes['title'];
		}

		// Has a global title been set
		if ($this->title) {
			return $this->title;
		}

		/**
		 * Construct a title from the classname
		 */

		// Convert the full classname into an array and reverse it
		$classNameArray = array_reverse(explode('\\', (string)get_class($this)) ?? []);

		// Take the first item (e.g. the name of the class)
		$className = $classNameArray[0] ?? '';

		// "explode" on capital letters (e.g. "AbstractKeyword" becomes ['Abstract', 'Keyword'])
		$classNameWords = preg_split('/(?=[A-Z])/', $className);

		// Filter out the word "Keywords" & empty values
		$classNameWords = array_filter($classNameWords ?? [], function ($value) {
			return !empty($value) && !in_array($value, ['Keyword']);
		});

		// Stitch back together with a space e.g. GoogleMapsKeyword => Google Maps
		return implode(' ', $classNameWords ?? []);
	}
}
