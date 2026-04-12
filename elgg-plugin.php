<?php

$plugin_root = __DIR__;
$root = dirname(dirname($plugin_root));
$alt_root = dirname(dirname(dirname($root)));

if (file_exists("$plugin_root/vendor/autoload.php")) {
	$path = $plugin_root;
} else if (file_exists("$root/vendor/autoload.php")) {
	$path = $root;
} else {
	$path = $alt_root;
}

return [
	'plugin' => [
		'name' => 'Form validation',
		'version' => '4.0.0',
	],
	'view_extensions' => [
		'input/form' => [
			'elements/forms/validation' => [],
		],
		'theme_sandbox/forms' => [
			'theme_sandbox/forms/validation' => [],
		],
		'elgg.css' => [
			'elements/forms/validation.css' => [],
		],
	],
	'views' => [
		'default' => [
			'parsley.js' => $path . '/vendor/bower-asset/parsleyjs/dist/parsley.min.js',
		],
	],
	'hooks' => [
		'view_vars' => [
			'input/form' => [
				\hypeJunction\FormsValidation\Forms::class => [],
			],
			'elements/forms/input' => [
				\hypeJunction\FormsValidation\Forms::class => [],
			],
		],
	],
];
