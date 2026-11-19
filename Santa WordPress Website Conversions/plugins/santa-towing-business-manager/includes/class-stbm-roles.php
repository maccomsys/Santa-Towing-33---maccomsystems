<?php
/**
 * WordPress Roles & Granular Capability Management.
 *
 * Maps original React `admin` (Operations Manager) and `staff` (Staff Operator)
 * permissions onto native WordPress Roles & Capabilities:
 * - `administrator` & `st_manager`: Full access including `stbm_manage_brand` (Colors & Logo).
 * - `st_staff`: Operations, Billing, Inventory, Content, Image Studio, Backups; NO `stbm_manage_brand`.
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STBM_Roles {

	const CAP_CONSOLE   = 'stbm_access_console';
	const CAP_BILLING   = 'stbm_manage_billing';
	const CAP_INVENTORY = 'stbm_manage_inventory';
	const CAP_CONTENT   = 'stbm_manage_content';
	const CAP_STUDIO    = 'stbm_manage_studio';
	const CAP_BRAND     = 'stbm_manage_brand';
	const CAP_BACKUPS   = 'stbm_manage_backups';

	/**
	 * Return all STBM custom capabilities.
	 *
	 * @return string[]
	 */
	public static function all_caps() {
		return array(
			self::CAP_CONSOLE,
			self::CAP_BILLING,
			self::CAP_INVENTORY,
			self::CAP_CONTENT,
			self::CAP_STUDIO,
			self::CAP_BRAND,
			self::CAP_BACKUPS,
		);
	}

	/**
	 * Capabilities assigned to Operations Staff (`st_staff`).
	 * Mirrors React `auth.ts` where staff cannot access Colors or Logo.
	 *
	 * @return string[]
	 */
	public static function staff_caps() {
		return array(
			self::CAP_CONSOLE,
			self::CAP_BILLING,
			self::CAP_INVENTORY,
			self::CAP_CONTENT,
			self::CAP_STUDIO,
			self::CAP_BACKUPS,
		);
	}

	/**
	 * Register custom WordPress roles and grant caps to Administrator on activation.
	 */
	public static function register_roles_and_caps() {
		// Grant all capabilities to Administrator.
		$admin_role = get_role( 'administrator' );
		if ( $admin_role ) {
			foreach ( self::all_caps() as $cap ) {
				$admin_role->add_cap( $cap );
			}
		}

		// Register or refresh Business Manager role (Admin equivalent inside Santa Manager).
		remove_role( 'st_manager' );
		$manager_caps = array(
			'read'                   => true,
			'upload_files'           => true,
			'edit_posts'             => true,
			'publish_posts'          => true,
			'delete_posts'           => true,
			'edit_published_posts'   => true,
			'delete_published_posts' => true,
		);
		foreach ( self::all_caps() as $cap ) {
			$manager_caps[ $cap ] = true;
		}
		add_role( 'st_manager', __( 'Santa Business Manager', 'santa-towing-business-manager' ), $manager_caps );

		// Register or refresh Operations Staff role (Restricted from Colors & Logo).
		remove_role( 'st_staff' );
		$staff_caps = array(
			'read'                   => true,
			'upload_files'           => true,
			'edit_posts'             => true,
			'publish_posts'          => true,
			'edit_published_posts'   => true,
		);
		foreach ( self::staff_caps() as $cap ) {
			$staff_caps[ $cap ] = true;
		}
		$staff_caps[ self::CAP_BRAND ] = false;
		add_role( 'st_staff', __( 'Santa Operations Staff', 'santa-towing-business-manager' ), $staff_caps );
	}

	/**
	 * Ensure Administrator always retains all STBM capabilities and custom roles exist.
	 */
	public static function ensure_admin_caps() {
		$admin_role = get_role( 'administrator' );
		if ( $admin_role && ! $admin_role->has_cap( self::CAP_BRAND ) ) {
			foreach ( self::all_caps() as $cap ) {
				$admin_role->add_cap( $cap );
			}
		}
		if ( ! get_role( 'st_manager' ) || ! get_role( 'st_staff' ) ) {
			self::register_roles_and_caps();
		}
	}

	/**
	 * Determine user role label ("admin" vs "staff" style badge parity).
	 *
	 * @param WP_User|null $user User object.
	 * @return array{key:string,label:string,can_brand:bool}
	 */
	public static function get_user_role_info( $user = null ) {
		if ( ! $user ) {
			$user = wp_get_current_user();
		}
		$can_brand = $user && $user->exists() && ( user_can( $user, 'manage_options' ) || user_can( $user, self::CAP_BRAND ) );
		if ( $can_brand ) {
			return array(
				'key'       => 'admin',
				'label'     => __( 'Administrator', 'santa-towing-business-manager' ),
				'can_brand' => true,
			);
		}
		return array(
			'key'       => 'staff',
			'label'     => __( 'Staff Operator', 'santa-towing-business-manager' ),
			'can_brand' => false,
		);
	}

	/**
	 * Verify AJAX request nonce and capability cleanly.
	 *
	 * @param string $capability Required capability.
	 */
	public static function verify_ajax( $capability = self::CAP_CONSOLE ) {
		if ( ! check_ajax_referer( 'stbm_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security token expired. Please reload the page.', 'santa-towing-business-manager' ) ),
				403
			);
		}
		if ( ! current_user_can( $capability ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to perform this action.', 'santa-towing-business-manager' ) ),
				403
			);
		}
	}

	/**
	 * Verify admin-post download/export request nonce and capability cleanly.
	 *
	 * @param string $capability   Required capability.
	 * @param string $nonce_action Nonce action name.
	 */
	public static function verify_admin_post( $capability, $nonce_action ) {
		if ( ! current_user_can( $capability ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Forbidden: You lack required permissions for this export.', 'santa-towing-business-manager' ), 403 );
		}
		$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			wp_die( esc_html__( 'Security check failed or download link expired.', 'santa-towing-business-manager' ), 403 );
		}
	}
}
