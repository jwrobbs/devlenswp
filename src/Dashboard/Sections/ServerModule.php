<?php
/**
 * Server Module
 *
 * Displays server data in the WordPress admin dashboard widget.
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * Server Module Class
 */
class ServerModule extends AbstractModule {


	/**
	 * Generate the server data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$env_data = self::get_server_data(); // Get the server data.

		// $content = '<ul class="admin-tools-dashboard-widget_list">'; // Initialize content variable.
		// foreach ( $env_data as $key => $value ) {
		// Build the content string with server data.
		// $content .= '<li><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( $value ) . '</li>';
		// }
		// $content .= '</ul>'; // Close the unordered list.

		$section = new Section(
			title: 'Server Data',
			data: $env_data, // Content will be generated in the render_section method.
			css_id: 'server-data-section' // CSS ID for the section.
		);

		$sections[] = $section;
		return $sections;
	}

	/**
	 * Add CSS
	 *
	 * @param string $widget_css The existing custom CSS from other modules or sections.
	 * @return string
	 */
	public static function add_cssx( $widget_css ) {
		$css = <<<HTML
			#server-data-section {
				ul {
				columns: 2;
				margin: 0;
				}
				li {
					font-size: .8rem;
					line-height: 1.2;
					margin: 0 0 .5em;
				}
			}
		HTML;

		return $widget_css . $css;
	}

	/**
	 * Get server data
	 * Creates and returns an array of server data such as:
	 * - PHP version
	 * - Server software
	 * - MySQL version
	 * - PHP memory limit
	 * - PHP max execution time
	 * - PHP max input time
	 * - PHP post max size
	 * - PHP upload max size
	 * - PHP max file uploads
	 * - PHP max input vars
	 * - PHP display errors
	 * - PHP error reporting
	 *
	 * @return array
	 */
	public static function get_server_data() {
		$server_info = $GLOBALS['wpdb']->get_var( 'SELECT VERSION()' );
		if ( stripos( $server_info, 'mariadb' ) !== false ) {
			$type = 'MariaDB';
		} else {
			$type = 'MySQL';
		}

		$server_data = array(
			array(
				'label' => 'PHP Version',
				'value' => phpversion(),
				'score' => version_compare( PHP_VERSION, '8.0.0' ) >= 0 ? 'green' : 'red',
			),
			array(
				'label' => 'Server Software',
			'value' => $_SERVER['SERVER_SOFTWARE'], // phpcs:ignore
			'score'     => false,
			),
			array(
				'label' => "$type Version",
				'value' => $GLOBALS['wpdb']->db_version(),
				'score' => false,
			),
			array(
				'label' => 'PHP Memory Limit',
				'value' => ini_get( 'memory_limit' ),
				'score' => false,
			),
			array(
				'label' => 'PHP Max Execution Time',
				'value' => ini_get( 'max_execution_time' ),
				'score' => false,
			),
			array(
				'label' => 'PHP Max Input Time',
				'value' => ini_get( 'max_input_time' ),
				'score' => false,
			),
			array(
				'label' => 'PHP Post Max Size',
				'value' => ini_get( 'post_max_size' ),
				'score' => false,
			),
			array(
				'label' => 'PHP Upload Max Size',
				'value' => ini_get( 'upload_max_filesize' ),
				'score' => false,
			),
			array(
				'label' => 'PHP Max File Uploads',
				'value' => ini_get( 'max_file_uploads' ),
				'score' => false,
			),
			array(
				'label' => 'Object Cache Enabled',
				'value' => wp_using_ext_object_cache() ? 'Yes' : 'No',
				'score' => wp_using_ext_object_cache() ? 'green' : 'orange',
			),
		);

			return $server_data;
	}
}
