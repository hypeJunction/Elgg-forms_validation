<?php

namespace hypeJunction\FormsValidation;

use Elgg\Event;
use Elgg\UnitTestCase;
use ReflectionMethod;

/**
 * Regression guards for the Elgg 6.x -> 7.x migration fixes landed on this
 * plugin. Each test pins a specific commit's fixed behaviour so a future edit
 * that reverts the shape fails CI. These are source-shape assertions (no DB,
 * no page render) because the failures they guard are boot/importmap-time.
 */
class MigrationFixesTest extends UnitTestCase {

	public function up() {
		// no-op
	}

	public function down() {
		// no-op
	}

	private function pluginRoot(): string {
		// tests/phpunit/unit/hypeJunction/FormsValidation/ -> plugin root
		return dirname(__DIR__, 5);
	}

	private function read(string $relative): string {
		$path = $this->pluginRoot() . '/' . ltrim($relative, '/');
		$this->assertFileExists($path);
		return (string) file_get_contents($path);
	}

	/**
	 * Regression for 8f912c5: the Parsley importmap alias must be registered via
	 * elgg_register_esm() pointing straight at the vendored min.js. The old
	 * elgg_get_simplecache_url('parsley.js') route was never added to the Elgg 7
	 * importmap, so `import 'parsley.js'` failed to resolve.
	 *
	 * @return void
	 */
	public function testParsleyEsmRegisteredAtVendoredUrl(): void {
		$bootstrap = $this->read('classes/hypeJunction/FormsValidation/Bootstrap.php');

		// The fixed registration: esm alias -> normalized vendored file URL.
		$this->assertMatchesRegularExpression(
			'/elgg_register_esm\(\s*[\'"]parsley\.js[\'"]\s*,/',
			$bootstrap,
			'Bootstrap::init must register the parsley.js ESM alias'
		);
		$this->assertStringContainsString(
			'vendors/parsleyjs/parsley.min.js',
			$bootstrap,
			'parsley.js alias must resolve to the vendored parsley.min.js'
		);

		// The broken pre-fix route must be gone: the esm registration must NOT be
		// wired through elgg_get_simplecache_url (never added to the 7.x importmap).
		$this->assertDoesNotMatchRegularExpression(
			'/elgg_register_esm\([^)]*elgg_get_simplecache_url/',
			$bootstrap,
			'parsley.js must not be registered via elgg_get_simplecache_url (Elgg 7 importmap bug)'
		);

		// The old elgg-plugin.php views/simplecache mapping for parsley must be dropped.
		$manifest = $this->read('elgg-plugin.php');
		$this->assertStringNotContainsString(
			'parsley',
			$manifest,
			'elgg-plugin.php must no longer map parsley via a views/simplecache entry'
		);
	}

	/**
	 * Regression for 751968f: the client code moved from an inline AMD require()
	 * in validation.php to a real ES module (validation.mjs) imported via
	 * elgg_import_esm, and only when the parsley flag is present.
	 *
	 * @return void
	 */
	public function testValidationViewImportsEsmModuleNotAmd(): void {
		// The real ES module must exist and import the parsley alias.
		$mjs = $this->read('views/default/elements/forms/validation.mjs');
		$this->assertStringContainsString("import 'parsley.js'", $mjs);

		$view = $this->read('views/default/elements/forms/validation.php');

		// Guarded import: bails unless the parsley flag is set, then imports the ESM.
		$this->assertMatchesRegularExpression(
			'/data-parsley-validate/',
			$view,
			'validation.php must gate on the data-parsley-validate flag'
		);
		$this->assertStringContainsString(
			"elgg_import_esm('elements/forms/validation')",
			$view,
			'validation.php must import the ES module, not inline-require it'
		);

		// No legacy AMD residue.
		$this->assertStringNotContainsString('elgg_require_js', $view);
		$this->assertDoesNotMatchRegularExpression('/\brequire\s*\(\s*\[/', $view);
		$this->assertDoesNotMatchRegularExpression('/\bdefine\s*\(\s*\[/', $view);
	}

	/**
	 * Regression for 10fe1e4: the Forms handler was migrated to the 5.x+ events
	 * API — an __invoke(\Elgg\Event) callable registered under the
	 * elgg-plugin.php 'events' => 'view_vars' block, not the removed 'hooks' API.
	 *
	 * @return void
	 */
	public function testFormsHandlerRegisteredUnderEventsNotHooks(): void {
		// __invoke must take a single \Elgg\Event parameter.
		$method = new ReflectionMethod(Forms::class, '__invoke');
		$params = $method->getParameters();
		$this->assertCount(1, $params);
		$type = $params[0]->getType();
		$this->assertNotNull($type);
		$this->assertSame(Event::class, (string) $type);

		$manifest = require $this->pluginRoot() . '/elgg-plugin.php';

		// No legacy 'hooks' key — it was removed in 6.x.
		$this->assertArrayNotHasKey('hooks', $manifest);

		// Handler wired under events => view_vars for both view names.
		$this->assertArrayHasKey('events', $manifest);
		$this->assertArrayHasKey('view_vars', $manifest['events']);
		$this->assertArrayHasKey('input/form', $manifest['events']['view_vars']);
		$this->assertArrayHasKey('elements/forms/input', $manifest['events']['view_vars']);
		$this->assertArrayHasKey(
			Forms::class,
			$manifest['events']['view_vars']['input/form']
		);
		$this->assertArrayHasKey(
			Forms::class,
			$manifest['events']['view_vars']['elements/forms/input']
		);
	}
}
