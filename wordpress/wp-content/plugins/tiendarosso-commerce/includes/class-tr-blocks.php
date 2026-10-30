<?php
/**
 * Dynamic Gutenberg blocks.
 *
 * @package TiendaRossoCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TR_Blocks {
	/** Register hooks. */
	public static function boot(): void {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'editor_assets' ) );
	}

	/** Register server-rendered blocks. */
	public static function register(): void {
		register_block_type( TR_COMMERCE_DIR . 'blocks/service-request-form', array( 'render_callback' => array( __CLASS__, 'render_form' ) ) );
		register_block_type( TR_COMMERCE_DIR . 'blocks/plans-comparison', array( 'render_callback' => array( __CLASS__, 'render_plans' ) ) );
		register_block_type( TR_COMMERCE_DIR . 'blocks/whatsapp-cta', array( 'render_callback' => array( __CLASS__, 'render_whatsapp' ) ) );
	}

	/** Load unbundled editor registrations. */
	public static function editor_assets(): void {
		wp_enqueue_script(
			'tr-commerce-blocks-editor',
			TR_COMMERCE_URL . 'blocks/editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render' ),
			TR_COMMERCE_VERSION,
			true
		);
	}

	/** Render form block. */
	public static function render_form( array $attributes ): string {
		return TR_Forms::render( $attributes );
	}

	/** Render editable plans. */
	public static function render_plans(): string {
		$plans = get_posts(
			array(
				'post_type'      => 'tr_plan',
				'post_status'    => 'publish',
				'posts_per_page' => 12,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			)
		);
		if ( ! $plans ) {
			return '<p>' . esc_html__( 'Los planes están pendientes de configuración.', 'tiendarosso-commerce' ) . '</p>';
		}

		ob_start();
		echo '<div class="tr-plan-grid alignwide">';
		foreach ( $plans as $plan ) {
			$features  = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $plan->ID, '_tr_features', true ) ) ) );
			$price     = get_post_meta( $plan->ID, '_tr_price', true );
			$currency  = get_post_meta( $plan->ID, '_tr_currency', true );
			$frequency = get_post_meta( $plan->ID, '_tr_frequency', true );
			$cta_label = get_post_meta( $plan->ID, '_tr_cta_label', true ) ?: __( 'Solicitar información', 'tiendarosso-commerce' );
			$cta_url   = get_post_meta( $plan->ID, '_tr_cta_url', true ) ?: '#solicitud';
			echo '<article class="tr-plan-card">';
			printf( '<h3>%s</h3>', esc_html( $plan->post_title ) );
			echo '<div class="tr-plan-price">';
			if ( '' !== (string) $price ) {
				echo esc_html( trim( $currency . ' ' . $price . ' ' . $frequency ) );
			} else {
				esc_html_e( 'Precio sujeto a propuesta', 'tiendarosso-commerce' );
			}
			echo '</div>';
			echo wp_kses_post( wpautop( $plan->post_content ) );
			if ( $features ) {
				echo '<ul class="tr-feature-list">';
				foreach ( $features as $feature ) {
					printf( '<li>%s</li>', esc_html( $feature ) );
				}
				echo '</ul>';
			}
			printf( '<a class="wp-element-button" href="%1$s" data-tr-plan="%2$s">%3$s</a>', esc_url( $cta_url ), esc_attr( $plan->post_title ), esc_html( $cta_label ) );
			echo '</article>';
		}
		echo '</div>';
		return (string) ob_get_clean();
	}

	/** Render configurable WhatsApp CTA. */
	public static function render_whatsapp( array $attributes ): string {
		$context = sanitize_key( (string) ( $attributes['context'] ?? 'general' ) );
		$label   = sanitize_text_field( (string) ( $attributes['label'] ?? __( 'Consultar por WhatsApp', 'tiendarosso-commerce' ) ) );
		$plan    = sanitize_text_field( (string) ( $attributes['plan'] ?? __( 'los servicios de TIENDA ROSSO', 'tiendarosso-commerce' ) ) );
		$message = sprintf( 'Hola, quiero información sobre %s de TIENDA ROSSO. Mi negocio se llama...', $plan );
		$url     = TR_Settings::whatsapp_url( $context, $message );

		if ( '' === $url ) {
			return '<span class="tr-whatsapp-unconfigured" role="status">' . esc_html__( 'WhatsApp pendiente de configuración', 'tiendarosso-commerce' ) . '</span>';
		}

		return sprintf(
			'<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="%1$s" target="_blank" rel="noopener noreferrer" data-tr-whatsapp="%2$s" data-tr-context="%3$s">%4$s</a></div>',
			esc_url( $url ),
			esc_attr( $plan ),
			esc_attr( $context ),
			esc_html( $label )
		);
	}
}
