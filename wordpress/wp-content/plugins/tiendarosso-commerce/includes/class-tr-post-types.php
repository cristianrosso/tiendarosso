<?php
/**
 * Plans and private service requests.
 *
 * @package TiendaRossoCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TR_Post_Types {
	/**
	 * Register hooks.
	 */
	public static function boot(): void {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_tr_plan', array( __CLASS__, 'save_plan' ), 10, 2 );
		add_action( 'save_post_tr_service_request', array( __CLASS__, 'save_request_status' ), 10, 2 );
		add_filter( 'manage_tr_service_request_posts_columns', array( __CLASS__, 'request_columns' ) );
		add_action( 'manage_tr_service_request_posts_custom_column', array( __CLASS__, 'render_request_column' ), 10, 2 );
	}

	/**
	 * Register data types and REST-visible plan metadata.
	 */
	public static function register(): void {
		register_post_type(
			'tr_plan',
			array(
				'labels' => array(
					'name'          => __( 'Planes comerciales', 'tiendarosso-commerce' ),
					'singular_name' => __( 'Plan comercial', 'tiendarosso-commerce' ),
					'add_new_item'  => __( 'Añadir plan comercial', 'tiendarosso-commerce' ),
					'edit_item'     => __( 'Editar plan comercial', 'tiendarosso-commerce' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'tiendarosso',
				'show_in_rest'        => true,
				'supports'            => array( 'title', 'editor', 'page-attributes' ),
				'capability_type'     => array( 'tr_plan', 'tr_plans' ),
				'map_meta_cap'        => true,
				'exclude_from_search' => true,
			)
		);

		register_post_type(
			'tr_service_request',
			array(
				'labels' => array(
					'name'          => __( 'Solicitudes comerciales', 'tiendarosso-commerce' ),
					'singular_name' => __( 'Solicitud comercial', 'tiendarosso-commerce' ),
					'edit_item'     => __( 'Revisar solicitud', 'tiendarosso-commerce' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'tiendarosso',
				'show_in_rest'        => false,
				'supports'            => array( 'title' ),
				'capability_type'     => array( 'tr_service_request', 'tr_service_requests' ),
				'map_meta_cap'        => true,
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'exclude_from_search' => true,
			)
		);

		$plan_meta = array(
			'_tr_price'         => 'string',
			'_tr_currency'      => 'string',
			'_tr_frequency'     => 'string',
			'_tr_product_limit' => 'integer',
			'_tr_features'      => 'string',
			'_tr_services'      => 'string',
			'_tr_conditions'    => 'string',
			'_tr_cta_label'     => 'string',
			'_tr_cta_url'       => 'string',
		);
		foreach ( $plan_meta as $key => $type ) {
			register_post_meta(
				'tr_plan',
				$key,
				array(
					'single'            => true,
					'type'              => $type,
					'show_in_rest'      => true,
					'sanitize_callback' => 'integer' === $type
						? 'absint'
						: ( in_array( $key, array( '_tr_features', '_tr_services', '_tr_conditions' ), true ) ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
					'auth_callback'     => static fn (): bool => current_user_can( 'edit_tr_plans' ),
				)
			);
		}
	}

	/**
	 * Register admin-only metadata panels.
	 */
	public static function add_meta_boxes(): void {
		add_meta_box( 'tr-plan-details', __( 'Configuración del plan', 'tiendarosso-commerce' ), array( __CLASS__, 'render_plan_meta_box' ), 'tr_plan', 'normal', 'high' );
		add_meta_box( 'tr-request-details', __( 'Datos y seguimiento', 'tiendarosso-commerce' ), array( __CLASS__, 'render_request_meta_box' ), 'tr_service_request', 'normal', 'high' );
	}

	/**
	 * Render plan controls.
	 *
	 * @param WP_Post $post Current plan.
	 */
	public static function render_plan_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'tr_save_plan', 'tr_plan_nonce' );
		$fields = array(
			'_tr_price'         => array( 'Precio', 'text' ),
			'_tr_currency'      => array( 'Moneda', 'text' ),
			'_tr_frequency'     => array( 'Frecuencia de pago', 'text' ),
			'_tr_product_limit' => array( 'Límite de productos', 'number' ),
			'_tr_features'      => array( 'Funcionalidades, una por línea', 'textarea' ),
			'_tr_services'      => array( 'Servicios incluidos, uno por línea', 'textarea' ),
			'_tr_conditions'    => array( 'Condiciones', 'textarea' ),
			'_tr_cta_label'     => array( 'Texto del CTA', 'text' ),
			'_tr_cta_url'       => array( 'Destino del CTA', 'url' ),
		);
		foreach ( $fields as $key => $definition ) {
			$value = get_post_meta( $post->ID, $key, true );
			printf( '<p><label for="%1$s"><strong>%2$s</strong></label><br>', esc_attr( $key ), esc_html( $definition[0] ) );
			if ( 'textarea' === $definition[1] ) {
				printf( '<textarea class="widefat" rows="5" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $key ), esc_textarea( $value ) );
			} else {
				printf( '<input class="widefat" type="%1$s" id="%2$s" name="%2$s" value="%3$s">', esc_attr( $definition[1] ), esc_attr( $key ), esc_attr( (string) $value ) );
			}
			echo '</p>';
		}
		echo '<p><em>' . esc_html__( 'El precio puede quedar vacío hasta su aprobación. El orden se controla desde Atributos de página.', 'tiendarosso-commerce' ) . '</em></p>';
	}

	/**
	 * Save validated plan metadata and append a bounded audit record.
	 *
	 * @param int     $post_id Plan ID.
	 * @param WP_Post $post Plan object.
	 */
	public static function save_plan( int $post_id, WP_Post $post ): void {
		if ( ! isset( $_POST['tr_plan_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tr_plan_nonce'] ) ), 'tr_save_plan' ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_tr_plan', $post_id ) ) {
			return;
		}

		$fields = array( '_tr_price', '_tr_currency', '_tr_frequency', '_tr_product_limit', '_tr_features', '_tr_services', '_tr_conditions', '_tr_cta_label', '_tr_cta_url' );
		$changes = array();
		foreach ( $fields as $key ) {
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
			if ( '_tr_product_limit' === $key ) {
				$value = absint( $raw );
			} elseif ( '_tr_cta_url' === $key ) {
				$value = esc_url_raw( $raw );
			} elseif ( in_array( $key, array( '_tr_features', '_tr_services', '_tr_conditions' ), true ) ) {
				$value = sanitize_textarea_field( $raw );
			} else {
				$value = sanitize_text_field( $raw );
			}
			$old = get_post_meta( $post_id, $key, true );
			if ( (string) $old !== (string) $value ) {
				$changes[] = $key;
			}
			update_post_meta( $post_id, $key, $value );
		}

		if ( $changes ) {
			$log   = get_post_meta( $post_id, '_tr_change_log', true );
			$log   = is_array( $log ) ? $log : array();
			$log[] = array( 'at' => current_time( 'mysql', true ), 'user' => get_current_user_id(), 'fields' => $changes );
			update_post_meta( $post_id, '_tr_change_log', array_slice( $log, -100 ) );
		}
	}

	/**
	 * Render private request details.
	 *
	 * @param WP_Post $post Request.
	 */
	public static function render_request_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'tr_save_request_status', 'tr_request_nonce' );
		$labels = array(
			'_tr_applicant_name'    => 'Solicitante',
			'_tr_business_name'     => 'Negocio',
			'_tr_sector'            => 'Rubro',
			'_tr_city'              => 'Ciudad',
			'_tr_whatsapp'          => 'WhatsApp',
			'_tr_email'             => 'Correo',
			'_tr_plan'              => 'Plan',
			'_tr_service_type'      => 'Servicio',
			'_tr_product_count'     => 'Cantidad aproximada',
			'_tr_message'           => 'Mensaje',
			'_tr_origin'            => 'Origen',
			'_tr_utm_source'        => 'UTM source',
			'_tr_utm_medium'        => 'UTM medium',
			'_tr_utm_campaign'      => 'UTM campaign',
			'_tr_utm_content'       => 'UTM content',
			'_tr_utm_term'          => 'UTM term',
			'_tr_campaign_ref'      => 'Referencia de campaña',
			'_tr_marketing_consent' => 'Consentimiento comercial',
			'_tr_created_at'        => 'Registrado (UTC)',
		);
		echo '<table class="widefat striped"><tbody>';
		foreach ( $labels as $key => $label ) {
			$value = get_post_meta( $post->ID, $key, true );
			printf( '<tr><th style="width:220px">%1$s</th><td>%2$s</td></tr>', esc_html( $label ), nl2br( esc_html( (string) $value ) ) );
		}
		echo '</tbody></table>';

		$status = get_post_meta( $post->ID, '_tr_followup_status', true ) ?: 'nuevo';
		echo '<p><label for="tr_followup_status"><strong>' . esc_html__( 'Estado comercial', 'tiendarosso-commerce' ) . '</strong></label><br><select id="tr_followup_status" name="tr_followup_status">';
		foreach ( self::followup_statuses() as $key => $label ) {
			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $status, $key, false ), esc_html( $label ) );
		}
		echo '</select></p>';
	}

	/**
	 * Save status changes with an audit record.
	 *
	 * @param int     $post_id Request ID.
	 * @param WP_Post $post Request.
	 */
	public static function save_request_status( int $post_id, WP_Post $post ): void {
		if ( ! isset( $_POST['tr_request_nonce'], $_POST['tr_followup_status'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tr_request_nonce'] ) ), 'tr_save_request_status' ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_tr_service_request', $post_id ) ) {
			return;
		}
		$status = sanitize_key( wp_unslash( $_POST['tr_followup_status'] ) );
		if ( ! isset( self::followup_statuses()[ $status ] ) ) {
			return;
		}
		$old = get_post_meta( $post_id, '_tr_followup_status', true ) ?: 'nuevo';
		update_post_meta( $post_id, '_tr_followup_status', $status );
		if ( $old !== $status ) {
			$log   = get_post_meta( $post_id, '_tr_status_log', true );
			$log   = is_array( $log ) ? $log : array();
			$log[] = array( 'at' => current_time( 'mysql', true ), 'user' => get_current_user_id(), 'from' => $old, 'to' => $status );
			update_post_meta( $post_id, '_tr_status_log', array_slice( $log, -100 ) );
		}
	}

	/**
	 * Commercial states.
	 *
	 * @return array<string,string>
	 */
	public static function followup_statuses(): array {
		return array(
			'nuevo'             => 'Nuevo',
			'contactado'        => 'Contactado',
			'calificado'        => 'Calificado',
			'propuesta-enviada' => 'Propuesta enviada',
			'contratado'        => 'Contratado',
			'perdido'           => 'Perdido',
		);
	}

	/**
	 * Admin columns.
	 *
	 * @param array<string,string> $columns Existing columns.
	 * @return array<string,string>
	 */
	public static function request_columns( array $columns ): array {
		return array(
			'cb'       => $columns['cb'],
			'title'    => __( 'Solicitud', 'tiendarosso-commerce' ),
			'business' => __( 'Negocio', 'tiendarosso-commerce' ),
			'plan'     => __( 'Plan', 'tiendarosso-commerce' ),
			'status'   => __( 'Estado', 'tiendarosso-commerce' ),
			'date'     => $columns['date'],
		);
	}

	/**
	 * Render custom column values.
	 *
	 * @param string $column Column.
	 * @param int    $post_id Request ID.
	 */
	public static function render_request_column( string $column, int $post_id ): void {
		$keys = array( 'business' => '_tr_business_name', 'plan' => '_tr_plan', 'status' => '_tr_followup_status' );
		if ( isset( $keys[ $column ] ) ) {
			echo esc_html( (string) get_post_meta( $post_id, $keys[ $column ], true ) );
		}
	}
}
