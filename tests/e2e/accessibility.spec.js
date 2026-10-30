import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const routes = [
  ['home', '/'],
  ['vende-con-nosotros', '/vende-con-nosotros/'],
  ['docentech', '/soluciones/docentech/'],
  ['violet-flow', '/soluciones/violet-flow/'],
  ['rguilex', '/soluciones/rguilex/']
];

for (const [name, route] of routes) {
  test(`${name} no tiene violaciones serias o críticas`, async ({ page }) => {
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.goto(route, { waitUntil: 'networkidle' });
    const results = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
      .analyze();
    const blocking = results.violations.filter((item) => ['serious', 'critical'].includes(item.impact));
    expect(blocking, JSON.stringify(blocking, null, 2)).toEqual([]);
  });
}

for (const viewport of [
  { width: 1440, height: 1000 },
  { width: 1024, height: 900 },
  { width: 768, height: 1024 },
  { width: 390, height: 844 }
]) {
  test(`portada sin desbordamiento a ${viewport.width}px`, async ({ page }) => {
    await page.setViewportSize(viewport);
    await page.goto('/');
    const dimensions = await page.evaluate(() => ({
      viewport: document.documentElement.clientWidth,
      scroll: document.documentElement.scrollWidth
    }));
    expect(dimensions.scroll).toBeLessThanOrEqual(dimensions.viewport);
  });
}

