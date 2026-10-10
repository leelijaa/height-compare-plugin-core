<?php
/**
 * Custom XML sitemaps: an index plus pages / celebrities / countries /
 * blog / versus children. Core sitemaps are disabled in inc/rewrites.php.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Intercept sitemap requests before core's canonical redirect (which would
 * otherwise bounce /sitemap.xml to the disabled /wp-sitemap.xml). Runs at
 * template_redirect priority 0, matching the request path directly so it is
 * independent of rewrite-rule timing.
 */
/**
 * Render the sitemap on template_redirect priority 1 — before core's
 * redirect_canonical (priority 10), which would otherwise bounce
 * /sitemap.xml to the disabled /wp-sitemap.xml. The hc_sitemap query var is
 * set deterministically by hc_force_sitemap_query() in inc/rewrites.php.
 */
function hc_render_sitemap_early(): void {
	$name = get_query_var( 'hc_sitemap' );
	if ( is_string( $name ) && '' !== $name ) {
		hc_render_sitemap( $name ); // Exits.
	}
}
add_action( 'template_redirect', 'hc_render_sitemap_early', 1 );

/**
 * Render a sitemap and exit.
 *
 * @param string $name index|pages|celebrities|countries|blog|versus|celebrity-groups|celebrity-cats.
 */
function hc_render_sitemap( string $name ): void {
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex, follow', true );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

	if ( 'index' === $name ) {
		hc_render_sitemap_index();
	} else {
		hc_render_sitemap_urlset( $name );
	}
	exit;
}

/**
 * Sitemap index listing the child sitemaps.
 */
function hc_render_sitemap_index(): void {
	global $wpdb;
	$children = array( 'pages', 'celebrities', 'height-references', 'countries', 'blog', 'versus', 'celebrity-groups', 'celebrity-cats' );

	// Map child name → post types/taxonomies for lastmod query.
	$type_map = array(
		'pages'            => array( 'type' => 'post', 'post_type' => 'page' ),
		'celebrities'      => array( 'type' => 'post', 'post_type' => 'celebrity' ),
		'height-references'=> array( 'type' => 'post', 'post_type' => 'height_reference' ),
		'countries'        => array( 'type' => 'post', 'post_type' => 'country_average' ),
		'blog'             => array( 'type' => 'post', 'post_type' => 'post' ),
		'celebrity-groups' => array( 'type' => 'tax', 'taxonomy' => 'celebrity_group' ),
		'celebrity-cats'   => array( 'type' => 'tax', 'taxonomy' => 'celebrity_cat' ),
	);

	// Cache lastmod data for 1 hour.
	$lastmods = get_transient( 'hc_sitemap_index_lastmods' );
	if ( ! is_array( $lastmods ) ) {
		$lastmods = array();
		foreach ( $children as $child ) {
			$lastmod = '';
			if ( isset( $type_map[ $child ] ) ) {
				$entry = $type_map[ $child ];
				if ( 'post' === $entry['type'] ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
					$row = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT MAX(post_modified_gmt) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
							$entry['post_type']
						)
					);
				} else {
					// Taxonomy: find the most-recently-modified post in any term of this taxonomy.
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
					$row = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT MAX(p.post_modified_gmt)
							 FROM {$wpdb->posts} p
							 INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
							 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
							 WHERE tt.taxonomy = %s AND p.post_status = 'publish'",
							$entry['taxonomy']
						)
					);
				}
				if ( is_string( $row ) && '' !== $row ) {
					$lastmod = gmdate( 'c', strtotime( $row ) );
				}
			}
			$lastmods[ $child ] = $lastmod;
		}
		set_transient( 'hc_sitemap_index_lastmods', $lastmods, HOUR_IN_SECONDS );
	}

	echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach ( $children as $child ) {
		$lastmod = $lastmods[ $child ] ?? '';
		printf(
			"<sitemap><loc>%s</loc>%s</sitemap>\n",
			esc_url( home_url( '/sitemap-' . $child . '.xml' ) ),
			'' !== $lastmod ? '<lastmod>' . esc_html( $lastmod ) . '</lastmod>' : ''
		);
	}
	echo '</sitemapindex>';
}

/**
 * Flush the sitemap index lastmod cache when posts/terms change.
 */
add_action( 'save_post', 'hc_flush_sitemap_index_cache' );
add_action( 'delete_post', 'hc_flush_sitemap_index_cache' );
add_action( 'edited_term', 'hc_flush_sitemap_index_cache' );
function hc_flush_sitemap_index_cache(): void {
	delete_transient( 'hc_sitemap_index_lastmods' );
}

