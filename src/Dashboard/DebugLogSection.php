<?php
/**
 * DebugLogSection class
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard;

use WP;

defined( 'ABSPATH' ) || exit;

/**
 * DebugLogSection class
 *
 * Displays the debug log section in the WordPress admin dashboard widget.
 */
class DebugLogSection {
	/**
	 * Render debug section.
	 */
	public static function render_section() {

		$debug_status         = WP_DEBUG ? 'Enabled' : 'Disabled';
		$debug_log_status     = WP_DEBUG_LOG ? 'Enabled' : 'Disabled';
		$debug_display_status = WP_DEBUG_DISPLAY ? 'Enabled' : 'Disabled';

		$debug_log_path   = WP_CONTENT_DIR . '/debug.log';
		$debug_log_exists = file_exists( $debug_log_path ) ? 'Yes' : 'No';
		if ( 'Yes' === $debug_log_exists ) {
			$debug_log_size          = filesize( $debug_log_path );
			$debug_log_size          = size_format( $debug_log_size, 2 );
			$debug_log_last_modified = gmdate( 'Y-m-d H:i:s', filemtime( $debug_log_path ) );

			$file_list = <<<HTML
				<ul>
					<li><strong>Debug Log Exists:</strong> $debug_log_exists</li>
					<li><strong>Debug Log Size:</strong> $debug_log_size</li>
					<li><strong>Last Modified:</strong> $debug_log_last_modified</li>
				</ul>
			HTML;
		} else {
			$file_list = '<div><strong>Debug Log Exists:</strong> $debug_log_exists</div>';
		}

		$html = <<<HTML
		<div class="debug-log-section">
			<h3>Debug Log Data</h3>
			<div class='content'>
				<div class='overview'>
					<ul>
						<li><strong>Debug Status:</strong> $debug_status</li>
						<li><strong>Debug Log Status:</strong> $debug_log_status</li>
						<li><strong>Debug Display Status:</strong> $debug_display_status</li>
					</ul>
				</div><!-- .debug-log-section__overview -->
				<div class='file-data'>
					$file_list
				</div><!-- .debug-log-section__file-data -->
			</div><!-- .debug-log-section__content -->
		</div><!-- .debug-log-section -->
		HTML;

		echo $html; //phpcs:ignore
	}
}
