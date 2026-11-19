<?php
/**
 * WordPress Admin Menu Routing, Asset Enqueuing, Bootstrap Payload, and Console Shell UI.
 *
 * Registers top-level menu `santa-towing-manager` and submenus for all 14 modules:
 * Dashboard, Customers, Inventory, Invoices, Receipts, Image Studio, Doc Settings,
 * Pages Text, Colors (CAP_BRAND), Logo (CAP_BRAND), Links, Images, Backup/Export, Profile.
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STBM_Admin_UI {

	/** @var string[] Screen hook suffixes belonging to our plugin. */
	private static $screen_hooks = array();

	/**
	 * Initialize admin menu and script hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'admin_body_class', array( __CLASS__, 'filter_body_class' ) );
	}

	/**
	 * Definition of all 14 module pages mirroring `AdminSidebar.tsx`.
	 *
	 * @return array[]
	 */
	public static function get_routes() {
		return array(
			array( 'slug' => 'santa-towing-manager', 'view' => 'dashboard',    'label' => __( 'Dashboard', 'santa-towing-business-manager' ),       'cap' => STBM_Roles::CAP_CONSOLE,   'group' => 'Overview' ),
			array( 'slug' => 'stbm-customers',       'view' => 'customers',    'label' => __( 'Customers', 'santa-towing-business-manager' ),       'cap' => STBM_Roles::CAP_BILLING,   'group' => 'Operations & Billing' ),
			array( 'slug' => 'stbm-inventory',       'view' => 'inventory',    'label' => __( 'Inventory', 'santa-towing-business-manager' ),       'cap' => STBM_Roles::CAP_INVENTORY, 'group' => 'Operations & Billing' ),
			array( 'slug' => 'stbm-invoices',        'view' => 'invoices',     'label' => __( 'Pro Forma Invoices', 'santa-towing-business-manager' ), 'cap' => STBM_Roles::CAP_BILLING, 'group' => 'Operations & Billing' ),
			array( 'slug' => 'stbm-receipts',        'view' => 'receipts',     'label' => __( 'Official Receipts', 'santa-towing-business-manager' ), 'cap' => STBM_Roles::CAP_BILLING, 'group' => 'Operations & Billing' ),
			array( 'slug' => 'stbm-image-studio',    'view' => 'image-studio', 'label' => __( 'Image Studio', 'santa-towing-business-manager' ),    'cap' => STBM_Roles::CAP_STUDIO,    'group' => 'Media & Studio' ),
			array( 'slug' => 'stbm-doc-settings',    'view' => 'doc-settings', 'label' => __( 'Doc Settings', 'santa-towing-business-manager' ),    'cap' => STBM_Roles::CAP_BILLING,   'group' => 'Operations & Billing' ),
			array( 'slug' => 'stbm-pages-text',      'view' => 'pages-text',   'label' => __( 'Pages & Text', 'santa-towing-business-manager' ),    'cap' => STBM_Roles::CAP_CONTENT,   'group' => 'Website Content' ),
			array( 'slug' => 'stbm-colors',          'view' => 'colors',       'label' => __( 'Theme Colors', 'santa-towing-business-manager' ),    'cap' => STBM_Roles::CAP_BRAND,     'group' => 'Brand & Design' ),
			array( 'slug' => 'stbm-logo',            'view' => 'logo',         'label' => __( 'Logo & Identity', 'santa-towing-business-manager' ), 'cap' => STBM_Roles::CAP_BRAND,     'group' => 'Brand & Design' ),
			array( 'slug' => 'stbm-links',           'view' => 'links',        'label' => __( 'Links & Contact', 'santa-towing-business-manager' ), 'cap' => STBM_Roles::CAP_CONTENT,   'group' => 'Website Content' ),
			array( 'slug' => 'stbm-images',          'view' => 'images',       'label' => __( 'Site Images', 'santa-towing-business-manager' ),     'cap' => STBM_Roles::CAP_CONTENT,   'group' => 'Media & Studio' ),
			array( 'slug' => 'stbm-backup',          'view' => 'backup',       'label' => __( 'Backup & Export', 'santa-towing-business-manager' ), 'cap' => STBM_Roles::CAP_BACKUPS,   'group' => 'System' ),
			array( 'slug' => 'stbm-profile',         'view' => 'profile',      'label' => __( 'My Profile', 'santa-towing-business-manager' ),      'cap' => STBM_Roles::CAP_CONSOLE,   'group' => 'System' ),
		);
	}

	/**
	 * Register WP Admin menu and 14 submenu routes cleanly.
	 */
	public static function register_menus() {
		self::$screen_hooks = array();

		$main_hook = add_menu_page(
			__( 'Santa Towing Business Manager', 'santa-towing-business-manager' ),
			__( 'Santa Manager', 'santa-towing-business-manager' ),
			STBM_Roles::CAP_CONSOLE,
			'santa-towing-manager',
			array( __CLASS__, 'render_console' ),
			'dashicons-car',
			26
		);
		self::$screen_hooks[] = $main_hook;

		foreach ( self::get_routes() as $route ) {
			$hook = add_submenu_page(
				'santa-towing-manager',
				$route['label'] . ' — ' . __( 'Santa Towing', 'santa-towing-business-manager' ),
				$route['label'],
				$route['cap'],
				$route['slug'],
				array( __CLASS__, 'render_console' )
			);
			if ( $hook ) {
				self::$screen_hooks[] = $hook;
			}
		}
	}

	/**
	 * Add body class strictly on Santa Manager pages for clean full-canvas styling.
	 */
	public static function filter_body_class( $classes ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && in_array( $screen->id, self::$screen_hooks, true ) ) {
			$classes .= ' stbm-admin-screen ';
		}
		return $classes;
	}

	/**
	 * Enqueue styles, WP Media Library, and modular vanilla JS files exclusively on plugin pages.
	 */
	public static function enqueue_assets( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, self::$screen_hooks, true ) ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'stbm-admin-css',
			STBM_PLUGIN_URL . 'assets/css/stbm-admin.css',
			array(),
			STBM_VERSION
		);

		wp_enqueue_script(
			'stbm-app-js',
			STBM_PLUGIN_URL . 'assets/js/stbm-app.js',
			array(),
			STBM_VERSION,
			true
		);

		wp_enqueue_script(
			'stbm-billing-js',
			STBM_PLUGIN_URL . 'assets/js/stbm-billing.js',
			array( 'stbm-app-js' ),
			STBM_VERSION,
			true
		);

		wp_enqueue_script(
			'stbm-image-studio-js',
			STBM_PLUGIN_URL . 'assets/js/stbm-image-studio.js',
			array( 'stbm-app-js' ),
			STBM_VERSION,
			true
		);

		wp_localize_script( 'stbm-app-js', 'STBM_BOOT', self::build_boot_payload() );
	}

	/**
	 * Determine current module view slug from `?page=` query arg.
	 *
	 * @return array Route definition.
	 */
	private static function current_route() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'santa-towing-manager';
		foreach ( self::get_routes() as $r ) {
			if ( $r['slug'] === $page ) {
				return $r;
			}
		}
		return self::get_routes()[0];
	}

	/**
	 * Build hydrated JSON payload passed once on initial load into `window.STBM_BOOT`.
	 *
	 * @return array
	 */
	public static function build_boot_payload() {
		$user      = wp_get_current_user();
		$role_info = STBM_Roles::get_user_role_info( $user );
		$avatar    = get_user_meta( $user->ID, 'stbm_avatar_url', true );
		$job_title = get_user_meta( $user->ID, 'stbm_job_title', true );
		$route     = self::current_route();

		$export_nonce = wp_create_nonce( 'stbm_export_nonce' );

		return array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'adminPostUrl' => admin_url( 'admin-post.php' ),
			'adminBaseUrl' => admin_url( 'admin.php' ),
			'homeUrl'      => home_url( '/' ),
			'logoutUrl'    => wp_logout_url( admin_url() ),
			'pluginUrl'    => STBM_PLUGIN_URL,
			'nonce'        => wp_create_nonce( 'stbm_nonce' ),
			'exportNonce'  => $export_nonce,
			'activeView'   => $route['view'],
			'activeSlug'   => $route['slug'],
			'user'         => array(
				'id'          => (int) $user->ID,
				'username'    => $user->user_login,
				'displayName' => $user->display_name,
				'email'       => $user->user_email,
				'avatar'      => stbm_resolve_media_url( $avatar ?: stbm_asset_image( 'avatar-operations-lead-256.jpg' ) ),
				'jobTitle'    => $job_title ?: $role_info['label'],
				'role'        => $role_info['key'],
				'roleLabel'   => $role_info['label'],
				'caps'        => array(
					'console'   => current_user_can( STBM_Roles::CAP_CONSOLE ) || current_user_can( 'manage_options' ),
					'billing'   => current_user_can( STBM_Roles::CAP_BILLING ) || current_user_can( 'manage_options' ),
					'inventory' => current_user_can( STBM_Roles::CAP_INVENTORY ) || current_user_can( 'manage_options' ),
					'content'   => current_user_can( STBM_Roles::CAP_CONTENT ) || current_user_can( 'manage_options' ),
					'studio'    => current_user_can( STBM_Roles::CAP_STUDIO ) || current_user_can( 'manage_options' ),
					'brand'     => $role_info['can_brand'],
					'backups'   => current_user_can( STBM_Roles::CAP_BACKUPS ) || current_user_can( 'manage_options' ),
				),
			),
			'routes'       => self::get_routes(),
			'avatarPresets'=> STBM_Defaults::get_avatar_presets(),
			'siteImages'   => STBM_Defaults::get_site_image_definitions(),
			'defaultDocSettings' => STBM_Defaults::get_default_doc_settings(),
			'defaultNavLists'    => STBM_Defaults::get_default_nav_lists(),
			'siteContent'  => STBM_Defaults::get_site_content(),
			'docSettings'  => STBM_Defaults::get_doc_settings(),
			'customers'    => STBM_DB::get_customers(),
			'invoices'     => STBM_DB::get_invoices(),
			'receipts'     => STBM_DB::get_receipts(),
			'studioImages' => STBM_DB::get_studio_images(),
			'logs'         => STBM_DB::get_activity_logs( 100 ),
			'inventory'    => array(
				'cars'    => STBM_Defaults::get_inventory_items( 'cars' ),
				'rentals' => STBM_Defaults::get_inventory_items( 'rentals' ),
				'parts'   => STBM_Defaults::get_inventory_items( 'parts' ),
				'gallery' => STBM_Defaults::get_inventory_items( 'gallery' ),
				'faqs'    => STBM_Defaults::get_inventory_items( 'faqs' ),
				'careers' => STBM_Defaults::get_inventory_items( 'careers' ),
			),
		);
	}

	/**
	 * Render standalone application shell container (strictly no iframes, no React, no Elementor).
	 */
	public static function render_console() {
		$route = self::current_route();
		if ( ! current_user_can( $route['cap'] ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you do not have permission to access this module.', 'santa-towing-business-manager' ), 403 );
		}
		?>
		<div id="stbm-root" class="stbm-shell">
			<aside id="stbm-sidebar" class="stbm-sidebar" aria-label="<?php esc_attr_e( 'Santa Towing Navigation', 'santa-towing-business-manager' ); ?>"></aside>
			<div id="stbm-mobile-backdrop" class="stbm-mobile-backdrop stbm-hidden"></div>
			<div class="stbm-main-wrap">
				<header id="stbm-topbar" class="stbm-topbar"></header>
				<main id="stbm-viewport" class="stbm-viewport" data-view="<?php echo esc_attr( $route['view'] ); ?>">
					<div class="stbm-loading-state">
						<div class="stbm-spinner"></div>
						<p><?php esc_html_e( 'Loading Santa Towing Business Manager...', 'santa-towing-business-manager' ); ?></p>
					</div>
				</main>
			</div>
			<div id="stbm-modal-root"></div>
			<div id="stbm-toast-root" class="stbm-toast-stack" aria-live="polite"></div>
		</div>
		<?php
	}
}
