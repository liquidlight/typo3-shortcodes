<?php

return [
	'frontend' => [
		'liquidlight/shortcodes/process-shortcodes' => [
			'target' => LiquidLight\Shortcodes\Middleware\CleanupShortcodes::class,
			'before' => [
				'typo3/cms-frontend/output-compression',
			],
		],
	],
];
