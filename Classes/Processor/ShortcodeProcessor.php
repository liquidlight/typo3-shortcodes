<?php

namespace LiquidLight\Shortcodes\Processor;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ShortcodeProcessor
{
	public $keywordConfig = [];

	public function __construct()
	{
		// Get our defined shortcodes
		$this->keywordConfigs = GeneralUtility::makeInstance(ExtensionConfiguration::class)
			->get('shortcodes', 'processShortcode')
		;
	}

	public function process(string $input, array $conf): string
	{
		$content = $input;

		// Find all the defined shortcodes in the page followed by a `:`, `=` or space
		preg_match_all(
			'/\[ ?((' . implode('|', array_keys($this->keywordConfigs)) . ') ?[:|= ] ?(.*?))\]/',
			$content,
			$pageShortcodes
		);

		if (
			// No registered keywords?
			!count($this->keywordConfigs) ||
			// No found shortcodes?
			!count($pageShortcodes)
		) {
			// Return the unmodified response
			return $input;
		}

		// Instantiate the classes we'll need for this page
		foreach (array_unique($pageShortcodes[2]) as $keyword) {
			$this->keywordConfigs[$keyword] = GeneralUtility::makeInstance(
				$this->keywordConfigs[$keyword],
				$input
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
			$this->keywordConfigs[$keyword]->removeAlienAttributes($attributes);

			// Fire method and get built HTML
			$result = $this->keywordConfigs[$keyword]->processShortcode(
				$keyword,
				$attributes,
				$match
			);

			// Did our config return something? Find and replace the origin match
			if ($result) {
				$content = str_replace($match, $result, $input);
			}
		}

		// Return the modified HTML
		return $content;
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
		$data = preg_replace('/' . $keyword . ' ?: ?/', $keyword . '=', $data);

		// Replace keyword = value with keyword=value
		$data = preg_replace('/' . $keyword . ' ?= ?/', $keyword . '=', $data);

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
			// Strip spaces and quotes (the addition of quotes is the only thing different to standard)
			$attributes[trim($attribute[0])] = trim($this->sanitiseData((string)$attribute[1]), " \n\r\t\v\0\"\'");
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
}
