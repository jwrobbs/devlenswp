<?php
/**
 * Hooks class
 *
 * @package DevLensWP
 */

namespace Devlens_Common;

use Devlens\Dashboard\DashboardPage;
use Devlens\Dashboard\Sections\PluginsModule;
use Devlens\Dashboard\Sections\ServerModule;
use Devlens\Dashboard\Sections\WordPressModule;

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
		WordPressModule::init();
		PluginsModule::init();
	}
}
