<?php

namespace hypeJunction\FormsValidation;

use Elgg\Hook;
use Elgg\UnitTestCase;

/**
 * Unit tests for the Forms hook handler.
 *
 * The Forms::__invoke() handler rewrites input/form and elements/forms/input
 * view vars so that Parsley.js validation attributes replace Elgg's native
 * HTML5 validation attributes.
 */
class FormsTest extends UnitTestCase {

	public function up() {
		// no-op
	}

	public function down() {
		// no-op
	}

	/**
	 * Build a Hook stub that returns the given value.
	 *
	 * @param mixed $value
	 * @return Hook
	 */
	protected function makeHook($value): Hook {
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$hook->method('getValue')->willReturn($value);
		return $hook;
	}

	public function testReturnsNullWhenValueIsNotArray(): void {
		$handler = new Forms();
		$this->assertNull($handler($this->makeHook('not-an-array')));
		$this->assertNull($handler($this->makeHook(null)));
		$this->assertNull($handler($this->makeHook(42)));
	}

	public function testValidateFlagRewrittenToParsleyAttribute(): void {
		$handler = new Forms();
		$hook = $this->makeHook(['validate' => true, 'action' => '/foo']);
		$result = $handler($hook);

		$this->assertIsArray($result);
		$this->assertArrayNotHasKey('validate', $result);
		$this->assertSame(1, $result['data-parsley-validate']);
		$this->assertSame(1, $result['data-parsley-errors-messages-disabled']);
		$this->assertSame('/foo', $result['action']);
	}

	public function testDataParsleyValidateFlagAlsoTriggersDisable(): void {
		$handler = new Forms();
		$hook = $this->makeHook(['data-parsley-validate' => true]);
		$result = $handler($hook);

		$this->assertSame(1, $result['data-parsley-validate']);
		$this->assertSame(1, $result['data-parsley-errors-messages-disabled']);
	}

	public function testNoValidateFlagLeavesAttributesUntouched(): void {
		$handler = new Forms();
		$hook = $this->makeHook(['action' => '/bar']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('data-parsley-validate', $result);
		$this->assertArrayNotHasKey('data-parsley-errors-messages-disabled', $result);
	}

	public function testRequiredAttributeRewritten(): void {
		$handler = new Forms();
		$hook = $this->makeHook(['required' => true, 'name' => 'title']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('required', $result);
		$this->assertSame(1, $result['data-parsley-required']);
		$this->assertSame('title', $result['name']);
	}

	public function testRequiredFalseLeavesAttributesUntouched(): void {
		$handler = new Forms();
		$hook = $this->makeHook(['required' => false]);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('data-parsley-required', $result);
	}

	public function testValidationRulesExpandedToParsleyDataAttributes(): void {
		$handler = new Forms();
		$hook = $this->makeHook([
			'validation_rules' => [
				'minlength' => 5,
				'maxlength' => 20,
				'type' => 'email',
			],
		]);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('validation_rules', $result);
		$this->assertSame(json_encode(5), $result['data-parsley-minlength']);
		$this->assertSame(json_encode(20), $result['data-parsley-maxlength']);
		$this->assertSame(json_encode('email'), $result['data-parsley-type']);
	}

	public function testValidationRulesNonArrayIsDropped(): void {
		$handler = new Forms();
		$hook = $this->makeHook(['validation_rules' => 'nope']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('validation_rules', $result);
		// No data-parsley-* keys should be generated from a non-array.
		foreach (array_keys($result) as $key) {
			$this->assertStringStartsNotWith('data-parsley-', (string) $key);
		}
	}

	public function testErrorsKeyIsAlwaysStripped(): void {
		$handler = new Forms();
		$hook = $this->makeHook(['errors' => ['some' => 'error'], 'action' => '/x']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('errors', $result);
		$this->assertSame('/x', $result['action']);
	}

	public function testCombinedFlagsAllApplied(): void {
		$handler = new Forms();
		$hook = $this->makeHook([
			'validate' => true,
			'required' => true,
			'validation_rules' => ['minlength' => 3],
			'errors' => ['bad' => 'thing'],
			'name' => 'combo',
		]);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('validate', $result);
		$this->assertArrayNotHasKey('required', $result);
		$this->assertArrayNotHasKey('validation_rules', $result);
		$this->assertArrayNotHasKey('errors', $result);

		$this->assertSame(1, $result['data-parsley-validate']);
		$this->assertSame(1, $result['data-parsley-errors-messages-disabled']);
		$this->assertSame(1, $result['data-parsley-required']);
		$this->assertSame(json_encode(3), $result['data-parsley-minlength']);
		$this->assertSame('combo', $result['name']);
	}

	public function testEmptyArrayReturnsEmptyArray(): void {
		$handler = new Forms();
		$result = $handler($this->makeHook([]));

		$this->assertIsArray($result);
		$this->assertSame([], $result);
	}
}
