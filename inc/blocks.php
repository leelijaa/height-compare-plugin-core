<?php
/**
 * Gutenberg block registration for Height Compare Core.
 *
 * @package HeightCompareCore
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'hc_register_blocks' );

function hc_register_blocks(): void {
	$bio_table_dir = plugin_dir_path( __DIR__ ) . 'blocks/bio-table';
	if ( is_dir( $bio_table_dir ) ) {
		register_block_type( $bio_table_dir );
	}
}
