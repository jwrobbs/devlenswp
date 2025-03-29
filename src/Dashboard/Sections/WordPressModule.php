<?php
/**
 * WordPress Module
 *
 * Displays server data in the WordPress admin dashboard widget.
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * WordPress Module Class
 */
class WordPressModule extends AbstractModule {


	/**
	 * Generate the server data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$data = self::get_data(); // Get the server data.

		$section = new Section(
			title: 'WordPress Data',
			data: $data, // Content will be generated in the render_section method.
			css_id: 'wordpress-data-section' // CSS ID for the section.
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
	 * - WordPress version
	 *
	 * @return array
	 */
	public static function get_data() {

		include_once ABSPATH . 'wp-admin/includes/update.php';
		$updates = get_core_updates();

		if ( ! empty( $updates ) && 'upgrade' === $updates[0]->response ) {
			$wp_score = 'red';
		} else {
			$wp_score = 'green';
		}

		$server_data = array(
			array(
				'label' => 'WordPress Version',
				'value' => get_bloginfo( 'version' ),
				'score' => $wp_score,
			),
		);

		return $server_data;
	}
}
