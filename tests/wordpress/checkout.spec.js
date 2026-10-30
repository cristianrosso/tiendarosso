import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const SIMPLE_PRODUCT = '/product/mochila-urbana-prueba-s3/';
const VARIABLE_PRODUCT = '/product/polera-configurable-prueba-s3/';
const SOLD_OUT_PRODUCT = '/product/producto-agotado-prueba-s3/';

async function openStorePage(page, path) {
  const response = await page.goto(path, { waitUntil: 'networkidle', timeout: 120_000 });
  expect(response?.ok()).toBeTruthy();
}

async function addSimpleProduct(page) {
  await openStorePage(page, SIMPLE_PRODUCT);
  await page.locator('button[name="add-to-cart"]').click();
  await page.waitForLoadState('networkidle');
}

test('el catálogo cubre carrito vacío, agotado y producto variable', async ({ page }) => {
  test.setTimeout(240_000);

  await openStorePage(page, '/cart/');
  await expect(page.locator('body')).toContainText(/cart is currently empty|shopping cart is empty|carrito está vacío/i);

  await openStorePage(page, SOLD_OUT_PRODUCT);
  await expect(page.locator('body')).toContainText(/out of stock|agotado/i);
  await expect(page.locator('button[name="add-to-cart"]')).toHaveCount(0);

  await openStorePage(page, VARIABLE_PRODUCT);
  await expect(page.locator('select[name="attribute_tamano"]')).toHaveValue('');
  await page.locator('select[name="attribute_tamano"]').selectOption({ label: 'Mediano' });
  await expect(page.locator('input[name="variation_id"]')).not.toHaveValue('0');
  await expect(page.locator('.single_variation')).toContainText('Bs.200.00');
  await page.locator('form.cart button.single_add_to_cart_button').click();
  await page.waitForLoadState('networkidle');
  await openStorePage(page, '/cart/');
  await expect(page.locator('.wc-block-cart-items')).toContainText('Polera configurable — prueba S3');
  await expect(page.locator('.wc-block-cart-items')).toContainText('Mediano');
  await expect(page.getByRole('link', { name: 'Seguir comprando' })).toHaveAttribute('href', '/shop/');
  await page.getByRole('link', { name: /Proceed to Checkout|Finalizar compra/i }).click();
  await page.waitForLoadState('networkidle');
  await expect(page.locator('.wc-block-components-order-summary')).toContainText('Polera configurable — prueba S3');

  await openStorePage(page, '/cart/');
  await page.getByRole('button', { name: /Remove Polera configurable/i }).click();
  await expect(page.locator('body')).toContainText(
    /cart is currently empty|shopping cart is empty|carrito está vacío/i,
    { timeout: 60_000 }
  );

  await openStorePage(page, SIMPLE_PRODUCT);
  await page.getByRole('button', { name: 'Comprar ahora' }).click();
  await page.waitForURL(/\/checkout\/?$/, { timeout: 120_000 });
  await expect(page.locator('.wc-block-components-order-summary')).toContainText('Mochila urbana — prueba S3');
});

