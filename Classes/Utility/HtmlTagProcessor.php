<?php

namespace LiquidLight\Shortcodes\Utility;

/**
 * Processes HTML tags to maintain valid structure when splitting content
 */
class HtmlTagProcessor
{
	/**
	 * Inline HTML tags that can wrap shortcodes and need to be balanced
	 */
	private const INLINE_TAGS = [
		'a', 'strong', 'em', 'b', 'i', 'span', 'u', 'small',
		'mark', 'code', 'kbd', 'abbr', 'cite',
	];

	/**
	 * Remove invalid wrapper tags around shortcode divs
	 *
	 * When shortcodes are wrapped in elements that only accept phrasing content,
	 * this extracts the shortcode and restructures the surrounding content.
	 */
	public function removeInvalidShortcodeWrappers(string $body): string
	{
		// Process each tag type separately to avoid cross-tag matching
		$tags = ['pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'];

		foreach ($tags as $tag) {
			// Pattern that won't match across multiple instances of the same tag
			// Using [^<]*  instead of .*? for more controlled matching
			$pattern = "/<($tag)([^>]*)>((?:(?!<\\/?$tag).)*?)(<div\s+class=\"shortcode[^\"]*\"[^>]*>.*?<\\/div>)((?:(?!<\\/?$tag).)*?)<\\/\\1>/is";

			$result = preg_replace_callback(
				$pattern,
				fn ($matches) => $this->restructureElement($matches),
				$body
			);

			if ($result !== null) {
				$body = $result;
			}
		}

		return $body;
	}

	/**
	 * Restructure element by extracting shortcode and balancing inline tags
	 */
	private function restructureElement(array $matches): string
	{
		[, $tagName, $tagAttributes, $textBefore, $shortcode, $textAfter] = $matches;

		$unclosedTags = $this->findUnclosedTags($textBefore);

		// Close unclosed tags before shortcode
		$textBefore .= $this->generateClosingTags($unclosedTags);

		// Reopen tags after shortcode
		$textAfter = $this->generateOpeningTags($unclosedTags) . $textAfter;

		// Clean whitespace and orphaned tags
		$textBefore = $this->cleanContent($textBefore);
		$textAfter = $this->cleanContent($textAfter);

		// Build restructured HTML
		$parts = [];

		if ($textBefore !== '') {
			$parts[] = "<{$tagName}{$tagAttributes}>{$textBefore}</{$tagName}>";
		}

		$parts[] = $shortcode;

		if ($textAfter !== '') {
			$parts[] = "<{$tagName}{$tagAttributes}>{$textAfter}</{$tagName}>";
		}

		return implode('', $parts);
	}

	/**
	 * Find inline tags that are opened but not closed
	 */
	private function findUnclosedTags(string $text): array
	{
		$stack = [];
		preg_match_all('/<\/?([a-z][a-z0-9]*)\b([^>]*)>/i', $text, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			$tagName = strtolower($match[1]);

			if (!in_array($tagName, self::INLINE_TAGS)) {
				continue;
			}

			// Skip self-closing tags (e.g., <br />, <img />)
			if (str_ends_with(rtrim($match[0]), '/>')) {
				continue;
			}

			if (str_starts_with($match[0], '</')) {
				// Closing tag - pop from stack if matches
				if (!empty($stack) && end($stack)['tag'] === $tagName) {
					array_pop($stack);
				}
			} else {
				// Opening tag - push to stack
				$stack[] = [
					'tag' => $tagName,
					'attributes' => $match[2] ?? '',
				];
			}
		}

		return $stack;
	}

	/**
	 * Generate closing tags for unclosed elements
	 */
	private function generateClosingTags(array $tags): string
	{
		return implode('', array_map(
			fn ($tag) => "</{$tag['tag']}>",
			array_reverse($tags)
		));
	}

	/**
	 * Generate opening tags to reopen previously closed elements
	 */
	private function generateOpeningTags(array $tags): string
	{
		return implode('', array_map(
			fn ($tag) => "<{$tag['tag']}{$tag['attributes']}>",
			$tags
		));
	}

	/**
	 * Clean whitespace and orphaned HTML tags
	 */
	private function cleanContent(string $text): string
	{
		$text = trim($text);

		// Remove if only whitespace/nbsp
		$text = preg_replace('/^(\s|&nbsp;)+$/i', '', $text) ?? $text;

		// Remove orphaned closing tags
		$text = preg_replace('/^(<\/[a-z0-9]+>\s*)+$/i', '', $text) ?? $text;

		// Remove orphaned opening tags
		if (preg_match('/^<[a-z0-9]+[^>]*>$/i', $text)) {
			return '';
		}

		// Remove empty inline tags
		$text = preg_replace('/<([a-z][a-z0-9]*)\b[^>]*><\/\1>/', '', $text) ?? $text;
		$text = trim($text);

		// Final whitespace check
		$finalCheck = preg_replace('/^(\s|&nbsp;)+$/i', '', $text);
		if ($finalCheck !== null && $finalCheck === '') {
			return '';
		}

		return $text;
	}
}
