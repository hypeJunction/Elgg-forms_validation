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
];
