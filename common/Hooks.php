<?php
/**
 * Hooks class
 *
 * @package DevLensWP
 */

namespace Devlens_Common;

use Devlens\Dashboard\DashboardPage;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks class
 */
class Hooks {
	/**
	 * Init
	 */
	public static function init() {
		DashboardPage::init();
	}
}
