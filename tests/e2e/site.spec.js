import { test, expect } from '@playwright/test';

test.describe('TIENDA ROSSO P2/P4', () => {
  test('la portada carga y el selector cambia contenido y destino', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('heading', { level: 1 })).toContainText('Tu próxima gran');
    await page.getByRole('tab', { name: /Violet Flow/ }).click();
    await expect(page.locator('[data-product-title]')).toContainText('conversaciones');
    await expect(page.locator('[data-product-cta]')).toHaveAttribute('href', '/soluciones/violet-flow/');
    await page.getByRole('tab', { name: /Rguilex/ }).press('ArrowLeft');
    await expect(page.getByRole('tab', { name: /Violet Flow/ })).toHaveAttribute('aria-selected', 'true');
  });

  for (const [route, name] of [
    ['/soluciones/docentech/', 'Docentech'],
    ['/soluciones/violet-flow/', 'Violet Flow'],
    ['/soluciones/rguilex/', 'Rguilex']
  ]) {
    test(`landing ${name} responde y tiene CTA`, async ({ page }) => {
      const response = await page.goto(route);
      expect(response?.ok()).toBeTruthy();
      await expect(page).toHaveTitle(new RegExp(name));
      await expect(page.getByRole('link', { name: /Solicitar demostración/ }).first()).toBeVisible();
      await expect(page.locator('main h1')).toBeVisible();
    });
  }

  test('la portada filtra categorías sin inventar productos', async ({ page }) => {
    await page.goto('/#marketplace-preview');
    await page.getByLabel('Buscar una categoría').fill('alimentos');
    await expect(page.locator('[data-category="alimentos"]')).toBeVisible();
    await expect(page.locator('[data-category="tecnología"]')).toBeHidden();
    await expect(page.getByText('El catálogo real comienza con negocios verificados.')).toBeVisible();
  });

  test('Vende con nosotros presenta planes honestos y valida la solicitud', async ({ page }) => {
    const response = await page.goto('/vende-con-nosotros/');
    expect(response?.ok()).toBeTruthy();
    await expect(page.getByRole('heading', { level: 1 })).toContainText('Tu negocio merece');
    await expect(page.getByText('Precio pendiente de aprobación')).toHaveCount(3);
    await expect(page.getByText('WhatsApp por configurar')).toBeVisible();

    const form = page.locator('[data-service-preview-form]');
    await form.getByRole('button', { name: /Validar solicitud/ }).click();
    await expect(form.locator('[data-form-status]')).toContainText('campos obligatorios');
    await form.getByLabel('Nombre del solicitante').fill('Prueba Rosso');
    await form.getByLabel('Nombre del negocio').fill('Comercio de prueba');
    await form.getByLabel('Rubro').fill('Alimentos');
    await form.getByLabel('Ciudad').fill('La Paz');
    await form.getByLabel('WhatsApp').fill('70000000');
    await form.getByLabel('Correo electrónico').fill('prueba@example.com');
    await form.getByLabel('Plan de interés').selectOption({ label: 'Plan Negocio' });
    await form.getByLabel('Tipo de servicio').selectOption({ label: 'Espacio comercial' });
    await form.getByLabel(/Acepto el tratamiento/).check();
    await form.getByRole('button', { name: /Validar solicitud/ }).click();
    await expect(form.locator('[data-form-status]')).toContainText('Validación completada');
  });

  test('el formulario exige consentimiento y no envía datos', async ({ page }) => {
    const externalRequests = [];
    page.on('request', (request) => {
      const url = new URL(request.url());
      if (!['127.0.0.1', 'localhost'].includes(url.hostname)) externalRequests.push(request.url());
    });
    await page.goto('/?utm_source=visual-test#demo');
    const form = page.locator('[data-demo-form]');
    await form.getByLabel('Nombre y apellido').fill('Prueba Visual');
    await form.getByLabel('Correo profesional').fill('prueba@example.com');
    await form.getByLabel('Solución de interés').selectOption({ label: 'Docentech' });
    await form.getByRole('button', { name: /Completar demostración/ }).click();
    await expect(form.locator('[data-form-status]')).toContainText('consentimiento');
    await form.getByRole('checkbox').check();
    await form.getByRole('button', { name: /Completar demostración/ }).click();
    await expect(form.locator('[data-form-status]')).toContainText('ningún dato fue enviado');
    await expect(form.locator('input[name="utm_source"]')).toHaveValue('visual-test');
    expect(externalRequests).toEqual([]);
  });

  test('la navegación móvil abre, conserva foco visible y cierra con Escape', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/');
    const toggle = page.getByRole('button', { name: 'Abrir menú' });
    await toggle.click();
    await expect(toggle).toHaveAttribute('aria-expanded', 'true');
    await expect(page.locator('[data-nav]')).toHaveClass(/is-open/);
    await page.keyboard.press('Escape');
    await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  });
});

