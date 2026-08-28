<?php
/**
 * Nav Sections — manages the left-sidebar navigation categories for the tool.
 *
 * Stores section list in WP option `hc_nav_sections`. Registers the
 * `hc_preset_cat` taxonomy on height_reference for category assignment.
 * Provides an admin CRUD page and PHP helpers used by templates/tool.php.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Icon library ──────────────────────────────────────────────────────────────

/**
 * @return array<string, string>
 */
function hc_nav_icon_paths(): array {
	return array(
		'person'     => '<path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-6 9v-1a6 6 0 0 1 12 0v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'celebrity'  => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'animal'     => '<path d="M4.5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm15 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm-4-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm-7 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3.5 7c-3.5 0-7 2-7 5.5h14c0-3.5-3.5-5.5-7-5.5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'apparel'    => '<path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.57c.1.57.59.98 1 .98V21a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V10.24c.41 0 .9-.41 1-.98l.58-3.57a2 2 0 0 0-1.34-2.23z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="none"/>',
		'object'     => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><polyline points="3.27 6.96 12 12.01 20.73 6.96" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><line x1="12" y1="22.08" x2="12" y2="12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		'fictional'  => '<path d="M2 10s3-3 10-3 10 3 10 3v3a10 10 0 0 1-20 0v-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M7 13s0 3 2 3 2-3 2-3m4 0s0 3 2 3 2-3 2-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'plant'      => '<path d="M17 8C8 10 5.9 16.17 3.82 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.5 3C14 3 18 5 20 9c-4 1-9 0-11-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 16c0-4 3-8 9-10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'sport'      => '<rect x="6" y="4" width="12" height="5" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6m12 5h1.5a2.5 2.5 0 0 0 0-5H18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 22h16M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22m10-8v2.34c0 .55.47.98.97 1.21C19.15 18.75 20 20.24 20 22M7.33 9h9.34" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		'building'   => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'vehicle'    => '<path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v9h-3m-9 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="7.5" cy="17.5" r="2.5" stroke="currentColor" stroke-width="1.8"/><circle cx="17.5" cy="17.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>',
		'food'       => '<path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3m4-3v3m4-3v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'technology' => '<rect x="2" y="3" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><line x1="8" y1="21" x2="16" y2="21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><line x1="12" y1="17" x2="12" y2="21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		'music'      => '<path d="M9 18V5l12-2v13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.8"/><circle cx="18" cy="16" r="3" stroke="currentColor" stroke-width="1.8"/>',
		'image'      => '<rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="1.8"/><polyline points="21 15 16 10 5 21" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'upload'     => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
	);
}

function hc_nav_icon_svg( string $key ): string {
	$paths = hc_nav_icon_paths();
	$inner = $paths[ $key ] ?? $paths['object'];
	return '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" aria-hidden="true">' . $inner . '</svg>';
}

// ── Default sections ──────────────────────────────────────────────────────────

/**
 * @return array<int, array{slug:string,label:string,icon:string,built_in:bool,enabled:bool,order:int}>
 */
function hc_default_nav_sections(): array {
	return array(
		array( 'slug' => 'person',    'label' => 'Person',     'icon' => 'person',    'built_in' => true,  'enabled' => true, 'order' => 0 ),
		array( 'slug' => 'celebrity', 'label' => 'Celebrities', 'icon' => 'celebrity', 'built_in' => true,  'enabled' => true, 'order' => 1 ),
		array( 'slug' => 'animal',    'label' => 'Animal',      'icon' => 'animal',    'built_in' => false, 'enabled' => true, 'order' => 2 ),
		array( 'slug' => 'apparel',   'label' => 'Apparel',     'icon' => 'apparel',   'built_in' => false, 'enabled' => true, 'order' => 3 ),
		array( 'slug' => 'object',    'label' => 'Objects',     'icon' => 'object',    'built_in' => false, 'enabled' => true, 'order' => 4 ),
		array( 'slug' => 'fictional', 'label' => 'Fictional',   'icon' => 'fictional', 'built_in' => false, 'enabled' => true, 'order' => 5 ),
		array( 'slug' => 'plant',     'label' => 'Plants',      'icon' => 'plant',     'built_in' => false, 'enabled' => true, 'order' => 6 ),
		array( 'slug' => 'sport',     'label' => 'Sports',      'icon' => 'sport',     'built_in' => false, 'enabled' => true, 'order' => 7 ),
	);
}

/**
 * @return array<int, array{slug:string,label:string,icon:string,built_in:bool,enabled:bool,order:int}>
 */
