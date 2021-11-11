<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyPagesIndexEntry'][] =
	\LiquidLight\KeSearch\Hook\IndexHook::class;

$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['shortcodes']['register'] = [
	'youtube' => \LiquidLight\Shortcodes\Keywords\YoutubeShortcode::class,
	'vimeo' => \LiquidLight\Shortcodes\Keywords\VimeoShortcode::class,
];
