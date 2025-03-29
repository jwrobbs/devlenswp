<?php
/**
 * Security Module
 *
 * Displays security data in the WordPress admin dashboard widget.
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * Security Module Class
 */
class SecurityModule extends AbstractModule {


	/**
	 * Generate the security data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$data = self::get_security_data(); // Get the security data.

		$section = new Section(
			title: 'Security Data',
			data: $data, // Content will be generated in the render_section method.
			css_id: 'security-data-section' // CSS ID for the section.
		);

		$sections[] = $section;
		return $sections;
	}

	/**
	 * Get security data
	 *
	 * @return array
	 */
	public static function get_security_data() {
		$data = array(
			array(
				'label' => 'Is Debug Log Public?',
				'value' => self::is_debug_log_public() ? 'Yes' : 'No',
				'score' => self::is_debug_log_public() ? 'green' : 'red',
			),
			array(
				'label' => 'Are Field Edits Enabled?',
				'value' => self::is_field_edits_enabled() ? 'Yes' : 'No',
				'score' => self::is_field_edits_enabled() ? 'red' : 'green',
			),
			array(
				'label' => 'Is wp-config.php Writable?',
				'value' => self::is_wp_config_writable() ? 'Yes' : 'No',
				'score' => self::is_wp_config_writable() ? 'green' : 'red',
			),
			array(
				'label' => 'Is HTTPS Enforced?',
				'value' => self::is_https_enforced() ? 'Yes' : 'No',
				'score' => self::is_https_enforced() ? 'green' : 'red',
			),
		);

		$autoupdate_data = self::get_autoupdate_data();

		$data[] = array(
			'label' => 'Core Auto Updates',
			'value' => $autoupdate_data['core'] ? 'Enabled' : 'Disabled',
			'score' => false,
		);
		$data[] = array(
			'label' => 'Plugin Auto Updates',
			'value' => $autoupdate_data['plugins'],
			'score' => false,
		);
		$data[] = array(
			'label' => 'Theme Auto Updates',
			'value' => $autoupdate_data['themes'],
			'score' => false,
		);

		return $data;
	}

	/**
	 * Is debug log public
	 *
	 * @return bool
	 */
	public static function is_debug_log_public() {
		$response = wp_remote_head( content_url( 'debug.log' ) );

		$is_public = false;
		if ( ! is_wp_error( $response ) ) {
			$status_code = wp_remote_retrieve_response_code( $response );
			$is_public   = ( 200 === $status_code );
		}
		return $is_public;
	}

	/**
	 * Is field edits enabled
	 *
	 * @return bool
	 */
	public static function is_field_edits_enabled() {
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', false );
		}
		return defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT;
	}

	/**
	 * Is wp-config writable?
	 *
	 * @return bool
	 */
	public static function is_wp_config_writable() {
		$wp_config = ABSPATH . 'wp-config.php';
		return is_writable( $wp_config ); // phpcs:ignore
	}

	/**
	 * Is HTTPS enforced
	 *
	 * @return bool
	 */
	public static function is_https_enforced() {
		$https_home = strpos( home_url(), 'https://' ) === 0;
		return is_ssl() && $https_home;
	}

	/**
	 * Get autoupdate data
	 *
	 * @return array
	 */
	public static function get_autoupdate_data() {
		$core_auto_updates = wp_is_auto_update_enabled_for_type( 'core' );

		$plugin_updates = get_site_option( 'auto_update_plugins', array() );
		$theme_updates  = get_site_option( 'auto_update_themes', array() );

		$auto_plugin_count = is_array( $plugin_updates ) ? count( $plugin_updates ) : 0;
		$auto_theme_count  = is_array( $theme_updates ) ? count( $theme_updates ) : 0;

		return array(
			'core'    => $core_auto_updates,
			'plugins' => $auto_plugin_count,
			'themes'  => $auto_theme_count,
		);
	}
}
