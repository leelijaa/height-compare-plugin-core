<?php
/**
 * Runs on plugin uninstall (not deactivation). Removes all plugin-owned
 * options, transients, and scheduled cron hooks from the database.
 *
 * @package HeightCompareCore
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Options.
$options = array(
	'hc_nav_sections',
	'hc_default_avatars',
	'hc_twitter_site',
	'hc_flush_rewrites_v1130',
	'hc_removed_pages_v1127',
	'hc_removed_pages_v1128',
);
foreach ( $options as $option ) {
	delete_option( $option );
}

// Transients: versus cache, schema taxonomy cache, preset cache.
global $wpdb;
$wpdb->query(
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '_transient_hc_%'
	    OR option_name LIKE '_transient_timeout_hc_%'"
);

// Cron hooks.
$hooks = array(
	'hc_daily_age_update',
);
foreach ( $hooks as $hook ) {
	$timestamp = wp_next_scheduled( $hook );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, $hook );
	}
}
