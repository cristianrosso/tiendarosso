<?php
/**
 * WooCommerce checkout experience and transaction safeguards.
 *
 * @package TiendaRossoCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TR_Checkout {
	private const VERSION_OPTION = 'tr_checkout_schema_version';
	private const EVENT_SESSION_KEY = 'tr_commerce_events';

	/** Register WooCommerce integration hooks. */
	public static function boot(): void {
		add_action( 'init', array( __CLASS__, 'maybe_upgrade' ), 6 );
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'render_buy_now_nonce' ) );
		add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'render_product_actions' ) );
		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'validate_buy_now' ), 10, 6 );
		add_filter( 'woocommerce_add_to_cart_redirect', array( __CLASS__, 'buy_now_redirect' ) );
		add_action( 'woocommerce_add_to_cart', array( __CLASS__, 'record_add_to_cart' ), 10, 6 );
		add_action( 'woocommerce_cart_item_removed', array( __CLASS__, 'record_remove_from_cart' ), 10, 2 );
		add_action( 'woocommerce_before_thankyou', array( __CLASS__, 'render_order_state' ) );
		add_action( 'woocommerce_payment_complete', array( __CLASS__, 'mark_confirmed_payment' ) );
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'copy_vendor_reference' ), 10, 4 );
	}

	/** Apply safe checkout defaults once per plugin schema version. */
	public static function maybe_upgrade(): void {
		if ( ! class_exists( 'WooCommerce' ) || TR_COMMERCE_VERSION === get_option( self::VERSION_OPTION ) ) {
			return;
		}

		update_option( 'woocommerce_currency', 'BOB' );
		update_option( 'woocommerce_enable_guest_checkout', 'yes' );
		update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'no' );
		update_option( 'woocommerce_enable_checkout_login_reminder', 'no' );
		update_option( self::VERSION_OPTION, TR_COMMERCE_VERSION );
	}

	/** Add CSRF protection to the direct-checkout action. */
	public static function render_buy_now_nonce(): void {
		global $product;
		if ( $product instanceof WC_Product ) {
			wp_nonce_field( 'tr_buy_now_' . $product->get_id(), 'tr_buy_now_nonce', false );
			if ( $product->is_type( 'simple' ) ) {
				printf( '<input type="hidden" name="add-to-cart" value="%d">', absint( $product->get_id() ) );
			}
		}
	}

	/** Render direct checkout and contextual WhatsApp actions. */
	public static function render_product_actions(): void {
		global $product;
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		echo '<div class="tr-product-actions">';
		if ( $product->is_purchasable() && $product->is_in_stock() ) {
			echo '<button type="submit" class="button alt tr-buy-now" name="tr_buy_now" value="1">' . esc_html__( 'Comprar ahora', 'tiendarosso-commerce' ) . '</button>';
		}

		$message      = sprintf( 'Hola, quiero información sobre el producto %s de TIENDA ROSSO.', $product->get_name() );
		$whatsapp_url = TR_Settings::whatsapp_url( 'general', $message );
		if ( $whatsapp_url ) {
			printf(
				'<a class="button tr-product-whatsapp" href="%1$s" target="_blank" rel="noopener noreferrer" data-tr-whatsapp="%2$s" data-tr-context="product">%3$s</a>',
				esc_url( $whatsapp_url ),
				esc_attr( $product->get_id() ),
				esc_html__( 'Consultar por WhatsApp', 'tiendarosso-commerce' )
			);
		} else {
			echo '<span class="tr-whatsapp-unconfigured" role="status">' . esc_html__( 'WhatsApp pendiente de configuración', 'tiendarosso-commerce' ) . '</span>';
		}
		echo '</div>';
	}

	/** Validate only the custom direct-checkout intent; native cart validation remains Woo-owned. */
	public static function validate_buy_now( bool $passed, int $product_id, int $quantity, int $variation_id = 0, array $variations = array(), array $cart_item_data = array() ): bool {
		unset( $quantity, $variation_id, $variations, $cart_item_data );
		if ( empty( $_POST['tr_buy_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return $passed;
		}

		$nonce = isset( $_POST['tr_buy_now_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['tr_buy_now_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'tr_buy_now_' . $product_id ) ) {
			wc_add_notice( __( 'No pudimos validar la compra directa. Actualiza la página e inténtalo nuevamente.', 'tiendarosso-commerce' ), 'error' );
			return false;
		}
		return $passed;
	}

	/** Redirect a validated direct purchase to WooCommerce checkout. */
	public static function buy_now_redirect( string $url ): string {
		if ( ! empty( $_POST['tr_buy_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return wc_get_checkout_url();
		}
		return $url;
	}

	/** Queue a privacy-safe add-to-cart event for the next rendered page. */
	public static function record_add_to_cart( string $cart_item_key, int $product_id, int $quantity, int $variation_id, array $variation, array $cart_item_data ): void {
		unset( $variation, $cart_item_data );
		$product = wc_get_product( $variation_id ?: $product_id );
		if ( ! $product ) {
			return;
		}
		self::queue_event(
			'add_to_cart',
			array(
				'item_id'  => (string) $product->get_id(),
				'sku'      => (string) $product->get_sku(),
				'quantity' => max( 1, $quantity ),
				'value'    => (float) wc_get_price_to_display( $product ) * max( 1, $quantity ),
				'currency' => get_woocommerce_currency(),
				'cart_key' => substr( hash( 'sha256', $cart_item_key ), 0, 16 ),
			)
		);
	}

	/** Queue a privacy-safe remove-from-cart event. */
	public static function record_remove_from_cart( string $cart_item_key, WC_Cart $cart ): void {
		$item = $cart->removed_cart_contents[ $cart_item_key ] ?? array();
		self::queue_event(
			'remove_from_cart',
			array(
				'item_id'  => (string) ( (int) ( $item['variation_id'] ?? 0 ) ?: (int) ( $item['product_id'] ?? 0 ) ),
				'quantity' => (int) ( $item['quantity'] ?? 0 ),
				'currency' => get_woocommerce_currency(),
			)
		);
	}

	/** Return and clear events owned by the current WooCommerce session. */
	public static function consume_events(): array {
		if ( ! function_exists( 'WC' ) || ! WC()->session ) {
			return array();
		}
		$events = WC()->session->get( self::EVENT_SESSION_KEY, array() );
		WC()->session->set( self::EVENT_SESSION_KEY, array() );
		return is_array( $events ) ? array_slice( $events, -20 ) : array();
	}

	/** Render a truthful status summary above the native order confirmation. */
	public static function render_order_state( int $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}
		$is_paid = $order->is_paid();
		printf(
			'<section class="tr-order-state %1$s" aria-labelledby="tr-order-state-title"><p class="tr-kicker">%2$s</p><h2 id="tr-order-state-title">%3$s</h2><p>%4$s</p><dl><div><dt>%5$s</dt><dd>#%6$s</dd></div><div><dt>%7$s</dt><dd>%8$s</dd></div><div><dt>%9$s</dt><dd>%10$s</dd></div></dl></section>',
			esc_attr( $is_paid ? 'is-paid' : 'is-pending' ),
			esc_html__( 'Estado del pedido', 'tiendarosso-commerce' ),
			esc_html( $is_paid ? __( 'Pago confirmado', 'tiendarosso-commerce' ) : __( 'Pedido recibido; pago no confirmado', 'tiendarosso-commerce' ) ),
			esc_html( $is_paid ? __( 'WooCommerce registró una confirmación válida de pago.', 'tiendarosso-commerce' ) : __( 'El pedido existe, pero no se considera pagado ni se prepara hasta recibir una confirmación auténtica.', 'tiendarosso-commerce' ) ),
			esc_html__( 'Pedido', 'tiendarosso-commerce' ),
			esc_html( $order->get_order_number() ),
			esc_html__( 'Estado', 'tiendarosso-commerce' ),
			esc_html( wc_get_order_status_name( $order->get_status() ) ),
			esc_html__( 'Total', 'tiendarosso-commerce' ),
			wp_kses_post( $order->get_formatted_order_total() )
		);
	}

	/** Mark a genuine WooCommerce payment once for future server-side analytics. */
	public static function mark_confirmed_payment( int $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order || ! $order->is_paid() || $order->get_meta( '_tr_purchase_event_id', true ) ) {
			return;
		}
		$order->update_meta_data( '_tr_purchase_event_id', wp_generate_uuid4() );
		$order->update_meta_data( '_tr_purchase_event_recorded_at', gmdate( 'c' ) );
		$order->save();
	}

	/** Preserve a future multivendor owner reference without exposing it publicly. */
	public static function copy_vendor_reference( WC_Order_Item_Product $item, string $cart_item_key, array $values, WC_Order $order ): void {
		unset( $cart_item_key, $order );
		$product_id = (int) ( $values['product_id'] ?? 0 );
		$vendor_id  = absint( get_post_meta( $product_id, '_tr_vendor_id', true ) );
		if ( $vendor_id ) {
			$item->add_meta_data( '_tr_vendor_id', $vendor_id, true );
		}
	}

	/** Store a bounded, non-identifying browser event in the WooCommerce session. */
	private static function queue_event( string $name, array $data ): void {
		if ( ! function_exists( 'WC' ) || ! WC()->session ) {
			return;
		}
		$events   = WC()->session->get( self::EVENT_SESSION_KEY, array() );
		$events   = is_array( $events ) ? $events : array();
		$events[] = array(
			'event'     => sanitize_key( $name ),
			'event_id'  => wp_generate_uuid4(),
			'timestamp' => gmdate( 'c' ),
			'data'      => $data,
		);
		WC()->session->set( self::EVENT_SESSION_KEY, array_slice( $events, -20 ) );
	}
}
