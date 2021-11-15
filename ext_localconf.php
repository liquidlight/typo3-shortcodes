<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {
	$shortcodesExtConf = &$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['shortcodes'];

	if (!is_array($shortcodesExtConf['processShortcode'])) {
		$shortcodesExtConf['processShortcode'] = [];
	}

	$shortcodesExtConf['config']['api']['googlemaps'] = $_ENV['GOOGLE_MAP_API'] ?: false;

	$shortcodesExtConf['processShortcode']['googlemaps'] =
		\LiquidLight\Shortcodes\Keywords\GoogleMapsKeyword::class;

	$shortcodesExtConf['processShortcode']['video'] =
		\LiquidLight\Shortcodes\Keywords\VideoKeyword::class;
	$shortcodesExtConf['processShortcode']['youtube'] =
		\LiquidLight\Shortcodes\Keywords\YoutubeKeyword::class;
	$shortcodesExtConf['processShortcode']['vimeo'] =
		\LiquidLight\Shortcodes\Keywords\VimeoKeyword::class;

	$shortcodesExtConf['processShortcode']['twitter'] =
		\LiquidLight\Shortcodes\Keywords\TwitterKeyword::class;
	$shortcodesExtConf['processShortcode']['tweet'] =
		\LiquidLight\Shortcodes\Keywords\TwitterKeyword::class;

	$shortcodesExtConf['processShortcode']['spotify'] =
		\LiquidLight\Shortcodes\Keywords\SpotifyKeyword::class;

	$shortcodesExtConf['processShortcode']['facebook'] =
		\LiquidLight\Shortcodes\Keywords\FacebookKeyword::class;
	$shortcodesExtConf['processShortcode']['facebookvideo'] =
		\LiquidLight\Shortcodes\Keywords\FacebookKeyword::class;

	$shortcodesExtConf['processShortcode']['iframe'] =
		\LiquidLight\Shortcodes\Keywords\IframeKeyword::class;

	$shortcodesExtConf['processShortcode']['instagram'] =
		\LiquidLight\Shortcodes\Keywords\InstagramKeyword::class;

	$shortcodesExtConf['processShortcode']['linkedin'] =
		\LiquidLight\Shortcodes\Keywords\LinkedInKeyword::class;
	$shortcodesExtConf['processShortcode']['soundcloud'] =
		\LiquidLight\Shortcodes\Keywords\SoundcloudKeyword::class;
});
