<?php
/**
 * REST API: /wp-json/hc/v1/*
 *
 * The JS tool fetches presets (celebrities + height references) without a
 * page reload. Responses are transient-cached for 15 minutes and flushed
 * whenever relevant posts save.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HC_PRESET_CACHE_GROUP = 'hc_presets_v3_';
const HC_PRESET_CACHE_TTL   = 15 * MINUTE_IN_SECONDS;

/**
 * Register routes.
 */
function hc_register_rest_routes(): void {
	register_rest_route(
		'hc/v1',
		'/presets',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hc_rest_presets',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q'    => array(
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'type' => array(
					'type'              => 'string',
					'default'           => 'all',
					'sanitize_callback' => 'sanitize_key',
				),
				'cat'       => array(
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'sanitize_key',
				),
				'celeb_cat' => array(
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'sanitize_key',
				),
			),
		)
	);

	register_rest_route(
		'hc/v1',
		'/celebrity-cats',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hc_rest_celebrity_cats',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'hc/v1',
		'/countries',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hc_rest_countries',
			'permission_callback' => '__return_true',
		)
	);

	// Celebrity archive "Load More" cards endpoint.
	register_rest_route(
		'hc/v1',
		'/celebrities',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'hc_rest_celebrities',
			'permission_callback' => '__return_true',
			'args'                => array(
				'page' => array(
					'type'              => 'integer',
					'default'           => 1,
					'minimum'           => 1,
					'sanitize_callback' => 'absint',
				),
				'per_page' => array(
					'type'              => 'integer',
					'default'           => 10,
					'minimum'           => 1,
					'maximum'           => 40,
					'sanitize_callback' => 'absint',
				),
				'term_id'  => array(
					'type'              => 'integer',
					'default'           => 0,
					'minimum'           => 0,
					'sanitize_callback' => 'absint',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'hc_register_rest_routes' );

/**
 * GET /hc/v1/presets?q=&type=  — autocomplete data for the tool.
 *
 * @param WP_REST_Request $request Request.
 */
function hc_rest_presets( WP_REST_Request $request ): WP_REST_Response {
	$q         = strtolower( (string) $request->get_param( 'q' ) );
	$type      = (string) $request->get_param( 'type' );
	$cat       = (string) $request->get_param( 'cat' );
	$celeb_cat = (string) $request->get_param( 'celeb_cat' );

	$all     = hc_all_presets();
	$results = array();

	foreach ( $all as $preset ) {
		if ( 'all' !== $type && $preset['type'] !== $type ) {
			continue;
		}
		if ( '' !== $cat && ! in_array( $cat, $preset['cats'] ?? array(), true ) ) {
			continue;
		}
		if ( '' !== $celeb_cat && ! in_array( $celeb_cat, $preset['celeb_cats'] ?? array(), true ) ) {
			continue;
		}
		if ( '' !== $q ) {
			$haystack = strtolower( $preset['name'] . ' ' . $preset['aliases'] );
			if ( ! str_contains( $haystack, $q ) ) {
				continue;
			}
		}
		unset( $preset['aliases'] );
		$results[] = $preset;
		if ( count( $results ) >= 20 ) {
			break;
		}
	}

	$response = new WP_REST_Response( $results );
	$response->header( 'Cache-Control', 'public, max-age=900' );
	return $response;
}

/**
 * All published presets, transient-cached.
 *
 * @return array<int, array{id:int,type:string,name:string,cm:float,gender:string,url:string,aliases:string,cats:list<string>,celeb_cats:list<string>}>
 */
function hc_all_presets(): array {
	$cached = get_transient( HC_PRESET_CACHE_GROUP . 'all' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	// One query for both hc_preset_cat (references) and celebrity_group (celebrities).
	global $wpdb;
	$term_rows = $wpdb->get_results(
		"SELECT tr.object_id, t.slug, tt.taxonomy
		 FROM {$wpdb->term_relationships} tr
		 JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
		 JOIN {$wpdb->terms} t          ON tt.term_id           = t.term_id
		 WHERE tt.taxonomy IN ('hc_preset_cat','celebrity_group')",
		ARRAY_A
	);
	$ref_cat_map = array();
	$cel_cat_map = array();
	foreach ( (array) $term_rows as $row ) {
		$id = (int) $row['object_id'];
		if ( 'hc_preset_cat' === $row['taxonomy'] ) {
			$ref_cat_map[ $id ][] = $row['slug'];
		} else {
			$cel_cat_map[ $id ][] = $row['slug'];
		}
	}

	$posts = get_posts(
		array(
			'post_type'      => array( 'celebrity', 'height_reference' ),
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$presets = array();
	foreach ( $posts as $post ) {
		$cm = (float) get_post_meta( $post->ID, 'hc_height_cm', true );
		if ( $cm <= 0 ) {
			continue;
		}
		$is_celebrity = 'celebrity' === $post->post_type;
		$preset_entry = array(
			'id'         => $post->ID,
			'type'       => $is_celebrity ? 'celebrity' : 'reference',
			'name'       => html_entity_decode( get_the_title( $post ), ENT_QUOTES ),
			'cm'         => $cm,
			'gender'     => $is_celebrity
				? hc_sanitize_gender( get_post_meta( $post->ID, 'hc_gender', true ) )
				: 'object',
			'url'        => (string) get_permalink( $post ),
			'aliases'    => (string) get_post_meta( $post->ID, 'hc_aliases', true ),
			'cats'       => $is_celebrity ? array() : ( $ref_cat_map[ $post->ID ] ?? array() ),
			'celeb_cats' => $is_celebrity ? ( $cel_cat_map[ $post->ID ] ?? array() ) : array(),
		);
		if ( $is_celebrity ) {
			$thumb = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );
			if ( is_string( $thumb ) && '' !== $thumb ) {
				$preset_entry['avatarUrl']      = $thumb;
				$preset_entry['preserveColors'] = true;
			}
		}
		$presets[] = $preset_entry;
	}

	set_transient( HC_PRESET_CACHE_GROUP . 'all', $presets, HC_PRESET_CACHE_TTL );
	return $presets;
}

/**
 * GET /hc/v1/celebrity-cats — leaf celebrity groups for the tool dropdown.
 * Returns only child terms (parent > 0) so the dropdown shows actionable groups.
 */
function hc_rest_celebrity_cats(): WP_REST_Response {
	$terms = get_terms(
		array(
			'taxonomy'   => 'celebrity_group',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	$data = array();
	foreach ( (array) $terms as $term ) {
		if ( $term instanceof WP_Term ) {
			$data[] = array(
				'slug'  => $term->slug,
				'name'  => $term->name,
				'count' => (int) $term->count,
			);
		}
	}
	$response = new WP_REST_Response( $data );
	$response->header( 'Cache-Control', 'public, max-age=300' );
	return $response;
}

/**
 * GET /hc/v1/countries — average heights for reference lines.
 */
function hc_rest_countries(): WP_REST_Response {
	$cached = get_transient( HC_PRESET_CACHE_GROUP . 'countries' );
	if ( ! is_array( $cached ) ) {
		$posts  = get_posts(
			array(
				'post_type'      => 'country_average',
				'post_status'    => 'publish',
				'posts_per_page' => 300,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		$cached = array();
		foreach ( $posts as $post ) {
			$cached[] = array(
				'name'   => html_entity_decode( get_the_title( $post ), ENT_QUOTES ),
				'iso'    => (string) get_post_meta( $post->ID, 'hc_iso_code', true ),
				'male'   => (float) get_post_meta( $post->ID, 'hc_avg_male_cm', true ),
				'female' => (float) get_post_meta( $post->ID, 'hc_avg_female_cm', true ),
				'url'    => (string) get_permalink( $post ),
			);
		}
		set_transient( HC_PRESET_CACHE_GROUP . 'countries', $cached, HC_PRESET_CACHE_TTL );
	}

	$response = new WP_REST_Response( $cached );
	$response->header( 'Cache-Control', 'public, max-age=900' );
	return $response;
}

/**
 * Flush preset transients (called from save handlers).
 */
function hc_flush_preset_cache(): void {
	delete_transient( HC_PRESET_CACHE_GROUP . 'all' );
	delete_transient( HC_PRESET_CACHE_GROUP . 'countries' );
}

/**
 * Also flush when posts change outside the meta-box path (quick edit, REST).
 *
 * @param int $post_id Post ID.
 */
function hc_flush_on_save( int $post_id ): void {
	$type = get_post_type( $post_id );
	if ( in_array( $type, array( 'celebrity', 'height_reference', 'country_average' ), true ) ) {
		hc_flush_preset_cache();
	}
}
add_action( 'save_post', 'hc_flush_on_save' );
add_action( 'deleted_post', 'hc_flush_on_save' );

/**
 * GET /hc/v1/celebrities?page=N&per_page=N
 * Returns rendered celebrity card HTML + pagination meta for Load More.
 *
 * @param WP_REST_Request $request Request.
 */
function hc_rest_celebrities( WP_REST_Request $request ): WP_REST_Response {
	$page     = (int) $request->get_param( 'page' );
	$per_page = (int) $request->get_param( 'per_page' );
	$term_id  = absint( $request->get_param( 'term_id' ) );

	$query_args = array(
		'post_type'      => 'celebrity',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $page,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( $term_id > 0 ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'celebrity_group',
				'field'    => 'term_id',
				'terms'    => $term_id,
			),
		);
	}

	$query = new WP_Query( $query_args );

	$cards = array();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			ob_start();
			hc_celebrity_card( get_post() );
			$cards[] = ob_get_clean();
		}
		wp_reset_postdata();
	}

	$response = new WP_REST_Response(
		array(
			'cards'       => $cards,
			'page'        => $page,
			'total_pages' => (int) $query->max_num_pages,
			'has_more'    => $page < (int) $query->max_num_pages,
		)
	);
	$response->header( 'Cache-Control', 'public, max-age=120' );
	return $response;
}
