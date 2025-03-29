<?php
/**
 * Abstract Module Class.
 *
 * Modules are used to build sections for the dashboard widget. Modules do all the
 * heavy lifting of creating the sections and supporting them.
 *
 * 1. Create a new section in generate_section().
 * 2. Add any custom CSS for the section in add_css().
 * 3. Optionally extend the init method to perform additional initialization tasks: extend_init().
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract Module Class.
 *
 * Provides a base for creating modules that can be used to build sections for the
 * dashboard widget. Each module can define its own generateing logic and data retrieval.
 */
abstract class AbstractModule {

	/**
	 * CSS
	 *
	 * @var string|null
	 */
	protected string|null $css;
	/**
	 * Init
	 */
	final public static function init() {
		add_filter( 'devlenswp_dashboard_sections', array( static::class, 'generate_section' ) );
		add_filter( 'devlenswp_custom_css', array( static::class, 'add_css' ) );

		static::extend_init();
	}

	/**
	 * Extend Init
	 *
	 * This method can be overridden in the child class to perform additional initialization
	 * tasks specific to that module.
	 */
	protected static function extend_init() {
		// This method can be overridden in the child class to perform additional
		// initialization tasks specific to that module.
		// For example, you might want to register additional hooks or filters,
		// or perform setup tasks that are unique to the module.
		// This is a placeholder for child classes to extend functionality.
	}

	/**
	 * Add CSS
	 *
	 * If a section needs CSS, override this in the concrete module class and return
	 * the CSS as a string.
	 *
	 * @param string $custom_css The existing custom CSS from other modules or sections.
	 * @return string
	 */
	public static function add_css( $custom_css ) {
		return $custom_css; // Return the existing CSS if no additional CSS is needed.
	}

	/**
	 * Generate the section.
	 *
	 * This method should be overridden in the child class to provide specific content.
	 *
	 * @param array $sections The sections.
	 */
	public static function generate_section( $sections ) {
		$sections;
		die( static::class . ' needs a fn for "generate_section()".' );
	}
}
