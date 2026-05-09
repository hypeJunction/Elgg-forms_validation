## [6.0.0] — 2026-05-09

### Migration: 5.x → 6.x

- `elgg/elgg ~6.1.0`, `php >=8.1`, `ext-intl` added in `composer.json`
- `validation.js` converted from AMD to ES module
- Docker test stack added for Elgg 6.x (docker/elgg6/)
- No data migration required

## [5.0.0] — 2026-05-04

### Migration: 4.x → 5.x

- `'hooks'` key in `elgg-plugin.php` renamed to `'events'` (hooks/events merged in Elgg 5.x)
- `\Elgg\Hook` type hint replaced with `\Elgg\Event` in `Forms::__invoke()`
- Integration tests updated: `elgg_trigger_plugin_hook()` → `elgg_trigger_event_results()`
- Unit tests updated to mock `\Elgg\Event` (with `disableOriginalConstructor()`)
- Composer constraints bumped: `elgg/elgg ^5.0`, PHP `>=8.2`
- `extra.elgg-plugin.elgg-release` set to `~5.0`
- Docker test stack rebuilt on `php:8.2-apache` + Elgg `~5.1.0`, with `phpcs` + `elgg/sniffs` available
- Test coverage parity with 4.x baseline: 18 PHPUnit tests passing on Elgg 5 bootstrap

## [4.0.0] — 2026-04-15

### Migration: 3.x → 4.x

- Removed `start.php` and `manifest.xml` (Elgg 4.x plugin format)
- Plugin manifest moved to `elgg-plugin.php` with `plugin`, `hooks`, `view_extensions`, and `views` sections
- Hook handlers registered declaratively via invokable `Forms` class (no closures in elgg-plugin.php)
- `elgg_view_input()` replaced with `elgg_view_field()` (removed in Elgg 4.x)
- Composer constraints updated: `elgg/elgg ^4.0`, `composer/installers ^2.0`, PHP `>=7.4`
- Per-plugin Docker test stack added (PHPUnit 9.6 + Playwright)
- PHPUnit unit + integration test suite added (18 tests)
- Playwright E2E test suite added (4 tests)

<a name="1.1.1"></a>
## [1.1.1](https://github.com/hypeJunction/Elgg-forms_validation/compare/1.0.0...v1.1.1) (2016-02-27)


### Bug Fixes

* **releases:** fetch git tags before generating changelog ([871b4a2](https://github.com/hypeJunction/Elgg-forms_validation/commit/871b4a2))

### Features

* **deps:** Update parsley to latest version ([2a6fe0e](https://github.com/hypeJunction/Elgg-forms_validation/commit/2a6fe0e))
* **grunt:** update automated releases ([11dab89](https://github.com/hypeJunction/Elgg-forms_validation/commit/11dab89))



<a name="1.1.0"></a>
# [1.1.0](https://github.com/hypeJunction/Elgg-forms_validation/compare/1.0.0...v1.1.0) (2016-02-27)


### Bug Fixes

* **releases:** fetch git tags before generating changelog ([871b4a2](https://github.com/hypeJunction/Elgg-forms_validation/commit/871b4a2))

### Features

* **deps:** Update parsley to latest version ([2a6fe0e](https://github.com/hypeJunction/Elgg-forms_validation/commit/2a6fe0e))
* **grunt:** update automated releases ([11dab89](https://github.com/hypeJunction/Elgg-forms_validation/commit/11dab89))



<a name="1.0.0"></a>
# 1.0.0 (2015-11-01)




