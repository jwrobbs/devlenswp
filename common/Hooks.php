<?php
/**
 * Hooks class
 *
 * @package DevLensWP
 */

namespace Devlens_Common;

use Devlens\Dashboard\DashboardPage;
use Devlens\Dashboard\Sections\ServerModule;

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

		ServerModule::init();
	}
}
