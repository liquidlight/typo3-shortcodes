<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'Shortcodes',
	'description' => 'Wordpress-style shortcodes for TYPO3',
	'category' => 'plugin',
	'author' => 'Mike Street',
	'author_email' => 'mike@liquidlight.co.uk',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '1.8.0',
	'constraints' => [
		'depends' => [
			'typo3' => '9.5.0-11.5.99',
		],
		'suggests' => [],
	],
];
