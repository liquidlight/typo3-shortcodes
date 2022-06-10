<?php

namespace LiquidLight\Shortcodes\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use LiquidLight\Shortcodes\Processor\ShortcodeProcessor;

class CleanupShortcodes implements MiddlewareInterface
{
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	): ResponseInterface {
		// Create a response
		$response = $handler->handle($request);

		// Get the page as a string
		$body = $response->getBody()->__toString();

		if (
			// Don't process if we're not HTML
			!$this->isHtml($response)
		) {
			// Return the unmodified response
			return $response;
		}

		/**
		 * Find all known shortcodes located in HTML attributes and remove them
		 *
		 * This prevents Shortcode HTML being rendered where it shouldn't be, e.g. in the head
		 */
		$processor = GeneralUtility::makeInstance(ShortcodeProcessor::class);
		$content = preg_replace(
			'(\[ ?(?>' . implode('|', array_keys($processor->keywordConfigs)) . ')[:|=|\s].*?\])/',
			'',
			$body
		);

		// Return the modified HTML
		return new HtmlResponse($body);
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
