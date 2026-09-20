<?php
/**
 * Plugin Name: Height Compare Core
 * Plugin URI:  https://github.com/wasimrejalijaofficially/height-compare
 * Description: CPTs, taxonomies, REST API, URL routing, and admin data layer for the Height Compare theme. Must be active for the theme to work.
 * Version:     1.0.0
 * Requires at least: 6.3
 * Tested up to: 6.7
 * Requires PHP: 8.1
 * License:     GPL-2.0-or-later
 *
 * @package HeightCompareCore
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/cpt.php';
require_once __DIR__ . '/inc/avatar-cpt.php';
require_once __DIR__ . '/inc/rewrites.php';
require_once __DIR__ . '/inc/rest-api.php';
require_once __DIR__ . '/inc/nav-sections.php';
require_once __DIR__ . '/inc/sitemaps.php';
require_once __DIR__ . '/inc/versus.php';
require_once __DIR__ . '/inc/svg-support.php';
require_once __DIR__ . '/inc/meta-boxes.php';
require_once __DIR__ . '/inc/blocks.php';

if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG )
	|| ( function_exists( 'wp_get_environment_type' ) &&
	     in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) )
) {
	require_once __DIR__ . '/inc/sample-data.php';
}

register_activation_hook(
	__FILE__,
	static function (): void {
		hc_register_post_types();
		hc_register_celebrity_group();
		hc_register_avatar_cpt();
		hc_register_avatar_taxonomies();
		hc_add_rewrite_rules();
		flush_rewrite_rules();
	}
);
