<?php
/**
 * DashboardPage class
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the DevLensWP dashboard page.
 */
class DashboardPage {
	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( static::class, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( static::class, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles for the dashboard page.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public static function enqueue_styles( $hook ) {
		$hook;
		$screen = get_current_screen();

		if ( $screen && 'toplevel_page_devlenswp-dashboard' === $screen->id ) {
			wp_enqueue_style(
				'devlenswp-dashboard',
				DEVLENS_PLUGIN_URL . 'assets/css/style.css',
				array(),
				'1.0'
			);
			wp_enqueue_style(
				'font-awesome',
				'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
				array(),
				null
			);
		}
	}

	/**
	 * Add the top-level admin menu.
	 */
	public static function add_menu_page() {
		add_menu_page(
			'DevLensWP',
			'DevLensWP',
			'manage_options',
			'devlenswp-dashboard',
			array( static::class, 'render_page' ),
			'dashicons-search',
			100
		);
	}

	/**
	 * Render the dashboard page.
	 */
	public static function render_page() {
		echo '<div class="wrap">';
		echo '<h1>DevLensWP Dashboard</h1>';

		/**
		 * Fires before dashboard content is output.
		 * Section classes should hook here.
		 */
		$sections = apply_filters( 'devlenswp_dashboard_sections', array() );

		$html = '';

		$html .= '<div class="devlenswp-section-list-wrapper">';
		foreach ( $sections as $section ) {
			$html .= self::render_section_data( $section->data );
		}

		echo wp_kses_post( $html );
		echo '</div><!-- end of wrapper -->'; // Close wrap.

		ServerSection::render_section();
		DebugLogSection::render_section();
		UploadsSection::render_section();
		DBSection::render_section();
	}

	/**
	 * Render the section data.
	 *
	 * @param array $data The data to render.
	 * @return string
	 */
	public static function render_section_data( $data ) {
		if ( isset( $data ) && 1 > count( $data ) ) {
			return '';
		}
		$html = '';

		foreach ( $data as $item ) {
			$html .= '<div>';
			$html .= "<span>{$item['label']}: {$item['value']}</span>";

			if ( ! $item['score'] ) {
				$result = '';
				$level  = '';
			} else {
				$result = '●';
				$level  = 'style="color: ' . $item['score'] . ';"';
			}

			$html .= "<span class='indicator' {$level}>{$result}</span>";
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Add custom CSS to the admin head.
	 */
	public static function add_custom_css() {
		$dashboard_css = <<<HTML
		
		HTML;
		$section_css   = apply_filters( 'devlenswp_custom_css', '' );

		$final_css = '<style>' . $dashboard_css . $section_css . '</style>';

		echo $final_css; //phpcs:ignore
	}
}
