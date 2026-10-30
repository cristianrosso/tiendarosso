<?php
/**
 * Theme bootstrap.
 *
 * @package TiendaRosso
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_editor_style( 'style.css' );
	}
);

add_action(
	'init',
	static function (): void {
		register_block_pattern_category(
			'tiendarosso-commercial',
			array( 'label' => __( 'TIENDA ROSSO comercial', 'tiendarosso' ) )
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style(
			'tiendarosso-style',
			get_stylesheet_uri(),
			array(),
			(string) wp_get_theme()->get( 'Version' )
		);
	}
);
