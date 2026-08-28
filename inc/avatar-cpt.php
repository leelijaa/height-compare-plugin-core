<?php
/**
 * Avatar system — two completely separate sub-systems:
 *
 * 1. DEFAULT AVATARS  — stored in WP option `hc_default_avatars`.
 *    Managed via Avatars → Default Avatars in wp-admin.
 *    REST: GET /hc/v1/default-avatars
 *    Applied automatically (per gender) when a person is added without using the picker.
 *
 * 2. AVATAR PICKER    — `hc_avatar` CPT with Build/Style taxonomies.
 *    Managed via Avatars → All Avatars / Add New in wp-admin.
 *    REST: GET /hc/v1/avatars, GET /hc/v1/avatar-filters
 *    Shown in the "Choose Avatar" picker inside the tool UI.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ─────────────────────────────────────────────────────────────────────────────
   1.  AVATAR PICKER CPT
   ───────────────────────────────────────────────────────────────────────────── */

function hc_register_avatar_cpt(): void {
	register_post_type(
		'hc_avatar',
		array(
			'labels'          => array(
				'name'               => 'Avatars',
				'singular_name'      => 'Avatar',
				'add_new_item'       => 'Add New Avatar',
				'edit_item'          => 'Edit Avatar',
				'search_items'       => 'Search Avatars',
				'all_items'          => 'All Avatars',
				'not_found'          => 'No avatars found.',
				'not_found_in_trash' => 'No avatars in trash.',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'supports'        => array( 'title' ),
			'menu_icon'       => 'dashicons-admin-users',
			'capability_type' => 'post',
			'rewrite'         => false,
		)
	);

	register_post_meta( 'hc_avatar', 'hc_avatar_file_id', array(
		'type'              => 'integer',
		'single'            => true,
		'sanitize_callback' => 'absint',
	) );
	register_post_meta( 'hc_avatar', 'hc_avatar_gender', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_key',
	) );
}
add_action( 'init', 'hc_register_avatar_cpt' );

/* ── Taxonomies ────────────────────────────────────────────────────────────── */

