import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/wordpress',
  fullyParallel: false,
  timeout: 45_000,
  reporter: [['list'], ['html', { open: 'never', outputFolder: 'playwright-report-wordpress' }]],
  use: {
    baseURL: process.env.WP_BASE_URL || 'http://127.0.0.1:8080',
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure'
  },
  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }]
});
