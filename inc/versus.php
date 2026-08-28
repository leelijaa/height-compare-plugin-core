<?php
/**
 * Versus page data builder + transient cache.
 *
 * A versus request carries "slug-a-vs-slug-b" (slugs without the -height
 * suffix). We resolve both celebrities, cache the resolved pair for a day,
 * and expose a data getter to the template, schema and SEO layers.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HC_VERSUS_TTL = DAY_IN_SECONDS;

/**
 * Split a versus key into its two celebrity slugs.
 *
 * "lionel-messi-vs-cristiano-ronaldo" → ['lionel-messi', 'cristiano-ronaldo'].
 * Ambiguity (hyphens in names) is resolved by splitting on the first "-vs-".
 *
 * @param string $key Versus key.
 * @return array{0: string, 1: string}|null
 */
function hc_versus_split( string $key ): ?array {
	$pos = strpos( $key, '-vs-' );
	if ( false === $pos ) {
		return null;
	}
	$a = substr( $key, 0, $pos );
	$b = substr( $key, $pos + 4 );
	if ( '' === $a || '' === $b ) {
		return null;
	}
	return array( $a, $b );
}

/**
 * Resolve a celebrity post from a slug that may or may not carry -height.
 *
 * @param string $slug Base slug.
 */
function hc_find_celebrity( string $slug ): ?WP_Post {
	foreach ( array( $slug . '-height', $slug ) as $candidate ) {
		$post = get_page_by_path( $candidate, OBJECT, 'celebrity' );
		if ( $post instanceof WP_Post ) {
			return $post;
		}
	}
	return null;
}

/**
 * Build (and cache) the data bundle for a versus key.
 *
 * @param string $key Versus key.
 * @return array{a: array, b: array, diff_cm: float, taller: string, indexable: bool, key: string}|null
 */
function hc_versus_data( string $key ): ?array {
	static $memo = array();
	if ( isset( $memo[ $key ] ) ) {
		/** @var array{a: array, b: array, diff_cm: float, taller: string, indexable: bool, key: string}|false $cached_memo */
		$cached_memo = $memo[ $key ];
		return ( false === $cached_memo ) ? null : $memo[ $key ];
	}

	$cache_id = 'hc_versus_' . md5( $key );
	$cached   = get_transient( $cache_id );
	if ( is_array( $cached ) ) {
		$memo[ $key ] = $cached;
		/** @var array{a: array, b: array, diff_cm: float, taller: string, indexable: bool, key: string} $cached */
		return $cached;
	}

	$pair = hc_versus_split( $key );
	if ( null === $pair ) {
		$memo[ $key ] = false;
		return null;
	}

	$post_a = hc_find_celebrity( $pair[0] );
	$post_b = hc_find_celebrity( $pair[1] );
	if ( ! $post_a instanceof WP_Post || ! $post_b instanceof WP_Post ) {
		$memo[ $key ] = false;
		return null;
	}

	$a = hc_celebrity_data( $post_a );
	$b = hc_celebrity_data( $post_b );

	$diff   = abs( $a['cm'] - $b['cm'] );
	$taller = ( $a['cm'] >= $b['cm'] ) ? $a['name'] : $b['name'];

	$data = array(
		'a'         => $a,
		'b'         => $b,
		'diff_cm'   => $diff,
		'taller'    => $taller,
		'indexable' => ( $a['volume'] >= 100 && $b['volume'] >= 100 ),
		'key'       => $key,
	);

	set_transient( $cache_id, $data, HC_VERSUS_TTL );
	$memo[ $key ] = $data;
	return $data;
}

/**
 * Human-readable versus title.
 *
 * @param string $key Versus key.
 */
function hc_versus_title( string $key ): string {
	$data = hc_versus_data( $key );
	if ( null === $data ) {
		return 'Height Comparison';
	}
	return sprintf( '%s vs %s Height', $data['a']['name'], $data['b']['name'] );
}

/**
 * Canonical URL for a versus key.
 *
 * @param string $key Versus key.
 */
function hc_versus_url( string $key ): string {
	return home_url( '/compare/' . $key . '-height/' );
}
