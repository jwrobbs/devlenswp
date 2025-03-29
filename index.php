<?php
/**
 * Plugin Name:     DevLensWP
 * Description:     Site diagnosis and developer tools for WordPress.
 * Version:         0.1.0
 * Author:          Josh Robbs
 * Author URI:      https://joshrobbs.com
 * License:         The Unlicense
 * License URI:     https://unlicense.org/
 * Text Domain:     devlenswp
 *
 * @package DevLensWP
 */

use Devlens_Common\Hooks;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/common/constants.php';

Hooks::init();