function hc_get_nav_sections(): array {
	$stored = get_option( 'hc_nav_sections', null );
	if ( ! is_array( $stored ) ) {
		$stored = hc_default_nav_sections();
		update_option( 'hc_nav_sections', $stored );
	}
	return $stored;
}

/**
 * Normalise a section's raw artworks value to a list of {id, cm, name}.
 * Accepts the legacy format (a plain list of attachment IDs) too.
 *
 * @param mixed $raw Stored artworks value.
 * @return array<int, array{id:int,cm:float,name:string}>
 */
function hc_normalize_artworks( $raw ): array {
	if ( ! is_array( $raw ) ) {
		return array();
	}
	$out = array();
	foreach ( $raw as $a ) {
		if ( is_array( $a ) && isset( $a['id'] ) ) {
			$id = (int) $a['id'];
			if ( $id > 0 ) {
				$out[] = array(
					'id'   => $id,
					'cm'   => isset( $a['cm'] ) ? (float) $a['cm'] : 0.0,
					'name' => isset( $a['name'] ) ? (string) $a['name'] : '',
				);
			}
		} elseif ( is_scalar( $a ) && (int) $a > 0 ) {
			$out[] = array( 'id' => (int) $a, 'cm' => 0.0, 'name' => '' );
		}
	}
	return $out;
}

/**
 * Get a section's normalised artworks by slug.
 *
 * @return array<int, array{id:int,cm:float,name:string}>
 */
function hc_section_artworks( string $slug ): array {
	foreach ( hc_get_nav_sections() as $s ) {
		if ( ( $s['slug'] ?? '' ) === $slug ) {
			return hc_normalize_artworks( $s['artworks'] ?? array() );
		}
	}
	return array();
}

/**
 * The URL of the dedicated artwork-management page for a section.
 */
function hc_section_artwork_url( string $slug ): string {
	return admin_url( 'admin.php?page=hc-nav-sections&section=' . rawurlencode( $slug ) );
}

// ── hc_preset_cat taxonomy (on height_reference) ──────────────────────────────

add_action(
	'init',
	static function (): void {
		register_taxonomy(
			'hc_preset_cat',
			array( 'height_reference' ),
			array(
				'label'             => 'Nav Category',
				'labels'            => array(
					'name'          => 'Nav Categories',
					'singular_name' => 'Nav Category',
					'add_new_item'  => 'Add New Category',
				),
				'hierarchical'      => false,
				'show_in_rest'      => true,
				'rewrite'           => false,
				'show_ui'           => true,
				'show_admin_column' => true,
			)
		);

		// Seed taxonomy terms for custom (non-built-in) sections.
		foreach ( hc_get_nav_sections() as $s ) {
			if ( empty( $s['built_in'] ) && ! empty( $s['slug'] ) ) {
				if ( ! term_exists( $s['slug'], 'hc_preset_cat' ) ) {
					wp_insert_term( $s['label'] ?? $s['slug'], 'hc_preset_cat', array( 'slug' => $s['slug'] ) );
				}
			}
		}
	},
	12 // after CPT registration (priority 10)
);

// ── Admin: Nav Sections ───────────────────────────────────────────────────────

add_action(
	'admin_menu',
	static function (): void {
		add_menu_page(
			__( 'Nav Sections', 'height-compare' ),
			__( 'Nav Sections', 'height-compare' ),
			'manage_options',
			'hc-nav-sections',
			'hc_nav_sections_page',
			'dashicons-menu-alt3',
			24 // just below Country Averages (menu_position 22)
		);
	}
);

// Enqueue WP media library on our admin page.
add_action(
	'admin_enqueue_scripts',
	static function ( string $hook ): void {
		if ( ! str_contains( $hook, 'hc-nav-sections' ) ) {
			return;
		}
		wp_enqueue_media();
	}
);

