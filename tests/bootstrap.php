<?php
/**
 * PHPUnit bootstrap for forms_validation plugin tests.
 * Plugin must be installed at {elgg_root}/mod/forms_validation/
 */

// tests/ -> mod/plugin/ -> mod/ -> elgg_root/
$elggRoot = dirname(dirname(dirname(__DIR__)));

require_once $elggRoot . '/vendor/autoload.php';

// Load Elgg test classes (UnitTestCase, IntegrationTestCase)
$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
    $file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load plugin autoloader if vendored, else register PSR-4 for plugin classes/
$pluginRoot = dirname(__DIR__);
if (file_exists($pluginRoot . '/vendor/autoload.php')) {
    require_once $pluginRoot . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) use ($pluginRoot) {
        $prefix = 'hypeJunction\\FormsValidation\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }
        $relative = substr($class, strlen($prefix));
        $file = $pluginRoot . '/classes/hypeJunction/FormsValidation/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

\Elgg\Application::loadCore();
