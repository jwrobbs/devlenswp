<?php
/**
 * Section Class.
 * *
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;
/**
 * Section Class.
 *
 * Provides HTML elements for title and subtitle.
 * Content is put into a single div. You must add internal HTML elements to the content
 * property.
 */
class Section {

	/**
	 * Constructor.
	 *
	 * @param string      $title Title of the section.
	 * @param string|null $subtitle Subtitle of the section.
	 * @param mixed       $data Data for the section.
	 * @param string|null $css_id CSS ID of the section, default is empty string.
	 */
	public function __construct(
		public string $title, // Title of the section.
		public string $subtitle = '', // Subtitle of the section.
		public mixed $data = null, // Data for the section, default is null.
		public string $css_id = '', // CSS ID of the section, default is empty string.
	) {
		$this->title    = $title; // Set the title.
		$this->subtitle = $subtitle; // Set the subtitle.
		$this->data     = $data; // Set the data.

		if ( empty( $this->css_id ) ) {
			// If no CSS ID is provided, generate a default one based on the title.
			$this->css_id = sanitize_title( $this->title );
		}
	}
}
