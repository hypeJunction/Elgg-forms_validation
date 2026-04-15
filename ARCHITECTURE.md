# forms_validation — Architecture (Elgg 4.x)

## Summary

A thin decorator plugin that replaces Elgg's native HTML5 form validation attributes
with [Parsley.js](https://parsleyjs.org/) equivalents. The plugin intercepts
`view_vars` hooks for `input/form` and `elements/forms/input` and rewrites:

- `validate => true` → `data-parsley-validate = 1` (form level)
- `required => true` → `data-parsley-required = 1` (field level)
- `validation_rules => [rule => expectation]` → `data-parsley-{rule} = json_encode(expectation)`
- Removes the `errors` key (legacy pass-through from action results)

The plugin has no entities, actions, routes, or persistent state. It is a pure
view-layer decorator.

## Directory Structure

```
forms_validation/
├── classes/
│   └── hypeJunction/FormsValidation/
│       └── Forms.php          # Single hook handler (invokable class)
├── views/default/
│   ├── elements/forms/
│   │   ├── validation.css     # Error/success field styles
│   │   ├── validation.js      # AMD module: initialises Parsley on validated forms
│   │   └── validation.php     # Inline require() call (only when data-parsley-validate present)
│   └── theme_sandbox/forms/
│       ├── validation.php     # Sandbox demo page wrapper
│       └── validation/
│           └── form.php       # Demo form body (plaintext + checkboxes)
├── languages/                 # Translation strings
├── docker/                    # Per-plugin Elgg 4 test stack
│   ├── docker-compose.yml
│   ├── Dockerfile             # php:7.4-apache + Elgg 4.3.6
│   ├── elgg-composer.json
│   └── elgg-install.sh
├── tests/
│   ├── phpunit.xml
│   ├── bootstrap.php
│   ├── phpunit/
│   │   ├── unit/              # Unit tests for Forms handler (mocked Hook)
│   │   └── integration/       # Integration tests against live Elgg 4 bootstrap
│   └── playwright/            # Browser E2E tests
│       ├── playwright.config.ts
│       ├── helpers/elgg.ts
│       └── tests/theme-sandbox-validation.spec.ts
├── elgg-plugin.php            # Plugin manifest (4.x format)
└── composer.json
```

## Registered Hooks (elgg-plugin.php)

| Hook name | Type | Handler | Purpose |
|-----------|------|---------|---------|
| `view_vars` | `input/form` | `Forms::__invoke` | Rewrites form-level `validate` → `data-parsley-validate` |
| `view_vars` | `elements/forms/input` | `Forms::__invoke` | Rewrites field-level `required` / `validation_rules` |

## View Extensions

| Base view | Extension | Purpose |
|-----------|-----------|---------|
| `input/form` | `elements/forms/validation` | Injects inline AMD `require()` for Parsley init |
| `theme_sandbox/forms` | `theme_sandbox/forms/validation` | Sandbox demo entry |
| `elgg.css` | `elements/forms/validation.css` | Field error/success styles |

## View Registrations

| View name | File | Purpose |
|-----------|------|---------|
| `parsley.js` | `vendor/bower-asset/parsleyjs/dist/parsley.min.js` | Parsley library served as Elgg view |

## Dependencies

- **composer**: `bower-asset/parsleyjs ~2.3` (loaded as Elgg view, not AMD module)
- **Elgg**: `^4.0`
- **PHP**: `>=7.4`

No plugin-level dependencies. Compatible with any Elgg 4.x installation.

## Migration Notes (3.x → 4.x)

- Removed `start.php` (Elgg 4.x rejects plugins with start.php)
- Removed `manifest.xml` (replaced by `elgg-plugin.php` `plugin` section)
- Hook handlers moved from `start.php` closure registrations to `elgg-plugin.php` `hooks` section using invokable class syntax
- `elgg_view_input()` replaced with `elgg_view_field()` (removed in 4.x)
- Composer `name` kept as `hypejunction/forms_validation` (underscore is valid; Elgg 4 only requires lowercase)
- `extra.elgg-plugin.elgg-release` updated to `~4.0`
- Per-plugin Docker test stack scaffolded for PHPUnit + Playwright

## Test Coverage

- **Unit** (PHPUnit): 11 tests covering `Forms::__invoke` in isolation via mocked `Hook`
- **Integration** (PHPUnit): 7 tests against live Elgg 4 bootstrap — hook wiring, view rendering, theme sandbox views
- **E2E** (Playwright): 4 tests — plugin activation smoke, CSS bundle inclusion, attribute rewrite on login form, admin login flow
