// @ts-check
const { test, expect } = require('./fixtures/auth.fixture');

test.describe('Core Student Admission Wizard Lifecycle', () => {

  test('Happy Path: Admit a new student through 3-step wizard and verify registration', async ({ authenticatedPage: page, baseURL }) => {
    const uniqueId = Date.now().toString().slice(-6);
    const admissionNo = `EDU2026TEST${uniqueId}`;
    const firstName = `TestAarav${uniqueId}`;
    const lastName = `Verma`;

    // Navigate to student admission wizard (Step 1)
    await page.goto(`${baseURL}students/add`);
    await page.waitForLoadState('networkidle');
    await expect(page.locator('#step1-form')).toBeVisible();

    // Step 1: Fill personal information
    await page.fill('#admission_number', admissionNo);
    await page.fill('#first_name', firstName);
    await page.fill('input[name="last_name"]', lastName);
    await page.click('#btn-step1-next');

    // Should proceed to Step 2
    await page.waitForURL(/step=2/);
    await expect(page.locator('#step2-form')).toBeVisible();

    // Step 2: Select class and mark no previous school
    await page.selectOption('#class_id', { index: 1 });
    await page.check('#no_previous_school');
    await page.click('#btn-step2-next');

    // Should proceed to Step 3 (Parent / Guardian Details & Confirmation)
    await page.waitForURL(/step=3/);
    await expect(page.locator('#step3-form')).toBeVisible();

    // Step 3: Fill guardian details and confirm
    await page.fill('#guardian_name', 'Suresh Verma');
    await page.fill('#guardian_phone', '9847011223');
    await page.click('#btn-save-student');

    // Should redirect to student profile / directory
    await page.waitForURL(/students\/(profile\/\d+|list|overview)/);
    await expect(page).toHaveURL(/students/);
  });

  test('Failure State: Submitting step 1 without mandatory first name triggers validation', async ({ authenticatedPage: page, baseURL }) => {
    await page.goto(`${baseURL}students/add`);
    await page.waitForLoadState('networkidle');
    await expect(page.locator('#step1-form')).toBeVisible();

    // Clear first name input
    await page.fill('#first_name', '');

    // Attempt step 1 next
    await page.click('#btn-step1-next');

    // Verify error shown and remains on step 1
    await expect(page.locator('#err-first_name')).toBeVisible();
    await expect(page.locator('#step1-form')).toBeVisible();
  });

});
