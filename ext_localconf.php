<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {
	$shortcodesExtConf = &$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['shortcodes'];

	if (!is_array($shortcodesExtConf['processShortcode'])) {
		$shortcodesExtConf['processShortcode'] = [];
	}

	$shortcodesExtConf['processShortcode']['video'] =
		\LiquidLight\Shortcodes\Keywords\VideoKeyword::class;
	$shortcodesExtConf['processShortcode']['youtube'] =
		\LiquidLight\Shortcodes\Keywords\YoutubeKeyword::class;
	$shortcodesExtConf['processShortcode']['vimeo'] =
		\LiquidLight\Shortcodes\Keywords\VimeoKeyword::class;
});
