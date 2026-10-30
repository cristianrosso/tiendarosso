<?php
/**
 * Consent-aware service request form.
 *
 * @package TiendaRossoCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TR_Forms {
	/** Register submission endpoints. */
	public static function boot(): void {
		add_action( 'admin_post_nopriv_tr_submit_service_request', array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_tr_submit_service_request', array( __CLASS__, 'handle' ) );
	}

	/**
	 * Render the public form.
	 *
	 * @param array<string,mixed> $attributes Block attributes.
	 */
	public static function render( array $attributes = array() ): string {
		$selected_plan = sanitize_text_field( (string) ( $attributes['selectedPlan'] ?? '' ) );
		$state         = isset( $_GET['tr_form'] ) ? sanitize_key( wp_unslash( $_GET['tr_form'] ) ) : '';
		$plans         = get_posts(
			array(
				'post_type'      => 'tr_plan',
				'post_status'    => 'publish',
				'posts_per_page' => 20,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			)
		);

		ob_start();
		?>
		<div class="tr-form-shell" style="padding:32px">
			<?php if ( 'success' === $state ) : ?><div class="tr-form-status tr-form-status-success" role="status"><?php esc_html_e( 'Solicitud registrada. El equipo comercial podrá revisarla desde WordPress.', 'tiendarosso-commerce' ); ?></div><?php endif; ?>
			<?php if ( 'error' === $state ) : ?><div class="tr-form-status tr-form-status-error" role="alert"><?php esc_html_e( 'No pudimos registrar la solicitud. Revisa los campos e inténtalo nuevamente.', 'tiendarosso-commerce' ); ?></div><?php endif; ?>
			<form class="tr-service-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-tr-service-form>
				<input type="hidden" name="action" value="tr_submit_service_request">
				<?php wp_nonce_field( 'tr_service_request', 'tr_nonce' ); ?>
				<div class="tr-honeypot" aria-hidden="true"><label for="tr_website">Website</label><input id="tr_website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
				<input type="hidden" name="origin" value=""><input type="hidden" name="utm_source" value=""><input type="hidden" name="utm_medium" value=""><input type="hidden" name="utm_campaign" value=""><input type="hidden" name="utm_content" value=""><input type="hidden" name="utm_term" value=""><input type="hidden" name="campaign_ref" value="">
				<div class="tr-form-grid">
					<div class="tr-field"><label for="tr_name"><?php esc_html_e( 'Nombre del solicitante *', 'tiendarosso-commerce' ); ?></label><input id="tr_name" name="applicant_name" type="text" autocomplete="name" maxlength="120" required></div>
					<div class="tr-field"><label for="tr_business"><?php esc_html_e( 'Nombre del negocio *', 'tiendarosso-commerce' ); ?></label><input id="tr_business" name="business_name" type="text" autocomplete="organization" maxlength="160" required></div>
					<div class="tr-field"><label for="tr_sector"><?php esc_html_e( 'Rubro *', 'tiendarosso-commerce' ); ?></label><input id="tr_sector" name="sector" type="text" maxlength="120" required></div>
					<div class="tr-field"><label for="tr_city"><?php esc_html_e( 'Ciudad *', 'tiendarosso-commerce' ); ?></label><input id="tr_city" name="city" type="text" autocomplete="address-level2" maxlength="120" required></div>
					<div class="tr-field"><label for="tr_whatsapp"><?php esc_html_e( 'WhatsApp *', 'tiendarosso-commerce' ); ?></label><input id="tr_whatsapp" name="whatsapp" type="tel" autocomplete="tel" maxlength="30" pattern="[+0-9 ()-]{7,30}" required></div>
					<div class="tr-field"><label for="tr_email"><?php esc_html_e( 'Correo electrónico *', 'tiendarosso-commerce' ); ?></label><input id="tr_email" name="email" type="email" autocomplete="email" maxlength="190" required></div>
					<div class="tr-field"><label for="tr_plan"><?php esc_html_e( 'Plan de interés *', 'tiendarosso-commerce' ); ?></label><select id="tr_plan" name="plan" required><option value=""><?php esc_html_e( 'Selecciona un plan', 'tiendarosso-commerce' ); ?></option><?php foreach ( $plans as $plan ) : ?><option value="<?php echo esc_attr( $plan->post_title ); ?>" <?php selected( $selected_plan, $plan->post_title ); ?>><?php echo esc_html( $plan->post_title ); ?></option><?php endforeach; ?><option value="Por definir"><?php esc_html_e( 'Necesito orientación', 'tiendarosso-commerce' ); ?></option></select></div>
					<div class="tr-field"><label for="tr_service"><?php esc_html_e( 'Tipo de servicio *', 'tiendarosso-commerce' ); ?></label><select id="tr_service" name="service_type" required><option value=""><?php esc_html_e( 'Selecciona un servicio', 'tiendarosso-commerce' ); ?></option><option value="Espacio comercial">Espacio comercial</option><option value="Catálogo administrado">Catálogo administrado</option><option value="Landing comercial">Landing comercial</option><option value="Marketing digital">Marketing digital</option><option value="Optimización con IA">Optimización con IA</option></select></div>
					<div class="tr-field"><label for="tr_products"><?php esc_html_e( 'Cantidad aproximada de productos', 'tiendarosso-commerce' ); ?></label><input id="tr_products" name="product_count" type="number" min="0" max="100000" inputmode="numeric"></div>
					<div class="tr-field tr-field-wide"><label for="tr_message"><?php esc_html_e( 'Mensaje', 'tiendarosso-commerce' ); ?></label><textarea id="tr_message" name="message" maxlength="2000" placeholder="Cuéntanos qué vendes y qué quieres mejorar."></textarea></div>
					<label class="tr-check tr-field-wide"><input name="data_consent" type="checkbox" value="1" required><span><?php esc_html_e( 'Acepto el tratamiento de mis datos para responder esta solicitud. *', 'tiendarosso-commerce' ); ?></span></label>
					<label class="tr-check tr-field-wide"><input name="marketing_consent" type="checkbox" value="1"><span><?php esc_html_e( 'Acepto recibir comunicaciones comerciales. Esta autorización es opcional y puede revocarse.', 'tiendarosso-commerce' ); ?></span></label>
					<div class="tr-field-wide"><button class="wp-element-button" type="submit"><?php esc_html_e( 'Enviar solicitud', 'tiendarosso-commerce' ); ?></button></div>
				</div>
			</form>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Validate and persist a private request.
	 */
	public static function handle(): void {
		$redirect = wp_validate_redirect( wp_get_referer(), home_url( '/vende-con-nosotros/#solicitud' ) );
		$redirect = remove_query_arg( 'tr_form', $redirect );

		if ( ! isset( $_POST['tr_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tr_nonce'] ) ), 'tr_service_request' ) ) {
			self::redirect( $redirect, 'error' );
		}
		if ( ! empty( $_POST['website'] ) ) {
			self::redirect( $redirect, 'success' );
		}

		$rate_key = 'tr_form_' . hash_hmac( 'sha256', self::client_address(), wp_salt( 'nonce' ) );
		if ( get_transient( $rate_key ) ) {
			self::redirect( $redirect, 'error' );
		}

		$required = array( 'applicant_name', 'business_name', 'sector', 'city', 'whatsapp', 'email', 'plan', 'service_type', 'data_consent' );
		foreach ( $required as $field ) {
			if ( empty( $_POST[ $field ] ) ) {
				self::redirect( $redirect, 'error' );
			}
		}

		$email = sanitize_email( wp_unslash( $_POST['email'] ) );
		if ( ! is_email( $email ) ) {
			self::redirect( $redirect, 'error' );
		}

		$phone = preg_replace( '/[^0-9+() -]/', '', sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) );
		if ( strlen( preg_replace( '/\D/', '', $phone ) ) < 7 ) {
			self::redirect( $redirect, 'error' );
		}

		$data = array(
			'_tr_applicant_name'    => sanitize_text_field( wp_unslash( $_POST['applicant_name'] ) ),
			'_tr_business_name'     => sanitize_text_field( wp_unslash( $_POST['business_name'] ) ),
			'_tr_sector'            => sanitize_text_field( wp_unslash( $_POST['sector'] ) ),
			'_tr_city'              => sanitize_text_field( wp_unslash( $_POST['city'] ) ),
			'_tr_whatsapp'          => $phone,
			'_tr_email'             => $email,
			'_tr_plan'              => sanitize_text_field( wp_unslash( $_POST['plan'] ) ),
			'_tr_service_type'      => sanitize_text_field( wp_unslash( $_POST['service_type'] ) ),
			'_tr_product_count'     => isset( $_POST['product_count'] ) ? absint( $_POST['product_count'] ) : 0,
			'_tr_message'           => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
			'_tr_origin'            => isset( $_POST['origin'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['origin'] ) ), '' ) : '',
			'_tr_utm_source'        => self::limited_field( 'utm_source' ),
			'_tr_utm_medium'        => self::limited_field( 'utm_medium' ),
			'_tr_utm_campaign'      => self::limited_field( 'utm_campaign' ),
			'_tr_utm_content'       => self::limited_field( 'utm_content' ),
			'_tr_utm_term'          => self::limited_field( 'utm_term' ),
			'_tr_campaign_ref'      => self::limited_field( 'campaign_ref' ),
			'_tr_data_consent'      => 'yes',
			'_tr_marketing_consent' => ! empty( $_POST['marketing_consent'] ) ? 'yes' : 'no',
			'_tr_followup_status'   => 'nuevo',
			'_tr_created_at'        => current_time( 'mysql', true ),
		);

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'tr_service_request',
				'post_status' => 'private',
				'post_title'  => sprintf( '%s — %s', $data['_tr_business_name'], gmdate( 'Y-m-d H:i' ) ),
			)
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			self::redirect( $redirect, 'error' );
		}

		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
		set_transient( $rate_key, 1, MINUTE_IN_SECONDS );

		wp_mail(
			get_option( 'admin_email' ),
			sprintf( '[TIENDA ROSSO] Nueva solicitud: %s', $data['_tr_business_name'] ),
			sprintf( "Se registró una solicitud para %s. Revísala en el panel comercial.\nID: %d", $data['_tr_plan'], $post_id )
		);

		self::redirect( $redirect, 'success' );
	}

	/** Redirect safely and stop. */
	private static function redirect( string $url, string $state ): never {
		wp_safe_redirect( add_query_arg( 'tr_form', $state, $url ) );
		exit;
	}

	/** Sanitize bounded campaign data. */
	private static function limited_field( string $key ): string {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		return substr( $value, 0, 190 );
	}

	/** Return a rate-limit input without persisting it. */
	private static function client_address(): string {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	}
}
