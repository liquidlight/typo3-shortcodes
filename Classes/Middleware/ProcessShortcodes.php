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
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// Create a response
		$response = $handler->handle($request);

		// Get the page as a string
		$body = $response->getBody()->__toString();

		// Get our defined shortcodes
		$keywordConfigs = GeneralUtility::makeInstance(ExtensionConfiguration::class)
			->get('shortcodes', 'processShortcode')
			;

		// Find all the shortcodes in the page
		preg_match_all('/\[([a-zA-Z0-9]*?):(.*?)\]/', $body, $pageShortcodes);

		// Lowercase all the found keywords to match registered keywords
		$pageShortcodes[1] = array_map('strtolower', $pageShortcodes[1]);

		if (
			// No registred keywords?
			!count($keywordConfigs) ||
			// No found shortcodes?
			!count($pageShortcodes) ||
			// None of the found shortcodes match registred keywords?
			!count(array_intersect(array_keys($keywordConfigs), $pageShortcodes[1]))
		) {
			// Return the unmodified response
			return $response;
		}

		// Instantiate the classes we'll need for this page
		foreach ($keywordConfigs as $keyword => $_classRef) {
			if (in_array($keyword, $pageShortcodes[1])) {
				$keywordConfigs[$keyword] = GeneralUtility::makeInstance(
					$_classRef,
					$response,
					$body
				);
			}
		}

		// Loop through the keywords and process
		foreach ($pageShortcodes[1] as $index => $keyword) {
			if (in_array($keyword, array_keys($keywordConfigs))) {
				// e.g. [youtube: www.youtube.com/?v=123]
				$match = $pageShortcodes[0][$index];

				$data = $this->extractData($pageShortcodes[2][$index]);

				// Remove any attributes we don't know about
				$keywordConfigs[$keyword]->sanitiseAttributes($data['attributes']);

				// Fire method and get built HTML
				$result = $keywordConfigs[$keyword]->processShortcode(
					$data['value'],
					$data['attributes'],
					$match
				);

				// Did our config return something? Find and replace the origin match
				if ($result) {
					$body = str_replace($match, $result, $body);
				}
			}
		}

		// Return the modified HTML
		return new HtmlResponse($body);
	}

	private function extractData(string $input): array
	{
		// Anything after the colon
		$value = $input;
		// Strip HTML Tags
		$value = strip_tags($value);
		// Clean up things like &amp;
		$value = html_entity_decode($value);
		// Replace space-like characters with a space
		$value = preg_replace('/\xc2\xa0/', ' ', $value);

		// Create array from any extra properties passed in
		$properties = explode(',', $value);

		// Store the first item - this is the URL or code
		$value = trim(array_shift($properties));

		$attributes = [];
		foreach ($properties as $property) {
			$attribute = explode('=', $property);

			if (count($attribute) !== 2) {
				continue;
			}

			$attributes[trim($attribute[0])] = trim($attribute[1]);
		}

		return [
			'value' => $value,
			'attributes' => $attributes,
		];
	}
}