test('el checkout invitado crea un pedido local no pagado con total del servidor', async ({ page }) => {
  test.setTimeout(420_000);

  await addSimpleProduct(page);
  const productEvents = await page.evaluate(() => window.trCommerceEvents || []);
  expect(productEvents.some((event) => event.event === 'view_item')).toBeTruthy();
  expect(productEvents.some((event) => event.event === 'add_to_cart')).toBeTruthy();
  await expect(page.locator('.wc-block-mini-cart__button')).toBeVisible();
  await expect(page.locator('.wc-block-mini-cart__badge')).toContainText('1');
  await page.locator('.wc-block-mini-cart__button').click();
  await expect(page.locator('.wc-block-mini-cart__drawer')).toHaveAttribute('aria-hidden', 'false');
  await page.locator('.wc-block-components-drawer__close').click();
  await expect(page.locator('.wc-block-mini-cart__drawer')).toHaveAttribute('aria-hidden', 'true');

  await openStorePage(page, '/cart/');
  const quantity = page.getByRole('spinbutton', { name: /Quantity of Mochila urbana/i });
  await expect(quantity).toHaveValue('1');
  await expect(page.locator('.wc-block-cart__totals-title').locator('..')).toContainText('Bs.120.00');
  await page.screenshot({ path: 'docs/screenshots/s3-cart-desktop-1440.png', fullPage: true });

  const increaseResponse = page.waitForResponse(
    (response) => response.url().includes('/wc/store/v1/cart/update-item') && response.ok(),
    { timeout: 60_000 }
  );
  await page.getByRole('button', { name: /Increase quantity of Mochila urbana/i }).click();
  await increaseResponse;
  await expect(quantity).toHaveValue('2', { timeout: 30_000 });
  await expect(page.locator('body')).toContainText('Bs.240.00', { timeout: 30_000 });
  const decreaseResponse = page.waitForResponse(
    (response) => response.url().includes('/wc/store/v1/cart/update-item') && response.ok(),
    { timeout: 60_000 }
  );
  await page.getByRole('button', { name: /Reduce quantity of Mochila urbana/i }).click();
  await decreaseResponse;
  await expect(quantity).toHaveValue('1', { timeout: 30_000 });
  await expect(page.locator('body')).toContainText('Bs.120.00', { timeout: 30_000 });

  await page.getByRole('link', { name: /Proceed to Checkout|Finalizar compra/i }).click();
  await page.waitForLoadState('networkidle');
  await expect(page).toHaveURL(/\/checkout\/?$/);
  await expect(page.getByText('Pago de prueba — no realizado')).toBeVisible();

  const accessibility = await new AxeBuilder({ page })
    .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
    .analyze();
  const blocking = accessibility.violations.filter((item) => ['serious', 'critical'].includes(item.impact));
  expect(blocking, JSON.stringify(blocking, null, 2)).toEqual([]);

  await page.setViewportSize({ width: 390, height: 844 });
  await expect.poll(() => page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth)).toBeTruthy();
  await page.screenshot({ path: 'docs/screenshots/s3-checkout-mobile-390.png', fullPage: true });

  await page.getByRole('button', { name: /Place Order|Realizar pedido/i }).click();
  await expect(page.locator('body')).toContainText(/required|obligatorio|please enter a valid/i);

  await page.locator('#email').fill('checkout-s3@example.test');
  await page.locator('#shipping-first_name').fill('Prueba');
  await page.locator('#shipping-last_name').fill('S3');
  await page.locator('#shipping-address_1').fill('Calle local 123');
  await page.locator('#shipping-city').fill('La Paz');
  await page.locator('#shipping-state').selectOption({ label: 'La Paz' });
  await page.locator('#shipping-phone').fill('70000000');
  await page.locator('#radio-control-wc-payment-method-options-cheque').check();
  await expect.poll(async () => {
    const events = await page.evaluate(() => window.trCommerceEvents || []);
    return ['add_shipping_info', 'add_payment_info'].every((name) => events.some((event) => event.event === name));
  }).toBeTruthy();

  await page.locator('.wc-block-components-totals-footer-item .wc-block-formatted-money-amount').last().evaluate((element) => {
    element.textContent = 'Bs.1.00';
  });
  await page.getByRole('button', { name: /Place Order|Realizar pedido/i }).click();
  await page.waitForURL(/\/checkout\/order-received\//, { timeout: 120_000 });
  await page.waitForLoadState('networkidle');

  await expect(page.locator('.tr-order-state')).toContainText(/pago no confirmado|no se considera pagado/i);
  await expect(page.locator('body')).toContainText('Bs.120.00');
  await expect(page.locator('body')).not.toContainText('Bs.1.00');
  await expect(page.locator('.tr-order-state')).toContainText(/Pedido/);
  const orderEvents = await page.evaluate(() => window.trCommerceEvents || []);
  expect(orderEvents.some((event) => event.event === 'purchase')).toBeFalsy();
  await page.screenshot({ path: 'docs/screenshots/s3-order-pending-mobile-390.png', fullPage: true });

  await page.setViewportSize({ width: 1440, height: 1000 });
  await page.screenshot({ path: 'docs/screenshots/s3-order-pending-desktop-1440.png', fullPage: true });
});

test('las vistas comerciales no generan desbordamiento horizontal', async ({ page }) => {
  test.setTimeout(240_000);
  await openStorePage(page, '/shop/');
  for (const width of [1440, 1024, 768, 390]) {
    await page.setViewportSize({ width, height: width === 390 ? 844 : 900 });
    await expect.poll(() => page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth)).toBeTruthy();
  }
});
