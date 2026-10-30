<?php
/** Verify the latest localhost-only S3 checkout order. */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 'This verification requires WP-CLI.' );
}

$fixture_host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
if ( ! in_array( $fixture_host, array( '127.0.0.1', 'localhost' ), true ) ) {
	WP_CLI::error( 'S3 order verification is restricted to localhost.' );
}

$orders = wc_get_orders(
	array(
		'billing_email' => 'checkout-s3@example.test',
		'limit'         => 1,
		'orderby'       => 'date',
		'order'         => 'DESC',
	)
);

if ( empty( $orders ) ) {
	WP_CLI::error( 'No S3 checkout order was found.' );
}

$order = $orders[0];
$items = $order->get_items();
$item  = reset( $items );
$sku   = $item ? $item->get_product()->get_sku() : '';
$checks = array(
	'currency_is_bob'       => 'BOB' === $order->get_currency(),
	'total_is_server_value' => '120.00' === $order->get_total(),
	'status_is_on_hold'     => 'on-hold' === $order->get_status(),
	'payment_is_unconfirmed' => ! $order->is_paid() && ! $order->get_date_paid(),
	'expected_product'      => 'TR-S3-SIMPLE' === $sku,
	'purchase_not_recorded' => ! $order->get_meta( '_tr_purchase_event_id', true ),
);

$failed = array_keys( array_filter( $checks, static fn ( bool $passed ): bool => ! $passed ) );
if ( $failed ) {
	WP_CLI::error( 'Failed checks: ' . implode( ', ', $failed ) );
}

WP_CLI::line(
	wp_json_encode(
		array(
			'order_id' => $order->get_id(),
			'status'   => $order->get_status(),
			'currency' => $order->get_currency(),
			'total'    => $order->get_total(),
			'sku'      => $sku,
			'checks'   => $checks,
		)
	)
);
WP_CLI::success( 'Latest S3 order is valid and remains unpaid.' );
