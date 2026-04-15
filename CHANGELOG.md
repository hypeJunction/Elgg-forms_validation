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




