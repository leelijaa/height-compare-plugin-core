<?php
/**
 * Admin edit screens for the three CPTs.
 *
 * Celebrity posts use a full custom inline template (rendered via
 * edit_form_after_title) instead of a classic meta box table.
 * height_reference and country_average use the standard meta box table.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Celebrity: disable Gutenberg, use custom template ─────────────────── */

add_filter(
	'use_block_editor_for_post_type',
	static function ( bool $use, string $post_type ): bool {
		return ( 'celebrity' === $post_type ) ? false : $use;
	},
	10,
	2
);

/**
 * Enqueue admin stylesheet for the celebrity edit template.
 *
 * @param string $hook Current admin page hook.
 */
function hc_admin_enqueue( string $hook ): void {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	$screen = get_current_screen();
	if ( null === $screen ) {
		return;
	}

	if ( 'celebrity' === $screen->post_type ) {
		wp_enqueue_style(
			'hc-celebrity-admin',
			HC_URI . '/assets/css/celebrity-admin.css',
			array(),
			HC_VERSION
		);

		// cm / ft sync + height-pair script
		add_action( 'admin_print_footer_scripts', 'hc_celebrity_admin_js' );
	} elseif ( 'page' === $screen->post_type && hc_is_converter_page( $screen ) ) {
		wp_enqueue_style(
			'hc-celebrity-admin',
			HC_URI . '/assets/css/celebrity-admin.css',
			array(),
			HC_VERSION
		);
		add_action( 'admin_print_footer_scripts', 'hc_page_faq_js' );
	} elseif ( array_key_exists( $screen->post_type, hc_meta_fields() ) ) {
		// Classic dual-input sync for height_reference / country_average
		add_action( 'admin_print_footer_scripts', 'hc_height_pair_js' );
	}
}
add_action( 'admin_enqueue_scripts', 'hc_admin_enqueue' );

/**
 * Render the celebrity edit template after the post title.
 *
 * @param WP_Post $post Post being edited.
 */
