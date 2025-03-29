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
		add_action( 'admin_head', array( self::class, 'add_custom_css' ) );
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

		foreach ( $sections as $section ) {
			$html .= '<div class="devlenswp-section" id="' . $section->css_id . '">';
			$html .= '<h2>' . $section->title . '</h2>';
			$html .= '<div class="devlenswp-section-content">';
			$html .= self::render_section_data( $section->data );
			$html .= '</div>'; // Close section content.
			$html .= '</div>'; // Close section.
		}

		echo wp_kses_post( $html );

		echo '</div>';
	}

	/**
	 * Render the section data.
	 *
	 * @param array $data The data to render.
	 * @return string
	 */
	public static function render_section_data( $data ) {
		if ( 1 > count( $data ) ) {
			return '';
		}

		$html = '<ul class="devlenswp-section-data">';

		foreach ( $data as $item ) {
			$html .= '<li>';
			$html .= "<span>{$item['label']}: {$item['value']}</span>";

			if ( ! $item['score'] ) {
				$result = '';
				$level  = '';
			} else {
				$result = '●';
				$level  = 'style="color: ' . $item['score'] . ';"';
			}

			$html .= "<span class='indicator' {$level}>{$result}</span>";
			$html .= '</li>';
		}

		$html .= '</ul>'; // Close unordered list.
		return $html;
	}

	/**
	 * Add custom CSS to the admin head.
	 */
	public static function add_custom_css() {
		$dashboard_css = <<<HTML
		.devlenswp-section-content {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 1rem;
		}
		.devlenswp-section-data {
			padding: 0;
			margin: 0;
			border: 1px solid #000;
			width: auto;
			max-width: 250px;

			li {
				display: grid;
				grid-template-columns: auto 20px;
				padding: .2rem .5rem;
				margin:0;

				.indicator {
					text-align: right;
					font-size: 2rem;
					line-height: .3;
					align-items: center;
				}
			}
			li:nth-of-type(even) {
				background-color: #ddd;
			}
		}
		HTML;
		$section_css   = apply_filters( 'devlenswp_custom_css', '' );

		$final_css = '<style>' . $dashboard_css . $section_css . '</style>';

		echo $final_css; //phpcs:ignore
	}
}
