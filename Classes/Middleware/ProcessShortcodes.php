<?php

namespace LiquidLight\Shortcodes\Middleware;

use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Http\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ProcessShortcodes implements MiddlewareInterface
{
	protected $keywordConfigs;

	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	): ResponseInterface {
		// Create a response
		$response = $handler->handle($request);

		// Get the page as a string
		$body = $response->getBody()->__toString();

		// Get our defined shortcodes
		$this->keywordConfigs = GeneralUtility::makeInstance(ExtensionConfiguration::class)
			->get('shortcodes', 'processShortcode') ?? []
		;

		// If there are no configs, don't continue
		if (!count($this->keywordConfigs)) {
			return $response;
		}

		$body = $this->removeUndesiredShortcodes($body);

		$pageShortcodes = $this->findShortcodes($body);

		// If we have shortcodes & are a HTML page
		if (count($pageShortcodes) && $this->isHtml($response)) {
			$this->instantiateRequiredShortcodes($pageShortcodes);
			$body = $this->hydrateShortcodes($pageShortcodes, $body);
			$page = new Stream('php://temp', 'rw');
			$page->write($body);
			$response = $response->withBody($page);
		}

		// Return the modified HTML
		return $response;
	}

	/**
	 * Remove all unwanted shortcodes from the HTML
	 */
	protected function removeUndesiredShortcodes(string $body): string
	{
		/**
		 * Find all known shortcodes located in HTML attributes and remove them
		 *
		 * This prevents Shortcode HTML being rendered where it shouldn't be, e.g. in the head
		 */
		$this->removeShortcodes(
			$body,
			'/(="[^"]*?)(\[\s?(?>' . implode('|', array_keys($this->keywordConfigs)) . ')[:|=|\s|].*?\])([^"]*?")/'
		);

		/**
		 * Remove all known shortcodes from between key/valued quotes - e.g. in JSON Schema
		 */
		$this->removeShortcodes(
			$body,
			'/("[^"]*"\s*:\s*"[^"]*)(\[\s?(?>' . implode('|', array_keys($this->keywordConfigs)) . ')[:|=|\s].*?\])([^"]*")/'
		);

		return $body;
	}

	/**
	 * Find all the registered shortcodes in the page
	 */
	protected function findShortcodes($body): array
	{
		// Find all the defined shortcodes in the page followed by a `:`, `=` or space
		preg_match_all(
			'/\[\s?((' . implode('|', array_keys($this->keywordConfigs)) . ')\s?[:|= ]\s?(.*?))\]/',
			$body,
			$pageShortcodes
		);

		return $pageShortcodes ?? [];
	}

	/**
	 * Instantiate all the shortcodes used on the page
	 */
	protected function instantiateRequiredShortcodes(array $pageShortcodes): void
	{
		// Instantiate the classes we'll need for this page
		foreach (array_unique($pageShortcodes[2]) as $keyword) {
			$this->keywordConfigs[$keyword] = GeneralUtility::makeInstance($this->keywordConfigs[$keyword]);
		}
	}

	/**
	 * Replace the shortcodes with their actual HTML
	 */
	protected function hydrateShortcodes($pageShortcodes, $body): string
	{
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
			$this->keywordConfigs[$keyword]->removeAlienAttributes($attributes);

			// Fire method and get built HTML
			$result = $this->keywordConfigs[$keyword]->processShortcode(
				$keyword,
				$attributes,
				$match
			);

			// Did our config return something? Find and replace the origin match
			if ($result) {
				$body = str_replace($match, $result, $body);
			}
		}

		return $body;
	}

	/**
	 * extractData
	 *
	 * Convert a shortcode from a string into a key value array. If the keyword
	 * and a `:` or `=` is used (e.g. `[youtube=123]` )then 123 will returned as
	 * `value => 123`
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

	/**
	 * While the regex finds entries in undesired places, remove them
	 */
	protected function removeShortcodes(string &$contents, string $regex): void
	{
		while (preg_match($regex, $contents, $matches)) {
			$contents = preg_replace(
				$regex,
				"$1$3",
				$contents,
			);
		}
	}
}
