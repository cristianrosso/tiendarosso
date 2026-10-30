<?php
/**
 * Main plugin coordinator.
 *
 * @package TiendaRossoCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TR_Commerce {
	/**
	 * Boot runtime services.
	 */
	public static function boot(): void {
		TR_Roles::boot();
		TR_Post_Types::boot();
		TR_Settings::boot();
		TR_Forms::boot();
		TR_Blocks::boot();
		TR_Checkout::boot();

		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_public_assets' ) );
	}

	/**
	 * Set up roles, data types and editable starter content.
	 */
	public static function activate(): void {
		TR_Roles::install();
		TR_Post_Types::register();
		TR_Checkout::maybe_upgrade();
		self::seed_plans();
		self::seed_seller_page();
		flush_rewrite_rules();
	}

	/**
	 * Register the commercial admin area.
	 */
	public static function register_menu(): void {
		add_menu_page(
			__( 'TIENDA ROSSO', 'tiendarosso-commerce' ),
			__( 'TIENDA ROSSO', 'tiendarosso-commerce' ),
			'edit_tr_service_requests',
			'tiendarosso',
			array( __CLASS__, 'render_dashboard' ),
			'dashicons-store',
			56
		);
	}

	/**
	 * Render an honest admin summary without fabricated metrics.
	 */
	public static function render_dashboard(): void {
		$request_counts = wp_count_posts( 'tr_service_request' );
		$plan_counts    = wp_count_posts( 'tr_plan' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'TIENDA ROSSO — Panel comercial', 'tiendarosso-commerce' ); ?></h1>
			<p><?php esc_html_e( 'Los indicadores muestran únicamente registros reales de este WordPress. No se generan métricas de ejemplo.', 'tiendarosso-commerce' ); ?></p>
			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;max-width:900px;margin-top:24px">
				<div class="card"><h2><?php esc_html_e( 'Solicitudes registradas', 'tiendarosso-commerce' ); ?></h2><p style="font-size:32px;margin:8px 0"><?php echo esc_html( (string) ( $request_counts->private ?? 0 ) ); ?></p></div>
				<div class="card"><h2><?php esc_html_e( 'Planes publicados', 'tiendarosso-commerce' ); ?></h2><p style="font-size:32px;margin:8px 0"><?php echo esc_html( (string) ( $plan_counts->publish ?? 0 ) ); ?></p></div>
				<div class="card"><h2><?php esc_html_e( 'Marketplace', 'tiendarosso-commerce' ); ?></h2><p><?php esc_html_e( 'Motor multivendedor pendiente de selección y aprobación.', 'tiendarosso-commerce' ); ?></p></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Load the public form and analytics adapter.
	 */
	public static function enqueue_public_assets(): void {
		wp_enqueue_style( 'tr-commerce-public', TR_COMMERCE_URL . 'assets/public.css', array(), TR_COMMERCE_VERSION );
		wp_enqueue_script( 'tr-commerce-public', TR_COMMERCE_URL . 'assets/public.js', array(), TR_COMMERCE_VERSION, true );

		$config = array(
			'currency' => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'BOB',
			'page'     => 'content',
			'events'   => class_exists( 'WooCommerce' ) ? TR_Checkout::consume_events() : array(),
		);
		if ( function_exists( 'is_product' ) && is_product() ) {
			global $product;
			$config['page'] = 'product';
			if ( $product instanceof WC_Product ) {
				$config['item'] = array(
					'item_id'  => (string) $product->get_id(),
					'sku'      => (string) $product->get_sku(),
					'value'    => (float) wc_get_price_to_display( $product ),
					'currency' => get_woocommerce_currency(),
				);
			}
		} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
			$config['page'] = 'cart';
		} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$config['page'] = function_exists( 'is_order_received_page' ) && is_order_received_page() ? 'order_received' : 'checkout';
		}
		wp_localize_script( 'tr-commerce-public', 'trCommerceConfig', $config );
	}

	/**
	 * Seed editable commercial plans without prices.
	 */
	private static function seed_plans(): void {
		$plans = array(
			array(
				'title'       => 'Plan Vitrina',
				'description' => 'Presencia comercial para comenzar a mostrar el negocio y su catálogo.',
				'features'    => "Espacio comercial dentro del marketplace\nPerfil del negocio\nCatálogo básico\nAutogestión\nContacto por WhatsApp\nEstadísticas básicas",
			),
			array(
				'title'       => 'Plan Negocio',
				'description' => 'Una propuesta ampliada para captar oportunidades y presentar mejor la oferta.',
				'features'    => "Todo lo incluido en Plan Vitrina\nCatálogo ampliado\nLanding comercial personalizada\nProductos destacados según condiciones\nFormularios de captación\nEstadísticas avanzadas\nOpciones de campañas adicionales",
			),
			array(
				'title'       => 'Plan Growth IA',
				'description' => 'Acompañamiento comercial y optimización asistida, siempre con aprobación humana.',
				'features'    => "Todo lo incluido en Plan Negocio\nEmbudos de ventas\nGestión de campañas según contrato\nRemarketing\nOptimización comercial asistida por IA\nReportes de conversiones\nRecomendaciones de mejora",
			),
		);

		foreach ( $plans as $index => $plan ) {
			$existing = get_page_by_title( $plan['title'], OBJECT, 'tr_plan' );
			if ( $existing ) {
				continue;
			}
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'tr_plan',
					'post_status'  => 'publish',
					'post_title'   => $plan['title'],
					'post_content' => $plan['description'],
					'menu_order'   => $index + 1,
				)
			);
			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_tr_features', $plan['features'] );
				update_post_meta( $post_id, '_tr_cta_label', 'Solicitar información' );
				update_post_meta( $post_id, '_tr_cta_url', '#solicitud' );
			}
		}
	}

	/**
	 * Seed a Gutenberg-editable seller landing.
	 */
	private static function seed_seller_page(): void {
		$page = get_page_by_path( 'vende-con-nosotros' );
		if ( $page ) {
			return;
		}

		$content = self::seller_page_blocks();
		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Vende con nosotros',
				'post_name'    => 'vende-con-nosotros',
				'post_content' => $content,
			)
		);
	}

	/**
	 * Return the editable block composition used on first install.
	 */
	private static function seller_page_blocks(): string {
		return <<<'BLOCKS'
<!-- wp:group {"align":"full","className":"tr-hero","style":{"spacing":{"padding":{"top":"96px","bottom":"96px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull tr-hero" style="padding-top:96px;padding-right:24px;padding-bottom:96px;padding-left:24px"><!-- wp:columns {"align":"wide","verticalAlignment":"center"} --><div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"tr-kicker"} --><p class="tr-kicker">Un espacio para crecer</p><!-- /wp:paragraph --><!-- wp:heading {"level":1,"fontSize":"x-large"} --><h1 class="wp-block-heading has-x-large-font-size">Tu negocio merece <span class="tr-gradient-text">vender más.</span></h1><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">Publica tus productos, llega a nuevos clientes y potencia tus ventas con tecnología, publicidad digital e inteligencia artificial.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#solicitud">Solicitar mi espacio</a></div><!-- /wp:button --><!-- wp:tiendarosso/whatsapp-cta {"context":"plans","label":"Consultar por WhatsApp","plan":"un espacio comercial"} /--></div><!-- /wp:buttons --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"tr-mockup","layout":{"type":"constrained"}} --><div class="wp-block-group tr-mockup"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Tu tienda individual</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Catálogo, captación y seguimiento en una experiencia coherente. Visual conceptual sin métricas ficticias.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:80px;padding-right:24px;padding-bottom:80px;padding-left:24px"><!-- wp:heading {"align":"wide","fontSize":"x-large"} --><h2 class="wp-block-heading alignwide has-x-large-font-size">No basta con publicar. Hay que construir una ruta para vender.</h2><!-- /wp:heading --><!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Visibilidad organizada</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Un catálogo claro y una presentación enfocada en la oferta real del negocio.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Seguimiento comercial</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Las solicitudes se registran de forma privada para responderlas y darles estado.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Marketing conectado</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Catálogo, landing, contacto y medición preparados como un mismo recorrido.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"tr-section-dark","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull tr-section-dark" style="padding-top:88px;padding-right:24px;padding-bottom:88px;padding-left:24px"><!-- wp:heading {"align":"wide","fontSize":"x-large"} --><h2 class="wp-block-heading alignwide has-x-large-font-size">Catálogo, captación y mejora continua.</h2><!-- /wp:heading --><!-- wp:list {"className":"tr-feature-list"} --><ul class="tr-feature-list"><li>Presencia dentro de un marketplace organizado</li><li>Tienda o landing individual según el plan</li><li>Contacto y solicitudes con consentimiento</li><li>Campañas solo cuando estén contratadas y configuradas</li><li>Optimización asistida por IA con revisión humana</li></ul><!-- /wp:list --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","anchor":"planes","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" id="planes" style="padding-top:88px;padding-right:24px;padding-bottom:88px;padding-left:24px"><!-- wp:heading {"align":"wide","fontSize":"x-large"} --><h2 class="wp-block-heading alignwide has-x-large-font-size">Elige una forma de crecer, sin precios inventados.</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"wide"} --><p class="alignwide">Precios, límites y condiciones se administran desde WordPress. El presupuesto publicitario siempre se informa por separado.</p><!-- /wp:paragraph --><!-- wp:tiendarosso/plans-comparison /--></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"24px","right":"24px"}},"color":{"background":"#eeeff6"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#eeeff6;padding-top:80px;padding-right:24px;padding-bottom:80px;padding-left:24px"><!-- wp:heading {"align":"wide","fontSize":"x-large"} --><h2 class="wp-block-heading alignwide has-x-large-font-size">Un proceso supervisado de principio a fin.</h2><!-- /wp:heading --><!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">1. Solicitud</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Conocemos el negocio y el servicio que necesita.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">2. Revisión</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Validamos alcance, catálogo y responsabilidades.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">3. Propuesta</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Documentamos precio, vigencia y condiciones.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">4. Activación</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Publicamos después de la aprobación correspondiente.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","anchor":"solicitud","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" id="solicitud" style="padding-top:88px;padding-right:24px;padding-bottom:88px;padding-left:24px"><!-- wp:columns {"align":"wide","verticalAlignment":"top"} --><div class="wp-block-columns alignwide are-vertically-aligned-top"><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph {"className":"tr-kicker"} --><p class="tr-kicker">Hablemos de tu negocio</p><!-- /wp:paragraph --><!-- wp:heading {"fontSize":"x-large"} --><h2 class="wp-block-heading has-x-large-font-size">Solicita tu espacio comercial.</h2><!-- /wp:heading --><!-- wp:paragraph --><p>La solicitud se registra con acceso restringido. Las comunicaciones comerciales son opcionales y tienen un consentimiento separado.</p><!-- /wp:paragraph --><!-- wp:tiendarosso/whatsapp-cta {"context":"plans","label":"Consultar por WhatsApp","plan":"los planes para negocios"} /--></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:tiendarosso/service-request-form /--></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
BLOCKS;
	}

	/**
	 * Previous seed retained for reference while installed pages remain untouched.
	 */
	private static function seller_page_blocks_legacy(): string {
		return <<<'BLOCKS'
<!-- wp:group {"align":"full","className":"tr-hero","style":{"spacing":{"padding":{"top":"96px","bottom":"96px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull tr-hero" style="padding:96px 24px"><!-- wp:columns {"align":"wide","verticalAlignment":"center"} --><div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"tr-kicker"} --><p class="tr-kicker">Un espacio para crecer</p><!-- /wp:paragraph --><!-- wp:heading {"level":1,"fontSize":"x-large"} --><h1 class="wp-block-heading has-x-large-font-size">Tu negocio merece <span class="tr-gradient-text">vender más.</span></h1><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">Publica tus productos, llega a nuevos clientes y potencia tus ventas con tecnología, publicidad digital e inteligencia artificial.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#solicitud">Solicitar mi espacio</a></div><!-- /wp:button --><!-- wp:tiendarosso/whatsapp-cta {"context":"plans","label":"Consultar por WhatsApp","plan":"un espacio comercial"} /--></div><!-- /wp:buttons --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"tr-mockup","layout":{"type":"constrained"}} --><div class="wp-block-group tr-mockup"><!-- wp:html --><div class="tr-mockup-window"><strong>Tienda individual · visual conceptual</strong><div class="tr-mockup-row"><span class="tr-mockup-box"></span><span class="tr-mockup-box"></span></div><div class="tr-mockup-row"><span class="tr-mockup-box"></span><span class="tr-mockup-box"></span><span class="tr-mockup-box"></span></div></div><!-- /wp:html --></div><!-- /wp:group --></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} --><div class="wp-block-group alignfull" style="padding:80px 24px"><!-- wp:heading {"align":"wide","fontSize":"x-large"} --><h2 class="wp-block-heading alignwide has-x-large-font-size">No basta con publicar. Hay que construir una ruta para vender.</h2><!-- /wp:heading --><!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide"><!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"tr-card","style":{"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"}}}} --><div class="wp-block-group tr-card" style="padding:32px"><!-- wp:paragraph {"className":"tr-number"} --><p class="tr-number">01</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Visibilidad fragmentada</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Muchos negocios dependen de publicaciones aisladas sin un catálogo ordenado ni una página enfocada en convertir.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"tr-card","style":{"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"}}}} --><div class="wp-block-group tr-card" style="padding:32px"><!-- wp:paragraph {"className":"tr-number"} --><p class="tr-number">02</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Seguimiento manual</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Las consultas pueden perderse cuando no existe un flujo claro para registrarlas, responderlas y medirlas.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"tr-card tr-card-dark","style":{"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"}}}} --><div class="wp-block-group tr-card tr-card-dark" style="padding:32px"><!-- wp:paragraph {"className":"tr-number"} --><p class="tr-number">03</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Marketing sin conexión</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Una campaña rinde mejor cuando catálogo, landing, contacto y medición forman parte del mismo recorrido.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"tr-section-dark","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} --><div class="wp-block-group alignfull tr-section-dark" style="padding:88px 24px"><!-- wp:columns {"align":"wide","verticalAlignment":"center"} --><div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph {"className":"tr-kicker"} --><p class="tr-kicker">Una propuesta comercial completa</p><!-- /wp:paragraph --><!-- wp:heading {"fontSize":"x-large"} --><h2 class="wp-block-heading has-x-large-font-size">Catálogo, captación y mejora continua.</h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:list {"className":"tr-feature-list"} --><ul class="tr-feature-list"><li>Presencia dentro de un marketplace organizado</li><li>Tienda o landing individual según el plan</li><li>Contacto y solicitudes registradas con consentimiento</li><li>Campañas y remarketing únicamente cuando estén contratados y configurados</li><li>Optimización asistida por IA con revisión humana</li></ul><!-- /wp:list --></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","anchor":"planes","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} --><div id="planes" class="wp-block-group alignfull" style="padding:88px 24px"><!-- wp:heading {"align":"wide","fontSize":"x-large"} --><h2 class="wp-block-heading alignwide has-x-large-font-size">Elige una forma de crecer, sin precios inventados.</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"wide"} --><p class="alignwide">Los precios, límites y condiciones se administran desde WordPress. El presupuesto publicitario siempre se informa por separado del servicio.</p><!-- /wp:paragraph --><!-- wp:tiendarosso/plans-comparison /--></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"24px","right":"24px"}},"color":{"background":"#eeeff6"}},"layout":{"type":"constrained"}} --><div class="wp-block-group alignfull has-background" style="background-color:#eeeff6;padding:80px 24px"><!-- wp:heading {"align":"wide","fontSize":"x-large"} --><h2 class="wp-block-heading alignwide has-x-large-font-size">Un proceso supervisado de principio a fin.</h2><!-- /wp:heading --><!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide"><!-- wp:column --><div class="wp-block-column"><h3>1. Solicitud</h3><p>Conocemos el negocio, su rubro y el servicio que necesita.</p></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><h3>2. Revisión</h3><p>Validamos alcance, catálogo, condiciones y responsabilidades.</p></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><h3>3. Propuesta</h3><p>Presentamos precio, vigencia y presupuesto publicitario por separado.</p></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><h3>4. Activación</h3><p>La publicación comienza únicamente después de la aprobación correspondiente.</p></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","anchor":"solicitud","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} --><div id="solicitud" class="wp-block-group alignfull" style="padding:88px 24px"><!-- wp:columns {"align":"wide","verticalAlignment":"top"} --><div class="wp-block-columns alignwide are-vertically-aligned-top"><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph {"className":"tr-kicker"} --><p class="tr-kicker">Hablemos de tu negocio</p><!-- /wp:paragraph --><!-- wp:heading {"fontSize":"x-large"} --><h2 class="wp-block-heading has-x-large-font-size">Solicita tu espacio comercial.</h2><!-- /wp:heading --><!-- wp:paragraph --><p>La solicitud se registra en WordPress con acceso restringido. Aceptar comunicaciones comerciales es opcional y está separado del consentimiento necesario para responder.</p><!-- /wp:paragraph --><!-- wp:tiendarosso/whatsapp-cta {"context":"plans","label":"Consultar por WhatsApp","plan":"los planes para negocios"} /--></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:tiendarosso/service-request-form /--></div><!-- /wp:column --></div><!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"tr-section-dark","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained"}} --><div class="wp-block-group alignfull tr-section-dark" style="padding:88px 24px"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} --><h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Publicar es el inicio. Vender mejor es el objetivo.</h2><!-- /wp:heading --><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#solicitud">Solicitar mi espacio</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div>
<!-- /wp:group -->
BLOCKS;
	}
}
