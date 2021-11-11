<?php

namespace LiquidLight\Shortcodes\Keywords;

class YoutubeShortcode
{
	public function processShortcode(
		$value,
		$full_string,
		$response
	) {
		return '<div class="video youtube"><iframe src="https://www.youtube-nocookie.com/embed/' . ($this->getYoutubeCode($value) ?: $value) . '?modestbranding=1" allowfullscreen></iframe></div>';
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