/**
 * Render a <urlset> for a given child sitemap.
 *
 * @param string $name Child name.
 */
function hc_render_sitemap_urlset( string $name ): void {
	$changefreq_map = array(
		'pages'            => 'daily',
		'celebrities'      => 'weekly',
		'height-references'=> 'monthly',
		'countries'        => 'monthly',
		'blog'             => 'weekly',
		'versus'           => 'monthly',
		'celebrity-groups' => 'weekly',
		'celebrity-cats'   => 'weekly',
	);
	$changefreq = $changefreq_map[ $name ] ?? '';

	$image_ns = 'celebrities' === $name
		? ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"'
		: '';
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $image_ns . '>' . "\n";
	foreach ( hc_sitemap_urls( $name ) as $url ) {
		$image_tag = '';
		if ( isset( $url['image_loc'] ) && '' !== $url['image_loc'] ) {
			$image_tag = sprintf(
				'<image:image><image:loc>%s</image:loc>%s</image:image>',
				esc_url( $url['image_loc'] ),
				( isset( $url['image_title'] ) && '' !== $url['image_title'] )
					? '<image:title>' . esc_html( $url['image_title'] ) . '</image:title>'
					: ''
			);
		}
		printf(
			"<url><loc>%s</loc>%s%s%s</url>\n",
			esc_url( $url['loc'] ),
			'' !== $url['lastmod'] ? '<lastmod>' . esc_html( $url['lastmod'] ) . '</lastmod>' : '',
			'' !== $changefreq ? '<changefreq>' . esc_html( $changefreq ) . '</changefreq>' : '',
			$image_tag
		);
	}
	echo '</urlset>';
}

/**
 * URLs for a child sitemap.
 *
 * @param string $name Child name.
 * @return array<int, array{loc: string, lastmod: string}>
 */
function hc_sitemap_urls( string $name ): array {
	$urls = array();

	switch ( $name ) {
		case 'pages':
			// Homepage lastmod: most-recently-modified celebrity post.
			global $wpdb;
			$hc_home_raw = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SELECT MAX(post_modified_gmt) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
					'celebrity'
				)
			);
			$hc_home_lastmod = ( is_string( $hc_home_raw ) && '' !== $hc_home_raw )
				? gmdate( 'c', strtotime( $hc_home_raw ) )
				: '';
			$urls[] = array( 'loc' => home_url( '/' ), 'lastmod' => $hc_home_lastmod );
			$hc_converter = get_page_by_path( 'height-converter' );
			if ( $hc_converter instanceof WP_Post ) {
				$urls[] = array(
					'loc'     => (string) get_permalink( $hc_converter ),
					'lastmod' => get_post_modified_time( 'c', true, $hc_converter ) ?: '',
				);
			}
			$celeb_archive = get_post_type_archive_link( 'celebrity' );
			if ( is_string( $celeb_archive ) && '' !== $celeb_archive ) {
				$urls[] = array( 'loc' => $celeb_archive, 'lastmod' => '' );
			}
			$blog_page_id = (int) get_option( 'page_for_posts' );
			if ( $blog_page_id > 0 ) {
				$blog_permalink = get_permalink( $blog_page_id );
				if ( is_string( $blog_permalink ) && '' !== $blog_permalink ) {
					$urls[] = array(
						'loc'     => $blog_permalink,
						'lastmod' => get_post_modified_time( 'c', true, $blog_page_id ) ?: '',
					);
				}
			}
			break;

		case 'celebrities':
			$urls = hc_sitemap_posts( 'celebrity' );
			break;

		case 'height-references':
			$urls = hc_sitemap_posts( 'height_reference' );
			break;

		case 'countries':
			$urls = hc_sitemap_posts( 'country_average' );
			break;

		case 'blog':
			$urls = hc_sitemap_posts( 'post' );
			break;

		case 'versus':
			$urls = hc_sitemap_versus();
			break;

		case 'celebrity-groups':
			$urls = hc_sitemap_terms( 'celebrity_group' );
			break;

		case 'celebrity-cats':
			$urls = hc_sitemap_terms( 'celebrity_cat' );
			break;
	}

	return $urls;
}

/**
 * URLs for a post type.
 *
 * @param string $post_type Post type.
 * @return array<int, array{loc: string, lastmod: string}>
 */