function hc_register_avatar_taxonomies(): void {
	register_taxonomy( 'hc_avatar_build', 'hc_avatar', array(
		'labels'       => array(
			'name'          => 'Builds',
			'singular_name' => 'Build',
			'add_new_item'  => 'Add New Build',
			'edit_item'     => 'Edit Build',
			'search_items'  => 'Search Builds',
			'all_items'     => 'All Builds',
			'menu_name'     => 'Builds',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'hierarchical' => false,
		'show_in_rest' => false,
		'rewrite'      => false,
	) );

	register_taxonomy( 'hc_avatar_style', 'hc_avatar', array(
		'labels'       => array(
			'name'          => 'Styles',
			'singular_name' => 'Style',
			'add_new_item'  => 'Add New Style',
			'edit_item'     => 'Edit Style',
			'search_items'  => 'Search Styles',
			'all_items'     => 'All Styles',
			'menu_name'     => 'Styles',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'hierarchical' => false,
		'show_in_rest' => false,
		'rewrite'      => false,
	) );
}
add_action( 'init', 'hc_register_avatar_taxonomies' );

function hc_seed_avatar_terms(): void {
	static $done = false;
	if ( $done ) return;
	$done = true;

	foreach ( array( 'Lean', 'Toned', 'Average', 'Muscular', 'Plus Size' ) as $name ) {
		if ( ! term_exists( $name, 'hc_avatar_build' ) ) wp_insert_term( $name, 'hc_avatar_build' );
	}
	foreach ( array( 'Normal', 'Professional', 'Fashion', 'Sports' ) as $name ) {
		if ( ! term_exists( $name, 'hc_avatar_style' ) ) wp_insert_term( $name, 'hc_avatar_style' );
	}
}
add_action( 'init', 'hc_seed_avatar_terms', 20 );

/* ── Admin list columns ────────────────────────────────────────────────────── */

/**
 * @param array<string,string> $cols
 * @return array<string,string>
 */
function hc_avatar_list_columns( array $cols ): array {
	return array(
		'cb'        => $cols['cb'] ?? '',
		'hc_thumb'  => 'Preview',
		'title'     => $cols['title'] ?? 'Name',
		'hc_gender' => 'Gender',
		'hc_build'  => 'Build',
		'hc_style'  => 'Style',
	);
}
add_filter( 'manage_hc_avatar_posts_columns', 'hc_avatar_list_columns' );

function hc_avatar_list_column_content( string $col, int $post_id ): void {
	if ( 'hc_thumb' === $col ) {
		$file_id = (int) get_post_meta( $post_id, 'hc_avatar_file_id', true );
		if ( $file_id > 0 ) {
			$url = (string) wp_get_attachment_url( $file_id );
			if ( '' !== $url ) {
				echo '<img src="' . esc_url( $url ) . '" style="height:44px;width:auto;max-width:55px;background:#f0f0f0;border:1px solid #ddd;padding:2px;">';
			}
		}
	} elseif ( 'hc_gender' === $col ) {
		echo esc_html( (string) get_post_meta( $post_id, 'hc_avatar_gender', true ) );
	} elseif ( 'hc_build' === $col ) {
		$terms = wp_get_post_terms( $post_id, 'hc_avatar_build', array( 'fields' => 'names' ) );
		echo ! is_wp_error( $terms ) && ! empty( $terms ) ? esc_html( implode( ', ', $terms ) ) : '&mdash;';
	} elseif ( 'hc_style' === $col ) {
		$terms = wp_get_post_terms( $post_id, 'hc_avatar_style', array( 'fields' => 'names' ) );
		echo ! is_wp_error( $terms ) && ! empty( $terms ) ? esc_html( implode( ', ', $terms ) ) : '&mdash;';
	}
}
add_action( 'manage_hc_avatar_posts_custom_column', 'hc_avatar_list_column_content', 10, 2 );

/* ── Meta box ──────────────────────────────────────────────────────────────── */

function hc_avatar_add_meta_boxes(): void {
	add_meta_box(
		'hc_avatar_fields',
		'Avatar Settings',
		'hc_avatar_meta_box_render',
		'hc_avatar',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'hc_avatar_add_meta_boxes' );

function hc_avatar_enqueue_scripts( string $hook ): void {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) return;
	$screen = get_current_screen();
	if ( ! $screen || 'hc_avatar' !== $screen->post_type ) return;
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'hc_avatar_enqueue_scripts' );

function hc_avatar_meta_box_render( WP_Post $post ): void {
	wp_nonce_field( 'hc_avatar_save', 'hc_avatar_nonce' );

	$file_id = (int) get_post_meta( $post->ID, 'hc_avatar_file_id', true );
	$gender  = (string) get_post_meta( $post->ID, 'hc_avatar_gender', true );
	if ( '' === $gender ) $gender = 'male';

	$file_url = $file_id > 0 ? (string) wp_get_attachment_url( $file_id ) : '';

	$build_terms   = get_terms( array( 'taxonomy' => 'hc_avatar_build', 'hide_empty' => false ) );
	$style_terms   = get_terms( array( 'taxonomy' => 'hc_avatar_style', 'hide_empty' => false ) );
	$raw_build_ids = wp_get_post_terms( $post->ID, 'hc_avatar_build', array( 'fields' => 'ids' ) );
	$raw_style_ids = wp_get_post_terms( $post->ID, 'hc_avatar_style', array( 'fields' => 'ids' ) );
	/** @var int[] $post_build_ids */
	$post_build_ids = is_wp_error( $raw_build_ids ) ? array() : array_map( 'intval', $raw_build_ids );
	/** @var int[] $post_style_ids */
	$post_style_ids = is_wp_error( $raw_style_ids ) ? array() : array_map( 'intval', $raw_style_ids );
	?>
	<style>
	.hc-av table{width:100%;border-collapse:collapse}
	.hc-av th{width:120px;text-align:left;padding:8px 4px;font-weight:600;vertical-align:top}
	.hc-av td{padding:8px 4px}
	.hc-av .desc{color:#666;font-size:12px;margin-top:4px}
	.hc-av .tax-row label{display:inline-flex;align-items:center;gap:4px;margin:0 10px 4px 0;cursor:pointer}
	#hc-av-preview img{max-height:100px;max-width:100px;border:1px solid #ddd;padding:4px;background:#f0f0f0}
	</style>
	<div class="hc-av">
		<table>
			<tr>
				<th>SVG File</th>
				<td>
					<input type="hidden" id="hc_avatar_file_id" name="hc_avatar_file_id"
						value="<?php echo esc_attr( (string) $file_id ); ?>">
					<div id="hc-av-preview" style="margin-bottom:6px;">
						<?php if ( '' !== $file_url ) : ?>
							<img src="<?php echo esc_url( $file_url ); ?>" alt="">
						<?php endif; ?>
					</div>
					<button type="button" class="button" id="hc-av-upload">
						<?php echo $file_id > 0 ? 'Change SVG' : 'Select SVG'; ?>
					</button>
					<?php if ( $file_id > 0 ) : ?>
						<button type="button" class="button" id="hc-av-remove" style="margin-left:4px;">Remove</button>
					<?php endif; ?>
					<p class="desc">Upload an SVG silhouette. Remove any baked-in fill colours so the tool can tint the figure with the user&rsquo;s chosen colour.</p>
				</td>
			</tr>
			<tr>
				<th><label for="hc_avatar_gender">Gender</label></th>
				<td>
					<select name="hc_avatar_gender" id="hc_avatar_gender">
						<option value="male"    <?php selected( $gender, 'male' ); ?>>Male</option>
						<option value="female"  <?php selected( $gender, 'female' ); ?>>Female</option>
						<option value="neutral" <?php selected( $gender, 'neutral' ); ?>>Neutral</option>
						<option value="child"   <?php selected( $gender, 'child' ); ?>>Child</option>
						<option value="object"  <?php selected( $gender, 'object' ); ?>>Object / Animal</option>
					</select>
				</td>
			</tr>
			<?php if ( ! is_wp_error( $build_terms ) && ! empty( $build_terms ) ) : ?>
			<tr>
				<th>Build</th>
				<td class="tax-row">
					<?php foreach ( $build_terms as $term ) : ?>
						<?php if ( ! $term instanceof WP_Term ) continue; ?>
						<label>
							<input type="checkbox" name="hc_avatar_build_ids[]"
								value="<?php echo esc_attr( (string) $term->term_id ); ?>"
								<?php checked( in_array( $term->term_id, $post_build_ids, true ) ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</label>
					<?php endforeach; ?>
					<p class="desc">Select all body build types this avatar suits.</p>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ( ! is_wp_error( $style_terms ) && ! empty( $style_terms ) ) : ?>
			<tr>
				<th>Style</th>
				<td class="tax-row">
					<?php foreach ( $style_terms as $term ) : ?>
						<?php if ( ! $term instanceof WP_Term ) continue; ?>
						<label>
							<input type="checkbox" name="hc_avatar_style_ids[]"
								value="<?php echo esc_attr( (string) $term->term_id ); ?>"
								<?php checked( in_array( $term->term_id, $post_style_ids, true ) ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</label>
					<?php endforeach; ?>
					<p class="desc">Select all visual styles this avatar suits.</p>
				</td>
			</tr>
			<?php endif; ?>
		</table>
	</div>
	<script>
	/* global jQuery, wp */
	jQuery( function( $ ) {
		$( '#hc-av-upload' ).on( 'click', function() {
			var frame = wp.media( {
				title: 'Select SVG Avatar',
				button: { text: 'Use this SVG' },
				multiple: false,
				library: { type: 'image/svg+xml' }
			} );
			frame.on( 'select', function() {
				var att = frame.state().get( 'selection' ).first().toJSON();
				$( '#hc_avatar_file_id' ).val( att.id );
				$( '#hc-av-preview' ).html( '<img src="' + att.url + '" style="max-height:100px;max-width:100px;border:1px solid #ddd;padding:4px;background:#f0f0f0;">' );
				$( '#hc-av-upload' ).text( 'Change SVG' );
			} );
			frame.open();
		} );
		$( '#hc-av-remove' ).on( 'click', function() {
			$( '#hc_avatar_file_id' ).val( '0' );
			$( '#hc-av-preview' ).empty();
			$( '#hc-av-upload' ).text( 'Select SVG' );
			$( this ).remove();
		} );
	} );
	</script>
	<?php
}

function hc_avatar_save_meta( int $post_id ): void {
	$nonce = isset( $_POST['hc_avatar_nonce'] )
		? sanitize_text_field( wp_unslash( (string) $_POST['hc_avatar_nonce'] ) )
		: '';
	if ( ! wp_verify_nonce( $nonce, 'hc_avatar_save' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['hc_avatar_file_id'] ) ) {
		update_post_meta( $post_id, 'hc_avatar_file_id', absint( $_POST['hc_avatar_file_id'] ) );
	}
	if ( isset( $_POST['hc_avatar_gender'] ) ) {
		$raw    = sanitize_key( (string) $_POST['hc_avatar_gender'] );
		$gender = in_array( $raw, array( 'male', 'female', 'neutral', 'child', 'object' ), true ) ? $raw : 'male';
		update_post_meta( $post_id, 'hc_avatar_gender', $gender );
	}

	$build_ids = isset( $_POST['hc_avatar_build_ids'] )
		? array_map( 'absint', (array) $_POST['hc_avatar_build_ids'] )
		: array();
	wp_set_post_terms( $post_id, $build_ids, 'hc_avatar_build' );

	$style_ids = isset( $_POST['hc_avatar_style_ids'] )
		? array_map( 'absint', (array) $_POST['hc_avatar_style_ids'] )
		: array();
	wp_set_post_terms( $post_id, $style_ids, 'hc_avatar_style' );
}
add_action( 'save_post_hc_avatar', 'hc_avatar_save_meta' );


/* ─────────────────────────────────────────────────────────────────────────────
   2.  DEFAULT AVATARS — separate settings page + WP option
   ───────────────────────────────────────────────────────────────────────────── */

/**
 * One-time migration: if avatars were previously marked via the hc_avatar_is_default
 * meta field, copy those entries into the hc_default_avatars option automatically.
 */
function hc_migrate_default_avatars(): void {
	if ( get_option( 'hc_default_avatars_migrated' ) ) return;

	$defaults = (array) get_option( 'hc_default_avatars', array() );
	$posts    = get_posts( array(
		'post_type'      => 'hc_avatar',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => array( array( 'key' => 'hc_avatar_is_default', 'value' => '1' ) ),
	) );

	foreach ( $posts as $post ) {
		if ( ! $post instanceof WP_Post ) continue;
		$gender  = (string) get_post_meta( $post->ID, 'hc_avatar_gender', true );
		$file_id = (int) get_post_meta( $post->ID, 'hc_avatar_file_id', true );
		if ( '' === $gender || $file_id <= 0 ) continue;
		$url = wp_get_attachment_url( $file_id );
		if ( ! $url || isset( $defaults[ $gender ] ) ) continue;
		$defaults[ $gender ] = array( 'id' => $file_id, 'url' => (string) $url );
	}

	update_option( 'hc_default_avatars', $defaults );
	update_option( 'hc_default_avatars_migrated', 1 );
}
add_action( 'admin_init', 'hc_migrate_default_avatars' );

function hc_default_avatars_admin_menu(): void {
	add_submenu_page(
		'edit.php?post_type=hc_avatar',
		'Default Avatars',
		'Default Avatars',
		'manage_options',
		'hc-default-avatars',
		'hc_default_avatars_page_render'
	);
}
add_action( 'admin_menu', 'hc_default_avatars_admin_menu' );

function hc_default_avatars_enqueue( string $hook ): void {
	if ( 'hc_avatar_page_hc-default-avatars' !== $hook ) return;
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'hc_default_avatars_enqueue' );

function hc_default_avatars_page_render(): void {
	if ( ! current_user_can( 'manage_options' ) ) return;

	// Handle save.
	if ( 'POST' === $_SERVER['REQUEST_METHOD']
		&& isset( $_POST['hc_def_nonce'] )
		&& wp_verify_nonce(
			sanitize_text_field( wp_unslash( (string) $_POST['hc_def_nonce'] ) ),
			'hc_default_avatars_save'
		)
	) {
		$stored = array();
		foreach ( array( 'male', 'female', 'neutral', 'child', 'object' ) as $g ) {
			$id = absint( $_POST[ 'hc_def_' . $g ] ?? 0 );
			if ( $id > 0 ) {
				$url = wp_get_attachment_url( $id );
				if ( $url ) {
					$stored[ $g ] = array( 'id' => $id, 'url' => (string) $url );
				}
			}
		}
		update_option( 'hc_default_avatars', $stored );
		echo '<div class="notice notice-success is-dismissible"><p><strong>Default avatars saved.</strong></p></div>';
	}

	$saved   = (array) get_option( 'hc_default_avatars', array() );
	$genders = array(
		'male'    => 'Male',
		'female'  => 'Female',
		'neutral' => 'Neutral',
		'child'   => 'Child',
		'object'  => 'Object / Animal',
	);
	?>
	<div class="wrap">
		<h1>Default Avatars</h1>
		<p>
			Set the silhouette applied <strong>automatically</strong> for each gender when a person is added
			without choosing a custom avatar from the picker.<br>
			This page is completely separate from the
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hc_avatar' ) ); ?>">Avatars</a>
			library used by the picker.
		</p>

		<style>
		.hc-def-row{display:flex;align-items:center;gap:16px;padding:16px 0;border-bottom:1px solid #ddd}
		.hc-def-row__label{width:120px;font-weight:600}
		.hc-def-row__preview{width:70px;height:80px;background:#f0f0f0;border:1px solid #ddd;display:flex;align-items:center;justify-content:center;flex-shrink:0}
		.hc-def-row__preview img{max-width:100%;max-height:100%;object-fit:contain}
		.hc-def-row__preview--empty{color:#aaa;font-size:11px;text-align:center}
		</style>

		<form method="post">
			<?php wp_nonce_field( 'hc_default_avatars_save', 'hc_def_nonce' ); ?>

			<?php foreach ( $genders as $key => $label ) :
				$entry    = $saved[ $key ] ?? array();
				$file_id  = (int) ( $entry['id'] ?? 0 );
				$file_url = (string) ( $entry['url'] ?? '' );
			?>
			<div class="hc-def-row">
				<div class="hc-def-row__label"><?php echo esc_html( $label ); ?></div>
				<div class="hc-def-row__preview" id="hc-def-preview-<?php echo esc_attr( $key ); ?>">
					<?php if ( '' !== $file_url ) : ?>
						<img src="<?php echo esc_url( $file_url ); ?>" alt="">
					<?php else : ?>
						<span class="hc-def-row__preview--empty">No default</span>
					<?php endif; ?>
				</div>
				<div>
					<input type="hidden"
						name="hc_def_<?php echo esc_attr( $key ); ?>"
						id="hc_def_<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( (string) $file_id ); ?>">
					<button type="button" class="button hc-def-upload" data-gender="<?php echo esc_attr( $key ); ?>">
						<?php echo $file_id > 0 ? 'Change SVG' : 'Select SVG'; ?>
					</button>
					<?php if ( $file_id > 0 ) : ?>
					<button type="button" class="button hc-def-remove"
						data-gender="<?php echo esc_attr( $key ); ?>"
						style="margin-left:4px;">Remove</button>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>

			<?php submit_button( 'Save Default Avatars' ); ?>
		</form>
	</div>

	<script>
	/* global jQuery, wp */
	jQuery( function( $ ) {
		function handleRemove() {
			var gender = $( this ).data( 'gender' );
			$( '#hc_def_' + gender ).val( '0' );
			$( '#hc-def-preview-' + gender ).html( '<span class="hc-def-row__preview--empty">No default</span>' );
			$( this ).prev( '.hc-def-upload' ).text( 'Select SVG' );
			$( this ).remove();
		}
		$( document ).on( 'click', '.hc-def-remove', handleRemove );

		$( document ).on( 'click', '.hc-def-upload', function() {
			var gender = $( this ).data( 'gender' );
			var $btn   = $( this );
			var frame  = wp.media( {
				title:  'Select Default SVG',
				button: { text: 'Use this SVG' },
				multiple: false,
				library: { type: 'image/svg+xml' }
			} );
			frame.on( 'select', function() {
				var att = frame.state().get( 'selection' ).first().toJSON();
				$( '#hc_def_' + gender ).val( att.id );
				$( '#hc-def-preview-' + gender ).html( '<img src="' + att.url + '" style="max-width:100%;max-height:100%;object-fit:contain;">' );
				$btn.text( 'Change SVG' );
				if ( ! $btn.next( '.hc-def-remove' ).length ) {
					$btn.after( '<button type="button" class="button hc-def-remove" data-gender="' + gender + '" style="margin-left:4px;">Remove</button>' );
				}
			} );
			frame.open();
		} );
	} );
	</script>
	<?php
}


/* ─────────────────────────────────────────────────────────────────────────────
   3.  REST ENDPOINTS
   ───────────────────────────────────────────────────────────────────────────── */

function hc_register_avatar_rest_routes(): void {
	register_rest_route( 'hc/v1', '/default-avatars', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'hc_rest_default_avatars',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'hc/v1', '/avatars', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'hc_rest_avatars',
		'permission_callback' => '__return_true',
		'args'                => array(
			'gender' => array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_key' ),
			'build'  => array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_key' ),
			'style'  => array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_key' ),
			'q'      => array( 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ),
		),
	) );

	register_rest_route( 'hc/v1', '/avatar-filters', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'hc_rest_avatar_filters',
		'permission_callback' => '__return_true',
	) );
}
add_action( 'rest_api_init', 'hc_register_avatar_rest_routes' );

/**
 * GET /hc/v1/default-avatars — reads from WP option, NOT from the CPT.
 *
 * @return WP_REST_Response
 */
function hc_rest_default_avatars(): WP_REST_Response {
	$stored  = (array) get_option( 'hc_default_avatars', array() );
	$genders = array( 'male', 'female', 'neutral', 'child', 'object' );
	$out     = array();
	foreach ( $genders as $g ) {
		$out[ $g ] = isset( $stored[ $g ]['url'] ) ? $stored[ $g ]['url'] : null;
	}
	$response = new WP_REST_Response( $out );
	$response->header( 'Cache-Control', 'public, max-age=60' );
	return $response;
}

/**
 * GET /hc/v1/avatars
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function hc_rest_avatars( WP_REST_Request $request ): WP_REST_Response {
	$gender = (string) $request->get_param( 'gender' );
	$build  = (string) $request->get_param( 'build' );
	$style  = (string) $request->get_param( 'style' );
	$q      = (string) $request->get_param( 'q' );

	$query_args = array(
		'post_type'      => 'hc_avatar',
		'post_status'    => 'publish',
		'posts_per_page' => 200,
		'orderby'        => 'title',
		'order'          => 'ASC',
		's'              => $q,
	);

	if ( '' !== $gender ) {
		$query_args['meta_query'] = array(
			array( 'key' => 'hc_avatar_gender', 'value' => $gender, 'compare' => '=' ),
		);
	}

	$tax_query = array();
	if ( '' !== $build ) {
		$tax_query[] = array( 'taxonomy' => 'hc_avatar_build', 'field' => 'slug', 'terms' => $build );
	}
	if ( '' !== $style ) {
		$tax_query[] = array( 'taxonomy' => 'hc_avatar_style', 'field' => 'slug', 'terms' => $style );
	}
	if ( ! empty( $tax_query ) ) {
		$query_args['tax_query'] = $tax_query;
	}

	$items = array();
	foreach ( get_posts( $query_args ) as $post ) {
		if ( ! $post instanceof WP_Post ) continue;
		$file_id = (int) get_post_meta( $post->ID, 'hc_avatar_file_id', true );
		if ( $file_id <= 0 ) continue;
		$url = wp_get_attachment_url( $file_id );
		if ( ! $url ) continue;

		$raw_builds = wp_get_post_terms( $post->ID, 'hc_avatar_build', array( 'fields' => 'slugs' ) );
		$raw_styles = wp_get_post_terms( $post->ID, 'hc_avatar_style', array( 'fields' => 'slugs' ) );

		$items[] = array(
			'id'     => $post->ID,
			'name'   => html_entity_decode( get_the_title( $post ), ENT_QUOTES ),
			'url'    => (string) $url,
			'gender' => (string) get_post_meta( $post->ID, 'hc_avatar_gender', true ) ?: 'male',
			'build'  => ! is_wp_error( $raw_builds ) ? $raw_builds : array(),
			'style'  => ! is_wp_error( $raw_styles ) ? $raw_styles : array(),
		);
	}

	$response = new WP_REST_Response( $items );
	$response->header( 'Cache-Control', 'public, max-age=120' );
	return $response;
}

/**
 * GET /hc/v1/avatar-filters
 *
 * @return WP_REST_Response
 */
function hc_rest_avatar_filters(): WP_REST_Response {
	$build_terms = get_terms( array( 'taxonomy' => 'hc_avatar_build', 'hide_empty' => false ) );
	$style_terms = get_terms( array( 'taxonomy' => 'hc_avatar_style', 'hide_empty' => false ) );

	$data = array( 'builds' => array(), 'styles' => array() );
	if ( ! is_wp_error( $build_terms ) ) {
		foreach ( $build_terms as $t ) {
			if ( $t instanceof WP_Term ) $data['builds'][] = array( 'slug' => $t->slug, 'name' => $t->name );
		}
	}
	if ( ! is_wp_error( $style_terms ) ) {
		foreach ( $style_terms as $t ) {
			if ( $t instanceof WP_Term ) $data['styles'][] = array( 'slug' => $t->slug, 'name' => $t->name );
		}
	}

	$response = new WP_REST_Response( $data );
	$response->header( 'Cache-Control', 'public, max-age=120' );
	return $response;
}
