<?php
/**
 * Themes Module
 *
 * Displays themes data in the WordPress admin dashboard widget.
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * Themes Module Class
 */
class ThemesModule extends AbstractModule {


	/**
	 * Generate the themes data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$data = self::get_themes_data(); // Get the themes data.

		$section = new Section(
			title: 'Themes Data',
			data: $data, // Content will be generated in the render_section method.
			css_id: 'themes-data-section' // CSS ID for the section.
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
		return $widget_css;
	}

	/**
	 * Get themes data
	 * Creates and returns an array of themes data such as:
	 * - Total themes
	 * - Inactive theme count
	 * - Current theme name
	 * - Current theme is child
	 * - Count of themes with updates
	 *
	 * @return array
	 */
	public static function get_themes_data() {

		$themes_data = array(
			array(
				'label' => 'Total Themes',
				'value' => count( wp_get_themes() ),
				'score' => false,
			),
			array(
				'label' => 'Current Theme',
				'value' => wp_get_theme()->get( 'Name' ),
				'score' => false,
			),
			array(
				'label' => 'Current Theme Version',
				'value' => wp_get_theme()->get( 'Version' ),
				'score' => false,
			),
			array(
				'label' => 'Current Theme is Child',
				'value' => wp_get_theme()->parent() ? 'Yes' : 'No',
				'score' => false,
			),
			array(
				'label' => 'Themes with Updates',
				'value' => self::wp_get_themes_with_updates(),
				'score' => false,
			),

		);

		return $themes_data;
	}

	/**
	 * Get themes with updates
	 *
	 * @return int
	 */
	public static function wp_get_themes_with_updates() {
		$update_data   = wp_get_themes();
		$theme_updates = get_site_transient( 'update_themes' );

		$count = 0;

		foreach ( $update_data as $slug => $theme ) {
			if (
			isset( $theme_updates->response[ $slug ] )
			&& version_compare( $theme->get( 'Version' ), $theme_updates->response[ $slug ]['new_version'], '<' )
			) {
				++$count;
			}
		}
		return $count;
	}
}
