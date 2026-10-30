import { test, expect } from '@playwright/test';
import { mkdir } from 'node:fs/promises';

const pages = [
  ['home', '/'],
  ['vende-con-nosotros', '/vende-con-nosotros/'],
  ['docentech', '/soluciones/docentech/'],
  ['violet-flow', '/soluciones/violet-flow/'],
  ['rguilex', '/soluciones/rguilex/']
];

test.beforeAll(async () => {
  await mkdir('docs/screenshots', { recursive: true });
});

for (const [name, route] of pages) {
  for (const viewport of [
    { label: 'desktop-1440', width: 1440, height: 1000 },
    { label: 'mobile-390', width: 390, height: 844 }
  ]) {
    test(`${name} ${viewport.label}`, async ({ page }) => {
      await page.setViewportSize({ width: viewport.width, height: viewport.height });
      await page.emulateMedia({ reducedMotion: 'reduce' });
      await page.goto(route, { waitUntil: 'networkidle' });
      await page.locator('.reveal').evaluateAll((items) => items.forEach((item) => item.classList.add('is-visible')));
      await expect(page.locator('main')).toBeVisible();
      await page.screenshot({ path: `docs/screenshots/${name}-${viewport.label}.png`, fullPage: true });
    });
  }
}

for (const viewport of [
  { label: 'tablet-1024', width: 1024, height: 900 },
  { label: 'tablet-768', width: 768, height: 1024 }
]) {
  test(`home ${viewport.label}`, async ({ page }) => {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.goto('/', { waitUntil: 'networkidle' });
    await page.locator('.reveal').evaluateAll((items) => items.forEach((item) => item.classList.add('is-visible')));
    await page.screenshot({ path: `docs/screenshots/home-${viewport.label}.png`, fullPage: true });
  });
}

