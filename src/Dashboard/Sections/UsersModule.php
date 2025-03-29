<?php
/**
 * Users Module
 *
 * Displays server data in the Users admin dashboard widget.
 *
 * @package DevLensWP
 */

namespace Devlens\Dashboard\Sections;

use Error;

defined( 'ABSPATH' ) || exit;

/**
 * Users Module Class
 */
class UsersModule extends AbstractModule {


	/**
	 * Generate the server data section
	 *
	 * @param array $sections The sections.
	 * @return array
	 */
	public static function generate_section( $sections ) {

		$data = self::get_data(); // Get the server data.

		$section = new Section(
			title: 'Users Data',
			data: $data, // Content will be generated in the render_section method.
			css_id: 'users-data-section' // CSS ID for the section.
		);

		$sections[] = $section;
		return $sections;
	}

	/**
	 * Get server data
	 * Creates and returns an array of server data such as:
	 * - Users version
	 *
	 * @return array
	 */
	public static function get_data() {

		$users = self::get_user_data();

		$role_data = self::get_role_data( $users );

		$server_data = array(
			array(
				'label' => 'Total Users',
				'value' => count( $users ),
				'score' => false,
			),
		);

		foreach ( $role_data['by_role'] as $key => $role ) {
			$server_data[] = array(
				'label' => 'Total ' . $key . 's',
				'value' => $role['count'],
				'score' => false,
			);
		}

		$server_data[] = array(
			'label' => 'Users with no posts',
			'value' => $role_data['no_posts'],
			'score' => false,
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
