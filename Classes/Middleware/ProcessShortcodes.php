<?php

namespace LiquidLight\Shortcodes\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ProcessShortcodes implements MiddlewareInterface
{
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	): ResponseInterface {
		// Create a response
		$response = $handler->handle($request);

		// Get the page as a string
		$body = $response->getBody()->__toString();

		// Get our defined shortcodes
		$keywordConfigs = GeneralUtility::makeInstance(ExtensionConfiguration::class)
			->get('shortcodes', 'processShortcode')
			;

		$keywords = implode('|', array_keys($keywordConfigs));

		// Find all the shortcodes in the page
		preg_match_all('/\[(' . $keywords . ')(.*?)\]/', $body, $pageShortcodes);

		if (
			// No registered keywords?
			!count($keywordConfigs) ||
			// No found shortcodes?
			!count($pageShortcodes)
		) {
			// Return the unmodified response
			return $response;
		}
		// Instantiate the classes we'll need for this page
		foreach (array_unique($pageShortcodes[1]) as $keyword) {
			$keywordConfigs[$keyword] = GeneralUtility::makeInstance(
				$keywordConfigs[$keyword],
				$response,
				$body
			);
		}

		// Loop through the keywords and process
		foreach ($pageShortcodes[1] as $index => $keyword) {
			// e.g. [youtube: www.youtube.com/?v=123]
			$match = $pageShortcodes[0][$index];

			$data = $this->extractData($pageShortcodes[2][$index]);
			debug($data);

			// Remove any attributes we don't know about
			$keywordConfigs[$keyword]->sanitiseAttributes($data['attributes']);

			// Fire method and get built HTML
			$result = $keywordConfigs[$keyword]->processShortcode(
				$keyword,
				$data['value'],
				$data['attributes'],
				$match
			);

			// Did our config return something? Find and replace the origin match
			if ($result) {
				$body = str_replace($match, $result, $body);
			}
		}

		// Return the modified HTML
		return new HtmlResponse($body);
	}

	private function extractData(string $input): array
	{
		$input = trim($input);

		switch (substr($input, 0, 1)) {
			case ':':
				$properties = explode(',', ltrim($input, ':'));
				$value = $this->sanitiseData(trim(array_shift($properties)));
				break;
			case '=':
				$properties = explode(' ', ltrim($input, '='));
				$value = $this->sanitiseData(trim(array_shift($properties)));
				break;
			default:
				$properties = explode(' ', $input);
				break;
		}

		$attributes = [];
		foreach ($properties as $property) {
			// key="value"
			$attribute = explode('=', $property);

			// If it is not a ['key', 'value']
			if (count($attribute) !== 2) {
				continue;
			}

			// $attributes['key'] = 'value';
			$attributes[trim($attribute[0])] = $this->sanitiseData($attribute[1]);
		}

		return [
			'value' => $value ?: '',
			'attributes' => $attributes,
		];
	}

	protected function sanitiseData($value)
	{
		// Strip HTML Tags
		$value = strip_tags($value);
		// Clean up things like &amp;
		$value = html_entity_decode($value);
		// Replace space-like characters with a space
		$value = preg_replace('/\xc2\xa0/', ' ', $value);
		// Strip spces and quotes
		$value = trim($value, " \n\r\t\v\0\"\'");

		return $value;
	}
}
