<?php
/**
 * ServerSection class
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * ServerSection class
 *
 * Displays the debug log section in the WordPress admin dashboard widget.
 */
class ServerSection {
	/**
	 * Render debug section.
	 */
	public static function render_section() {
		$list_one   = '';
		$list_two   = '';
		$list_three = '';
		$user_list  = '';

		$list_one .= self::get_php_version_li();
		$list_one .= self::get_wp_core_version_li();
		$list_one .= self::get_revisions_li();
		$list_one .= self::get_plugin_li();
		$list_one .= self::get_theme_li();

		$list_two .= self::get_server_data();
		$list_two .= self::get_https_enforced();
		$list_two .= self::get_field_edits_enabled();

		$list_two .= self::get_cron_li();

		$list_three .= self::get_theme_data();
		$list_three .= self::get_wp_config_writable();

		$user_list .= self::get_user_list();

		// Output.
		$html = <<<HTML
		<div class="server-section section">
			<h3>WP & Server Data</h3>
			<div class='content'>
				<div class='overview'>
					<ul>
						{$list_one}
					</ul>
				</div><!-- .overview -->
				<div class='file-data'>
					<ul>
						{$list_two}
					</ul>
				</div><!-- .file-data -->
				<div class='info'>
					<ul>
						{$list_three}
					</ul>
				</div><!-- .info -->
				<div class='user-data'>
					<ul>
						{$user_list}
					</ul>
				</div><!-- .user-data -->
			</div><!-- .content -->
		</div><!-- .server-section-section -->
		HTML;

		echo $html; //phpcs:ignore
	}

	/**
	 * Get PHP version list item.
	 *
	 * @return string
	 */
	public static function get_php_version_li() {
		$php_version       = phpversion();
		$php_version_level = version_compare( $php_version, '8.0.0' ) >= 0 ? 'good' : 'alert';

		return "<li class='{$php_version_level}'><strong>PHP Version:</strong> $php_version</li>";
	}

	/**
	 * Get WordPress core version list item.
	 *
	 * @return string
	 */
	public static function get_wp_core_version_li() {
		include_once ABSPATH . 'wp-admin/includes/update.php';
		$updates = get_core_updates();

		if ( ! empty( $updates ) && 'upgrade' === $updates[0]->response ) {
			$wp_score = 'alert';
		} else {
			$wp_score = 'good';
		}

		return "<li class='{$wp_score}'><strong>WordPress Version:</strong> " . get_bloginfo( 'version' ) . '</li>';
	}

	/**
	 * Is wp-config writable?
	 *
	 * @return string
	 */
	public static function get_wp_config_writable() {
		$wp_config = ABSPATH . 'wp-config.php';
		if ( is_writable( $wp_config ) ) { // phpcs:ignore
			$wp_score = 'good';
			$value    = 'Yes';
		} else {
			$wp_score = 'alert';
			$value    = 'No';
		}
		return "<li class='{$wp_score}'><strong>wp-config.php writable:</strong> {$value}</li>";
	}

	/**
	 * Is HTTPS enforced
	 *
	 * @return string
	 */
	public static function get_https_enforced() {
		$https_home = strpos( home_url(), 'https://' ) === 0;
		if ( is_ssl() && $https_home ) {
			$https_score = 'good';
			$value       = 'Yes';
		} else {
			$https_score = 'alert';
			$value       = 'No';
		}
		return "<li class='{$https_score}'><strong>HTTPS Enforced:</strong> {$value}</li>";
	}

