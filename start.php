<?php

/**
 * Form validation component
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */

require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'forms_validation_init');

/**
 * Initialize the plugin
 * @return void
 */
function forms_validation_init() {

	elgg_register_plugin_hook_handler('view_vars', 'input/form', 'forms_validation_vars');
	elgg_register_plugin_hook_handler('view_vars', 'elements/forms/input', 'forms_validation_vars');

	elgg_extend_view('input/form', 'elements/forms/validation');

	elgg_extend_view('theme_sandbox/forms', 'theme_sandbox/forms/validation');

	elgg_extend_view('elgg.css', 'elements/forms/validation.css');
}

/**
 * Filters some of the vars for compatibility with parsley
 *
 * @param string $hook   "view_vars"
 * @param string $type   Input name
 * @param array  $return View vars
 * @param array  $params Hook params
 * @return array
 */
function forms_validation_vars($hook, $type, $return, $params) {

	if (elgg_extract('validate', $return) || elgg_extract('data-parsley-validate', $return)) {
		unset($return['validate']);
		$return['data-parsley-validate'] = 1;
		$return['data-parsley-errors-messages-disabled'] = 1;
	}

	if (elgg_extract('required', $return)) {
		unset($return['required']);
		$return['data-parsley-required'] = 1;
	}

	if ($validation_rules = elgg_extract('validation_rules', $return)) {
		unset($return['validation_rules']);
		if (is_array($validation_rules)) {
			foreach ($validation_rules as $rule => $expectation) {
				$return["data-parsley-$rule"] = json_encode($expectation);
			}
		}
	}

	unset($return['errors']);
	
	return $return;
}