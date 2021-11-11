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
		$response = $handler->handle($request);
		$body = $response->getBody()->__toString();

		// Get our defined shortcodes
		$keywordConfigs = GeneralUtility::makeInstance(ExtensionConfiguration::class)
			->get('shortcodes', 'register')
		;

		// Find all the shortcodes in the page
		preg_match_all('/\[([a-zA-Z0-9]*?):(.*?)\]/', $body, $pageShortcodes);

		// Check we have shortcodes & classes to handle them
		if (!count($keywordConfigs) || !count($pageShortcodes)) {
			return $response;
		}

		// Instantiate the classes we'll need for this page
		// (i.e. if their keyword was found)
		foreach ($keywordConfigs as $keyword => $_classRef) {
			if (in_array($keyword, $pageShortcodes[1])) {
				$keywordConfigs[$keyword] = GeneralUtility::makeInstance($_classRef);
			}
		}

		// Loop through the keywords and process
		foreach ($pageShortcodes[1] as $index => $keyword) {
			if (in_array($keyword, array_keys($keywordConfigs))) {
				// e.g. [youtube: www.youtube.com/?v=123]
				$full_string = $pageShortcodes[0][$index];

				// Extract value, e.g. www.youtube.com/?v=123
				$value = $pageShortcodes[2][$index];
				// Strip HTML Tags
				$value = strip_tags($value);
				// Clean up things like &amp;
				$value = html_entity_decode($value);
				// Strip out any url-encoded stuff
				$value = urldecode($value);
				// Remove all spaces
				$value = preg_replace('/\xc2\xa0/', ' ', $value);
				// // Trim the string of leading/trailing space
				$value = trim($value);

				$result = $keywordConfigs[$keyword]->processShortcode(
					$value,
					$full_string,
					$response
				);

				// Did our config return something? Find and replace the origin full_String
				if ($result) {
					$body = str_replace($full_string, $result, $body);
				}
			}
		}

		// Return the modified HTML
		return new HtmlResponse($body);
	}
}
