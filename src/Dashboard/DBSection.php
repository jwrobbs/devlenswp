<?php
/**
 * DBSection class
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * DBSection class
 *
 * Displays the debug log section in the WordPress admin dashboard widget.
 */
class DBSection {
	/**
	 * Render debug section.
	 */
	public static function render_section() {
		global $wpdb;

		// Fetch all table statuses.
		$tables = $wpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A ); //phpcs:ignore

		$total_size   = 0;
		$table_count  = 0;
		$top_tables   = array();
		$innodb_count = 0;
		$myisam_count = 0;
		$options_size = 0;
		$heavy_tables = array();

		foreach ( $tables as $table ) {
			$table_name  = $table['Name'];
			$engine      = $table['Engine'];
			$size        = (int) $table['Data_length'] + (int) $table['Index_length'];
			$row_count   = (int) $table['Rows'];
			$total_size += $size;
			++$table_count;

			if ( stripos( $table_name, 'options' ) !== false ) {
				$options_size = $size;
			}

			if ( 'InnoDB' === $engine ) {
				++$innodb_count;
			} elseif ( 'MyISAM' === $engine ) {
				++$myisam_count;
			}

			$top_tables[] = array(
				'name' => $table_name,
				'size' => $size,
			);
			if ( $row_count > 100000 ) {
				$heavy_tables[] = array(
					'name' => $table_name,
					'rows' => $row_count,
				);
			}
		}

		usort( $top_tables, fn( $a, $b ) => $b['size'] <=> $a['size'] );
		$top_tables = array_slice( $top_tables, 0, 5 );

		$size_mb    = size_format( $total_size, 2 );
		$options_mb = size_format( $options_size, 2 );

		$autoloaded = $wpdb->get_var( //phpcs:ignore
			"SELECT COUNT(*) FROM {$wpdb->options} WHERE autoload = 'yes'"
		);

		$transients = $wpdb->get_var( //phpcs:ignore
			"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_%'"
		);

		$expired_transients = $wpdb->get_var( //phpcs:ignore
			"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_timeout\_%' AND option_value < UNIX_TIMESTAMP()"
		);

		$table_data_html = '<ul>';
		foreach ( $top_tables as $table ) {
			$table_size       = size_format( $table['size'], 2 );
			$table_data_html .= "<li><strong>{$table['name']}</strong> - {$table_size}</li>";
		}
		$table_data_html .= '</ul>';

		$heavy_table_count = count( $heavy_tables );
		$heavy_table_html  = '';
		$heavy_table_html .= "<h4>Heavy Tables (> 100k rows): {$heavy_table_count}</h4>";
		if ( $heavy_table_count > 0 ) {

			$heavy_table_html .= '<ul>';
			foreach ( $heavy_tables as $table ) {
				$heavy_table_html .= "<li><strong>{$table['name']}</strong> - {$table['rows']} rows</li>";
			}
			$heavy_table_html .= '</ul>';
		}

		// Display the data.
		$html = <<<HTML
		<div class="db-section section">
			<h3>DB Data</h3>
			<div class='content'>
				<div class='overview'>
					<ul>
						<li><strong>Total Size:</strong> $size_mb</li>
						<li><strong>Table Count:</strong> $table_count</li>
						<li><strong>InnoDB Count:</strong> $innodb_count</li>
						<li><strong>MyISAM Count:</strong> $myisam_count</li>
						<li><strong>Options Size:</strong> $options_mb</li>
						<li><strong>Autoloaded Options:</strong> $autoloaded</li>
						<li><strong>Transients:</strong> $transients</li>
						<li><strong>Expired Transients:</strong> $expired_transients</li>
					</ul>
				</div><!-- .overview -->
				<div class='table-data'>
					{$table_data_html}
				</div><!-- .table-data -->
				<div class='heavy-table-data'>
					{$heavy_table_html}
				</div><!-- .heavy-table-data -->
			</div><!-- .content -->
		</div><!-- .db-section-section -->
		HTML;

		echo $html; //phpcs:ignore
	}
}
