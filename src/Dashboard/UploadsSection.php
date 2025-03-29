<?php
/**
 * UploadsSection class
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * UploadsSection class
 *
 * Displays the debug log section in the WordPress admin dashboard widget.
 */
class UploadsSection {
	/**
	 * Render debug section.
	 */
	public static function render_section() {

		$upload_dir   = wp_upload_dir();
		$uploads_path = $upload_dir['basedir'];

		$large_size  = 50 * 1024 * 1024; // 50 MB
		$pretty_size = size_format( $large_size, 2 );

		$data              = self::scan_directory_summary( $uploads_path, $large_size, 3 );
		$pretty_total_size = size_format( $data['size_bytes'], 2 );

		$file_list_html = '<ul>';
		foreach ( $data['top_files'] as $file ) {
			$file_size       = size_format( $file['size'], 2 );
			$file_list_html .= "<li><strong>{$file['path']}</strong> - {$file_size}</li>";
		}
		$file_list_html .= '</ul>';

		$html = <<<HTML
		<div class="uploads-section">
			<h3>Uploads Data</h3>
			<div class='content'>
				<div class='overview'>
					<ul>
						<li><strong>Uploads Directory:</strong> {$upload_dir['baseurl']}</li>
						<li><strong>Total Size:</strong> {$pretty_total_size} bytes</li>
						<li><strong>Total Files:</strong> {$data['file_count']}</li>
						<li><strong>Files larger than {$pretty_size}:</strong> {$data['large_files']}</li>
					</ul>
				</div><!-- .overview -->
				<div class='file-data'>
					{$file_list_html}
				</div><!-- .file-data -->
			</div><!-- .content -->
		</div><!-- .uploads-section-section -->
		HTML;

		echo $html; //phpcs:ignore
	}

	/**
	 * Scans a directory and returns total size, file count, and top large files.
	 *
	 * @param string $path            Absolute path to the directory.
	 * @param int    $large_threshold Large file cutoff in bytes. Default: 50MB.
	 * @param int    $top_limit       Number of largest files to return. Default: 5.
	 * @return array
	 */
	public static function scan_directory_summary( $path, $large_threshold = 50 * 1024 * 1024, $top_limit = 5 ) {
		$results = array(
			'size_bytes'  => 0,
			'file_count'  => 0,
			'large_files' => 0,
			'top_files'   => array(),
		);

		if ( ! is_dir( $path ) ) {
			return $results;
		}

		$top_files = array();

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $path, \FilesystemIterator::SKIP_DOTS )
		);

		foreach ( $iterator as $file ) {
			if ( $file->isFile() ) {
				$size                   = $file->getSize();
				$results['size_bytes'] += $size;
				++$results['file_count'];

				if ( $size >= $large_threshold ) {
					++$results['large_files'];
				}

				$top_files[] = array(
					'path' => $file->getRealPath(),
					'size' => $size,
				);
			}
		}

		usort( $top_files, fn( $a, $b ) => $b['size'] <=> $a['size'] );
		$results['top_files'] = array_slice( $top_files, 0, $top_limit );

		return $results;
	}
}
