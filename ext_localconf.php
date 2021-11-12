<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {

	$shortcodesExtConf = & $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['shortcodes'];

	if(!is_array($shortcodesExtConf['processShortcode'])) {
		$shortcodesExtConf['processShortcode'] = [];
	}

	$shortcodesExtConf['processShortcode']['youtube'] =
		\LiquidLight\Shortcodes\Keywords\YoutubeShortcode::class;
		$shortcodesExtConf['processShortcode']['vimeo'] =
		\LiquidLight\Shortcodes\Keywords\VimeoShortcode::class;
});