function hc_celebrity_edit_template( WP_Post $post ): void {
	if ( 'celebrity' !== $post->post_type ) {
		return;
	}
	wp_nonce_field( 'hc_meta_save', 'hc_meta_nonce' );

	$cm          = (float) get_post_meta( $post->ID, 'hc_height_cm', true );
	$ft_val      = ( $cm > 0 ) ? (int) floor( $cm / 30.48 ) : 0;
	$in_val      = ( $cm > 0 ) ? round( fmod( $cm, 30.48 ) / 2.54, 1 ) : 0.0;
	$gender      = (string) ( get_post_meta( $post->ID, 'hc_gender', true ) ?: 'male' );
	$country     = (string) get_post_meta( $post->ID, 'hc_country', true );
	$cat         = (string) get_post_meta( $post->ID, 'hc_category', true );
	$dob         = (string) get_post_meta( $post->ID, 'hc_dob', true );
	$birthplace  = (string) get_post_meta( $post->ID, 'hc_birthplace', true );
	$weight_kg   = (int) get_post_meta( $post->ID, 'hc_weight_kg', true );
	$eye_color   = (string) get_post_meta( $post->ID, 'hc_eye_color', true );
	$hair_color  = (string) get_post_meta( $post->ID, 'hc_hair_color', true );
	$body_color  = (string) get_post_meta( $post->ID, 'hc_body_color', true );
	$body_type              = (string) get_post_meta( $post->ID, 'hc_body_type', true );
	$physical_attrs_para    = (string) get_post_meta( $post->ID, 'hc_physical_attributes_para', true );
	$phys_attrs_rows_json   = get_post_meta( $post->ID, 'hc_phys_attrs_rows', true );
	if ( ! empty( $phys_attrs_rows_json ) ) {
		$phys_attrs_rows = json_decode( $phys_attrs_rows_json, true ) ?: array();
	} else {
		$phys_attrs_rows = array_values( array_filter( array(
			array( 'Gender',      ucfirst( $gender ) ),
			array( 'Weight (kg)', $weight_kg > 0 ? (string) $weight_kg : '' ),
			array( 'Eye Colour',  $eye_color ),
			array( 'Hair Color',  $hair_color ),
			array( 'Body Color',  $body_color ),
			array( 'Body Type',   $body_type ),
		), function ( $r ) { return '' !== $r[1]; } ) );
	}
	$biography_para         = (string) get_post_meta( $post->ID, 'hc_biography_para', true );
	$hero_bio               = (string) get_post_meta( $post->ID, 'hc_hero_bio', true );
	$birth_name  = (string) get_post_meta( $post->ID, 'hc_birth_name', true );
	$full_name   = (string) get_post_meta( $post->ID, 'hc_full_name', true );
	$nickname    = (string) get_post_meta( $post->ID, 'hc_nickname', true );
	$profession      = (string) get_post_meta( $post->ID, 'hc_profession', true );
	$school          = (string) get_post_meta( $post->ID, 'hc_school', true );
	$college         = (string) get_post_meta( $post->ID, 'hc_college', true );
	$father_name     = (string) get_post_meta( $post->ID, 'hc_father_name', true );
	$mother_name     = (string) get_post_meta( $post->ID, 'hc_mother_name', true );
	$siblings        = (string) get_post_meta( $post->ID, 'hc_siblings', true );
	$marital_status  = (string) get_post_meta( $post->ID, 'hc_marital_status', true );
	$girlfriend_name = (string) get_post_meta( $post->ID, 'hc_girlfriend_name', true );
	$wife_name       = (string) get_post_meta( $post->ID, 'hc_wife_name', true );
	$friends_names   = (string) get_post_meta( $post->ID, 'hc_friends_names', true );
	$religion        = (string) get_post_meta( $post->ID, 'hc_religion', true );
	$hometown        = (string) get_post_meta( $post->ID, 'hc_hometown', true );
	$current_address = (string) get_post_meta( $post->ID, 'hc_current_address', true );
	$children        = (string) get_post_meta( $post->ID, 'hc_children', true );
	$hobbies         = (string) get_post_meta( $post->ID, 'hc_hobbies', true );
	$family_bg_rows_json = get_post_meta( $post->ID, 'hc_family_bg_rows', true );
	if ( ! empty( $family_bg_rows_json ) ) {
		$family_bg_rows = json_decode( $family_bg_rows_json, true ) ?: array();
	} else {
		$family_bg_rows = array_values( array_filter( array(
			array( 'School',          $school ),
			array( 'College',         $college ),
			array( "Father's Name",   $father_name ),
			array( "Mother's Name",   $mother_name ),
			array( 'Siblings',        $siblings ),
			array( 'Marital Status',  $marital_status ),
			array( 'Girlfriend Name', $girlfriend_name ),
			array( 'Wife Name',       $wife_name ),
			array( 'Friends Name',    $friends_names ),
			array( 'Religion',        $religion ),
			array( 'Hometown',        $hometown ),
			array( 'Current Address', $current_address ),
			array( 'Children',        $children ),
			array( 'Hobbies',         $hobbies ),
		), function ( $r ) { return '' !== $r[1]; } ) );
	}
	$awards          = (string) get_post_meta( $post->ID, 'hc_awards', true );
	$net_worth       = (string) get_post_meta( $post->ID, 'hc_net_worth', true );
	$monthly_earning = (string) get_post_meta( $post->ID, 'hc_monthly_earning', true );
	$career_rows_json = get_post_meta( $post->ID, 'hc_career_rows', true );
	if ( ! empty( $career_rows_json ) ) {
		$career_rows = json_decode( $career_rows_json, true ) ?: array();
	} else {
		$career_rows = array_values( array_filter( array(
			array( 'Awards',          $awards ),
			array( 'Monthly Earning', $monthly_earning ),
		), function ( $r ) { return '' !== $r[1]; } ) );
	}
	$age_display     = '';
	if ( '' !== $dob ) {
		$birth = DateTimeImmutable::createFromFormat( 'Y-m-d', $dob );
		if ( $birth instanceof DateTimeImmutable ) {
			$age_display = (string) (int) $birth->diff( new DateTimeImmutable( 'today' ) )->y;
		}
	}
	$aliases     = (string) get_post_meta( $post->ID, 'hc_aliases', true );
	$source          = (string) get_post_meta( $post->ID, 'hc_source_url', true );
	$volume          = (int) get_post_meta( $post->ID, 'hc_search_volume', true );
	$faq_heading     = (string) get_post_meta( $post->ID, 'hc_faq_heading', true );
	$first_name      = explode( ' ', get_the_title( $post ) )[0];
	$lede_text       = (string) get_post_meta( $post->ID, 'hc_lede_text', true );
	$tpl_show_stats  = (string) get_post_meta( $post->ID, 'hc_tpl_show_stats', true );
	$tpl_show_cta    = (string) get_post_meta( $post->ID, 'hc_tpl_show_cta', true );
	$tpl_show_related = (string) get_post_meta( $post->ID, 'hc_tpl_show_related', true );
	$tpl_show_faq    = (string) get_post_meta( $post->ID, 'hc_tpl_show_faq', true );
	// '' = inherit global default, '1' = force show, '0' = force hide.
	$global_tpl = hc_get_template_defaults();

	// Bio table rows: JSON first, then migrate from individual meta fields.
	$bio_table_rows_json = get_post_meta( $post->ID, 'hc_bio_table_rows', true );
	if ( ! empty( $bio_table_rows_json ) ) {
		$bio_table_rows = json_decode( $bio_table_rows_json, true ) ?: array();
	} else {
		$_dob_display = '';
		if ( '' !== $dob ) {
			$_d = DateTimeImmutable::createFromFormat( 'Y-m-d', $dob );
			if ( $_d instanceof DateTimeImmutable ) {
				$_dob_display = date_i18n( 'F j, Y', $_d->getTimestamp() );
			}
		}
		$bio_table_rows = array_values( array_filter(
			array(
				array( 'Birth Name',      $birth_name ),
				array( 'Full Name',       $full_name ),
				array( 'Nickname',        $nickname ),
				array( 'Profession',      $profession ),
				array( 'Birthday',        $_dob_display ),
				array( 'Birthplace',      $birthplace ),
				array( 'School',          $school ),
				array( 'College',         $college ),
				array( "Father's Name",   $father_name ),
				array( "Mother's Name",   $mother_name ),
				array( 'Siblings',        $siblings ),
				array( 'Marital Status',  $marital_status ),
				array( 'Girlfriend',      $girlfriend_name ),
				array( 'Wife',            $wife_name ),
				array( 'Children',        $children ),
				array( 'Friends',         $friends_names ),
				array( 'Religion',        $religion ),
				array( 'Hometown',        $hometown ),
				array( 'Current Address', $current_address ),
				array( 'Hobbies',         $hobbies ),
				array( 'Net Worth',       $net_worth ),
				array( 'Monthly Earning', $monthly_earning ),
				array( 'Awards',          $awards ),
			),
			static fn( array $r ): bool => '' !== $r[1]
		) );
	}

	// Custom page sections (JSON).
	$page_sections_json = get_post_meta( $post->ID, 'hc_page_sections', true );
	$page_sections = ! empty( $page_sections_json ) ? json_decode( $page_sections_json, true ) ?: array() : array();
	?>
	<div class="hc-cel-tpl">

		<!-- ── Section: Hero Section ────────────────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">🎬</span>
				<h2 class="hc-cel-tpl__section-title"><?php esc_html_e( 'Hero Section', 'height-compare' ); ?></h2>
			</div>
			<div class="hc-cel-tpl__grid">

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label">
						<?php esc_html_e( 'Hero Bio Paragraph', 'height-compare' ); ?>
						<span class="hc-cel-tpl__hint"><?php esc_html_e( 'Shown below the auto-generated height sentence in the hero column', 'height-compare' ); ?></span>
					</label>
					<?php wp_editor( $hero_bio, 'hcherobi', array(
						'textarea_name' => 'hc_hero_bio',
						'media_buttons' => false,
						'textarea_rows' => 5,
						'tinymce'       => array( 'toolbar1' => 'bold italic link unlink | undo redo' ),
					) ); ?>
				</div>

				<div class="hc-cel-tpl__field">
					<label class="hc-cel-tpl__label" for="hc_dob">
						<?php esc_html_e( 'Date of Birth', 'height-compare' ); ?>
						<span class="hc-cel-tpl__hint" id="hc_dob_age_hint"><?php echo '' !== $age_display ? 'Age: ' . esc_html( $age_display ) : ''; ?></span>
					</label>
					<input class="hc-cel-tpl__input" type="date"
						name="hc_dob" id="hc_dob"
						value="<?php echo esc_attr( $dob ); ?>">
				</div>

				<div class="hc-cel-tpl__field">
					<label class="hc-cel-tpl__label" for="hc_profession">
						<?php esc_html_e( 'Profession', 'height-compare' ); ?>
						<span class="hc-cel-tpl__hint"><?php esc_html_e( 'Shown in the Profession stat tile', 'height-compare' ); ?></span>
					</label>
					<input class="hc-cel-tpl__input" type="text"
						name="hc_profession" id="hc_profession"
						value="<?php echo esc_attr( $profession ); ?>"
						placeholder="<?php esc_attr_e( 'Singer-Songwriter', 'height-compare' ); ?>">
				</div>

				<div class="hc-cel-tpl__field">
					<label class="hc-cel-tpl__label" for="hc_net_worth">
						<?php esc_html_e( 'Net Worth', 'height-compare' ); ?>
						<span class="hc-cel-tpl__hint"><?php esc_html_e( 'Shown in the Net Worth stat tile', 'height-compare' ); ?></span>
					</label>
					<input class="hc-cel-tpl__input" type="text"
						name="hc_net_worth" id="hc_net_worth"
						value="<?php echo esc_attr( $net_worth ); ?>"
						placeholder="<?php esc_attr_e( '$50 Million', 'height-compare' ); ?>">
				</div>

			</div>
		</div>

		<!-- ── Section: Height in Every Unit ───────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">📏</span>
				<h2 class="hc-cel-tpl__section-title"><?php echo esc_html( $first_name ); ?> <?php esc_html_e( 'Height in Every Unit', 'height-compare' ); ?></h2>
			</div>
			<p class="hc-cel-tpl__desc"><?php esc_html_e( 'Drives the unit cards, comparison chart, and the Height stat tile in the hero.', 'height-compare' ); ?></p>
			<div class="hc-cel-tpl__height-row hc-height-pair" data-key="hc_height_cm">
				<div class="hc-cel-tpl__height-field">
					<label class="hc-cel-tpl__label" for="hc_height_cm">cm</label>
					<input class="hc-cel-tpl__num hc-cm" type="number" step="0.1" min="1" max="300"
						id="hc_height_cm" name="hc_height_cm"
						value="<?php echo esc_attr( $cm > 0 ? (string) $cm : '' ); ?>">
				</div>
				<span class="hc-cel-tpl__eq">=</span>
				<div class="hc-cel-tpl__height-field">
					<label class="hc-cel-tpl__label" for="hc_cel_ft">ft</label>
					<input class="hc-cel-tpl__num hc-ft" type="number" step="1" min="0" max="9"
						id="hc_cel_ft"
						value="<?php echo esc_attr( $cm > 0 ? (string) $ft_val : '' ); ?>"
						aria-label="<?php esc_attr_e( 'feet', 'height-compare' ); ?>">
				</div>
				<div class="hc-cel-tpl__height-field">
					<label class="hc-cel-tpl__label" for="hc_cel_in">in</label>
					<input class="hc-cel-tpl__num hc-in" type="number" step="0.1" min="0" max="11.9"
						id="hc_cel_in"
						value="<?php echo esc_attr( $cm > 0 ? (string) $in_val : '' ); ?>"
						aria-label="<?php esc_attr_e( 'inches', 'height-compare' ); ?>">
				</div>
				<div class="hc-cel-tpl__height-field">
					<label class="hc-cel-tpl__label" for="hc_cel_m">m</label>
					<input class="hc-cel-tpl__num hc-m" type="text" id="hc_cel_m" readonly
						value="<?php echo esc_attr( $cm > 0 ? number_format( $cm / 100, 2 ) : '' ); ?>"
						aria-label="<?php esc_attr_e( 'metres', 'height-compare' ); ?>"
						tabindex="-1">
				</div>
				<div class="hc-cel-tpl__height-field">
					<label class="hc-cel-tpl__label" for="hc_cel_yd">yd</label>
					<input class="hc-cel-tpl__num hc-yd" type="text" id="hc_cel_yd" readonly
						value="<?php echo esc_attr( $cm > 0 ? number_format( $cm / 91.44, 2 ) : '' ); ?>"
						aria-label="<?php esc_attr_e( 'yards', 'height-compare' ); ?>"
						tabindex="-1">
				</div>
			</div>
		</div>

		<!-- ── Section: Physical Attributes ─────────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">💪</span>
				<h2 class="hc-cel-tpl__section-title"><?php esc_html_e( 'Physical Attributes', 'height-compare' ); ?></h2>
			</div>
			<div class="hc-cel-tpl__grid">

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label"><?php esc_html_e( 'Attribute Rows', 'height-compare' ); ?></label>
					<div id="hc-phys-attrs-list" class="hc-repeater-list">
						<?php foreach ( $phys_attrs_rows as $hc_par ) :
							$hc_par_label = is_array( $hc_par ) ? ( $hc_par[0] ?? '' ) : '';
							$hc_par_value = is_array( $hc_par ) ? ( $hc_par[1] ?? '' ) : '';
						?>
						<div class="hc-repeater-row">
							<input type="text" name="hc_phys_attr_label[]"
								class="hc-repeater-row__label hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_par_label ); ?>"
								placeholder="<?php esc_attr_e( 'Label', 'height-compare' ); ?>">
							<input type="text" name="hc_phys_attr_value[]"
								class="hc-repeater-row__value hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_par_value ); ?>"
								placeholder="<?php esc_attr_e( 'Value', 'height-compare' ); ?>">
							<button type="button" class="hc-repeater-remove button">✕</button>
						</div>
						<?php endforeach; ?>
					</div>
					<button type="button" id="hc-phys-attr-add" class="button" style="margin-top:8px">
						<?php esc_html_e( '+ Add Row', 'height-compare' ); ?>
					</button>
				</div>

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label">
						<?php esc_html_e( 'Physical Attributes — Paragraph', 'height-compare' ); ?>
						<span class="hc-cel-tpl__hint"><?php esc_html_e( 'Shown below the attributes on the celebrity page', 'height-compare' ); ?></span>
					</label>
					<?php wp_editor( $physical_attrs_para, 'hcphysattrspara', array(
						'textarea_name' => 'hc_physical_attributes_para',
						'media_buttons' => false,
						'textarea_rows' => 5,
						'tinymce'       => array( 'toolbar1' => 'bold italic link unlink | undo redo' ),
					) ); ?>
				</div>

			</div>
		</div>

		<!-- ── Section: Biography & Personal Info ────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">📝</span>
				<h2 class="hc-cel-tpl__section-title"><?php echo esc_html( $first_name ); ?> <?php esc_html_e( 'Biography & Personal Info', 'height-compare' ); ?></h2>
			</div>
			<div class="hc-cel-tpl__grid">

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label">
						<?php esc_html_e( 'Biography Paragraph', 'height-compare' ); ?>
						<span class="hc-cel-tpl__hint"><?php esc_html_e( 'Shown above the info table on the celebrity page', 'height-compare' ); ?></span>
					</label>
					<?php wp_editor( $biography_para, 'hcbiopara', array(
						'textarea_name' => 'hc_biography_para',
						'media_buttons' => false,
						'textarea_rows' => 6,
					) ); ?>
				</div>

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label"><?php esc_html_e( 'Info Table Rows', 'height-compare' ); ?></label>
					<div id="hc-bio-rows-list" class="hc-repeater-list">
						<?php foreach ( $bio_table_rows as $hc_btr ) :
							$hc_btr_label = is_array( $hc_btr ) ? ( $hc_btr[0] ?? '' ) : '';
							$hc_btr_value = is_array( $hc_btr ) ? ( $hc_btr[1] ?? '' ) : '';
						?>
						<div class="hc-repeater-row">
							<input type="text" name="hc_bio_row_label[]"
								class="hc-repeater-row__label hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_btr_label ); ?>"
								placeholder="<?php esc_attr_e( 'Label', 'height-compare' ); ?>">
							<input type="text" name="hc_bio_row_value[]"
								class="hc-repeater-row__value hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_btr_value ); ?>"
								placeholder="<?php esc_attr_e( 'Value', 'height-compare' ); ?>">
							<button type="button" class="hc-repeater-remove button">✕</button>
						</div>
						<?php endforeach; ?>
					</div>
					<button type="button" id="hc-bio-row-add" class="button" style="margin-top:8px">
						<?php esc_html_e( '+ Add Row', 'height-compare' ); ?>
					</button>
				</div>

			</div>
		</div>

		<!-- ── Section: Family & Background ──────────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">👨‍👩‍👧‍👦</span>
				<h2 class="hc-cel-tpl__section-title"><?php esc_html_e( 'Family & Background', 'height-compare' ); ?></h2>
			</div>
			<div class="hc-cel-tpl__grid">

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label"><?php esc_html_e( 'Rows', 'height-compare' ); ?></label>
					<div id="hc-family-bg-list" class="hc-repeater-list">
						<?php foreach ( $family_bg_rows as $hc_fbr ) :
							$hc_fbr_label = is_array( $hc_fbr ) ? ( $hc_fbr[0] ?? '' ) : '';
							$hc_fbr_value = is_array( $hc_fbr ) ? ( $hc_fbr[1] ?? '' ) : '';
						?>
						<div class="hc-repeater-row">
							<input type="text" name="hc_family_bg_label[]"
								class="hc-repeater-row__label hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_fbr_label ); ?>"
								placeholder="<?php esc_attr_e( 'Label', 'height-compare' ); ?>">
							<input type="text" name="hc_family_bg_value[]"
								class="hc-repeater-row__value hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_fbr_value ); ?>"
								placeholder="<?php esc_attr_e( 'Value', 'height-compare' ); ?>">
							<button type="button" class="hc-repeater-remove button">✕</button>
						</div>
						<?php endforeach; ?>
					</div>
					<button type="button" id="hc-family-bg-add" class="button" style="margin-top:8px">
						<?php esc_html_e( '+ Add Row', 'height-compare' ); ?>
					</button>
				</div>

			</div>
		</div>

		<!-- ── Section: Career & Financials ──────────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">💰</span>
				<h2 class="hc-cel-tpl__section-title"><?php esc_html_e( 'Career & Financials', 'height-compare' ); ?></h2>
			</div>
			<div class="hc-cel-tpl__grid">

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label"><?php esc_html_e( 'Rows', 'height-compare' ); ?></label>
					<div id="hc-career-list" class="hc-repeater-list">
						<?php foreach ( $career_rows as $hc_cr ) :
							$hc_cr_label = is_array( $hc_cr ) ? ( $hc_cr[0] ?? '' ) : '';
							$hc_cr_value = is_array( $hc_cr ) ? ( $hc_cr[1] ?? '' ) : '';
						?>
						<div class="hc-repeater-row">
							<input type="text" name="hc_career_label[]"
								class="hc-repeater-row__label hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_cr_label ); ?>"
								placeholder="<?php esc_attr_e( 'Label', 'height-compare' ); ?>">
							<input type="text" name="hc_career_value[]"
								class="hc-repeater-row__value hc-cel-tpl__input"
								value="<?php echo esc_attr( $hc_cr_value ); ?>"
								placeholder="<?php esc_attr_e( 'Value', 'height-compare' ); ?>">
							<button type="button" class="hc-repeater-remove button">✕</button>
						</div>
						<?php endforeach; ?>
					</div>
					<button type="button" id="hc-career-add" class="button" style="margin-top:8px">
						<?php esc_html_e( '+ Add Row', 'height-compare' ); ?>
					</button>
				</div>

			</div>
		</div>

		<!-- ── Section: Social Media ──────────────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">📱</span>
				<h2 class="hc-cel-tpl__section-title"><?php esc_html_e( 'Social Media', 'height-compare' ); ?></h2>
			</div>
			<div class="hc-cel-tpl__grid">

				<?php
				$sm_fields = array(
					'hc_twitter_handle'      => array( 'label' => 'Twitter/X Handle',      'ph' => '@elonmusk' ),
					'hc_twitter_followers'   => array( 'label' => 'Twitter/X Followers',    'ph' => '180M+' ),
					'hc_instagram_handle'    => array( 'label' => 'Instagram Handle',       'ph' => '@elonmusk' ),
					'hc_instagram_followers' => array( 'label' => 'Instagram Followers',    'ph' => '2.5M+' ),
					'hc_youtube_channel'     => array( 'label' => 'YouTube Channel',        'ph' => 'SpaceX' ),
					'hc_youtube_followers'   => array( 'label' => 'YouTube Subscribers',    'ph' => '12M+' ),
					'hc_facebook_handle'     => array( 'label' => 'Facebook Name/Handle',   'ph' => 'Elon Musk' ),
					'hc_facebook_followers'  => array( 'label' => 'Facebook Followers',     'ph' => '3.5M+' ),
					'hc_tiktok_handle'       => array( 'label' => 'TikTok Handle',          'ph' => '@elonmusk' ),
					'hc_tiktok_followers'    => array( 'label' => 'TikTok Followers',       'ph' => '5M+' ),
				);
				foreach ( $sm_fields as $sm_key => $sm_cfg ) :
					$sm_val = (string) get_post_meta( $post->ID, $sm_key, true );
				?>
				<div class="hc-cel-tpl__field">
					<label class="hc-cel-tpl__label" for="<?php echo esc_attr( $sm_key ); ?>"><?php echo esc_html( $sm_cfg['label'] ); ?></label>
					<input class="hc-cel-tpl__input" type="text" name="<?php echo esc_attr( $sm_key ); ?>" id="<?php echo esc_attr( $sm_key ); ?>"
						value="<?php echo esc_attr( $sm_val ); ?>"
						placeholder="<?php echo esc_attr( $sm_cfg['ph'] ); ?>">
				</div>
				<?php endforeach; ?>

			</div>
		</div>

		<!-- ── Section: SEO ────────────────────────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">🔍</span>
				<h2 class="hc-cel-tpl__section-title"><?php esc_html_e( 'SEO', 'height-compare' ); ?></h2>
			</div>
			<div class="hc-cel-tpl__grid">

				<div class="hc-cel-tpl__field hc-cel-tpl__field--full">
					<label class="hc-cel-tpl__label" for="hc_source_url">
						<?php esc_html_e( 'Source URL', 'height-compare' ); ?>
					</label>
					<input class="hc-cel-tpl__input" type="url"
						name="hc_source_url" id="hc_source_url"
						value="<?php echo esc_attr( $source ); ?>"
						placeholder="https://…">
				</div>

				<div class="hc-cel-tpl__field">
					<label class="hc-cel-tpl__label" for="hc_search_volume">
						<?php esc_html_e( 'Monthly Search Volume', 'height-compare' ); ?>
					</label>
					<input class="hc-cel-tpl__input" type="number" min="0"
						name="hc_search_volume" id="hc_search_volume"
						value="<?php echo esc_attr( $volume > 0 ? (string) $volume : '' ); ?>"
						placeholder="0">
					<p class="hc-cel-tpl__desc">
						<?php esc_html_e( 'Versus pages are indexed when ≥ 100', 'height-compare' ); ?>
					</p>
				</div>

			</div>
		</div>

		<!-- ── Section: Page Content ────────────────────────────────────────── -->
		<div class="hc-cel-tpl__section">
			<div class="hc-cel-tpl__section-head">
				<span class="hc-cel-tpl__icon">📄</span>
				<h2 class="hc-cel-tpl__section-title"><?php esc_html_e( 'Page Content', 'height-compare' ); ?></h2>
			</div>

			<!-- FAQ Section Heading -->
			<div class="hc-cel-tpl__field hc-cel-tpl__field--full" style="margin-bottom:20px">
				<label class="hc-cel-tpl__label" for="hc_faq_heading">
					<?php esc_html_e( 'FAQ Section Heading', 'height-compare' ); ?>
					<span class="hc-cel-tpl__hint">
						<?php echo esc_html( sprintf( __( 'Default: "%s height — FAQ"', 'height-compare' ), $first_name ) ); ?>
					</span>
				</label>
				<input class="hc-cel-tpl__input" type="text"
					name="hc_faq_heading" id="hc_faq_heading"
					value="<?php echo esc_attr( $faq_heading ); ?>"
					placeholder="<?php echo esc_attr( sprintf( __( '%s height — FAQ', 'height-compare' ), $first_name ) ); ?>">
			</div>

			<!-- Custom FAQs repeater -->
			<div style="margin-bottom:8px">
				<strong><?php esc_html_e( 'Custom FAQs', 'height-compare' ); ?></strong>
				<span class="hc-cel-tpl__hint" style="margin-left:6px">
					<?php esc_html_e( 'Displayed on the celebrity page', 'height-compare' ); ?>
				</span>
			</div>

			<div class="hc-faq-list" id="hc-faq-list">
				<?php
				$hc_saved_faqs = (string) get_post_meta( $post->ID, 'hc_faqs', true );
				$hc_faqs_arr   = ( '' !== $hc_saved_faqs ) ? json_decode( $hc_saved_faqs, true ) : array();
				$hc_faqs_arr   = is_array( $hc_faqs_arr ) ? $hc_faqs_arr : array();
				foreach ( $hc_faqs_arr as $hc_faq ) :
					$hc_fq = isset( $hc_faq['q'] ) && is_string( $hc_faq['q'] ) ? $hc_faq['q'] : '';
					$hc_fa = isset( $hc_faq['a'] ) && is_string( $hc_faq['a'] ) ? $hc_faq['a'] : '';
				?>
				<div class="hc-faq-row">
					<div class="hc-faq-row__fields">
						<div class="hc-cel-tpl__field">
							<label class="hc-cel-tpl__label"><?php esc_html_e( 'Question', 'height-compare' ); ?></label>
							<input class="hc-cel-tpl__input" type="text" name="hc_faq_q[]"
								value="<?php echo esc_attr( $hc_fq ); ?>"
								placeholder="<?php esc_attr_e( 'e.g. How tall is …?', 'height-compare' ); ?>">
						</div>
						<div class="hc-cel-tpl__field">
							<label class="hc-cel-tpl__label"><?php esc_html_e( 'Answer', 'height-compare' ); ?></label>
							<textarea class="hc-cel-tpl__textarea" name="hc_faq_a[]" rows="2"
								placeholder="<?php esc_attr_e( 'e.g. They are …', 'height-compare' ); ?>"><?php echo esc_textarea( $hc_fa ); ?></textarea>
						</div>
					</div>
					<button type="button" class="hc-faq-remove" aria-label="<?php esc_attr_e( 'Remove FAQ', 'height-compare' ); ?>">✕</button>
				</div>
				<?php endforeach; ?>
			</div>

			<button type="button" class="button hc-faq-add" id="hc-faq-add">
				<?php esc_html_e( '+ Add FAQ', 'height-compare' ); ?>
			</button>

			<!-- Custom content sections repeater -->
			<hr style="margin:24px 0">
			<div style="margin-bottom:8px">
				<strong><?php esc_html_e( 'Custom Content Sections', 'height-compare' ); ?></strong>
				<span class="hc-cel-tpl__hint" style="margin-left:6px">
					<?php esc_html_e( 'Each section renders as a full-width content block below the biography section on the front end.', 'height-compare' ); ?>
				</span>
			</div>
			<?php
			add_filter( 'tiny_mce_before_init', static function ( $init ) {
				if ( isset( $init['plugins'] ) && false === strpos( $init['plugins'], 'table' ) ) {
					$init['plugins'] .= ',table';
				}
				return $init;
			} );
			?>
			<div id="hc-sections-list">
				<?php foreach ( $page_sections as $hc_sec_idx => $hc_sec ) :
					$hc_sec_title   = isset( $hc_sec['title'] )   && is_string( $hc_sec['title'] )   ? $hc_sec['title']   : '';
					$hc_sec_content = isset( $hc_sec['content'] ) && is_string( $hc_sec['content'] ) ? $hc_sec['content'] : '';
					$hc_sec_editor_id = 'hcsec_' . $hc_sec_idx;
				?>
				<div class="hc-section-block">
					<input type="text" name="hc_section_title[]"
						class="hc-cel-tpl__input hc-section-block__title"
						value="<?php echo esc_attr( $hc_sec_title ); ?>"
						placeholder="<?php esc_attr_e( 'Section heading', 'height-compare' ); ?>">
					<?php wp_editor( $hc_sec_content, $hc_sec_editor_id, array(
						'textarea_name' => 'hc_section_content[]',
						'media_buttons' => false,
						'textarea_rows' => 8,
						'tinymce'       => array(
							'toolbar1' => 'bold italic | blockquote | bullist numlist | link unlink | table | undo redo',
							'toolbar2' => '',
						),
					) ); ?>
					<button type="button" class="hc-section-remove button" style="margin-top:8px">
						<?php esc_html_e( '✕ Remove section', 'height-compare' ); ?>
					</button>
				</div>
				<?php endforeach; ?>
			</div>
			<button type="button" id="hc-section-add" class="button button-primary" style="margin-top:10px">
				<?php esc_html_e( '+ Add Section', 'height-compare' ); ?>
			</button>

		</div>


	</div><!-- /hc-cel-tpl -->
	<?php
}
add_action( 'edit_form_after_title', 'hc_celebrity_edit_template' );

