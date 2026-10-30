<?php
/**
 * Commercial roles and capabilities.
 *
 * @package TiendaRossoCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TR_Roles {
	/**
	 * Keep role setup idempotent when versions change.
	 */
	public static function boot(): void {
		if ( get_option( 'tr_roles_version' ) !== TR_COMMERCE_VERSION ) {
			self::install();
		}
	}

	/**
	 * Install least-privilege roles.
	 */
	public static function install(): void {
		add_role(
			'tr_commercial_manager',
			__( 'Administrador comercial', 'tiendarosso-commerce' ),
			array( 'read' => true, 'upload_files' => true )
		);

		add_role(
			'tr_merchant',
			__( 'Comerciante', 'tiendarosso-commerce' ),
			array( 'read' => true )
		);

		$admin_caps = array_merge( self::request_caps(), self::plan_caps() );
		foreach ( array( 'administrator', 'tr_commercial_manager' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( ! $role ) {
				continue;
			}
			foreach ( $admin_caps as $cap ) {
				$role->add_cap( $cap );
			}
		}

		$administrator = get_role( 'administrator' );
		if ( $administrator ) {
			$administrator->add_cap( 'manage_tr_settings' );
		}

		update_option( 'tr_roles_version', TR_COMMERCE_VERSION, false );
	}

	/**
	 * Capabilities for private service requests.
	 *
	 * @return string[]
	 */
	public static function request_caps(): array {
		return array(
			'edit_tr_service_request',
			'read_tr_service_request',
			'delete_tr_service_request',
			'edit_tr_service_requests',
			'edit_others_tr_service_requests',
			'delete_tr_service_requests',
			'delete_others_tr_service_requests',
			'read_private_tr_service_requests',
		);
	}

	/**
	 * Capabilities for editable plans.
	 *
	 * @return string[]
	 */
	public static function plan_caps(): array {
		return array(
			'edit_tr_plan',
			'read_tr_plan',
			'delete_tr_plan',
			'edit_tr_plans',
			'edit_others_tr_plans',
			'publish_tr_plans',
			'read_private_tr_plans',
			'delete_tr_plans',
			'delete_private_tr_plans',
			'delete_published_tr_plans',
			'delete_others_tr_plans',
			'edit_private_tr_plans',
			'edit_published_tr_plans',
		);
	}
}
