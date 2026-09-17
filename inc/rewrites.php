<?php
/**
 * Custom rewrite rules: versus pages, sitemaps, share-URL query vars.
 *
 * /compare/{a}-vs-{b}-height/  → virtual versus page (templates/versus.php)
 * /sitemap.xml + children      → inc/sitemaps.php
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register query vars.
 *
 * @param array<int, string> $vars Public query vars.
 * @return array<int, string>
 */
function hc_query_vars( array $vars ): array {
	$vars[] = 'hc_versus';  // "slug-a-vs-slug-b" (without -height suffix).
	$vars[] = 'hc_sitemap'; // sitemap name: index|pages|celebrities|countries|blog|versus.
	$vars[] = 's';          // share state (already core search var; tool reads ?s= client-side only).
	return array_values( array_unique( $vars ) );
}
add_filter( 'query_vars', 'hc_query_vars' );

/**
 * Add rewrite rules. Called on init and from the activation hook before
 * flush_rewrite_rules().
 */
function hc_add_rewrite_rules(): void {
	// Single segment: /celebrity/{slug}/
	// Treated as celebrity_group first; the request filter remaps to CPT
	// when the slug matches an actual celebrity post.
	add_rewrite_rule(
		'^celebrity/([a-z0-9-]+)/?$',
		'index.php?celebrity_group=$matches[1]',
		'top'
	);

	// Two-level: /celebrity/{parent}/{child}/ → leaf term slug only.
	add_rewrite_rule(
		'^celebrity/([a-z0-9-]+)/([a-z0-9-]+)/?$',
		'index.php?celebrity_group=$matches[2]',
		'top'
	);

	// Versus: /compare/lionel-messi-vs-cristiano-ronaldo-height/
	add_rewrite_rule(
		'^compare/([a-z0-9-]+-vs-[a-z0-9-]+)-height/?$',
		'index.php?hc_versus=$matches[1]',
		'top'
	);

	// Sitemaps.
	add_rewrite_rule( '^sitemap\.xml$', 'index.php?hc_sitemap=index', 'top' );
	add_rewrite_rule(
		'^sitemap-(pages|celebrities|countries|blog|versus|celebrity-groups|celebrity-cats)\.xml$',
		'index.php?hc_sitemap=$matches[1]',
		'top'
	);
}
add_action( 'init', 'hc_add_rewrite_rules' );

/**
 * Force the hc_sitemap query var for sitemap URLs, dropping core's competing
 * `sitemap` var. Deterministic regardless of rewrite-rule ordering, and it
 * runs during parse_request (before canonical redirects), so /sitemap.xml is
 * ours rather than core's disabled /wp-sitemap.xml.
 *
 * @param array<string, mixed> $vars Query vars.
 * @return array<string, mixed>
 */
function hc_force_sitemap_query( array $vars ): array {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	if ( preg_match( '#/sitemap(?:-(pages|celebrities|countries|blog|versus|celebrity-groups|celebrity-cats))?\.xml$#', $path, $m ) === 1 ) {
		unset( $vars['sitemap'], $vars['sitemap-subtype'], $vars['sitemap-stylesheet'] );
		$vars['hc_sitemap'] = ( '' !== ( $m[1] ?? '' ) ) ? $m[1] : 'index';
	}
	return $vars;
}
add_filter( 'request', 'hc_force_sitemap_query' );

/**
 * Resolve URL ambiguity under /celebrity/.
 *
 * WordPress's hierarchical taxonomy rule (celebrity/.+?) matches BOTH
 * taxonomy terms (/celebrity/profession/) and celebrity posts (/celebrity/kevin-hart/).
 * This filter checks single-segment URLs: if the slug belongs to a celebrity
 * post, reroute to the CPT single instead of the taxonomy archive.
 *
 * @param array<string, mixed> $vars Query vars.
 * @return array<string, mixed>
 */
function hc_resolve_celebrity_group_url( array $vars ): array {
	if ( ! isset( $vars['celebrity_group'] ) ) {
		return $vars;
	}
	$slug     = (string) $vars['celebrity_group'];
	$cache_key = 'hc_celeb_slug_' . md5( $slug );
	$cached   = get_transient( $cache_key );

	if ( false === $cached ) {
		$posts = get_posts(
			array(
				'post_type'      => 'celebrity',
				'name'           => $slug,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$cached = ! empty( $posts ) ? 'yes' : 'no';
		set_transient( $cache_key, $cached, HOUR_IN_SECONDS );
	}

	if ( 'yes' === $cached ) {
		unset( $vars['celebrity_group'] );
		$vars['name']      = $slug;
		$vars['post_type'] = 'celebrity';
	}
	return $vars;
}
add_filter( 'request', 'hc_resolve_celebrity_group_url', 5 );

/**
 * 301 redirect old /celebrity/{slug}-height/ URLs to the clean /celebrity/{slug}/.
 * Runs only on 404s so it has zero cost on normal requests.
 */
function hc_redirect_height_urls(): void {
	if ( ! is_404() ) {
		return;
	}
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
	if ( preg_match( '#^celebrity/([a-z0-9-]+)-height$#', $path, $m ) ) {
		$posts = get_posts(
			array(
				'post_type'      => 'celebrity',
				'name'           => $m[1],
				'post_status'    => 'publish',
				'posts_per_page' => 1,
			)
		);
		if ( ! empty( $posts ) ) {
			wp_safe_redirect( (string) get_permalink( $posts[0] ), 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'hc_redirect_height_urls' );

/**
 * Route versus + sitemap requests to their handlers/templates.
 *
 * @param string $template Resolved template path.
 */
function hc_template_router( string $template ): string {
	$sitemap = get_query_var( 'hc_sitemap' );
	if ( is_string( $sitemap ) && '' !== $sitemap ) {
		hc_render_sitemap( $sitemap ); // Exits.
	}

	$versus = get_query_var( 'hc_versus' );
	if ( is_string( $versus ) && '' !== $versus ) {
		$data = hc_versus_data( $versus );
		if ( null === $data ) {
			// Unknown pair: genuine 404.
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			return get_404_template();
		}
		status_header( 200 );
		return HC_DIR . '/templates/versus.php';
	}

	return $template;
}
add_filter( 'template_include', 'hc_template_router' );

/**
 * Ensure versus/sitemap requests are not treated as 404 by the main query.
 *
 * @param WP_Query $query Main query.
 */
function hc_prevent_404( WP_Query $query ): void {
	if ( ! $query->is_main_query() || is_admin() ) {
		return;
	}
	$versus  = $query->get( 'hc_versus' );
	$sitemap = $query->get( 'hc_sitemap' );
	if ( ( is_string( $versus ) && '' !== $versus ) || ( is_string( $sitemap ) && '' !== $sitemap ) ) {
		$query->is_404  = false;
		$query->is_home = false;
	}
}
add_action( 'pre_get_posts', 'hc_prevent_404' );

/**
 * Disable core sitemaps — the theme ships its own (inc/sitemaps.php).
 */
add_filter( 'wp_sitemaps_enabled', '__return_false' );