function hc_sitemap_posts( string $post_type ): array {
	$posts = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 5000,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		)
	);
	$urls = array();
	foreach ( $posts as $post ) {
		$entry = array(
			'loc'     => (string) get_permalink( $post ),
			'lastmod' => get_post_modified_time( 'c', true, $post ) ?: '',
		);
		if ( 'celebrity' === $post_type ) {
			$thumb_id = get_post_thumbnail_id( $post );
			if ( $thumb_id ) {
				$thumb = wp_get_attachment_image_src( $thumb_id, 'large' );
				if ( is_array( $thumb ) && '' !== $thumb[0] ) {
					$entry['image_loc']   = $thumb[0];
					$entry['image_title'] = get_the_title( $post );
				}
			}
		}
		$urls[] = $entry;
	}
	return $urls;
}

/**
 * URLs for a taxonomy's published terms.
 *
 * @param string $taxonomy Taxonomy name.
 * @return array<int, array{loc: string, lastmod: string}>
 */
function hc_sitemap_terms( string $taxonomy, bool $skip_parents = false ): array {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'number'     => 0,
		)
	);
	if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
		return array();
	}
	$urls = array();
	global $wpdb;
	foreach ( $terms as $term ) {
		if ( ! ( $term instanceof WP_Term ) ) {
			continue;
		}
		// Skip hub/parent terms (shallow content, links only to children).
		if ( $skip_parents && 0 === $term->parent ) {
			continue;
		}
		// Query lastmod from posts in this term.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$row = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(p.post_modified_gmt)
				 FROM {$wpdb->posts} p
				 INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
				 INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				 WHERE tt.term_id = %d AND p.post_status = 'publish'",
				$term->term_id
			)
		);
		$lastmod = ( is_string( $row ) && '' !== $row ) ? gmdate( 'c', strtotime( $row ) ) : '';
		$urls[]  = array(
			'loc'     => (string) get_term_link( $term ),
			'lastmod' => $lastmod,
		);
	}
	return $urls;
}

/**
 * Top versus URLs, crawl-budget aware: only indexable pairs (both ≥ 100
 * search volume). Generated by pairing high-volume celebrities.
 *
 * @return array<int, array{loc: string, lastmod: string}>
 */
function hc_sitemap_versus(): array {
	$cached = get_transient( 'hc_sitemap_versus' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$celebs = get_posts(
		array(
			'post_type'      => 'celebrity',
			'post_status'    => 'publish',
			'posts_per_page' => 40,
			'meta_key'       => 'hc_search_volume',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'     => 'hc_search_volume',
					'value'   => 100,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				),
			),
		)
	);

	// All celebrities returned already satisfy hc_search_volume >= 100 (enforced by the
	// meta_query above), so every pair is indexable — no hc_versus_data() call needed.
	$urls  = array();
	$count = count( $celebs );
	for ( $i = 0; $i < $count; $i++ ) {
		for ( $j = $i + 1; $j < $count; $j++ ) {
			$slug_a = $celebs[ $i ]->post_name;
			$slug_b = $celebs[ $j ]->post_name;
			// Canonical direction: alphabetically first slug leads.
			if ( strcmp( $slug_a, $slug_b ) > 0 ) {
				[ $slug_a, $slug_b ] = [ $slug_b, $slug_a ];
				// Swap i and j references for lastmod computation.
				$mod_a = strtotime( $celebs[ $j ]->post_modified_gmt );
				$mod_b = strtotime( $celebs[ $i ]->post_modified_gmt );
			} else {
				$mod_a = strtotime( $celebs[ $i ]->post_modified_gmt );
				$mod_b = strtotime( $celebs[ $j ]->post_modified_gmt );
			}
			$lastmod = gmdate( 'c', max( (int) $mod_a, (int) $mod_b ) );
			$urls[] = array(
				'loc'     => hc_versus_url( $slug_a . '-vs-' . $slug_b ),
				'lastmod' => $lastmod,
			);
			if ( count( $urls ) >= 500 ) {
				break 2;
			}
		}
	}

	set_transient( 'hc_sitemap_versus', $urls, 12 * HOUR_IN_SECONDS );
	return $urls;
}

/**
 * Flush the versus sitemap cache when a celebrity post is saved.
 *
 * @param int $post_id Post ID.
 */
function hc_flush_versus_sitemap_cache( int $post_id ): void {
	delete_transient( 'hc_sitemap_versus' );
}
add_action( 'save_post_celebrity', 'hc_flush_versus_sitemap_cache' );

/**
 * Advertise the sitemap in robots.txt.
 *
 * @param string $output Robots content.
 * @return string
 */
function hc_robots_txt( string $output ): string {
	$output .= "\nSitemap: " . home_url( '/sitemap.xml' ) . "\n";
	return $output;
}
add_filter( 'robots_txt', 'hc_robots_txt' );

