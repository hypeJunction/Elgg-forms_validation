<?php

namespace hypeJunction\FormsValidation;

use Elgg\Event;
use Elgg\UnitTestCase;

/**
 * Unit tests for the Forms event handler.
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
	 * Build an Event stub that returns the given value.
	 *
	 * @param mixed $value
	 * @return Event
	 */
	protected function makeEvent($value): Event {
		$event = $this->getMockBuilder(Event::class)
			->disableOriginalConstructor()
			->getMock();
		$event->method('getValue')->willReturn($value);
		return $event;
	}

	/**
     * @return void
     */
    public function testReturnsNullWhenValueIsNotArray(): void {
		$handler = new Forms();
		$this->assertNull($handler($this->makeEvent('not-an-array')));
		$this->assertNull($handler($this->makeEvent(null)));
		$this->assertNull($handler($this->makeEvent(42)));
	}

	/**
     * @return void
     */
    public function testValidateFlagRewrittenToParsleyAttribute(): void {
		$handler = new Forms();
		$hook = $this->makeEvent(['validate' => true, 'action' => '/foo']);
		$result = $handler($hook);

		$this->assertIsArray($result);
		$this->assertArrayNotHasKey('validate', $result);
		$this->assertSame(1, $result['data-parsley-validate']);
		$this->assertSame(1, $result['data-parsley-errors-messages-disabled']);
		$this->assertSame('/foo', $result['action']);
	}

	/**
     * @return void
     */
    public function testDataParsleyValidateFlagAlsoTriggersDisable(): void {
		$handler = new Forms();
		$hook = $this->makeEvent(['data-parsley-validate' => true]);
		$result = $handler($hook);

		$this->assertSame(1, $result['data-parsley-validate']);
		$this->assertSame(1, $result['data-parsley-errors-messages-disabled']);
	}

	/**
     * @return void
     */
    public function testNoValidateFlagLeavesAttributesUntouched(): void {
		$handler = new Forms();
		$hook = $this->makeEvent(['action' => '/bar']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('data-parsley-validate', $result);
		$this->assertArrayNotHasKey('data-parsley-errors-messages-disabled', $result);
	}

	/**
     * @return void
     */
    public function testRequiredAttributeRewritten(): void {
		$handler = new Forms();
		$hook = $this->makeEvent(['required' => true, 'name' => 'title']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('required', $result);
		$this->assertSame(1, $result['data-parsley-required']);
		$this->assertSame('title', $result['name']);
	}

	/**
     * @return void
     */
    public function testRequiredFalseLeavesAttributesUntouched(): void {
		$handler = new Forms();
		$hook = $this->makeEvent(['required' => false]);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('data-parsley-required', $result);
	}

	/**
     * @return void
     */
    public function testValidationRulesExpandedToParsleyDataAttributes(): void {
		$handler = new Forms();
$hook = $this->makeEvent([
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

	/**
     * @return void
     */
    public function testValidationRulesNonArrayIsDropped(): void {
		$handler = new Forms();
		$hook = $this->makeEvent(['validation_rules' => 'nope']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('validation_rules', $result);
		// No data-parsley-* keys should be generated from a non-array.
		foreach (array_keys($result) as $key) {
			$this->assertStringStartsNotWith('data-parsley-', (string) $key);
		}
	}

	/**
     * @return void
     */
    public function testErrorsKeyIsAlwaysStripped(): void {
		$handler = new Forms();
		$hook = $this->makeEvent(['errors' => ['some' => 'error'], 'action' => '/x']);
		$result = $handler($hook);

		$this->assertArrayNotHasKey('errors', $result);
		$this->assertSame('/x', $result['action']);
	}

	/**
     * @return void
     */
    public function testCombinedFlagsAllApplied(): void {
		$handler = new Forms();
$hook = $this->makeEvent([
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

	/**
     * @return void
     */
    public function testEmptyArrayReturnsEmptyArray(): void {
		$handler = new Forms();
		$result = $handler($this->makeEvent([]));

		$this->assertIsArray($result);
		$this->assertSame([], $result);
	}
}