function hc_nav_sections_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Dedicated per-section artwork manager.
	if ( isset( $_GET['section'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sec_slug = sanitize_key( wp_unslash( $_GET['section'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		hc_nav_section_artwork_page( $sec_slug );
		return;
	}

	$notice = '';

	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && check_admin_referer( 'hc_nav_sections_save', 'hc_ns_nonce' ) ) {
		$sections = hc_get_nav_sections();

		if ( isset( $_POST['hc_move_up'] ) ) {
			$target = sanitize_key( $_POST['hc_move_up'] );
			$idx    = (int) array_search( $target, array_column( $sections, 'slug' ), true );
			if ( $idx > 0 ) {
				[ $sections[ $idx ], $sections[ $idx - 1 ] ] = [ $sections[ $idx - 1 ], $sections[ $idx ] ];
				update_option( 'hc_nav_sections', $sections );
			}
		} elseif ( isset( $_POST['hc_move_down'] ) ) {
			$target = sanitize_key( $_POST['hc_move_down'] );
			$idx    = (int) array_search( $target, array_column( $sections, 'slug' ), true );
			if ( $idx < count( $sections ) - 1 ) {
				[ $sections[ $idx ], $sections[ $idx + 1 ] ] = [ $sections[ $idx + 1 ], $sections[ $idx ] ];
				update_option( 'hc_nav_sections', $sections );
			}
		} elseif ( isset( $_POST['hc_delete'] ) ) {
			$del      = sanitize_key( $_POST['hc_delete'] );
			$sections = array_values( array_filter( $sections, fn( $s ) => $s['slug'] !== $del ) );
			update_option( 'hc_nav_sections', $sections );
			$notice = '<div class="notice notice-success is-dismissible"><p>Section deleted.</p></div>';
		} else {
			// Save labels + enabled states. (Artwork lives on its own page.)
			$labels  = isset( $_POST['hc_ns_label'] ) && is_array( $_POST['hc_ns_label'] ) ? $_POST['hc_ns_label'] : array();
			$enabled = isset( $_POST['hc_ns_enabled'] ) && is_array( $_POST['hc_ns_enabled'] ) ? $_POST['hc_ns_enabled'] : array();
			foreach ( $sections as &$s ) {
				$slug = $s['slug'];
				if ( isset( $labels[ $slug ] ) ) {
					$s['label'] = sanitize_text_field( $labels[ $slug ] );
				}
				$s['enabled'] = isset( $enabled[ $slug ] );
			}
			unset( $s );
			update_option( 'hc_nav_sections', $sections );
			$notice = '<div class="notice notice-success is-dismissible"><p>Sections saved.</p></div>';
		}

		$sections = hc_get_nav_sections(); // re-read
	}

	// Handle Add New (separate form)
	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && check_admin_referer( 'hc_nav_sections_add', 'hc_ns_add_nonce' ) ) {
		$new_label = sanitize_text_field( $_POST['hc_ns_new_label'] ?? '' );
		$new_icon  = sanitize_key( $_POST['hc_ns_new_icon'] ?? 'object' );
		if ( '' !== $new_label ) {
			$sections  = hc_get_nav_sections();
			$new_slug  = sanitize_title( $new_label );
			$all_slugs = array_column( $sections, 'slug' );
			$base      = $new_slug;
			$n         = 1;
			while ( in_array( $new_slug, $all_slugs, true ) ) {
				$new_slug = $base . '-' . $n++;
			}
			$sections[] = array(
				'slug'     => $new_slug,
				'label'    => $new_label,
				'icon'     => $new_icon,
				'built_in' => false,
				'enabled'  => true,
				'order'    => count( $sections ),
			);
			update_option( 'hc_nav_sections', $sections );
			if ( ! term_exists( $new_slug, 'hc_preset_cat' ) ) {
				wp_insert_term( $new_label, 'hc_preset_cat', array( 'slug' => $new_slug ) );
			}
			$notice = '<div class="notice notice-success is-dismissible"><p>Section "' . esc_html( $new_label ) . '" added. Now add Height Reference posts and assign them to the <strong>' . esc_html( $new_label ) . '</strong> Nav Category.</p></div>';
		}
	}

	$sections = hc_get_nav_sections();
	$icons    = hc_nav_icon_paths();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Nav Sections', 'height-compare' ); ?></h1>
		<p><?php esc_html_e( 'Manage the left-sidebar navigation categories shown in the comparison tool.', 'height-compare' ); ?></p>
		<?php echo wp_kses_post( $notice ); ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'hc_nav_sections_save', 'hc_ns_nonce' ); ?>
			<table class="wp-list-table widefat fixed striped" style="max-width:1000px">
				<thead>
					<tr>
						<th style="width:60px"><?php esc_html_e( 'Order', 'height-compare' ); ?></th>
						<th style="width:180px"><?php esc_html_e( 'Label', 'height-compare' ); ?></th>
						<th style="width:60px"><?php esc_html_e( 'Icon', 'height-compare' ); ?></th>
						<th style="width:70px"><?php esc_html_e( 'Type', 'height-compare' ); ?></th>
						<th style="width:60px"><?php esc_html_e( 'Visible', 'height-compare' ); ?></th>
						<th style="width:220px"><?php esc_html_e( 'Artwork', 'height-compare' ); ?></th>
						<th style="width:80px"><?php esc_html_e( 'Actions', 'height-compare' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $sections as $s ) :
						$slug     = $s['slug'];
						$is_first = ( $sections[0]['slug'] === $slug );
						$is_last  = ( $sections[ count( $sections ) - 1 ]['slug'] === $slug );
						?>
					<tr>
						<td>
							<?php if ( ! $is_first ) : ?>
							<button type="submit" name="hc_move_up" value="<?php echo esc_attr( $slug ); ?>"
								title="<?php esc_attr_e( 'Move up', 'height-compare' ); ?>"
								class="button button-small">▲</button>
							<?php endif; ?>
							<?php if ( ! $is_last ) : ?>
							<button type="submit" name="hc_move_down" value="<?php echo esc_attr( $slug ); ?>"
								title="<?php esc_attr_e( 'Move down', 'height-compare' ); ?>"
								class="button button-small">▼</button>
							<?php endif; ?>
						</td>
						<td>
							<input type="text" name="hc_ns_label[<?php echo esc_attr( $slug ); ?>]"
								value="<?php echo esc_attr( $s['label'] ); ?>"
								class="regular-text" style="max-width:160px">
						</td>
						<td>
							<svg viewBox="0 0 24 24" width="22" height="22" fill="none"
								style="vertical-align:middle;color:#555">
								<?php echo $icons[ $s['icon'] ] ?? $icons['object']; // phpcs:ignore ?>
							</svg>
						</td>
						<td>
							<?php echo ! empty( $s['built_in'] ) ? '<span style="color:#999">Built-in</span>' : 'Custom'; // phpcs:ignore ?>
						</td>
						<td>
							<input type="checkbox" name="hc_ns_enabled[<?php echo esc_attr( $slug ); ?>]"
								value="1" <?php checked( ! empty( $s['enabled'] ) ); ?>>
						</td>
						<td>
							<?php if ( 'person' === $slug ) : ?>
								<span style="color:#999">— <?php esc_html_e( 'manual entry', 'height-compare' ); ?></span>
							<?php elseif ( 'celebrity' === $slug ) : ?>
								<span style="color:#999"><?php esc_html_e( 'From celebrity posts', 'height-compare' ); ?></span>
							<?php else :
								$art_count = count( hc_normalize_artworks( $s['artworks'] ?? array() ) );
								?>
							<a href="<?php echo esc_url( hc_section_artwork_url( $slug ) ); ?>"
								class="button button-small">
								<?php
								/* translators: %d: number of artwork images. */
								printf( esc_html__( 'Manage Artwork (%d)', 'height-compare' ), (int) $art_count );
								?>
							</a>
							<?php endif; ?>
						</td>
						<td>
							<button type="submit" name="hc_delete" value="<?php echo esc_attr( $slug ); ?>"
								class="button button-small"
								onclick="return confirm('Delete the &quot;<?php echo esc_js( $s['label'] ); ?>&quot; section? Posts assigned to it are not deleted.')">
								<?php esc_html_e( 'Delete', 'height-compare' ); ?>
							</button>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p style="margin-top:12px">
				<button type="submit" class="button button-primary">
					<?php esc_html_e( 'Save Changes', 'height-compare' ); ?>
				</button>
			</p>
		</form>

		<hr style="max-width:860px;margin:24px 0">

		<h2><?php esc_html_e( 'Add New Section', 'height-compare' ); ?></h2>
		<p><?php esc_html_e( 'After adding, go to Height References and assign posts to the new Nav Category to populate it.', 'height-compare' ); ?></p>

		<form method="post" action="" style="max-width:860px">
			<?php wp_nonce_field( 'hc_nav_sections_add', 'hc_ns_add_nonce' ); ?>
			<table class="form-table" style="max-width:600px">
				<tr>
					<th scope="row"><label for="hc-ns-new-label"><?php esc_html_e( 'Name', 'height-compare' ); ?></label></th>
					<td>
						<input type="text" id="hc-ns-new-label" name="hc_ns_new_label"
							class="regular-text" placeholder="e.g. Vehicles" required>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Icon', 'height-compare' ); ?></th>
					<td>
						<div style="display:flex;flex-wrap:wrap;gap:8px;max-width:500px" id="hc-icon-picker">
							<?php foreach ( $icons as $key => $inner ) :
								if ( in_array( $key, array( 'person', 'celebrity' ), true ) ) continue;
								?>
							<label class="hc-icon-opt"
								style="display:flex;flex-direction:column;align-items:center;gap:4px;
								cursor:pointer;padding:8px 6px;border:2px solid #ddd;border-radius:4px;
								width:58px;text-align:center">
								<input type="radio" name="hc_ns_new_icon" value="<?php echo esc_attr( $key ); ?>"
									style="position:absolute;opacity:0" <?php checked( $key, 'object' ); ?>>
								<svg viewBox="0 0 24 24" width="22" height="22" fill="none" style="color:#555">
									<?php echo $inner; // phpcs:ignore ?>
								</svg>
								<span style="font-size:9px;line-height:1.2;color:#555;
									overflow:hidden;max-width:100%;white-space:nowrap;text-overflow:ellipsis">
									<?php echo esc_html( $key ); ?>
								</span>
							</label>
							<?php endforeach; ?>
						</div>
					</td>
				</tr>
			</table>
			<button type="submit" class="button button-secondary">
				<?php esc_html_e( '+ Add Section', 'height-compare' ); ?>
			</button>
		</form>
	</div>

	<script>
	(function() {
		/* ── Icon picker ── */
		var labels = document.querySelectorAll('#hc-icon-picker .hc-icon-opt');
		function syncBorders() {
			labels.forEach(function(lbl) {
				var radio = lbl.querySelector('input[type=radio]');
				lbl.style.borderColor     = radio.checked ? '#0073aa' : '#ddd';
				lbl.style.backgroundColor = radio.checked ? '#f0f6fc' : '';
			});
		}
		labels.forEach(function(lbl) { lbl.addEventListener('click', syncBorders); });
		syncBorders();
	})();
	</script>
	<?php
}

/**
 * Dedicated page: add / remove artwork (with height + name) for one section.
 */
function hc_nav_section_artwork_page( string $slug ): void {
	$sections = hc_get_nav_sections();
	$idx      = array_search( $slug, array_column( $sections, 'slug' ), true );
	$back     = admin_url( 'admin.php?page=hc-nav-sections' );

	if ( false === $idx || in_array( $slug, array( 'person', 'celebrity' ), true ) ) {
		$msg = ( 'celebrity' === $slug )
			? __( 'Celebrity images come from each celebrity post, so there is no artwork library here.', 'height-compare' )
			: ( ( 'person' === $slug )
				? __( 'The Person section uses manual entry, so it has no artwork library.', 'height-compare' )
				: __( 'Section not found.', 'height-compare' ) );
		echo '<div class="wrap"><h1>' . esc_html__( 'Artwork', 'height-compare' ) . '</h1>' .
			'<p>' . esc_html( $msg ) . '</p>' .
			'<p><a href="' . esc_url( $back ) . '">&larr; ' .
			esc_html__( 'Back to Nav Sections', 'height-compare' ) . '</a></p></div>';
		return;
	}

	$label  = $sections[ $idx ]['label'] ?? $slug;
	$notice = '';

	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && check_admin_referer( 'hc_ns_artwork_save', 'hc_ns_art_nonce' ) ) {
		$ids    = isset( $_POST['art_id'] ) && is_array( $_POST['art_id'] ) ? array_map( 'absint', $_POST['art_id'] ) : array();
		$cms    = isset( $_POST['art_cm'] ) && is_array( $_POST['art_cm'] ) ? (array) $_POST['art_cm'] : array();
		$names  = isset( $_POST['art_name'] ) && is_array( $_POST['art_name'] ) ? (array) $_POST['art_name'] : array();
		$new    = array();
		foreach ( $ids as $i => $aid ) {
			if ( $aid <= 0 ) {
				continue;
			}
			$cm = isset( $cms[ $i ] ) ? (float) $cms[ $i ] : 0.0;
			$new[] = array(
				'id'   => $aid,
				'cm'   => $cm > 0 ? round( min( $cm, 30000.0 ), 1 ) : 100.0,
				'name' => isset( $names[ $i ] ) ? sanitize_text_field( wp_unslash( $names[ $i ] ) ) : '',
			);
		}
		$sections[ $idx ]['artworks'] = $new;
		update_option( 'hc_nav_sections', $sections );
		$notice = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Artwork saved.', 'height-compare' ) . '</p></div>';
	}

	$artworks = hc_section_artworks( $slug );
	?>
	<div class="wrap">
		<h1>
			<?php
			/* translators: %s: section label. */
			printf( esc_html__( 'Artwork — %s', 'height-compare' ), esc_html( $label ) );
			?>
		</h1>
		<p>
			<a href="<?php echo esc_url( $back ); ?>">
				&larr; <?php esc_html_e( 'Back to Nav Sections', 'height-compare' ); ?>
			</a>
		</p>
		<p><?php esc_html_e( 'These images appear as a gallery under this section in the tool. Click "+ Add Images", set a height (cm) and optional name for each, then Save.', 'height-compare' ); ?></p>
		<?php echo wp_kses_post( $notice ); ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'hc_ns_artwork_save', 'hc_ns_art_nonce' ); ?>

			<p>
				<button type="button" class="button button-secondary" id="hc-art-add">
					<?php esc_html_e( '+ Add Images', 'height-compare' ); ?>
				</button>
			</p>

			<div id="hc-art-grid" style="display:flex;flex-wrap:wrap;gap:14px">
				<?php foreach ( $artworks as $a ) :
					$thumb = wp_get_attachment_image_url( $a['id'], 'thumbnail' );
					if ( ! $thumb ) {
						continue;
					}
					?>
				<div class="hc-art-card" style="width:150px;border:1px solid #dcdcde;border-radius:6px;padding:10px;background:#fff">
					<img src="<?php echo esc_url( $thumb ); ?>"
						style="width:100%;height:90px;object-fit:contain;background:#f6f7f7;border-radius:4px;margin-bottom:6px">
					<input type="hidden" name="art_id[]" value="<?php echo esc_attr( (string) $a['id'] ); ?>">
					<input type="text" name="art_name[]" value="<?php echo esc_attr( $a['name'] ); ?>"
						placeholder="<?php esc_attr_e( 'Name', 'height-compare' ); ?>"
						style="width:100%;margin-bottom:5px">
					<label style="display:flex;align-items:center;gap:4px;font-size:12px">
						<?php esc_html_e( 'Height', 'height-compare' ); ?>
						<input type="number" name="art_cm[]" min="1" max="30000" step="0.5"
							value="<?php echo esc_attr( (string) ( $a['cm'] > 0 ? $a['cm'] : 100 ) ); ?>"
							style="width:70px"> cm
					</label>
					<button type="button" class="button-link hc-art-remove"
						style="color:#b32d2e;margin-top:6px;display:inline-block">
						<?php esc_html_e( 'Remove', 'height-compare' ); ?>
					</button>
				</div>
				<?php endforeach; ?>
			</div>

			<p style="margin-top:16px">
				<button type="submit" class="button button-primary">
					<?php esc_html_e( 'Save Artwork', 'height-compare' ); ?>
				</button>
			</p>
		</form>
	</div>

	<script>
	(function() {
		var grid = document.getElementById('hc-art-grid');

		function cardHtml(id, thumb) {
			var wrap = document.createElement('div');
			wrap.className = 'hc-art-card';
			wrap.style.cssText = 'width:150px;border:1px solid #dcdcde;border-radius:6px;padding:10px;background:#fff';
			wrap.innerHTML =
				'<img src="' + thumb + '" style="width:100%;height:90px;object-fit:contain;background:#f6f7f7;border-radius:4px;margin-bottom:6px">' +
				'<input type="hidden" name="art_id[]" value="' + id + '">' +
				'<input type="text" name="art_name[]" value="" placeholder="Name" style="width:100%;margin-bottom:5px">' +
				'<label style="display:flex;align-items:center;gap:4px;font-size:12px">Height ' +
					'<input type="number" name="art_cm[]" min="1" max="30000" step="0.5" value="100" style="width:70px"> cm</label>' +
				'<button type="button" class="button-link hc-art-remove" style="color:#b32d2e;margin-top:6px;display:inline-block">Remove</button>';
			wireRemove(wrap);
			return wrap;
		}

		function wireRemove(card) {
			card.querySelector('.hc-art-remove').addEventListener('click', function() { card.remove(); });
		}

		grid.querySelectorAll('.hc-art-card').forEach(wireRemove);

		document.getElementById('hc-art-add').addEventListener('click', function() {
			var frame = wp.media({
				title:    'Add Artwork',
				button:   { text: 'Add to section' },
				multiple: true,
				library:  { type: 'image' }
			});
			frame.on('select', function() {
				frame.state().get('selection').each(function(attachment) {
					var att   = attachment.toJSON();
					var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
					grid.appendChild(cardHtml(att.id, thumb));
				});
			});
			frame.open();
		});
	})();
	</script>
	<?php
}
