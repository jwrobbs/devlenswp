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
		echo '<h1>DevLensWP Overview</h1>';

		/**
		 * Fires before dashboard content is output.
		 * Section classes should hook here.
		 */
		do_action( 'devlenswp_dashboard_sections' );

		echo '</div>';
	}
}
