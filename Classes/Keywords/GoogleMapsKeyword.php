<?php

namespace LiquidLight\Shortcodes\Keywords;

use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * GoogleMapsKeyword
 *
 * https://developers.google.com/maps/documentation/embed/embedding-map
 *
 * Requires API key - see README
 */
class GoogleMapsKeyword extends AbstractKeyword
{
	protected $attributes = [
		'search',
		'map_mode',
		'center',
		'zoom',
		'maptype',
		'language',
		'region',
		'origin',
		'destination',
		'waypoints',
		'mode',
		'avoid',
		'units',
		'heading',
		'pitch',
		'fov',
		'width',
		'height',
	];

	protected $key;

	public function __construct(Response $response, string $body)
	{
		parent::__construct($response, $body);
		$config = GeneralUtility::makeInstance(ExtensionConfiguration::class)
			->get('shortcodes', 'config')
			;

		$this->key = $config['api']['googlemaps'] ?? false;
	}

	public function processShortcode(
		string $keyword,
		array $attributes,
		string $match
	) {
		if (!$this->key || !isset($attributes['search'])) {
			return;
		}

		$parameters = [
			'key' => $this->key,
		];

		$map_mode = 'place';
		if (isset($attributes['map_mode'])) {
			$map_mode = $attributes['map_mode'];
			unset($attributes['map_mode']);
		}

		if (isset($attributes['search'])) {
			$parameters['q'] = $attributes['search'];
			unset($attributes['search']);
		}

		// Merge passed in parameters
		$parameters = array_merge($attributes, $parameters);

		$url = sprintf('https://www.google.com/maps/embed/v1/%s?', $map_mode);

		return sprintf(
			'<div class="shortcodes map googlemap"><iframe src="%s" width="%s" height="%s" frameborder="0" allowfullscreen></iframe></div>',
			$url . http_build_query($parameters),
			($attributes['width'] ?? '100%'),
			($attributes['height'] ?? 450)
		);
	}
}
