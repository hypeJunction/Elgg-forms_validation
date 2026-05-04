# forms_validation — Architecture (Elgg 5.x)

## Summary

A thin decorator plugin that replaces Elgg's native HTML5 form validation attributes
with [Parsley.js](https://parsleyjs.org/) equivalents. The plugin intercepts
`view_vars` events for `input/form` and `elements/forms/input` and rewrites:

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
│       └── Forms.php          # Single event handler (invokable class)
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
├── docker/                    # Per-plugin Elgg 5 test stack
│   ├── docker-compose.yml
│   ├── Dockerfile             # php:8.2-apache + Elgg 5.1
│   ├── elgg-composer.json
│   └── elgg-install.sh
├── tests/
│   ├── phpunit.xml
│   ├── bootstrap.php
│   ├── phpunit/
│   │   ├── unit/              # Unit tests for Forms handler (mocked Event)
│   │   └── integration/       # Integration tests against live Elgg 5 bootstrap
│   └── playwright/            # Browser E2E tests
│       ├── playwright.config.ts
│       ├── helpers/elgg.ts
│       └── tests/theme-sandbox-validation.spec.ts
├── elgg-plugin.php            # Plugin manifest (5.x format)
└── composer.json
```

## Registered Events (elgg-plugin.php)

| Event name | Type | Handler | Purpose |
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
- **Elgg**: `^5.0`
- **PHP**: `>=8.2`

No plugin-level dependencies. Compatible with any Elgg 5.x installation.

## Migration Notes (4.x → 5.x)

- `'hooks'` key in `elgg-plugin.php` renamed to `'events'` (hooks/events merged in 5.x)
- `\Elgg\Hook` type hint replaced with `\Elgg\Event` in `Forms::__invoke()`
- Tests updated: `elgg_trigger_plugin_hook()` → `elgg_trigger_event_results()`,
  mocked `Hook` → mocked `Event` (with `disableOriginalConstructor()` because
  `\Elgg\Event` requires constructor args)
- PHP minimum bumped from 7.4 → 8.2
- `elgg/elgg` constraint bumped from `^4.0` → `^5.0`
- `extra.elgg-plugin.elgg-release` updated to `~5.0`
- Docker test stack rebuilt on `php:8.2-apache` + Elgg `~5.1.0`

No data migration required — the plugin has no persistent state.

## Test Coverage

- **Unit** (PHPUnit): 11 tests covering `Forms::__invoke` in isolation via mocked `Event`
- **Integration** (PHPUnit): 7 tests against live Elgg 5 bootstrap — event wiring, view rendering, theme sandbox views
- **E2E** (Playwright): theme sandbox validation flow
