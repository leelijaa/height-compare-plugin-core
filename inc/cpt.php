<?php
/**
 * Custom post types: celebrity, height_reference, country_average.
 *
 * All meta is registered with explicit types + sanitizers and exposed in
 * REST (show_in_rest) so the JS tool can fetch presets without reloads.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta field definitions per post type.
 *
 * @return array<string, array<string, array{type: string, sanitize: callable, label: string}>>
 */
function hc_meta_fields(): array {
	return array(
		'celebrity'        => array(
			'hc_height_cm'     => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Height (cm)',
			),
			'hc_gender'        => array(
				'type'     => 'string',
				'sanitize' => 'hc_sanitize_gender',
				'label'    => 'Gender',
			),
			'hc_country'       => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Country (ISO 3166-1 alpha-2, e.g. US)',
			),
			'hc_category'      => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Category / Sport (e.g. Basketball, Actor)',
			),
			'hc_aliases'       => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Aliases (comma separated)',
			),
			'hc_source_url'    => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
				'label'    => 'Source URL',
			),
			'hc_search_volume' => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
				'label'    => 'Monthly search volume (versus pages index at ≥ 100)',
			),
			'hc_birthplace'    => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Birthplace',
			),
			'hc_weight_kg'     => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
				'label'    => 'Weight (kg)',
			),
			'hc_eye_color'         => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Eye Colour',
			),
			'hc_hair_color'        => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Hair Color',
			),
			'hc_body_color'        => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Body Color',
			),
			'hc_birth_name'        => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Birth Name',
			),
			'hc_full_name'         => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Full Name',
			),
			'hc_nickname'          => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Nickname',
			),
			'hc_profession'        => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Profession',
			),
			'hc_school'            => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'School',
			),
			'hc_college'           => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'College',
			),
			'hc_father_name'       => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => "Father's Name",
			),
			'hc_mother_name'       => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => "Mother's Name",
			),
			'hc_siblings'          => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Siblings',
			),
			'hc_marital_status'    => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Marital Status',
			),
			'hc_girlfriend_name'   => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Girlfriend Name',
			),
			'hc_wife_name'         => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Wife Name',
			),
			'hc_friends_names'     => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Friends Name',
			),
			'hc_religion'          => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Religion',
			),
			'hc_hometown'          => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Hometown',
			),
			'hc_current_address'   => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Current Address',
			),
			'hc_children'          => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Children',
			),
			'hc_hobbies'           => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Hobbies',
			),
			'hc_awards'            => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Awards',
			),
			'hc_net_worth'         => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Net Worth',
			),
			'hc_monthly_earning'   => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Monthly Earning',
			),
			'hc_wingspan_cm'       => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Wingspan (cm)',
			),
			'hc_leg_length_cm'     => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Leg Length / Inseam (cm)',
			),
			'hc_torso_length_cm'   => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Torso Length (cm)',
			),
			'hc_shoulder_width_cm' => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Shoulder Width (cm)',
			),
			'hc_hip_width_cm'      => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Hip Width (cm)',
			),
			'hc_hand_size_cm'      => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Hand Size (cm)',
			),
			'hc_foot_size_cm'      => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Foot Size (cm)',
			),
			'hc_body_source_url'   => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
				'label'    => 'Body Measurements Source URL',
			),
			'hc_body_source_label' => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Body Measurements Source Label',
			),
		),
		'height_reference' => array(
			'hc_height_cm' => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Height (cm)',
			),
			'hc_category'  => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Category (Animal, Building, Object, Fictional)',
			),
		),
		'country_average'  => array(
			'hc_iso_code'      => array(
				'type'     => 'string',
				'sanitize' => 'hc_sanitize_iso',
				'label'    => 'ISO 3166-1 alpha-2 code (e.g. NL)',
			),
			'hc_avg_male_cm'   => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Average male height (cm)',
			),
			'hc_avg_female_cm' => array(
				'type'     => 'number',
				'sanitize' => 'hc_sanitize_height',
				'label'    => 'Average female height (cm)',
			),
			'hc_source'        => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
				'label'    => 'Data source',
			),
			'hc_year'          => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
				'label'    => 'Data year',
			),
		),
	);
}

