<?php
/**
 * Local-only WooCommerce fixtures for Sprint S3 end-to-end tests.
 *
 * Run with: wp eval-file tests/fixtures/seed-s3-catalog.php
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 'This fixture requires WP-CLI.' );
}

$fixture_host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
if ( ! in_array( $fixture_host, array( '127.0.0.1', 'localhost' ), true ) ) {
	WP_CLI::error( 'S3 fixtures are restricted to localhost.' );
}
if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce must be active.' );
}

update_option( 'woocommerce_currency', 'BOB' );
update_option( 'woocommerce_default_country', 'BO:LP' );
update_option( 'woocommerce_allowed_countries', 'specific' );
update_option( 'woocommerce_specific_allowed_countries', array( 'BO' ) );
update_option( 'woocommerce_ship_to_countries', array( 'BO' ) );
update_option( 'woocommerce_enable_guest_checkout', 'yes' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'no' );
update_option( 'woocommerce_calc_taxes', 'no' );
update_option( 'woocommerce_coming_soon', 'no' );
update_option( 'woocommerce_store_pages_only', 'no' );

$cheque_settings = get_option( 'woocommerce_cheque_settings', array() );
$cheque_settings = is_array( $cheque_settings ) ? $cheque_settings : array();
update_option(
	'woocommerce_cheque_settings',
	array_merge(
		$cheque_settings,
		array(
			'enabled'     => 'yes',
			'title'       => 'Pago de prueba — no realizado',
			'description' => 'Método nativo de prueba. Crea un pedido pendiente y no confirma ningún pago.',
			'instructions'=> 'No realices transferencias. Este pedido existe únicamente para validar el flujo local S3.',
		)
	)
);
foreach ( array( 'bacs', 'cod', 'paypal' ) as $gateway_id ) {
	$settings = get_option( 'woocommerce_' . $gateway_id . '_settings', array() );
	if ( is_array( $settings ) ) {
		$settings['enabled'] = 'no';
		update_option( 'woocommerce_' . $gateway_id . '_settings', $settings );
	}
}

foreach ( array( 'new_order', 'customer_on_hold_order', 'customer_processing_order', 'customer_completed_order' ) as $email_id ) {
	$settings = get_option( 'woocommerce_' . $email_id . '_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$settings['enabled'] = 'no';
	update_option( 'woocommerce_' . $email_id . '_settings', $settings );
}

$zone_id = 0;
foreach ( WC_Shipping_Zones::get_zones() as $zone_data ) {
	if ( 'Bolivia — pruebas S3' === $zone_data['zone_name'] ) {
		$zone_id = (int) $zone_data['zone_id'];
		break;
	}
}
if ( ! $zone_id ) {
	$zone = new WC_Shipping_Zone();
	$zone->set_zone_name( 'Bolivia — pruebas S3' );
	$zone->set_zone_order( 1 );
	$zone->add_location( 'BO', 'country' );
	$zone->save();
	$zone->add_shipping_method( 'free_shipping' );
}

$term = term_exists( 'Pruebas S3', 'product_cat' );
if ( ! $term ) {
	$term = wp_insert_term( 'Pruebas S3', 'product_cat', array( 'slug' => 'pruebas-s3' ) );
}
$category_id = is_wp_error( $term ) ? 0 : (int) ( is_array( $term ) ? $term['term_id'] : $term );

$upsert_simple = static function ( string $sku, string $name, string $price, int $stock, bool $virtual = false ) use ( $category_id ): int {
	$product_id = wc_get_product_id_by_sku( $sku );
	$product    = $product_id ? new WC_Product_Simple( $product_id ) : new WC_Product_Simple();
	$product->set_name( $name );
	$product->set_sku( $sku );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_description( 'Producto ficticio creado exclusivamente para las pruebas locales automatizadas del Sprint S3.' );
	$product->set_short_description( 'Fixture local S3. No representa una oferta comercial real.' );
	$product->set_regular_price( $price );
	$product->set_price( $price );
	$product->set_virtual( $virtual );
	$product->set_manage_stock( true );
	$product->set_stock_quantity( $stock );
	$product->set_stock_status( $stock > 0 ? 'instock' : 'outofstock' );
	$product->set_backorders( 'no' );
	if ( $category_id ) {
		$product->set_category_ids( array( $category_id ) );
	}
	return $product->save();
};

$simple_id   = $upsert_simple( 'TR-S3-SIMPLE', 'Mochila urbana — prueba S3', '120', 10 );
$sold_out_id = $upsert_simple( 'TR-S3-OUT', 'Producto agotado — prueba S3', '75', 0 );

$variable_id = wc_get_product_id_by_sku( 'TR-S3-VARIABLE' );
$variable    = $variable_id ? new WC_Product_Variable( $variable_id ) : new WC_Product_Variable();
$variable->set_name( 'Polera configurable — prueba S3' );
$variable->set_sku( 'TR-S3-VARIABLE' );
$variable->set_status( 'publish' );
$variable->set_catalog_visibility( 'visible' );
$variable->set_description( 'Producto variable ficticio para validar selección, precio y stock en el entorno local S3.' );
$variable->set_short_description( 'Fixture local S3 con dos variantes.' );
if ( $category_id ) {
	$variable->set_category_ids( array( $category_id ) );
}
$attribute = new WC_Product_Attribute();
$attribute->set_id( 0 );
$attribute->set_name( 'Tamaño' );
$attribute->set_options( array( 'Pequeño', 'Mediano' ) );
$attribute->set_position( 0 );
$attribute->set_visible( true );
$attribute->set_variation( true );
$variable->set_attributes( array( $attribute ) );
$variable_id = $variable->save();

$variation_ids = array();
foreach ( array( 'pequeno' => array( 'Pequeño', '180', 4 ), 'mediano' => array( 'Mediano', '200', 3 ) ) as $slug => $values ) {
	$sku          = 'TR-S3-VAR-' . strtoupper( $slug );
	$variation_id = wc_get_product_id_by_sku( $sku );
	$variation    = $variation_id ? new WC_Product_Variation( $variation_id ) : new WC_Product_Variation();
	$variation->set_parent_id( $variable_id );
	$variation->set_sku( $sku );
	$variation->set_status( 'publish' );
	$variation->set_attributes( array( 'tamano' => $values[0] ) );
	$variation->set_regular_price( $values[1] );
	$variation->set_price( $values[1] );
	$variation->set_manage_stock( true );
	$variation->set_stock_quantity( $values[2] );
	$variation->set_stock_status( 'instock' );
	$variation->set_backorders( 'no' );
	$variation_ids[] = $variation->save();
}

WC_Product_Variable::sync( $variable_id );
wc_delete_product_transients();
flush_rewrite_rules();

WP_CLI::line(
	wp_json_encode(
		array(
			'simple'     => $simple_id,
			'variable'   => $variable_id,
			'variations' => $variation_ids,
			'sold_out'   => $sold_out_id,
			'currency'   => get_woocommerce_currency(),
			'note'       => 'Fictitious localhost-only fixtures.',
		)
	)
);
