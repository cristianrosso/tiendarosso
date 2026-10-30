import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test('la landing WordPress muestra planes configurables y formulario funcional', async ({ page }) => {
  const response = await page.goto('/vende-con-nosotros/', { waitUntil: 'networkidle' });
  expect(response?.ok()).toBeTruthy();
  await expect(page.getByRole('heading', { level: 1 })).toContainText('Tu negocio merece vender más');
  await expect(page.getByText('Precio sujeto a propuesta')).toHaveCount(3);
  await expect(page.getByText('WhatsApp pendiente de configuración')).toHaveCount(2);

  const form = page.locator('form.tr-service-form');
  await form.getByLabel('Nombre del solicitante').fill('Prueba E2E');
  await form.getByLabel('Nombre del negocio').fill('Negocio E2E');
  await form.getByLabel('Rubro').fill('Tecnología');
  await form.getByLabel('Ciudad').fill('La Paz');
  await form.getByLabel('WhatsApp').fill('70000000');
  await form.getByLabel('Correo electrónico').fill('e2e@example.test');
  await form.getByLabel('Plan de interés').selectOption({ label: 'Plan Negocio' });
  await form.getByLabel('Tipo de servicio').selectOption({ label: 'Espacio comercial' });
  await form.getByLabel(/Acepto el tratamiento/).check();
  await form.getByRole('button', { name: /Enviar solicitud/ }).click();
  await page.waitForLoadState('networkidle');
  await expect(page).toHaveURL(/tr_form=success/);
  await expect(page.locator('.tr-form-status-success')).toContainText('Solicitud registrada');
});

test('la landing WordPress no tiene violaciones serias o críticas', async ({ page }) => {
  await page.goto('/vende-con-nosotros/', { waitUntil: 'networkidle' });
  const results = await new AxeBuilder({ page })
    .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
    .analyze();
  const blocking = results.violations.filter((item) => ['serious', 'critical'].includes(item.impact));
  expect(blocking, JSON.stringify(blocking, null, 2)).toEqual([]);
});

test('el administrador puede abrir la página en Gutenberg y la configuración comercial', async ({ page }) => {
  test.setTimeout(150_000);
  test.skip(!process.env.WP_ADMIN_USER || !process.env.WP_ADMIN_PASSWORD, 'Requiere credenciales efímeras de la instalación local.');
  await page.goto('/wp-login.php');
  await page.locator('#user_login').fill(process.env.WP_ADMIN_USER);
  await page.locator('#user_pass').fill(process.env.WP_ADMIN_PASSWORD);
  await page.getByRole('button', { name: /Acceder|Log In/ }).click();
  await page.waitForURL(/wp-admin/);

  const pageId = process.env.WP_SELLER_PAGE_ID;
  expect(pageId).toBeTruthy();
  await page.goto(`/wp-admin/post.php?post=${pageId}&action=edit`);
  await expect(page.locator('body')).toHaveClass(/block-editor-page/);
  await expect(page.locator('.interface-interface-skeleton')).toBeVisible({ timeout: 90_000 });

  await page.goto('/wp-admin/admin.php?page=tr-commerce-settings');
  await expect(page.getByRole('heading', { name: /Configuración comercial/ })).toBeVisible();
  await expect(page.getByLabel('Número general')).toBeVisible();
});