	/**
	 * Is field edits enabled
	 *
	 * @return bool
	 */
	public static function get_field_edits_enabled() {
		if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
			define( 'DISALLOW_FILE_EDIT', false );
		}
		if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) {
			$score = 'alert';
			$value = 'No';
		} else {
			$score = 'good';
			$value = 'Yes';
		}
		return "<li class='{$score}'><strong>File Editing disallowed:</strong> {$value}</li>";
	}

	/**
	 * Get plugin list item.
	 *
	 * @return string
	 */
	public static function get_plugin_li() {
		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		$update_plugins = get_site_transient( 'update_plugins' );
		$total_count    = count( $all_plugins );
		$active_count   = count( $active_plugins );
		$inactive_count = $total_count - $active_count;
		$needs_update   = 0;

		if ( 0 === $inactive_count ) {
			$inactive_score = 'good';
		} elseif ( 5 < $inactive_count ) {
			$inactive_score = 'warn';
		} else {
			$inactive_score = 'alert';
		}

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			if ( isset( $update_plugins->response[ $plugin_file ] ) ) {
				++$needs_update;
			}
		}

		if ( 0 === $needs_update ) {
			$needs_update_score = 'good';
		} elseif ( 5 <= $needs_update ) {
			$needs_update_score = 'warn';
		} else {
			$needs_update_score = 'alert';
		}

		$html  = '';
		$html .= "<li class='info'><strong>Total Plugins:</strong> {$total_count}</li>";
		$html .= "<li class='{$inactive_score}'><strong>Inactive Plugins:</strong> {$inactive_count}</li>";
		$html .= "<li class='{$needs_update_score}'><strong>Plugins with Updates:</strong> {$needs_update}</li>";

		return $html;
	}

	/**
	 * Get revisions list item.
	 *
	 * @return string
	 */
	public static function get_revisions_li() {
		if ( ! defined( 'WP_POST_REVISIONS' ) ) {
			define( 'WP_POST_REVISIONS', 10 );
		}
		if ( 0 === WP_POST_REVISIONS ) {
			$revisions_score = 'good';
			$value           = 'No';
		} elseif ( 5 < WP_POST_REVISIONS ) {
			$revisions_score = 'alert';
			$value           = WP_POST_REVISIONS;
		} else {
			$revisions_score = 'warn';
			$value           = WP_POST_REVISIONS;
		}
		return "<li class='{$revisions_score}'><strong>Revisions Limit:</strong> {$value}</li>";
	}
	/**
	 * Get theme list item.
	 *
	 * @return string
	 */
	public static function get_theme_li() {
		$total_themes     = count( wp_get_themes() );
		$themes_w_updates = self::wp_get_themes_with_updates();

		if ( 0 === $themes_w_updates ) {
			$theme_score = 'good';
		} elseif ( 2 < $themes_w_updates ) {
			$theme_score = 'warn';
		} else {
			$theme_score = 'alert';
		}

		$html  = '';
		$html .= "<li class='info'><strong>Total Themes:</strong> {$total_themes}</li>";
		$html .= "<li class='{$theme_score}'><strong>Themes with updates:</strong> {$theme_score}</li>";

		return $html;
	}

	/**
	 * Get themes with updates
	 *
	 * @return int
	 */
	public static function wp_get_themes_with_updates() {
		$update_data   = wp_get_themes();
		$theme_updates = get_site_transient( 'update_themes' );

		$count = 0;

		foreach ( $update_data as $slug => $theme ) {
			if (
			isset( $theme_updates->response[ $slug ] )
			&& version_compare( $theme->get( 'Version' ), $theme_updates->response[ $slug ]['new_version'], '<' )
			) {
				++$count;
			}
		}
		return $count;
	}

	/**
	 * Get theme data
	 *
	 * @return string
	 */
	public static function get_theme_data() {
		$theme_name = wp_get_theme()->get( 'Name' );
		$is_child   = wp_get_theme()->parent() ? 'Yes' : 'No';

		$html  = '';
		$html .= "<li class='info'><strong>Current Theme:</strong> {$theme_name}</li>";
		$html .= "<li class='info'><strong>Is child theme:</strong> {$is_child}</li>";

		return $html;
	}

	/**
	 * Get cron list item.
	 *
	 * @return string
	 */
	public static function get_cron_li() {
		$using_wp_cron = ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ? 'No' : 'Yes';
		$crons         = _get_cron_array();
		$late_cron     = 'no';
		foreach ( $crons as $timestamp => $cron_hooks ) {
			if ( $timestamp < time() ) {
				foreach ( $cron_hooks as $hook => $args ) {
					$late_cron = 'yes';
					break 2; // Break out of both loops.
				}
			}
		}
		if ( 'yes' === $late_cron ) {
			$cron_score = 'alert';
		} else {
			$cron_score = 'good';
		}

		$html  = '';
		$html .= "<li class='info'><strong>Using WP Cron:</strong> {$using_wp_cron}</li>";
		$html .= "<li class='{$cron_score}'><strong>Late Cron Jobs:</strong> {$late_cron}</li>";

		return $html;
	}

	/**
	 * Get server data
	 *
	 * @return string
	 */
	public static function get_server_data() {
		$server_info = $GLOBALS['wpdb']->get_var( 'SELECT VERSION()' );
		if ( stripos( $server_info, 'mariadb' ) !== false ) {
			$type = 'MariaDB';
		} else {
			$type = 'MySQL';
		}
		$db_version = $GLOBALS['wpdb']->db_version();

		$html  = '';
		$html .= "<li class='info'><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . '</li>'; // phpcs:ignore
		$html .= "<li class='info'><strong>{$type} version:</strong> {$db_version}</li>";

		return $html;
	}

	/**
	 * Get user list
	 *
	 * @return string
	 */
	public static function get_user_list() {
		$users     = self::get_user_data();
		$role_data = self::get_role_data( $users );

		$html  = '';
		$html .= "<li class='info'><strong>Total Users:</strong> " . count( $users ) . '</li>';
		foreach ( $role_data['by_role'] as $key => $role ) {
			$html .= "<li class='info'><strong>Total {$key}s:</strong> " . $role['count'] . '</li>';
		}
		if ( 0 === $role_data['no_posts'] ) {
			$html .= "<li class='good'><strong>Writers with 0 posts:</strong> " . $role_data['no_posts'] . '</li>';
		} else {
			$html .= "<li class='warn'><strong>Writers with 0 posts:</strong> " . $role_data['no_posts'] . '</li>';
		}

		return $html;
	}
	/**
	 * Get user data.
	 *
	 * @return array
	 */
	public static function get_user_data() {
		$users     = get_users();
		$user_data = array();

		foreach ( $users as $user ) {
			$user_data[] = array(
				'ID'         => $user->ID,
				'login'      => $user->user_login,
				'roles'      => $user->roles, // array of roles.
				'post_count' => count_user_posts( $user->ID, 'any', true ),
			);
		}
		return $user_data;
	}

	/**
	 * Get role data.
	 *
	 * @param array $users The users.
	 * @return array
	 */
	public static function get_role_data( $users ) {
		$roles     = array();
		$role_data = array();
		$no_posts  = 0;

		foreach ( $users as $user ) {
			foreach ( $user['roles'] as $role ) {
				if ( ! isset( $roles[ $role ] ) ) {
					$roles[ $role ] = array(
						'count' => 0,
						'users' => array(),
					);
				}
				++$roles[ $role ]['count'];
				$roles[ $role ]['users'][] = $user;

				$writers = array( 'editory', 'author', 'contributor' );
				if ( in_array( $role, $writers, true ) && 0 === $user['post_count'] ) {
					++$no_posts;
				}
			}
		}
		$role_data = array(
			'by_role'  => $roles,
			'no_posts' => $no_posts,
		);

		return $role_data;
	}
}
