<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'Shortcodes',
	'description' => 'Wordpress-style shortcodes for TYPO3',
	'category' => 'plugin',
	'author' => 'Liquid Light',
	'author_email' => 'info@liquidlight.co.uk',
	'author_company' => 'Liquid Light Ltd',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '2.4.1',
	'constraints' => [
		'depends' => [
			'typo3' => '11.5.0-14.99.99',
		],
		'suggests' => [],
	],
];
