<?php

namespace LiquidLight\Shortcodes\Keywords;

class FacebookKeyword extends AbstractKeyword
{
	protected $attributes = [
		'width',
		'height',
	];

	public function processShortcode(
		string $keyword,
		string $value,
		array $attributes,
		string $match
	) {
		if (strpos($value, '/videos/') !== false || $keyword === 'facebookvideo') {
			return sprintf(
				'<div class="shortcode video facebook"><iframe src="https://www.facebook.com/plugins/video.php?href=%s scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe></div>',
				$value
			);
		} else {
			return '<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script><div width="500" class="fb-post" data-href="' . $value . '"></div>';
			return sprintf(
				'<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script><div data-href="%s" %s class="fb-post"></div>',
				$value,
				(
					(isset($attributes['width']) ? 'width="' . $attributes['width'] . '" ' : 'width="500" ') .
					(isset($attributes['height']) ? 'height="' . $attributes['height'] . '" ' : '')
				)
			);
		}
	}
}
