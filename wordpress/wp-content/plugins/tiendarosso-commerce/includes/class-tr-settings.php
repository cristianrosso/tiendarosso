<?php
/**
 * Configurable WhatsApp destinations and commercial settings.
 *
 * @package TiendaRossoCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TR_Settings {
	private const OPTION = 'tr_commerce_options';

	/** Register hooks. */
	public static function boot(): void {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_page' ), 20 );
	}

	/** Register typed settings. */
	public static function register_settings(): void {
		register_setting(
			'tr_commerce_settings',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => array(),
			)
		);

		add_settings_section( 'tr_whatsapp', __( 'WhatsApp comercial', 'tiendarosso-commerce' ), array( __CLASS__, 'render_whatsapp_intro' ), 'tr-commerce-settings' );
		foreach ( self::channels() as $key => $label ) {
			add_settings_field(
				'whatsapp_' . $key,
				$label,
				array( __CLASS__, 'render_phone_field' ),
				'tr-commerce-settings',
				'tr_whatsapp',
				array( 'key' => $key, 'label_for' => 'tr-whatsapp-' . $key )
			);
		}
	}

	/** Register settings submenu. */
	public static function register_page(): void {
		add_submenu_page(
			'tiendarosso',
			__( 'Configuración comercial', 'tiendarosso-commerce' ),
			__( 'Configuración', 'tiendarosso-commerce' ),
			'manage_tr_settings',
			'tr-commerce-settings',
			array( __CLASS__, 'render_page' )
		);
	}

	/** Sanitize all supported settings. */
	public static function sanitize( mixed $input ): array {
		$input = is_array( $input ) ? $input : array();
		$out   = array();
		foreach ( array_keys( self::channels() ) as $channel ) {
			$value = isset( $input['whatsapp'][ $channel ] ) ? (string) $input['whatsapp'][ $channel ] : '';
			$out['whatsapp'][ $channel ] = preg_replace( '/[^0-9]/', '', $value );
		}
		return $out;
	}

	/** Render page. */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_tr_settings' ) ) {
			return;
		}
		?>
		<div class="wrap"><h1><?php esc_html_e( 'Configuración comercial', 'tiendarosso-commerce' ); ?></h1>
		<form action="options.php" method="post"><?php settings_fields( 'tr_commerce_settings' ); do_settings_sections( 'tr-commerce-settings' ); submit_button(); ?></form></div>
		<?php
	}

	/** Explain phone storage. */
	public static function render_whatsapp_intro(): void {
		echo '<p>' . esc_html__( 'Introduce números en formato internacional, solo con código de país y dígitos. Ningún número se incluye por defecto.', 'tiendarosso-commerce' ) . '</p>';
	}

	/** Render one number field. */
	public static function render_phone_field( array $args ): void {
		$key     = sanitize_key( $args['key'] ?? 'general' );
		$options = get_option( self::OPTION, array() );
		$value   = $options['whatsapp'][ $key ] ?? '';
		printf(
			'<input id="tr-whatsapp-%2$s" class="regular-text" inputmode="numeric" autocomplete="off" name="%1$s[whatsapp][%2$s]" value="%3$s" placeholder="59170000000"><p class="description">%4$s</p>',
			esc_attr( self::OPTION ),
			esc_attr( $key ),
			esc_attr( $value ),
			esc_html__( 'La URL pública contiene únicamente el número y un mensaje contextual sin datos personales.', 'tiendarosso-commerce' )
		);
	}

	/** Return a configured phone for a channel, falling back to general. */
	public static function phone( string $channel = 'general' ): string {
		$options = get_option( self::OPTION, array() );
		$phones  = $options['whatsapp'] ?? array();
		return (string) ( $phones[ $channel ] ?? $phones['general'] ?? '' );
	}

	/** Build a privacy-safe wa.me URL. */
	public static function whatsapp_url( string $channel, string $message ): string {
		$phone = self::phone( $channel );
		if ( '' === $phone ) {
			return '';
		}
		return 'https://wa.me/' . rawurlencode( $phone ) . '?text=' . rawurlencode( $message );
	}

	/** Available routing channels. */
	public static function channels(): array {
		return array(
			'general'   => __( 'Número general', 'tiendarosso-commerce' ),
			'plans'     => __( 'Planes y espacios comerciales', 'tiendarosso-commerce' ),
			'marketing' => __( 'Servicios de marketing', 'tiendarosso-commerce' ),
		);
	}
}
