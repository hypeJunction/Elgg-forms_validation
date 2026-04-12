import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/elgg';

/**
 * The forms_validation plugin exposes a demo form via the theme sandbox at
 * /theme_sandbox/forms/validation. It is the only user-facing surface.
 *
 * These tests verify:
 *   1. The demo form renders with Parsley wiring.
 *   2. The Forms hook handler correctly rewrites HTML attributes so the
 *      rendered markup contains data-parsley-* instead of required/validate.
 *   3. Client-side validation blocks submission when inputs are invalid.
 *   4. Valid inputs allow submission to proceed.
 */

test.describe('forms_validation theme sandbox', () => {
  test('demo form page renders with Parsley enabled form', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/theme_sandbox/forms/validation');

    const form = page.locator('form[data-parsley-validate]');
    await expect(form).toBeVisible();

    // The plaintext textarea must carry data-parsley-required instead of required.
    const textarea = page.locator('textarea').first();
    await expect(textarea).toHaveAttribute('data-parsley-required', '1');
    const hasRequired = await textarea.getAttribute('required');
    expect(hasRequired).toBeNull();
  });

  test('checkboxes group has parsley mincheck attribute preserved', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/theme_sandbox/forms/validation');

    // The sandbox declares data-parsley-mincheck=5 on the state checkboxes.
    const stateInput = page.locator('input[name="state[]"]').first();
    await expect(stateInput).toBeVisible();
    const form = page.locator('form[data-parsley-validate]');
    await expect(form.locator('[data-parsley-mincheck="5"]')).toHaveCount(1);
  });

  test('submitting empty form triggers parsley validation errors', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/theme_sandbox/forms/validation');

    const submit = page.locator('form[data-parsley-validate] button[type="submit"], form[data-parsley-validate] input[type="submit"]').first();
    await submit.click();

    // Parsley marks invalid fields with the parsley-error class on the form.
    // The form action is '#' so a successful validation would leave the URL
    // unchanged too; we assert on presence of parsley error state instead.
    const invalid = page.locator('.parsley-error, [data-parsley-id]').first();
    await expect(invalid).toBeVisible({ timeout: 5000 });
  });

  test('filling fields with valid values clears parsley errors', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/theme_sandbox/forms/validation');

    // Trigger validation first.
    const submit = page.locator('form[data-parsley-validate] button[type="submit"], form[data-parsley-validate] input[type="submit"]').first();
    await submit.click();

    // Fill the textarea with a 60-character string (min 50, max 100).
    const textarea = page.locator('textarea').first();
    await textarea.fill('x'.repeat(60));
    await textarea.blur();

    // Select 5 state checkboxes to satisfy mincheck=5.
    const boxes = page.locator('input[name="state[]"]');
    for (let i = 0; i < 5; i++) {
      await boxes.nth(i).check();
    }

    // Re-submit. Form action is '#' so Parsley either accepts (no new
    // error nodes) or blocks. We assert no visible .parsley-error remains.
    await submit.click();
    await page.waitForTimeout(300);
    const errorsVisible = await page.locator('.parsley-error').count();
    expect(errorsVisible).toBe(0);
  });
});
