<?php

namespace hypeJunction\FormsValidation;

use Elgg\IntegrationTestCase;

/**
 * Integration tests for the forms_validation plugin.
 *
 * Verifies that hook handlers are wired, that view extensions are registered,
 * and that the theme sandbox demo views render without errors.
 */
class PluginIntegrationTest extends IntegrationTestCase {

	public function up() {
		// no-op
	}

	public function down() {
		// no-op
	}

	public function testFormsValidationHookRewritesInputFormVars(): void {
		$vars = [
			'validate' => true,
			'required' => true,
			'validation_rules' => ['minlength' => 5],
			'action' => '/test',
		];

		$result = \elgg_trigger_plugin_hook('view_vars', 'input/form', [], $vars);

		$this->assertIsArray($result);
		$this->assertArrayNotHasKey('validate', $result);
		$this->assertArrayNotHasKey('required', $result);
		$this->assertArrayNotHasKey('validation_rules', $result);
		$this->assertSame(1, $result['data-parsley-validate']);
		$this->assertSame(1, $result['data-parsley-required']);
		$this->assertSame(json_encode(5), $result['data-parsley-minlength']);
	}

	public function testFormsValidationHookRewritesElementsFormsInputVars(): void {
		$vars = ['required' => true, 'name' => 'title'];

		$result = \elgg_trigger_plugin_hook('view_vars', 'elements/forms/input', [], $vars);

		$this->assertIsArray($result);
		$this->assertArrayNotHasKey('required', $result);
		$this->assertSame(1, $result['data-parsley-required']);
	}

	public function testFormsValidationHookLeavesPlainVarsUntouched(): void {
		$vars = ['action' => '/plain', 'method' => 'POST'];

		$result = \elgg_trigger_plugin_hook('view_vars', 'input/form', [], $vars);

		$this->assertSame('/plain', $result['action']);
		$this->assertSame('POST', $result['method']);
		$this->assertArrayNotHasKey('data-parsley-validate', $result);
	}

	public function testValidationViewRendersWithParsleyFlag(): void {
		$output = \elgg_view('elements/forms/validation', [
			'data-parsley-validate' => 1,
		]);
		$this->assertIsString($output);
		$this->assertStringContainsString('parsley', $output);
	}

	public function testValidationViewEmptyWithoutFlag(): void {
		$output = \elgg_view('elements/forms/validation', []);
		$this->assertIsString($output);
		// Without the flag, the view should return nothing.
		$this->assertSame('', trim($output));
	}

	public function testThemeSandboxFormRenders(): void {
		$output = \elgg_view('theme_sandbox/forms/validation');
		$this->assertIsString($output);
		$this->assertNotEmpty($output);
		// Theme sandbox wraps the body in a module titled "Form Validation".
		$this->assertStringContainsString('Form Validation', $output);
	}

	public function testThemeSandboxFormBodyContainsInputs(): void {
		$output = \elgg_view('theme_sandbox/forms/validation/form');
		$this->assertIsString($output);
		$this->assertNotEmpty($output);
		// The demo renders a plaintext textarea and a checkboxes group.
		$this->assertStringContainsString('textarea', $output);
		$this->assertStringContainsString('state', $output);
	}
}
