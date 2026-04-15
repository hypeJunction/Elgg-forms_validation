import { Page } from '@playwright/test';

export async function loginAs(
  page: Page,
  username: string,
  password: string = 'admin12345'
) {
  await page.goto('/login');
  // Elgg renders two login forms: a hidden header dropdown and a visible sidebar
  // form inside .elgg-module-aside. Target the sidebar inputs directly.
  await page.locator('.elgg-module-aside input[name="username"]').fill(username);
  await page.locator('.elgg-module-aside input[name="password"]').fill(password);
  await page.locator('.elgg-module-aside').locator('input[type="submit"], button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
}
