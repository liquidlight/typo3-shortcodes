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

		/**
		 * Find all known shortcodes located in HTML attributes and remove them
		 *
		 * This prevents Shortcode HTML being renderd where it shouldn't be, e.g. in the head
		 */
		$body = preg_replace(
			'/(="[^"]*?)(\[\s?(?>' . implode('|', array_keys($keywordConfigs)) . ')[:|=|\s].*?\])([^"]*?")/',
			"$1$3",
			$body
		);

		// Find all the defined shortcodes in the page followed by a `:`, `=` or space
		preg_match_all(
			'/\[\s?((' . implode('|', array_keys($keywordConfigs)) . ')\s?[:|= ]\s?(.*?))\]/',
			$body,
			$pageShortcodes
		);

		if (
			// No registered keywords?
			!count($keywordConfigs) ||
			// No found shortcodes?
			!count($pageShortcodes) ||
			// Don't process if we're not HTML
			!$this->isHtml($response)
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

			// If we have a link, replace the value with the href
			preg_match('/<a.*?href="([^"].*?)".*?<\/a>/', $pageShortcodes[1][$index], $linkHref);
			if (count($linkHref)) {
				$pageShortcodes[1][$index] = str_replace($linkHref[0], $linkHref[1], $pageShortcodes[1][$index]);
			}

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
		// Get rid of non-brekaing spaces in the RTE
		$data = preg_replace('/&nbsp;/', ' ', $data);

		// Replace keyword:value with keyword=value
		$data = preg_replace('/' . $keyword . '\s?:\s?/', $keyword . '=', $data);

		// Replace keyword = value with keyword=value
		$data = preg_replace('/' . $keyword . '\s?=\s?/', $keyword . '=', $data);

		// Strip tags before we even begin processing
		$data = $this->sanitiseData($data);

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
			// Strip spces and quotes (the addition of quotes is the only thing different to standard)
			$attributes[trim($attribute[0])] = trim($this->sanitiseData($attribute[1]), " \n\r\t\v\0\"\'");
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

		return $value;
	}

	/**
	 * isHtml
	 *
	 * Determine if the current page is HTML
	 *
	 * @param  ResponseInterface $response
	 * @return bool
	 */
	protected function isHtml(ResponseInterface $response): bool
	{
		$headers = $response->getHeaders();

		// Do we have a content type header?
		if (!isset($headers['Content-Type'])) {
			return false;
		}

		// Does that content type header contain text/html?
		if (strpos(strtolower($headers['Content-Type'][0]), 'text/html') > -1) {
			return true;
		}

		return false;
	}
}
