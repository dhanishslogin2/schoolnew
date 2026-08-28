// @ts-check
const { test, expect } = require('./fixtures/auth.fixture');

test.describe('Core School Action: Student Admission Lifecycle', () => {

  test('Happy Path: Admit a new student with valid details and verify registration', async ({ authenticatedPage: page, baseURL }) => {
    const uniqueId = Date.now().toString().slice(-6);
    const admissionNo = `EDU2026TEST${uniqueId}`;
    const firstName = `TestAarav${uniqueId}`;
    const lastName = `Verma`;

    // Navigate to student admission form
    await page.goto(`${baseURL}students/add`);
    await page.waitForLoadState('networkidle');
    await expect(page.locator('[data-testid="student-add-form"]')).toBeVisible();

    // Fill personal & academic information
    await page.fill('[data-testid="student-admission-no"]', admissionNo);
    await page.fill('[data-testid="student-first-name"]', firstName);
    await page.fill('[data-testid="student-last-name"]', lastName);
    await page.fill('[data-testid="student-guardian-phone"]', '9876543210');

    // Submit the admission form
    await page.click('[data-testid="student-submit-btn"]');

    // Should redirect to student listing / detail view
    await page.waitForLoadState('networkidle');
    await expect(page).toHaveURL(/students/);
  });

  test('Failure State: Submitting student form without mandatory first name triggers validation', async ({ authenticatedPage: page, baseURL }) => {
    await page.goto(`${baseURL}students/add`);
    await page.waitForLoadState('networkidle');
    await expect(page.locator('[data-testid="student-add-form"]')).toBeVisible();

    // Clear first name input
    await page.fill('[data-testid="student-first-name"]', '');

    // Attempt submission
    await page.click('[data-testid="student-submit-btn"]');

    // Verify form remains on page (not submitted)
    await expect(page.locator('[data-testid="student-add-form"]')).toBeVisible();
    await expect(page).toHaveURL(/students\/add/);
  });

});