/**
 * Sanitize a height value in cm. Accepts 30–30000 (child to skyscraper).
 *
 * @param mixed $value Raw value.
 */
function hc_sanitize_height( $value ): float {
	$v = (float) $value;
	if ( $v < 1.0 ) {
		return 0.0;
	}
	return round( min( $v, 30000.0 ), 1 );
}

/**
 * Sanitize gender to a known silhouette set.
 *
 * @param mixed $value Raw value.
 */
function hc_sanitize_gender( $value ): string {
	$v = is_string( $value ) ? strtolower( $value ) : '';
	return in_array( $v, array( 'male', 'female', 'child', 'object' ), true ) ? $v : 'male';
}

/**
 * Sanitize an ISO alpha-2 code.
 *
 * @param mixed $value Raw value.
 */
function hc_sanitize_iso( $value ): string {
	$v = is_string( $value ) ? strtoupper( preg_replace( '/[^A-Za-z]/', '', $value ) ?? '' ) : '';
	return substr( $v, 0, 2 );
}

/**
 * Register the three CPTs.
 */
function hc_register_post_types(): void {
	register_post_type(
		'celebrity',
		array(
			'labels'       => array(
				'name'          => 'Celebrities',
				'singular_name' => 'Celebrity',
				'add_new_item'  => 'Add New Celebrity',
				'edit_item'     => 'Edit Celebrity',
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-groups',
			'menu_position'=> 20,
			'supports'     => array( 'title', 'thumbnail' ),
			'rewrite'      => array(
				'slug'       => 'celebrity',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	register_post_type(
		'height_reference',
		array(
			'labels'       => array(
				'name'          => 'Height References',
				'singular_name' => 'Height Reference',
				'add_new_item'  => 'Add New Height Reference',
			),
			'public'       => true,
			'has_archive'  => false,
			'menu_icon'    => 'dashicons-building',
			'menu_position'=> 21,
			'supports'     => array( 'title', 'thumbnail', 'editor' ),
			'rewrite'      => array(
				'slug'       => 'reference',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	register_post_type(
		'country_average',
		array(
			'labels'       => array(
				'name'          => 'Country Averages',
				'singular_name' => 'Country Average',
				'add_new_item'  => 'Add New Country',
			),
			'public'       => true,
			'has_archive'  => false,
			'menu_icon'    => 'dashicons-admin-site-alt3',
			'menu_position'=> 22,
			'supports'     => array( 'title', 'editor' ),
			'rewrite'      => array(
				'slug'       => 'average-height',
				'with_front' => false,
			),
			'show_in_rest' => true,
		)
	);

	foreach ( hc_meta_fields() as $post_type => $fields ) {
		foreach ( $fields as $key => $def ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $def['type'],
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $def['sanitize'],
					'auth_callback'     => static function (): bool {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'hc_register_post_types' );

/**
 * Register Celebrity Groups hierarchical taxonomy.
 * Parent terms (Profession, Country, Age Group…) become hub pages.
 * Child terms (Actors, US, Under 30…) become celebrity card grid pages.
 * URL structure: /celebrity/{group}/{term}/ for leaves, /celebrity/{group}/ for hubs.
 */
function hc_register_celebrity_group(): void {
	register_taxonomy(
		'celebrity_group',
		array( 'celebrity' ),
		array(
			'labels'            => array(
				'name'              => 'Celebrity Groups',
				'singular_name'     => 'Celebrity Group',
				'add_new_item'      => 'Add New Group',
				'edit_item'         => 'Edit Group',
				'search_items'      => 'Search Groups',
				'all_items'         => 'All Groups',
				'not_found'         => 'No groups found.',
				'menu_name'         => 'Celebrity Groups',
				'parent_item'       => 'Parent Group',
				'parent_item_colon' => 'Parent Group:',
			),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => 'celebrity',
				'hierarchical' => true,
				'with_front'   => false,
			),
		)
	);
}
add_action( 'init', 'hc_register_celebrity_group', 11 );

/**
 * One-time migration: strip -height suffix from all celebrity slugs.
 * Runs once on admin_init, guarded by an option flag.
 */
function hc_migrate_celebrity_slugs(): void {
	if ( get_option( 'hc_slugs_migrated_v1200' ) ) {
		return;
	}
	$posts = get_posts(
		array(
			'post_type'      => 'celebrity',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ( $posts as $post_id ) {
		$slug = (string) get_post_field( 'post_name', $post_id );
		if ( str_ends_with( $slug, '-height' ) ) {
			wp_update_post(
				array(
					'ID'        => $post_id,
					'post_name' => substr( $slug, 0, -7 ),
				)
			);
		}
	}
	update_option( 'hc_slugs_migrated_v1200', true );
	flush_rewrite_rules( false );
}
add_action( 'admin_init', 'hc_migrate_celebrity_slugs' );

/**
 * Seed default Celebrity Group terms on first activation.
 * Runs once, guarded by an option flag.
 */
function hc_seed_celebrity_groups(): void {
	if ( get_option( 'hc_groups_seeded_v1200' ) ) {
		return;
	}
	$defaults = array(
		'Profession' => array( 'Actors', 'Actresses', 'Businessmen' ),
		'Country'    => array( 'US', 'UK', 'India', 'China' ),
		'Age Group'  => array( 'Under 30', '30 to 50', 'Over 50' ),
	);
	foreach ( $defaults as $parent_name => $children ) {
		$parent = term_exists( $parent_name, 'celebrity_group' );
		if ( ! $parent ) {
			$parent = wp_insert_term( $parent_name, 'celebrity_group' );
		}
		if ( is_wp_error( $parent ) ) {
			continue;
		}
		$parent_id = (int) ( $parent['term_id'] ?? 0 );
		foreach ( $children as $child_name ) {
			if ( ! term_exists( $child_name, 'celebrity_group', $parent_id ) ) {
				wp_insert_term( $child_name, 'celebrity_group', array( 'parent' => $parent_id ) );
			}
		}
	}
	update_option( 'hc_groups_seeded_v1200', true );
}
add_action( 'admin_init', 'hc_seed_celebrity_groups' );

/**
 * One-time seed: ensure a Height Group parent term exists.
 * Guarded by a v1201 flag so it runs on existing sites that already have v1200.
 */
function hc_seed_height_group(): void {
	if ( get_option( 'hc_groups_seeded_v1201' ) ) {
		return;
	}
	if ( ! term_exists( 'Height Group', 'celebrity_group' ) ) {
		wp_insert_term( 'Height Group', 'celebrity_group' );
	}
	update_option( 'hc_groups_seeded_v1201', true );
}
add_action( 'admin_init', 'hc_seed_height_group' );

/**
 * Register hc_dob (YYYY-MM-DD) post meta for celebrity.
 */
function hc_register_dob_meta(): void {
	register_post_meta(
		'celebrity',
		'hc_dob',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => static function ( $v ): string {
				$s = sanitize_text_field( (string) $v );
				return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $s ) ? $s : '';
			},
			'auth_callback'     => static function (): bool {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'hc_register_dob_meta' );

/**
 * Find or create an exact age term (e.g. "28") under the "Age Group" parent
 * and assign it to the celebrity. Removes any previously assigned age terms first.
 *
 * @param int $post_id Celebrity post ID.
 */
function hc_assign_age_group( int $post_id ): void {
	$dob = (string) get_post_meta( $post_id, 'hc_dob', true );
	if ( '' === $dob ) {
		return;
	}
	$birth = DateTimeImmutable::createFromFormat( 'Y-m-d', $dob );
	if ( ! $birth instanceof DateTimeImmutable ) {
		return;
	}
	$age = (int) $birth->diff( new DateTimeImmutable( 'today' ) )->y;

	// Parent could be 'age' or 'age-group' depending on when the term was created.
	$parent = get_term_by( 'slug', 'age', 'celebrity_group' );
	if ( ! $parent instanceof WP_Term ) {
		$parent = get_term_by( 'slug', 'age-group', 'celebrity_group' );
	}
	if ( ! $parent instanceof WP_Term ) {
		return;
	}

	$age_name = (string) $age;
	$term     = get_term_by( 'slug', $age_name, 'celebrity_group' );
	if ( ! $term instanceof WP_Term ) {
		$result = wp_insert_term( $age_name, 'celebrity_group', array( 'parent' => $parent->term_id ) );
		if ( is_wp_error( $result ) ) {
			return;
		}
		$term = get_term( (int) $result['term_id'], 'celebrity_group' );
	}
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	// Remove previously assigned age group child terms (siblings of the new term).
	$current = get_the_terms( $post_id, 'celebrity_group' );
	if ( $current && ! is_wp_error( $current ) ) {
		$to_remove = array();
		foreach ( $current as $t ) {
			if ( (int) $t->parent === (int) $parent->term_id && (int) $t->term_id !== (int) $term->term_id ) {
				$to_remove[] = $t->term_id;
			}
		}
		if ( ! empty( $to_remove ) ) {
			wp_remove_object_terms( $post_id, $to_remove, 'celebrity_group' );
		}
	}

	wp_set_object_terms( $post_id, (int) $term->term_id, 'celebrity_group', true );
}

/**
 * Find or create an exact height term (e.g. "175 cm") under "Height Group" and assign it.
 * Removes any previously assigned height group child terms first.
 *
 * @param int $post_id Celebrity post ID.
 */
function hc_assign_height_group( int $post_id ): void {
	$cm = (float) get_post_meta( $post_id, 'hc_height_cm', true );
	if ( $cm <= 0 ) {
		return;
	}
	$rounded = (int) round( $cm );

	$parent = get_term_by( 'slug', 'height-group', 'celebrity_group' );
	if ( ! $parent instanceof WP_Term ) {
		// Auto-create if the admin_init seeder hasn't run yet.
		$result = wp_insert_term( 'Height Group', 'celebrity_group' );
		if ( is_wp_error( $result ) ) {
			return;
		}
		$parent = get_term( (int) $result['term_id'], 'celebrity_group' );
	}
	if ( ! $parent instanceof WP_Term ) {
		return;
	}

	$height_name = $rounded . ' cm';
	$height_slug = $rounded . '-cm';

	$term = get_term_by( 'slug', $height_slug, 'celebrity_group' );
	if ( ! $term instanceof WP_Term ) {
		$result = wp_insert_term(
			$height_name,
			'celebrity_group',
			array(
				'parent' => $parent->term_id,
				'slug'   => $height_slug,
			)
		);
		if ( is_wp_error( $result ) ) {
			return;
		}
		$term = get_term( (int) $result['term_id'], 'celebrity_group' );
	}
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$current = get_the_terms( $post_id, 'celebrity_group' );
	if ( $current && ! is_wp_error( $current ) ) {
		$to_remove = array();
		foreach ( $current as $t ) {
			if ( (int) $t->parent === (int) $parent->term_id && (int) $t->term_id !== (int) $term->term_id ) {
				$to_remove[] = $t->term_id;
			}
		}
		if ( ! empty( $to_remove ) ) {
			wp_remove_object_terms( $post_id, $to_remove, 'celebrity_group' );
		}
	}

	wp_set_object_terms( $post_id, (int) $term->term_id, 'celebrity_group', true );
}

/**
 * Schedule the daily age-group sync cron if not already registered.
 */
function hc_schedule_daily_age_sync(): void {
	if ( ! wp_next_scheduled( 'hc_daily_age_sync' ) ) {
		wp_schedule_event( time(), 'daily', 'hc_daily_age_sync' );
	}
}
add_action( 'init', 'hc_schedule_daily_age_sync' );

/**
 * Cron callback: recalculate the age group term for every published celebrity that has a DOB.
 * On each celebrity's birthday the old age term is swapped for the new one automatically.
 */
function hc_run_daily_age_sync(): void {
	$posts = get_posts(
		array(
			'post_type'      => 'celebrity',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => 'hc_dob',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	foreach ( $posts as $post_id ) {
		hc_assign_age_group( (int) $post_id );
	}
}
add_action( 'hc_daily_age_sync', 'hc_run_daily_age_sync' );
