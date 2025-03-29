<?php
/**
 * WordPress Module
 *
 * Displays server data in the WordPress admin dashboard widget.
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * WordPress Module Class
 */
class WordPressModule extends AbstractModule {


	/**
	 * Generate the server data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$data = self::get_data(); // Get the server data.

		$section = new Section(
			title: 'WordPress Data',
			data: $data, // Content will be generated in the render_section method.
			css_id: 'wordpress-data-section' // CSS ID for the section.
		);

		$sections[] = $section;
		return $sections;
	}

	/**
	 * Get server data
	 * Creates and returns an array of server data such as:
	 * - WordPress version
	 *
	 * @return array
	 */
	public static function get_data() {

		include_once ABSPATH . 'wp-admin/includes/update.php';
		$updates = get_core_updates();

		if ( ! empty( $updates ) && 'upgrade' === $updates[0]->response ) {
			$wp_score = 'red';
		} else {
			$wp_score = 'green';
		}

		// Using WP Cron?
		if ( ! defined( 'DISABLE_WP_CRON' ) || ! DISABLE_WP_CRON ) {
			define( 'DISABLE_WP_CRON', false );
		}
		$using_wp_cron = ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ? 'No' : 'Yes';

		// Late cron jobs?
		$crons     = _get_cron_array();
		$late_cron = 'no';
		foreach ( $crons as $timestamp => $cron_hooks ) {
			if ( $timestamp < time() ) {
				foreach ( $cron_hooks as $hook => $args ) {
					$late_cron = 'yes';
					break 2; // Break out of both loops.
				}
			}
		}

		$server_data = array(
			array(
				'label' => 'WordPress Version',
				'value' => get_bloginfo( 'version' ),
				'score' => $wp_score,
			),

			array(
				'label' => 'Total Posts',
				'value' => wp_count_posts()->publish,
				'score' => false,
			),
			array(
				'label' => 'Total Pages',
				'value' => wp_count_posts( 'page' )->publish,
				'score' => false,
			),
			array(
				'label' => 'Using WP Cron',
				'value' => $using_wp_cron,
				'score' => false,
			),
			array(
				'label' => 'Late Cron Jobs',
				'value' => $late_cron,
				'score' => 'yes' === $late_cron ? 'red' : 'green',
			),
			array(
				'label' => 'WordPress Multisite',
				'value' => is_multisite() ? 'Yes' : 'No',
				'score' => false,
			),
			array(
				'label' => 'Revision Limit',
				'value' => WP_POST_REVISIONS,
				'score' => 5 < WP_POST_REVISIONS ? 'red' : 'green',
			),
		);

			return $server_data;
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
				'last_login' => get_user_meta( $user->ID, 'last_login', true ),
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
		$roles           = array();
		$role_data       = array();
		$never_logged_in = 0;
		$no_recent_login = 0;
		$no_posts        = 0;

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

				$last_login     = (int) get_user_meta( $user->ID, 'last_login', true );
				$six_months_ago = strtotime( '-6 months' );

				if ( empty( $user['last_login'] ) ) {
					++$never_logged_in;
				} elseif ( $last_login < $six_months_ago ) {
					++$no_recent_login;
				}

				$writers = array( 'editory', 'author', 'contributor' );
				if ( in_array( $role, $writers, true ) && 0 === $user['post_count'] ) {
					++$no_posts;
				}
			}
		}
		$role_data = array(
			'by_role'         => $roles,
			'never_logged_in' => $never_logged_in,
			'no_posts'        => $no_posts,
		);

		return $role_data;
	}
}
