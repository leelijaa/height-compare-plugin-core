<?php
/**
 * Dev-only sample data seeder (loaded only when WP_DEBUG is true).
 *
 * Adds an admin tools page with a "Seed sample data" button so templates,
 * REST and schema can be built and verified without hand-entering posts.
 * Idempotent: skips anything already present (matched by title).
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sample celebrities: [name, cm, gender, country, category, aliases, volume].
 *
 * @return array<int, array{0: string, 1: float, 2: string, 3: string, 4: string, 5: string, 6: int}>
 */
function hc_sample_celebrities(): array {
	return array(
		array( 'Lionel Messi', 170.0, 'male', 'AR', 'Football', 'Leo Messi', 90500 ),
		array( 'Cristiano Ronaldo', 187.0, 'male', 'PT', 'Football', 'CR7', 74000 ),
		array( 'LeBron James', 206.0, 'male', 'US', 'Basketball', '', 40500 ),
		array( 'Tom Cruise', 170.0, 'male', 'US', 'Actor', '', 60500 ),
		array( 'Taylor Swift', 178.0, 'female', 'US', 'Musician', '', 49500 ),
		array( 'Zendaya', 178.0, 'female', 'US', 'Actor', '', 33100 ),
		array( 'Shaquille O\'Neal', 216.0, 'male', 'US', 'Basketball', 'Shaq', 22200 ),
		array( 'Ariana Grande', 154.0, 'female', 'US', 'Musician', '', 40500 ),
		array( 'Dwayne Johnson', 196.0, 'male', 'US', 'Actor', 'The Rock', 27100 ),
		array( 'Kevin Hart', 157.0, 'male', 'US', 'Comedian', '', 18100 ),
		array( 'Simone Biles', 142.0, 'female', 'US', 'Gymnastics', '', 12100 ),
		array( 'Yao Ming', 229.0, 'male', 'CN', 'Basketball', '', 9900 ),
	);
}

/**
 * Sample height references: [name, cm, category].
 *
 * @return array<int, array{0: string, 1: float, 2: string}>
 */
function hc_sample_references(): array {
	return array(
		array( 'Average Door', 203.0, 'Object' ),
		array( 'Giraffe', 550.0, 'Animal' ),
		array( 'African Elephant', 330.0, 'Animal' ),
		array( 'Basketball Hoop', 305.0, 'Object' ),
		array( 'Emperor Penguin', 120.0, 'Animal' ),
	);
}

/**
 * Sample country averages: [name, iso, male, female, source, year].
 *
 * @return array<int, array{0: string, 1: string, 2: float, 3: float, 4: string, 5: int}>
 */
function hc_sample_countries(): array {
	return array(
		array( 'United States', 'US', 177.0, 163.0, 'CDC NHANES', 2020 ),
		array( 'Netherlands', 'NL', 183.8, 170.4, 'NCD-RisC', 2019 ),
		array( 'Argentina', 'AR', 174.5, 161.0, 'NCD-RisC', 2019 ),
		array( 'Portugal', 'PT', 173.9, 161.0, 'NCD-RisC', 2019 ),
		array( 'China', 'CN', 175.7, 163.5, 'NCD-RisC', 2019 ),
	);
}

/**
 * Register the admin tools page.
 */
function hc_sample_menu(): void {
	add_management_page(
		'Height Compare — Sample Data',
		'HC Sample Data',
		'manage_options',
		'hc-sample-data',
		'hc_sample_page'
	);
}
add_action( 'admin_menu', 'hc_sample_menu' );

/**
 * Render + handle the seeding page.
 */
function hc_sample_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$done = 0;
	if ( isset( $_POST['hc_seed'] )
		&& isset( $_POST['hc_seed_nonce'] )
		&& wp_verify_nonce( sanitize_key( wp_unslash( $_POST['hc_seed_nonce'] ) ), 'hc_seed' )
	) {
		$done = hc_seed_sample_data();
	}
	echo '<div class="wrap"><h1>Height Compare — Sample Data</h1>';
	if ( $done > 0 ) {
		printf( '<div class="notice notice-success"><p>Seeded %d new items.</p></div>', (int) $done );
	}
	echo '<form method="post"><p>Creates sample celebrities, references and country averages for development.</p>';
	wp_nonce_field( 'hc_seed', 'hc_seed_nonce' );
	echo '<p><button class="button button-primary" name="hc_seed" value="1">Seed sample data</button></p></form></div>';
}

/**
 * Insert sample posts. Returns the number created.
 */
function hc_seed_sample_data(): int {
	$created = 0;

	foreach ( hc_sample_celebrities() as $row ) {
		$id = hc_seed_post( 'celebrity', $row[0] );
		if ( $id > 0 ) {
			update_post_meta( $id, 'hc_height_cm', $row[1] );
			update_post_meta( $id, 'hc_gender', $row[2] );
			update_post_meta( $id, 'hc_country', $row[3] );
			update_post_meta( $id, 'hc_category', $row[4] );
			if ( '' !== $row[5] ) {
				update_post_meta( $id, 'hc_aliases', $row[5] );
			}
			update_post_meta( $id, 'hc_search_volume', $row[6] );
			++$created;
		}
	}

	foreach ( hc_sample_references() as $row ) {
		$id = hc_seed_post( 'height_reference', $row[0] );
		if ( $id > 0 ) {
			update_post_meta( $id, 'hc_height_cm', $row[1] );
			update_post_meta( $id, 'hc_category', $row[2] );
			++$created;
		}
	}

	foreach ( hc_sample_countries() as $row ) {
		$id = hc_seed_post( 'country_average', $row[0] );
		if ( $id > 0 ) {
			update_post_meta( $id, 'hc_iso_code', $row[1] );
			update_post_meta( $id, 'hc_avg_male_cm', $row[2] );
			update_post_meta( $id, 'hc_avg_female_cm', $row[3] );
			update_post_meta( $id, 'hc_source', $row[4] );
			update_post_meta( $id, 'hc_year', $row[5] );
			++$created;
		}
	}

	hc_flush_preset_cache();
	return $created;
}

/**
 * Insert a post if none with the same title/type exists.
 *
 * @param string $post_type Post type.
 * @param string $title     Title.
 * @return int New post ID, or 0 if it already existed / failed.
 */
function hc_seed_post( string $post_type, string $title ): int {
	$existing = get_posts(
		array(
			'post_type'      => $post_type,
			'title'          => $title,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( array() !== $existing ) {
		return 0;
	}
	$id = wp_insert_post(
		array(
			'post_type'   => $post_type,
			'post_status' => 'publish',
			'post_title'  => $title,
			'post_content'=> '',
		)
	);
	return ( $id > 0 ) ? $id : 0;
}
