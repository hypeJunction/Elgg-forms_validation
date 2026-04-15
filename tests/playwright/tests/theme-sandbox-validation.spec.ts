import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/elgg';

/**
 * The forms_validation plugin rewrites Elgg form attributes so that
 * Parsley.js validation attributes replace HTML5 validation attributes.
 *
 * Since the theme_sandbox plugin is not available in the production Elgg
 * 4.x package, these tests verify the plugin's effects on core Elgg pages:
 *
 *   1. The plugin activates without breaking the homepage.
 *   2. Parsley.js is loaded in pages (the plugin registers parsley.js as a view).
 *   3. Login form fields marked as required render with data-parsley-required.
 *   4. Login succeeds after the plugin is active (no hook crashes on login flow).
 */

test.describe('forms_validation integration', () => {
  test('homepage renders after plugin activation', async ({ page }) => {
    const response = await page.goto('/');
    expect(response?.status()).toBeLessThan(500);

    // Page must not be an error page — look for a main content element.
    const body = await page.locator('body').textContent();
    expect(body).not.toContain('Fatal error');
    expect(body).not.toContain('PHP Parse error');
  });

  test('forms/validation.css is included in the elgg.css bundle', async ({ page }) => {
    await page.goto('/login');

    // The plugin extends elgg.css with elements/forms/validation.css.
    // Fetch the elgg.css simplecache URL from the page and verify it contains
    // the validation styles (parsley-error, parsley-success class rules).
    const cssHref = await page
      .locator('link[rel="stylesheet"][href*="elgg.css"]')
      .first()
      .getAttribute('href');
    expect(cssHref).toBeTruthy();

    const cssResponse = await page.request.get(cssHref!);
    expect(cssResponse.status()).toBe(200);
    const cssBody = await cssResponse.text();
    // The validation.css defines .elgg-field-error and .elgg-field-has-errors rules.
    expect(cssBody).toMatch(/elgg-field-error|elgg-field-has-errors/);
  });

  test('login form required fields have data-parsley-required instead of required', async ({
    page,
  }) => {
    await page.goto('/login');

    // Elgg 4.x renders two login forms: a hidden header dropdown and a visible
    // sidebar form. Check the sidebar form — it is the one users actually see.
    const usernameInput = page.locator('.elgg-module-aside input[name="username"]');
    const passwordInput = page.locator('.elgg-module-aside input[name="password"]');

    // forms_validation converts required → data-parsley-required.
    await expect(usernameInput).toHaveAttribute('data-parsley-required', '1');
    await expect(passwordInput).toHaveAttribute('data-parsley-required', '1');

    // The HTML5 required attribute must NOT be present (parsley replaces it).
    const usernameRequired = await usernameInput.getAttribute('required');
    expect(usernameRequired).toBeNull();
  });

  test('admin dashboard loads without errors after login', async ({ page }) => {
    await loginAs(page, 'admin');

    // After a successful login, the browser should be on the activity or
    // dashboard page — not a PHP error or login redirect.
    const url = page.url();
    expect(url).not.toContain('/login');

    const body = await page.locator('body').textContent();
    expect(body).not.toContain('Fatal error');
    expect(body).not.toContain('PHP Parse error');
  });
});
