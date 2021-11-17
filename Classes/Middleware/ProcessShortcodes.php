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

		// Find all the defined shortcodes in the page
		preg_match_all(
			'/\[((' . implode('|', array_keys($keywordConfigs)) . ').*?)\]/',
			$body,
			$pageShortcodes
		);

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
		foreach (array_unique($pageShortcodes[2]) as $keyword) {
			$keywordConfigs[$keyword] = GeneralUtility::makeInstance(
				$keywordConfigs[$keyword],
				$response,
				$body
			);
		}

		// Loop through the keywords and process
		foreach ($pageShortcodes[2] as $index => $keyword) {
			// e.g. youtube: www.youtube.com/?v=123
			$match = $pageShortcodes[0][$index];

			$attributes = $this->extractData($keyword, $pageShortcodes[1][$index]);

			// Remove any attributes we don't know about
			$keywordConfigs[$keyword]->removeAlienAttributes($attributes);

			// Fire method and get built HTML
			$result = $keywordConfigs[$keyword]->processShortcode(
				$keyword,
				$attributes,
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

	/**
	 * extractData
	 *
	 * Convert a shortcode from a string into a key value array. If the keyword
	 * and a `:` or `=` is used (e.g. `[youtube=123]` )then 123 will returned as
	 * `value => 123`
	 *
	 * @param  string $keyword The keyword (e.g. youtube)
	 * @param  string $data The whole of the string
	 * @return array
	 */
	private function extractData(string $keyword, string $data): array
	{
		// Replace keyword:value with keyword=value
		$data = preg_replace('/' . $keyword . ' ?: ?/', $keyword . '=', $data);

		// Strip tags before we even begin processing
		$data = strip_tags($data);

		// Split on spaces that are not in quotes
		$properties = preg_split('/\s(?=([^"]*"[^"]*")*[^"]*$)/', $data);

		foreach ($properties as $property) {
			// key="value"
			$attribute = explode('=', $property, 2);

			// If it is not a ['key', 'value']
			if (count($attribute) !== 2) {
				continue;
			}

			// $attributes['key'] = 'value';
			if (trim($attribute[0]) === $keyword) {
				$attribute[0] = 'value';
			}
			$attributes[trim($attribute[0])] = $this->sanitiseData($attribute[1]);
		}

		return $attributes;
	}

	/**
	 * sanitiseData
	 *
	 * Remove all the unwanted gumpf that the WYSIWYG might add
	 *
	 * @param  string $value
	 * @return void
	 */
	protected function sanitiseData(string $value): string
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
