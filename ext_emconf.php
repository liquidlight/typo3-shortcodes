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
	'version' => '2.3.0',
	'constraints' => [
		'depends' => [
			'typo3' => '11.5.0-13.99.99',
		],
		'suggests' => [],
	],
];
