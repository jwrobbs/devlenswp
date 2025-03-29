<?php
/**
 * Plugins Module
 *
 * Displays server data in the WordPress admin dashboard widget.
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * Plugins Module Class
 */
class PluginsModule extends AbstractModule {


	/**
	 * Generate the plugins data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$env_data = self::get_plugins_data(); // Get the plugin data.

		// $content = '<ul class="admin-tools-dashboard-widget_list">'; // Initialize content variable.
		// foreach ( $env_data as $key => $value ) {
		// Build the content string with plugin data.
		// $content .= '<li><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( $value ) . '</li>';
		// }
		// $content .= '</ul>'; // Close the unordered list.

		$section = new Section(
			title: 'Plugins Data',
			data: $env_data, // Content will be generated in the render_section method.
			css_id: 'plugins-data-section' // CSS ID for the section.
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
			#plugins-data-section {
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
	 * Get plugins data
	 * Creates and returns an array of plugins data such as:
	 * - Total plugins
	 * - Active plugins
	 * - Inactive plugins
	 * - Plugins with updates
	 *
	 * @return array
	 */
	public static function get_plugins_data() {
		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		$update_plugins = get_site_transient( 'update_plugins' );
		$total          = count( $all_plugins );
		$active         = count( $active_plugins );
		$inactive       = $total - $active;
		$needs_update   = 0;

		switch ( $inactive ) {
			case 5 < $inactive:
				$inactive_score = 'orange';
				break;
			case 10 < $inactive:
				$inactive_score = 'red';
				break;
			default:
				$inactive_score = 'green';
				break;
		}

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			if ( isset( $update_plugins->response[ $plugin_file ] ) ) {
				++$needs_update;
			}
		}

		if ( 0 === $needs_update ) {
			$needs_update_score = 'green';
		} elseif ( 1 <= $needs_update ) {
			$needs_update_score = 'orange';
		} else {
			$needs_update_score = 'red';
		}

		$plugins_data = array(
			array(
				'label' => 'Total Plugins',
				'value' => $total,
				'score' => false,
			),
			array(
				'label' => 'Active Plugins',
				'value' => $active,
				'score' => false,
			),
			array(
				'label' => 'Inactive Plugins',
				'value' => $inactive,
				'score' => $inactive_score,
			),
			array(
				'label' => 'Plugins with Updates',
				'value' => $needs_update,
				'score' => $needs_update_score,
			),

		);

		return $plugins_data;
	}
}
