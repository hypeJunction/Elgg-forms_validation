<?php

namespace hypeJunction\FormsValidation;

use Elgg\Hook;

/**
 * Form validation hook handlers
 */
class Forms {

	/**
	 * Filters some of the vars for compatibility with parsley
	 *
	 * @param Hook $hook Hook
	 * @return array
	 */
	public function __invoke(Hook $hook) {
		$return = $hook->getValue();

		if (!is_array($return)) {
			return;
		}

		if (\elgg_extract('validate', $return) || \elgg_extract('data-parsley-validate', $return)) {
			unset($return['validate']);
			$return['data-parsley-validate'] = 1;
			$return['data-parsley-errors-messages-disabled'] = 1;
		}

		if (\elgg_extract('required', $return)) {
			unset($return['required']);
			$return['data-parsley-required'] = 1;
		}

		if ($validation_rules = \elgg_extract('validation_rules', $return)) {
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
}
