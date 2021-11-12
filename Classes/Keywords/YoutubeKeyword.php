<?php

namespace LiquidLight\Shortcodes\Keywords;

class YoutubeKeyword extends VideoKeyword
{
	public function processShortcode(
		string $value,
		array $attributes,
		string $match
	) {
		return sprintf(
			'<div class="shortcode video youtube" data-ratio="%s"><iframe src="https://www.youtube-nocookie.com/embed/%s" %s allowfullscreen></iframe></div>',
			$this->getRatio($attributes),
			($this->getYoutubeCode($value) ?: $value),
			(
				(isset($attributes['width']) ? 'width="' . $attributes['width'] . '" ' : '') .
				(isset($attributes['height']) ? 'height="' . $attributes['height'] . '" ' : '')
			)
		);
	}

	/**
	 *  Check if input string is a valid YouTube URL
	 *  and try to extract the YouTube Video ID from it.
	 *  from https://stackoverflow.com/a/10527590
	 *  @author  Stephan Schmitz <eyecatchup@gmail.com>
	 *  @param   $url   string   The string that shall be checked.
	 *  @return  mixed           Returns YouTube Video ID, or (boolean) false.
	 */
	private function getYoutubeCode($url)
	{
		$pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
		preg_match($pattern, $url, $matches);
		return (isset($matches[1])) ? $matches[1] : false;
	}
}
