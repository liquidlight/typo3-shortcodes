<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {
	$shortcodesExtConf = &$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['shortcodes'];

	if (!is_array($shortcodesExtConf['processShortcode'])) {
		$shortcodesExtConf['processShortcode'] = [];
	}

	$shortcodesExtConf['config']['api']['googlemaps'] = $_ENV['GOOGLE_MAP_API'] ?: false;

	$shortcodesExtConf = array_merge([
		'facebook' => \LiquidLight\Shortcodes\Keywords\FacebookKeyword::class,
		'facebookvideo' => \LiquidLight\Shortcodes\Keywords\FacebookKeyword::class,
		'googlemaps' => \LiquidLight\Shortcodes\Keywords\GoogleMapsKeyword::class,
		'iframe' => \LiquidLight\Shortcodes\Keywords\IframeKeyword::class,
		'instagram' => \LiquidLight\Shortcodes\Keywords\InstagramKeyword::class,
		'linkedin' => \LiquidLight\Shortcodes\Keywords\LinkedInKeyword::class,
		'soundcloud' => \LiquidLight\Shortcodes\Keywords\SoundcloudKeyword::class,
		'spotify' => \LiquidLight\Shortcodes\Keywords\SpotifyKeyword::class,
		'tweet' => \LiquidLight\Shortcodes\Keywords\TwitterKeyword::class,
		'twitter' => \LiquidLight\Shortcodes\Keywords\TwitterKeyword::class,
		'video' => \LiquidLight\Shortcodes\Keywords\VideoKeyword::class,
		'vimeo' => \LiquidLight\Shortcodes\Keywords\VimeoKeyword::class,
		'youtube' => \LiquidLight\Shortcodes\Keywords\YoutubeKeyword::class,
	], $shortcodesExtConf);
});
