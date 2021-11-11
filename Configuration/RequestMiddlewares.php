<?php

return [
	'frontend' => [
		'liquidlight/shortcodes/process-shortcodes' => [
			'target' => LiquidLight\Shortcodes\Middleware\ProcessShortcodes::class,
			'before' => [
				'typo3/cms-frontend/output-compression',
			],
		],
	],
];
