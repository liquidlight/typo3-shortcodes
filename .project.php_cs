<?php

$finder = PhpCsFixer\Finder::create()
	->exclude('vendor')
	->ignoreDotFiles(true)
	->ignoreUnreadableDirs()
	->ignoreVCSIgnored(true)

	->in(__DIR__)
	;


$config = new PhpCsFixer\Config();
return $config->setRules([
		'@PSR12' => true,

		'indentation_type' => false,

		'array_syntax' => ['syntax' => 'short'],
		'concat_space' => ['spacing' => 'one'],
		'no_empty_statement' => false,
		'no_whitespace_in_blank_line' => true,

		'blank_line_after_opening_tag' => true,
		'cast_spaces' => ['space' => 'none'],
		'class_attributes_separation' => [
			'elements' => [
				'method' => 'one',
				'property' => 'one',
				'const' => 'one',
			],
		],
		'function_typehint_space' => true,
		'include' => true,
		'lowercase_cast' => true,
		'lowercase_static_reference' => true,
		'magic_constant_casing' => true,
		'native_function_casing' => true,
		'no_alternative_syntax' => true,
		'ternary_operator_spaces' => true,
		'unary_operator_spaces' => true,

		'multiline_whitespace_before_semicolons' => [
			'strategy' => 'new_line_for_chained_calls',
		],
		'trailing_comma_in_multiline' => true,
	])
	->setIndent('	')
	->setFinder($finder)
	;
