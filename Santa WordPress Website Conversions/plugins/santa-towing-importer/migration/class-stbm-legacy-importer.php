<?php
/**
 * Santa Towing — Standalone Legacy Browser & Defaults Importer Runner
 *
 * Provides:
 *   1. WP Admin Tools Screen: `Tools -> Santa Legacy Browser Importer`
 *   2. WP-CLI Runner: `ST_LEGACY_JSON=/path/to/export.json wp eval-file "Santa WordPress Website Conversions/migration/class-stbm-legacy-importer.php"`
 *      (or `ST_LEGACY_JSON=defaults` to validate/import `migration/business-manager-defaults.json`).
 *
 * Delegates directly to `STBM_Legacy_Importer` when Santa Towing Business Manager is installed & active.
 *
 * @package Santa_Towing_Migration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ST_Migration_Legacy_Importer {

	/**
	 * Register WP Admin Tools submenu safely.
	 */
	public static function register_admin_page() {
		add_action(
			'admin_menu',
			function () {
				add_management_page(
					__( 'Santa Legacy Browser Importer', 'santa-towing' ),
					__( 'Santa Legacy Browser Importer', 'santa-towing' ),
					'manage_options',
					'stbm-legacy-importer',
					array( __CLASS__, 'render_admin_page' )
				);
			}
		);
	}

	/**
	 * Ensure Business Manager importer class is available cleanly.
	 *
	 * @return bool
	 */
	private static function ensure_engine() {
		if ( class_exists( 'STBM_Legacy_Importer' ) ) {
			return true;
		}
		$candidates = array(
			dirname( __DIR__ ) . '/plugins/santa-towing-business-manager',
			defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR . '/santa-towing-business-manager' : '',
		);
		foreach ( $candidates as $bm_dir ) {
			if ( '' !== $bm_dir && file_exists( $bm_dir . '/santa-towing-business-manager.php' ) ) {
				require_once $bm_dir . '/includes/class-stbm-roles.php';
				require_once $bm_dir . '/includes/class-stbm-defaults.php';
				require_once $bm_dir . '/includes/class-stbm-db.php';
				require_once $bm_dir . '/includes/class-stbm-legacy-importer.php';
				return class_exists( 'STBM_Legacy_Importer' );
			}
		}
		return false;
	}

	/**
	 * Run import on a JSON file path or decoded payload.
	 *
	 * @param string|array $source File path (`defaults` for bundled defaults) or array.
	 * @param array        $opts   Options (`mode`, `dry_run`, `enforce_caps`).
	 * @return array
	 */
	public static function run_import( $source, $opts = array() ) {
		if ( ! self::ensure_engine() ) {
			return array(
				'ok'                   => false,
				'schema'               => 'unavailable',
				'failed'               => array( 'Santa Towing Business Manager plugin is not active or found.' ),
				'successful'           => array(),
				'skipped_duplicates'   => array(),
				'missing_images'       => array(),
				'broken_relationships' => array(),
			);
		}

		$bundle = $source;
		if ( is_string( $source ) ) {
			$path = ( 'defaults' === $source ) ? ( __DIR__ . '/business-manager-defaults.json' ) : $source;
			if ( ! file_exists( $path ) ) {
				return array(
					'ok'     => false,
					'schema' => 'missing_file',
					'failed' => array( 'JSON file not found: ' . $path ),
				);
			}
			$decoded = json_decode( (string) file_get_contents( $path ), true );
			if ( ! is_array( $decoded ) ) {
				return array(
					'ok'     => false,
					'schema' => 'invalid_json',
					'failed' => array( 'Could not decode valid JSON from: ' . $path ),
				);
			}
			$bundle = $decoded;
		}

		return STBM_Legacy_Importer::import_bundle( $bundle, $opts );
	}

	/**
	 * Render WordPress Tools -> Santa Legacy Browser Importer UI.
	 */
	public static function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'santa-towing' ) );
		}

		$result = null;
		if ( ( isset( $_POST['stbm_legacy_submit'] ) || isset( $_POST['stbm_import_defaults'] ) ) && check_admin_referer( 'stbm_legacy_cli_tool' ) ) {
			$mode    = ( isset( $_POST['stbm_mode'] ) && 'replace' === $_POST['stbm_mode'] ) ? 'replace' : 'merge';
			$dry_run = ! empty( $_POST['stbm_dry_run'] );

			if ( ! empty( $_FILES['stbm_json_file']['tmp_name'] ) && is_uploaded_file( $_FILES['stbm_json_file']['tmp_name'] ) ) {
				$raw = (string) file_get_contents( $_FILES['stbm_json_file']['tmp_name'] );
				$dec = json_decode( $raw, true );
				$result = is_array( $dec )
					? self::run_import( $dec, array( 'mode' => $mode, 'dry_run' => $dry_run, 'enforce_caps' => true ) )
					: array( 'ok' => false, 'failed' => array( 'Uploaded file is not valid JSON.' ) );
			} elseif ( ! empty( $_POST['stbm_import_defaults'] ) ) {
				$result = self::run_import( 'defaults', array( 'mode' => $mode, 'dry_run' => $dry_run, 'enforce_caps' => true ) );
			}
		}

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Santa Towing — Legacy React Browser Export Importer', 'santa-towing' ); ?></h1>
			<p><?php esc_html_e( 'Import exported localStorage & IndexedDB archives created with legacy-browser-export-snippet.js into Santa Towing Business Manager.', 'santa-towing' ); ?></p>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'stbm_legacy_cli_tool' ); ?>
				<table class="form-table">
					<tr>
						<th><label for="stbm_json_file"><?php esc_html_e( 'Legacy Export JSON File', 'santa-towing' ); ?></label></th>
						<td><input type="file" name="stbm_json_file" id="stbm_json_file" accept=".json" /></td>
					</tr>
					<tr>
						<th><label for="stbm_mode"><?php esc_html_e( 'Import Mode', 'santa-towing' ); ?></label></th>
						<td>
							<select name="stbm_mode" id="stbm_mode">
								<option value="merge"><?php esc_html_e( 'Merge (Deduplicate & Upsert Safely)', 'santa-towing' ); ?></option>
								<option value="replace"><?php esc_html_e( 'Replace Matching Datasets', 'santa-towing' ); ?></option>
							</select>
							<label style="margin-left:16px"><input type="checkbox" name="stbm_dry_run" value="1" /> <?php esc_html_e( 'Dry-Run Audit Only', 'santa-towing' ); ?></label>
						</td>
					</tr>
				</table>
				<p class="submit">
					<button type="submit" name="stbm_legacy_submit" value="1" class="button button-primary"><?php esc_html_e( 'Import Uploaded JSON', 'santa-towing' ); ?></button>
					<button type="submit" name="stbm_import_defaults" value="1" class="button"><?php esc_html_e( 'Sync Factory Defaults JSON', 'santa-towing' ); ?></button>
				</p>
			</form>
			<?php if ( is_array( $result ) ) : ?>
				<div class="notice notice-<?php echo ! empty( $result['ok'] ) ? 'success' : 'error'; ?>"><pre style="white-space:pre-wrap;font-size:12px"><?php echo esc_html( wp_json_encode( $result, JSON_PRETTY_PRINT ) ); ?></pre></div>
			<?php endif; ?>
		</div>
		<?php
	}
}

ST_Migration_Legacy_Importer::register_admin_page();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$json_arg = getenv( 'ST_LEGACY_JSON' ) ?: 'defaults';
	$mode_arg = getenv( 'ST_LEGACY_MODE' ) ?: 'merge';
	$dry_arg  = filter_var( getenv( 'ST_LEGACY_DRY_RUN' ), FILTER_VALIDATE_BOOLEAN );

	$out = ST_Migration_Legacy_Importer::run_import(
		$json_arg,
		array(
			'mode'         => $mode_arg,
			'dry_run'      => $dry_arg,
			'enforce_caps' => false,
		)
	);
	WP_CLI::log( wp_json_encode( $out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
	if ( empty( $out['ok'] ) ) {
		WP_CLI::error( 'Legacy import reported errors.' );
	}
	WP_CLI::success( 'Legacy import finished cleanly.' );
}