/**
 * Whether the current admin screen is editing the height-converter page.
 *
 * @param WP_Screen $screen Current screen.
 */
function hc_is_converter_page( WP_Screen $screen ): bool {
	if ( 'page' !== $screen->post_type ) {
		return false;
	}
	$post_id = (int) ( $_GET['post'] ?? 0 );
	if ( $post_id <= 0 ) {
		return false;
	}
	return 'height-converter' === (string) get_post_field( 'post_name', $post_id );
}

/* ── Classic meta boxes for height_reference & country_average ─────────── */

function hc_add_meta_boxes(): void {
	$meta_box_types = array_filter(
		array_keys( hc_meta_fields() ),
		static fn( string $t ) => 'celebrity' !== $t
	);
	foreach ( $meta_box_types as $post_type ) {
		add_meta_box(
			'hc-fields',
			'Height Compare Data',
			'hc_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}

	// FAQ meta box on the height-converter page edit screen.
	add_meta_box(
		'hc-page-faqs',
		__( 'Page FAQs', 'height-compare' ),
		'hc_render_page_faq_box',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'hc_add_meta_boxes' );

/**
 * Render the FAQ repeater meta box for the height-converter page.
 * The box is registered for all pages but returns early unless this is the
 * height-converter page, keeping the admin tidy everywhere else.
 *
 * @param WP_Post $post Post being edited.
 */
function hc_render_page_faq_box( WP_Post $post ): void {
	// Only show on the height-converter page.
	if ( 'page' !== $post->post_type ) {
		return;
	}
	$converter = get_page_by_path( 'height-converter' );
	if ( ! $converter instanceof WP_Post || (int) $post->ID !== (int) $converter->ID ) {
		return;
	}

	wp_nonce_field( 'hc_page_faqs_save', 'hc_page_faqs_nonce' );

	$raw  = (string) get_post_meta( $post->ID, 'hc_page_faqs', true );
	$faqs = ( '' !== $raw ) ? json_decode( $raw, true ) : array();
	$faqs = is_array( $faqs ) ? $faqs : array();
	?>
	<p style="color:#646970;margin-bottom:12px">
		<?php esc_html_e( 'These FAQs appear above the built-in converter FAQs on the front end.', 'height-compare' ); ?>
	</p>
	<div class="hc-faq-list" id="hc-page-faq-list">
		<?php foreach ( $faqs as $faq ) :
			$fq = isset( $faq['q'] ) && is_string( $faq['q'] ) ? $faq['q'] : '';
			$fa = isset( $faq['a'] ) && is_string( $faq['a'] ) ? $faq['a'] : '';
		?>
		<div class="hc-faq-row">
			<div class="hc-faq-row__fields">
				<div class="hc-cel-tpl__field">
					<label class="hc-cel-tpl__label"><?php esc_html_e( 'Question', 'height-compare' ); ?></label>
					<input class="hc-cel-tpl__input" type="text" name="hc_page_faq_q[]"
						value="<?php echo esc_attr( $fq ); ?>"
						placeholder="<?php esc_attr_e( 'e.g. What is 5ft 11 in cm?', 'height-compare' ); ?>">
				</div>
				<div class="hc-cel-tpl__field">
					<label class="hc-cel-tpl__label"><?php esc_html_e( 'Answer', 'height-compare' ); ?></label>
					<textarea class="hc-cel-tpl__textarea" name="hc_page_faq_a[]" rows="2"
						placeholder="<?php esc_attr_e( 'e.g. 5 ft 11 in = 180.3 cm.', 'height-compare' ); ?>"><?php echo esc_textarea( $fa ); ?></textarea>
				</div>
			</div>
			<button type="button" class="hc-faq-remove" aria-label="<?php esc_attr_e( 'Remove FAQ', 'height-compare' ); ?>">✕</button>
		</div>
		<?php endforeach; ?>
	</div>
	<button type="button" class="button hc-faq-add" id="hc-page-faq-add">
		<?php esc_html_e( '+ Add FAQ', 'height-compare' ); ?>
	</button>
	<?php
}

/**
 * JS for the height-converter page FAQ repeater.
 */
function hc_page_faq_js(): void {
	$q_label      = esc_js( __( 'Question', 'height-compare' ) );
	$a_label      = esc_js( __( 'Answer', 'height-compare' ) );
	$q_ph         = esc_js( __( 'e.g. What is 5ft 11 in cm?', 'height-compare' ) );
	$a_ph         = esc_js( __( 'e.g. 5 ft 11 in = 180.3 cm.', 'height-compare' ) );
	$remove_label = esc_js( __( 'Remove FAQ', 'height-compare' ) );
	?>
	<script>
	(function () {
		var list = document.getElementById('hc-page-faq-list');
		var add  = document.getElementById('hc-page-faq-add');
		if (!list || !add) return;

		function makeRow() {
			var row = document.createElement('div');
			row.className = 'hc-faq-row';
			row.innerHTML =
				'<div class="hc-faq-row__fields">' +
					'<div class="hc-cel-tpl__field">' +
						'<label class="hc-cel-tpl__label"><?php echo $q_label; ?></label>' +
						'<input class="hc-cel-tpl__input" type="text" name="hc_page_faq_q[]"' +
							' placeholder="<?php echo $q_ph; ?>">' +
					'</div>' +
					'<div class="hc-cel-tpl__field">' +
						'<label class="hc-cel-tpl__label"><?php echo $a_label; ?></label>' +
						'<textarea class="hc-cel-tpl__textarea" name="hc_page_faq_a[]" rows="2"' +
							' placeholder="<?php echo $a_ph; ?>"></textarea>' +
					'</div>' +
				'</div>' +
				'<button type="button" class="hc-faq-remove" aria-label="<?php echo $remove_label; ?>">✕</button>';
			row.querySelector('.hc-faq-remove').addEventListener('click', function () { row.remove(); });
			return row;
		}

		list.querySelectorAll('.hc-faq-remove').forEach(function (btn) {
			btn.addEventListener('click', function () { btn.closest('.hc-faq-row').remove(); });
		});

		add.addEventListener('click', function () {
			var row = makeRow();
			list.appendChild(row);
			var inp = row.querySelector('input');
			if (inp) inp.focus();
		});
	})();
	</script>
	<?php
}

/**
 * Save height-converter page FAQs.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function hc_save_page_faqs( int $post_id, WP_Post $post ): void {
	if ( ! isset( $_POST['hc_page_faqs_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['hc_page_faqs_nonce'] ) ), 'hc_page_faqs_save' )
	) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'page' !== $post->post_type ) {
		return;
	}

	$raw_qs = isset( $_POST['hc_page_faq_q'] ) && is_array( $_POST['hc_page_faq_q'] )
		? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_page_faq_q'] ) )
		: array();
	$raw_as = isset( $_POST['hc_page_faq_a'] ) && is_array( $_POST['hc_page_faq_a'] )
		? array_map( 'sanitize_textarea_field', array_map( 'wp_unslash', $_POST['hc_page_faq_a'] ) )
		: array();

	$faqs = array();
	foreach ( $raw_qs as $i => $q ) {
		$q = trim( $q );
		$a = trim( $raw_as[ $i ] ?? '' );
		if ( '' !== $q && '' !== $a ) {
			$faqs[] = array( 'q' => $q, 'a' => $a );
		}
	}

	if ( array() === $faqs ) {
		delete_post_meta( $post_id, 'hc_page_faqs' );
	} else {
		update_post_meta( $post_id, 'hc_page_faqs', wp_json_encode( $faqs ) );
	}
}
add_action( 'save_post_page', 'hc_save_page_faqs', 10, 2 );

/**
 * Render classic meta box table for height_reference / country_average.
 *
 * @param WP_Post $post Post being edited.
 */
function hc_render_meta_box( WP_Post $post ): void {
	$fields = hc_meta_fields()[ $post->post_type ] ?? array();
	if ( array() === $fields ) {
		return;
	}
	wp_nonce_field( 'hc_meta_save', 'hc_meta_nonce' );

	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields as $key => $def ) {
		$value = get_post_meta( $post->ID, $key, true );
		$value = is_scalar( $value ) ? (string) $value : '';
		echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $def['label'] ) . '</label></th><td>';

		if ( 'hc_height_cm' === $key || 'hc_avg_male_cm' === $key || 'hc_avg_female_cm' === $key ) {
			hc_render_height_input( $key, $value );
		} elseif ( 'hc_gender' === $key ) {
			echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">';
			foreach ( array( 'male', 'female', 'child' ) as $opt ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $opt ),
					selected( $value, $opt, false ),
					esc_html( ucfirst( $opt ) )
				);
			}
			echo '</select>';
		} else {
			$type = ( 'integer' === $def['type'] ) ? 'number' : 'text';
			printf(
				'<input type="%1$s" class="regular-text" id="%2$s" name="%2$s" value="%3$s">',
				esc_attr( $type ),
				esc_attr( $key ),
				esc_attr( $value )
			);
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Dual cm / ft+in input pair (used in classic meta box for non-celebrity CPTs).
 *
 * @param string $key   Meta key.
 * @param string $value Current cm value.
 */
function hc_render_height_input( string $key, string $value ): void {
	$cm  = ( '' !== $value ) ? (float) $value : 0.0;
	$ft  = ( $cm > 0 ) ? (int) floor( $cm / 30.48 ) : 0;
	$in  = ( $cm > 0 ) ? round( fmod( $cm, 30.48 ) / 2.54, 1 ) : 0.0;
	$yd  = ( $cm > 0 ) ? round( $cm / 91.44, 2 ) : 0.0;
	?>
	<span class="hc-height-pair" data-key="<?php echo esc_attr( $key ); ?>">
		<input type="number" step="0.1" min="0" max="30000" id="<?php echo esc_attr( $key ); ?>"
			name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $cm > 0 ? (string) $cm : '' ); ?>"
			class="small-text hc-cm"> cm
		&nbsp;=&nbsp;
		<input type="number" step="1" min="0" class="small-text hc-ft" value="<?php echo esc_attr( $cm > 0 ? (string) $ft : '' ); ?>" aria-label="feet"> ft
		<input type="number" step="0.1" min="0" max="11.9" class="small-text hc-in" value="<?php echo esc_attr( $cm > 0 ? (string) $in : '' ); ?>" aria-label="inches"> in
		&nbsp;&nbsp;<span class="hc-yd" style="font-weight:500"><?php echo $cm > 0 ? esc_html( (string) $yd ) : ''; ?></span> yd
	</span>
	<?php
}

/**
 * Inline JS for the celebrity custom template (cm ↔ ft/in sync).
 */
function hc_celebrity_admin_js(): void {
	$add_label    = esc_js( __( '+ Add FAQ', 'height-compare' ) );
	$q_label      = esc_js( __( 'Question', 'height-compare' ) );
	$a_label      = esc_js( __( 'Answer', 'height-compare' ) );
	$q_ph         = esc_js( __( 'e.g. How tall is …?', 'height-compare' ) );
	$a_ph         = esc_js( __( 'e.g. They are …', 'height-compare' ) );
	$remove_label = esc_js( __( 'Remove FAQ', 'height-compare' ) );
	?>
	<script>
	/* ── cm ↔ ft/in sync ─────────────────────────────────────────────── */
	document.querySelectorAll('.hc-height-pair').forEach(function(pair) {
		var cm   = pair.querySelector('.hc-cm');
		var ft   = pair.querySelector('.hc-ft');
		var inch = pair.querySelector('.hc-in');
		var m    = pair.querySelector('.hc-m');
		var yd   = pair.querySelector('.hc-yd');
		function fromCm() {
			var v = parseFloat(cm.value);
			if (!isFinite(v) || v <= 0) {
				ft.value = ''; inch.value = '';
				if (m) m.value = '';
				if (yd) yd.value = '';
				return;
			}
			ft.value   = Math.floor(v / 30.48);
			inch.value = Math.round((v % 30.48) / 2.54 * 10) / 10;
			if (m) m.value   = Math.round(v / 100 * 100) / 100;
			if (yd) yd.value = Math.round(v / 91.44 * 100) / 100;
		}
		function fromFt() {
			var f = parseFloat(ft.value) || 0;
			var i = parseFloat(inch.value) || 0;
			if (f <= 0 && i <= 0) return;
			cm.value = Math.round((f * 30.48 + i * 2.54) * 10) / 10;
		}
		cm.addEventListener('input', fromCm);
		ft.addEventListener('input', fromFt);
		inch.addEventListener('input', fromFt);
	});

	/* ── DOB → age hint ──────────────────────────────────────────────── */
	(function () {
		var dob  = document.getElementById('hc_dob');
		var hint = document.getElementById('hc_dob_age_hint');
		if (!dob || !hint) return;
		function refresh() {
			var v = dob.value;
			if (!v) { hint.textContent = ''; return; }
			var b = new Date(v), t = new Date();
			var age = t.getFullYear() - b.getFullYear();
			var m = t.getMonth() - b.getMonth();
			if (m < 0 || (m === 0 && t.getDate() < b.getDate())) age--;
			hint.textContent = age >= 0 ? 'Age: ' + age : '';
		}
		dob.addEventListener('change', refresh);
		refresh();
	})();

	/* ── FAQ repeater ─────────────────────────────────────────────────── */
	var faqList = document.getElementById('hc-faq-list');
	var faqAdd  = document.getElementById('hc-faq-add');

	function makeFaqRow(q, a) {
		var row = document.createElement('div');
		row.className = 'hc-faq-row';
		row.innerHTML =
			'<div class="hc-faq-row__fields">' +
				'<div class="hc-cel-tpl__field">' +
					'<label class="hc-cel-tpl__label"><?php echo $q_label; ?></label>' +
					'<input class="hc-cel-tpl__input" type="text" name="hc_faq_q[]"' +
						' value="' + (q || '').replace(/"/g, '&quot;') + '"' +
						' placeholder="<?php echo $q_ph; ?>">' +
				'</div>' +
				'<div class="hc-cel-tpl__field">' +
					'<label class="hc-cel-tpl__label"><?php echo $a_label; ?></label>' +
					'<textarea class="hc-cel-tpl__textarea" name="hc_faq_a[]" rows="2"' +
						' placeholder="<?php echo $a_ph; ?>">' + (a || '') + '</textarea>' +
				'</div>' +
			'</div>' +
			'<button type="button" class="hc-faq-remove" aria-label="<?php echo $remove_label; ?>">✕</button>';
		row.querySelector('.hc-faq-remove').addEventListener('click', function() {
			row.remove();
		});
		return row;
	}

	if (faqList) {
		/* Wire remove buttons on existing (PHP-rendered) rows */
		faqList.querySelectorAll('.hc-faq-remove').forEach(function(btn) {
			btn.addEventListener('click', function() { btn.closest('.hc-faq-row').remove(); });
		});
	}

	if (faqAdd && faqList) {
		faqAdd.addEventListener('click', function() {
			faqList.appendChild(makeFaqRow('', ''));
			var newInput = faqList.lastElementChild.querySelector('input');
			if (newInput) newInput.focus();
		});
	}

	/* ── Bio table row repeater ──────────────────────────────────────── */
	(function () {
		var bioList = document.getElementById('hc-bio-rows-list');
		var bioAdd  = document.getElementById('hc-bio-row-add');
		if (!bioList || !bioAdd) return;

		function makeBioRow(label, value) {
			var row = document.createElement('div');
			row.className = 'hc-repeater-row';
			row.innerHTML =
				'<input type="text" name="hc_bio_row_label[]" class="hc-repeater-row__label hc-cel-tpl__input"' +
					' value="' + (label || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Label', 'height-compare' ) ); ?>">' +
				'<input type="text" name="hc_bio_row_value[]" class="hc-repeater-row__value hc-cel-tpl__input"' +
					' value="' + (value || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Value', 'height-compare' ) ); ?>">' +
				'<button type="button" class="hc-repeater-remove button">✕</button>';
			row.querySelector('.hc-repeater-remove').addEventListener('click', function () { row.remove(); });
			return row;
		}

		bioList.querySelectorAll('.hc-repeater-remove').forEach(function (btn) {
			btn.addEventListener('click', function () { btn.closest('.hc-repeater-row').remove(); });
		});

		bioAdd.addEventListener('click', function () {
			var row = makeBioRow('', '');
			bioList.appendChild(row);
			row.querySelector('input').focus();
		});
	})();

	/* ── Physical attrs row repeater ─────────────────────────────────── */
	(function () {
		var physList = document.getElementById('hc-phys-attrs-list');
		var physAdd  = document.getElementById('hc-phys-attr-add');
		if (!physList || !physAdd) return;

		function makePhysRow(label, value) {
			var row = document.createElement('div');
			row.className = 'hc-repeater-row';
			row.innerHTML =
				'<input type="text" name="hc_phys_attr_label[]" class="hc-repeater-row__label hc-cel-tpl__input"' +
					' value="' + (label || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Label', 'height-compare' ) ); ?>">' +
				'<input type="text" name="hc_phys_attr_value[]" class="hc-repeater-row__value hc-cel-tpl__input"' +
					' value="' + (value || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Value', 'height-compare' ) ); ?>">' +
				'<button type="button" class="hc-repeater-remove button">✕</button>';
			row.querySelector('.hc-repeater-remove').addEventListener('click', function () { row.remove(); });
			return row;
		}

		physList.querySelectorAll('.hc-repeater-remove').forEach(function (btn) {
			btn.addEventListener('click', function () { btn.closest('.hc-repeater-row').remove(); });
		});

		physAdd.addEventListener('click', function () {
			var row = makePhysRow('', '');
			physList.appendChild(row);
			row.querySelector('input').focus();
		});
	})();

	/* ── Family & Background row repeater ────────────────────────────── */
	(function () {
		var fbList = document.getElementById('hc-family-bg-list');
		var fbAdd  = document.getElementById('hc-family-bg-add');
		if (!fbList || !fbAdd) return;

		function makeFbRow(label, value) {
			var row = document.createElement('div');
			row.className = 'hc-repeater-row';
			row.innerHTML =
				'<input type="text" name="hc_family_bg_label[]" class="hc-repeater-row__label hc-cel-tpl__input"' +
					' value="' + (label || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Label', 'height-compare' ) ); ?>">' +
				'<input type="text" name="hc_family_bg_value[]" class="hc-repeater-row__value hc-cel-tpl__input"' +
					' value="' + (value || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Value', 'height-compare' ) ); ?>">' +
				'<button type="button" class="hc-repeater-remove button">✕</button>';
			row.querySelector('.hc-repeater-remove').addEventListener('click', function () { row.remove(); });
			return row;
		}

		fbList.querySelectorAll('.hc-repeater-remove').forEach(function (btn) {
			btn.addEventListener('click', function () { btn.closest('.hc-repeater-row').remove(); });
		});

		fbAdd.addEventListener('click', function () {
			var row = makeFbRow('', '');
			fbList.appendChild(row);
			row.querySelector('input').focus();
		});
	})();

	/* ── Career & Financials row repeater ────────────────────────────── */
	(function () {
		var crList = document.getElementById('hc-career-list');
		var crAdd  = document.getElementById('hc-career-add');
		if (!crList || !crAdd) return;

		function makeCrRow(label, value) {
			var row = document.createElement('div');
			row.className = 'hc-repeater-row';
			row.innerHTML =
				'<input type="text" name="hc_career_label[]" class="hc-repeater-row__label hc-cel-tpl__input"' +
					' value="' + (label || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Label', 'height-compare' ) ); ?>">' +
				'<input type="text" name="hc_career_value[]" class="hc-repeater-row__value hc-cel-tpl__input"' +
					' value="' + (value || '').replace(/"/g, '&quot;') + '"' +
					' placeholder="<?php echo esc_js( __( 'Value', 'height-compare' ) ); ?>">' +
				'<button type="button" class="hc-repeater-remove button">✕</button>';
			row.querySelector('.hc-repeater-remove').addEventListener('click', function () { row.remove(); });
			return row;
		}

		crList.querySelectorAll('.hc-repeater-remove').forEach(function (btn) {
			btn.addEventListener('click', function () { btn.closest('.hc-repeater-row').remove(); });
		});

		crAdd.addEventListener('click', function () {
			var row = makeCrRow('', '');
			crList.appendChild(row);
			row.querySelector('input').focus();
		});
	})();

	/* ── Custom section repeater (with dynamic TinyMCE) ─────────────── */
	(function () {
		var secList = document.getElementById('hc-sections-list');
		var secAdd  = document.getElementById('hc-section-add');
		if (!secList || !secAdd) return;

		var sectionCount = secList.querySelectorAll('.hc-section-block').length;

		function initSecEditor(edId) {
			if (!window.tinymce || !window.tinyMCEPreInit) return;
			var baseKey = Object.keys(tinyMCEPreInit.mceInit || {})[0];
			if (!baseKey) return;
			var cfg = Object.assign({}, tinyMCEPreInit.mceInit[baseKey]);
			cfg.selector  = '#' + edId;
			cfg.toolbar1  = 'bold italic | blockquote | bullist numlist | link unlink | table | undo redo';
			cfg.toolbar2  = '';
			cfg.toolbar3  = '';
			cfg.toolbar4  = '';
			if (cfg.plugins && cfg.plugins.indexOf('table') === -1) {
				cfg.plugins += ',table';
			}
			cfg.body_class = edId + ' post-type-celebrity';
			delete cfg.wpautoresize; // allow manual resize
			tinyMCEPreInit.mceInit[edId] = cfg;
			tinymce.init(cfg);
			if (window.quicktags) { quicktags({ id: edId }); }
		}

		function wireRemove(block) {
			var btn = block.querySelector('.hc-section-remove');
			if (!btn) return;
			btn.addEventListener('click', function () {
				var ta = block.querySelector('textarea');
				if (ta && ta.id) {
					if (window.tinymce) { tinymce.remove('#' + ta.id); }
					delete (tinyMCEPreInit.mceInit || {})[ta.id];
				}
				block.remove();
			});
		}

		// Wire remove on existing PHP-rendered blocks
		secList.querySelectorAll('.hc-section-block').forEach(wireRemove);

		secAdd.addEventListener('click', function () {
			var edId = 'hcsec_' + sectionCount++;
			var block = document.createElement('div');
			block.className = 'hc-section-block';
			block.innerHTML =
				'<input type="text" name="hc_section_title[]" class="hc-cel-tpl__input hc-section-block__title"' +
					' placeholder="<?php echo esc_js( __( 'Section heading', 'height-compare' ) ); ?>">' +
				'<textarea id="' + edId + '" name="hc_section_content[]" rows="8" class="wp-editor-area"></textarea>' +
				'<button type="button" class="hc-section-remove button" style="margin-top:8px">' +
					'<?php echo esc_js( __( '✕ Remove section', 'height-compare' ) ); ?>' +
				'</button>';
			secList.appendChild(block);
			// Defer so the textarea is in the DOM before TinyMCE queries it
			setTimeout(function () { initSecEditor(edId); }, 50);
			wireRemove(block);
		});
	})();
	</script>
	<?php
}

/**
 * Inline JS for classic height-pair meta boxes (height_reference / country_average).
 */
function hc_height_pair_js(): void {
	hc_celebrity_admin_js();
}

/**
 * Save all CPT meta on post save.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function hc_save_meta( int $post_id, WP_Post $post ): void {
	if ( ! isset( $_POST['hc_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['hc_meta_nonce'] ) ), 'hc_meta_save' )
	) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = hc_meta_fields()[ $post->post_type ] ?? array();
	foreach ( $fields as $key => $def ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw   = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		$clean = call_user_func( $def['sanitize'], $raw );
		if ( '' === $clean || 0 === $clean || 0.0 === $clean ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $clean );
		}
	}

	// Save celebrity-specific fields.
	if ( 'celebrity' === $post->post_type ) {

		// FAQ heading.
		$faq_heading = isset( $_POST['hc_faq_heading'] )
			? sanitize_text_field( wp_unslash( $_POST['hc_faq_heading'] ) )
			: '';
		if ( '' === trim( $faq_heading ) ) {
			delete_post_meta( $post_id, 'hc_faq_heading' );
		} else {
			update_post_meta( $post_id, 'hc_faq_heading', $faq_heading );
		}

		// Custom lede text.
		$lede = isset( $_POST['hc_lede_text'] )
			? sanitize_textarea_field( wp_unslash( $_POST['hc_lede_text'] ) )
			: '';
		if ( '' === trim( $lede ) ) {
			delete_post_meta( $post_id, 'hc_lede_text' );
		} else {
			update_post_meta( $post_id, 'hc_lede_text', $lede );
		}

		// Per-celebrity section visibility overrides ('1', '0', or '' = global default).
		foreach ( array( 'hc_tpl_show_stats', 'hc_tpl_show_cta', 'hc_tpl_show_related', 'hc_tpl_show_faq' ) as $tpl_key ) {
			$val = isset( $_POST[ $tpl_key ] ) ? sanitize_key( wp_unslash( $_POST[ $tpl_key ] ) ) : '';
			if ( '' === $val || ( '1' !== $val && '0' !== $val ) ) {
				delete_post_meta( $post_id, $tpl_key );
			} else {
				update_post_meta( $post_id, $tpl_key, $val );
			}
		}
	}

	// DOB + auto age-group assignment.
	if ( 'celebrity' === $post->post_type ) {
		$dob = isset( $_POST['hc_dob'] )
			? sanitize_text_field( wp_unslash( $_POST['hc_dob'] ) )
			: '';
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $dob ) ) {
			update_post_meta( $post_id, 'hc_dob', $dob );
			hc_assign_age_group( $post_id );
		} else {
			delete_post_meta( $post_id, 'hc_dob' );
		}
		// Height group is assigned from the already-saved hc_height_cm.
		hc_assign_height_group( $post_id );

		// String meta fields — personal, family, career.
		foreach ( array(
			'hc_birthplace', 'hc_eye_color', 'hc_hair_color', 'hc_body_color', 'hc_body_type',
			'hc_birth_name', 'hc_full_name', 'hc_nickname', 'hc_profession',
			'hc_school', 'hc_college', 'hc_father_name', 'hc_mother_name',
			'hc_siblings', 'hc_marital_status', 'hc_girlfriend_name', 'hc_wife_name',
			'hc_friends_names', 'hc_religion', 'hc_hometown', 'hc_current_address',
			'hc_children', 'hc_hobbies', 'hc_awards', 'hc_net_worth', 'hc_monthly_earning',
		) as $str_key ) {
			$val = isset( $_POST[ $str_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $str_key ] ) ) : '';
			if ( '' === $val ) {
				delete_post_meta( $post_id, $str_key );
			} else {
				update_post_meta( $post_id, $str_key, $val );
			}
		}

		// Hero bio, biography paragraph, and physical attrs paragraph — stored as HTML (TinyMCE).
		foreach ( array( 'hc_hero_bio', 'hc_biography_para', 'hc_physical_attributes_para' ) as $html_key ) {
			$html_val = isset( $_POST[ $html_key ] ) ? wp_kses_post( wp_unslash( $_POST[ $html_key ] ) ) : '';
			if ( '' === trim( $html_val ) ) {
				delete_post_meta( $post_id, $html_key );
			} else {
				update_post_meta( $post_id, $html_key, $html_val );
			}
		}

		// Bio table rows (JSON).
		$bio_labels = isset( $_POST['hc_bio_row_label'] ) && is_array( $_POST['hc_bio_row_label'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_bio_row_label'] ) )
			: array();
		$bio_values = isset( $_POST['hc_bio_row_value'] ) && is_array( $_POST['hc_bio_row_value'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_bio_row_value'] ) )
			: array();
		$hc_bio_rows = array();
		foreach ( $bio_labels as $i => $lbl ) {
			$lbl = trim( $lbl );
			$val = trim( $bio_values[ $i ] ?? '' );
			if ( '' !== $lbl && '' !== $val ) {
				$hc_bio_rows[] = array( $lbl, $val );
			}
		}
		if ( ! empty( $hc_bio_rows ) ) {
			update_post_meta( $post_id, 'hc_bio_table_rows', wp_json_encode( $hc_bio_rows ) );
		} else {
			delete_post_meta( $post_id, 'hc_bio_table_rows' );
		}

		// Physical attributes rows (JSON).
		$phys_labels = isset( $_POST['hc_phys_attr_label'] ) && is_array( $_POST['hc_phys_attr_label'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_phys_attr_label'] ) )
			: array();
		$phys_values = isset( $_POST['hc_phys_attr_value'] ) && is_array( $_POST['hc_phys_attr_value'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_phys_attr_value'] ) )
			: array();
		$hc_phys_rows = array();
		foreach ( $phys_labels as $i => $plbl ) {
			$plbl = trim( $plbl );
			$pval = trim( $phys_values[ $i ] ?? '' );
			if ( '' !== $plbl && '' !== $pval ) {
				$hc_phys_rows[] = array( $plbl, $pval );
				$plbl_lower = strtolower( $plbl );
				if ( str_contains( $plbl_lower, 'gender' ) ) {
					$gv = strtolower( $pval );
					if ( in_array( $gv, array( 'male', 'female', 'child' ), true ) ) {
						update_post_meta( $post_id, 'hc_gender', $gv );
					}
				} elseif ( str_contains( $plbl_lower, 'weight' ) ) {
					$wv = absint( preg_replace( '/[^0-9]/', '', $pval ) );
					if ( $wv > 0 ) { update_post_meta( $post_id, 'hc_weight_kg', $wv ); }
				} elseif ( str_contains( $plbl_lower, 'eye' ) ) {
					update_post_meta( $post_id, 'hc_eye_color', $pval );
				} elseif ( str_contains( $plbl_lower, 'hair' ) ) {
					update_post_meta( $post_id, 'hc_hair_color', $pval );
				} elseif ( str_contains( $plbl_lower, 'body color' ) || str_contains( $plbl_lower, 'skin' ) ) {
					update_post_meta( $post_id, 'hc_body_color', $pval );
				} elseif ( str_contains( $plbl_lower, 'body type' ) ) {
					update_post_meta( $post_id, 'hc_body_type', $pval );
				}
			}
		}
		if ( ! empty( $hc_phys_rows ) ) {
			update_post_meta( $post_id, 'hc_phys_attrs_rows', wp_json_encode( $hc_phys_rows ) );
		} else {
			delete_post_meta( $post_id, 'hc_phys_attrs_rows' );
		}

		// Family & Background rows (JSON).
		$fb_labels = isset( $_POST['hc_family_bg_label'] ) && is_array( $_POST['hc_family_bg_label'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_family_bg_label'] ) )
			: array();
		$fb_values = isset( $_POST['hc_family_bg_value'] ) && is_array( $_POST['hc_family_bg_value'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_family_bg_value'] ) )
			: array();
		$hc_fb_rows = array();
		foreach ( $fb_labels as $i => $flbl ) {
			$flbl = trim( $flbl );
			$fval = trim( $fb_values[ $i ] ?? '' );
			if ( '' !== $flbl && '' !== $fval ) {
				$hc_fb_rows[] = array( $flbl, $fval );
			}
		}
		if ( ! empty( $hc_fb_rows ) ) {
			update_post_meta( $post_id, 'hc_family_bg_rows', wp_json_encode( $hc_fb_rows ) );
		} else {
			delete_post_meta( $post_id, 'hc_family_bg_rows' );
		}

		// Career & Financials rows (JSON).
		$cr_labels = isset( $_POST['hc_career_label'] ) && is_array( $_POST['hc_career_label'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_career_label'] ) )
			: array();
		$cr_values = isset( $_POST['hc_career_value'] ) && is_array( $_POST['hc_career_value'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_career_value'] ) )
			: array();
		$hc_cr_rows = array();
		foreach ( $cr_labels as $i => $clbl ) {
			$clbl = trim( $clbl );
			$cval = trim( $cr_values[ $i ] ?? '' );
			if ( '' !== $clbl && '' !== $cval ) {
				$hc_cr_rows[] = array( $clbl, $cval );
			}
		}
		if ( ! empty( $hc_cr_rows ) ) {
			update_post_meta( $post_id, 'hc_career_rows', wp_json_encode( $hc_cr_rows ) );
		} else {
			delete_post_meta( $post_id, 'hc_career_rows' );
		}

		// Custom page sections (JSON).
		$sec_titles   = isset( $_POST['hc_section_title'] ) && is_array( $_POST['hc_section_title'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_section_title'] ) )
			: array();
		$sec_contents = isset( $_POST['hc_section_content'] ) && is_array( $_POST['hc_section_content'] )
			? $_POST['hc_section_content']
			: array();
		$hc_page_secs = array();
		foreach ( $sec_titles as $i => $sec_title ) {
			$sec_content = wp_kses_post( wp_unslash( $sec_contents[ $i ] ?? '' ) );
			if ( '' !== trim( $sec_title ) || '' !== trim( $sec_content ) ) {
				$hc_page_secs[] = array( 'title' => $sec_title, 'content' => $sec_content );
			}
		}
		if ( ! empty( $hc_page_secs ) ) {
			update_post_meta( $post_id, 'hc_page_sections', wp_json_encode( $hc_page_secs ) );
		} else {
			delete_post_meta( $post_id, 'hc_page_sections' );
		}
		// Weight.
		$wt = isset( $_POST['hc_weight_kg'] ) ? absint( $_POST['hc_weight_kg'] ) : 0;
		if ( $wt > 0 ) {
			update_post_meta( $post_id, 'hc_weight_kg', $wt );
		} else {
			delete_post_meta( $post_id, 'hc_weight_kg' );
		}

		// Body measurements (numeric cm fields).
		$bm_keys = array( 'hc_wingspan_cm', 'hc_leg_length_cm', 'hc_torso_length_cm', 'hc_shoulder_width_cm', 'hc_hip_width_cm', 'hc_hand_size_cm', 'hc_foot_size_cm' );
		foreach ( $bm_keys as $bm_key ) {
			$bm_val = isset( $_POST[ $bm_key ] ) ? (float) $_POST[ $bm_key ] : 0.0;
			if ( $bm_val > 0 ) {
				update_post_meta( $post_id, $bm_key, round( $bm_val, 1 ) );
			} else {
				delete_post_meta( $post_id, $bm_key );
			}
		}
		// Body source.
		foreach ( array( 'hc_body_source_label' ) as $str_key ) {
			$val = isset( $_POST[ $str_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $str_key ] ) ) : '';
			$val === '' ? delete_post_meta( $post_id, $str_key ) : update_post_meta( $post_id, $str_key, $val );
		}
		$bm_src_url = isset( $_POST['hc_body_source_url'] ) ? esc_url_raw( wp_unslash( $_POST['hc_body_source_url'] ) ) : '';
		$bm_src_url === '' ? delete_post_meta( $post_id, 'hc_body_source_url' ) : update_post_meta( $post_id, 'hc_body_source_url', $bm_src_url );
	}

	// Save custom FAQs for celebrity posts.
	if ( 'celebrity' === $post->post_type ) {
		$raw_qs = isset( $_POST['hc_faq_q'] ) && is_array( $_POST['hc_faq_q'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['hc_faq_q'] ) )
			: array();
		$raw_as = isset( $_POST['hc_faq_a'] ) && is_array( $_POST['hc_faq_a'] )
			? array_map( 'sanitize_textarea_field', array_map( 'wp_unslash', $_POST['hc_faq_a'] ) )
			: array();
		$hc_custom_faqs = array();
		foreach ( $raw_qs as $i => $q ) {
			$q = trim( $q );
			$a = trim( $raw_as[ $i ] ?? '' );
			if ( '' !== $q && '' !== $a ) {
				$hc_custom_faqs[] = array( 'q' => $q, 'a' => $a );
			}
		}
		if ( array() === $hc_custom_faqs ) {
			delete_post_meta( $post_id, 'hc_faqs' );
		} else {
			update_post_meta( $post_id, 'hc_faqs', wp_json_encode( $hc_custom_faqs ) );
		}
	}

	hc_flush_preset_cache();
}
add_action( 'save_post', 'hc_save_meta', 10, 2 );

/* ── Global celebrity template defaults ────────────────────────────────── */

/**
 * Returns the global celebrity page template defaults from wp_options.
 *
 * @return array{show_stats:bool,show_cta:bool,show_related:bool,show_faq:bool,lede_template:string}
 */
function hc_get_template_defaults(): array {
	$saved = get_option( 'hc_celeb_template', array() );
	$saved = is_array( $saved ) ? $saved : array();
	return array(
		'show_stats'   => isset( $saved['show_stats'] ) ? (bool) $saved['show_stats'] : true,
		'show_cta'     => isset( $saved['show_cta'] ) ? (bool) $saved['show_cta'] : true,
		'show_related' => isset( $saved['show_related'] ) ? (bool) $saved['show_related'] : true,
		'show_faq'     => isset( $saved['show_faq'] ) ? (bool) $saved['show_faq'] : true,
		'lede_template' => isset( $saved['lede_template'] ) ? (string) $saved['lede_template'] : '',
	);
}

/**
 * Resolve whether a section is visible for a specific celebrity post.
 * Per-celebrity meta ('1'/'0') overrides the global default.
 *
 * @param int    $post_id   Celebrity post ID.
 * @param string $section   Key: 'stats', 'cta', 'related', 'faq'.
 * @param array  $defaults  From hc_get_template_defaults().
 */
function hc_tpl_section_visible( int $post_id, string $section, array $defaults ): bool {
	$meta_key = 'hc_tpl_show_' . $section;
	$override = (string) get_post_meta( $post_id, $meta_key, true );
	if ( '1' === $override ) {
		return true;
	}
	if ( '0' === $override ) {
		return false;
	}
	return ! empty( $defaults[ 'show_' . $section ] );
}


/* ── Admin list columns ────────────────────────────────────────────────── */

/**
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function hc_celebrity_columns( array $columns ): array {
	$columns['hc_height']  = 'Height';
	$columns['hc_country'] = 'Country';
	$columns['hc_volume']  = 'Search vol.';
	return $columns;
}
add_filter( 'manage_celebrity_posts_columns', 'hc_celebrity_columns' );

/**
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function hc_celebrity_column_value( string $column, int $post_id ): void {
	if ( 'hc_height' === $column ) {
		$cm = (float) get_post_meta( $post_id, 'hc_height_cm', true );
		echo ( $cm > 0 ) ? esc_html( hc_format_height( $cm ) ) : '—';
	} elseif ( 'hc_country' === $column ) {
		$c = (string) get_post_meta( $post_id, 'hc_country', true );
		echo ( '' !== $c ) ? esc_html( $c ) : '—';
	} elseif ( 'hc_volume' === $column ) {
		echo esc_html( (string) absint( get_post_meta( $post_id, 'hc_search_volume', true ) ) );
	}
}
add_action( 'manage_celebrity_posts_custom_column', 'hc_celebrity_column_value', 10, 2 );
