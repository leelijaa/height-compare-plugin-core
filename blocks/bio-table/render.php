<?php
/**
 * Server-side render for height-compare/bio-table block.
 *
 * Available vars: $attributes (array), $content (string), $block (WP_Block).
 *
 * @package HeightCompareCore
 */

declare( strict_types=1 );

$hc_groups = $attributes['groups'] ?? array();

// Nothing to show if every row value is empty.
$hc_has_content = false;
foreach ( $hc_groups as $hc_g ) {
	foreach ( $hc_g['rows'] ?? array() as $hc_r ) {
		if ( '' !== trim( (string) ( $hc_r['value'] ?? '' ) ) ) {
			$hc_has_content = true;
			break 2;
		}
	}
}

if ( ! $hc_has_content ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'hc-bio-info-table' ) ); ?>>
<?php foreach ( $hc_groups as $hc_group ) :
	$hc_rows = array_values( array_filter(
		$hc_group['rows'] ?? array(),
		static fn( array $r ): bool => '' !== trim( (string) ( $r['value'] ?? '' ) )
	) );
	if ( empty( $hc_rows ) ) { continue; }
?>
	<div class="hc-bio-info-group">
		<div class="hc-bio-info-group__head"><?php echo esc_html( (string) ( $hc_group['label'] ?? '' ) ); ?></div>
		<?php foreach ( $hc_rows as $hc_row ) : ?>
		<div class="hc-bio-info-row">
			<span class="hc-bio-info-row__label"><?php echo esc_html( (string) ( $hc_row['label'] ?? '' ) ); ?></span>
			<span class="hc-bio-info-row__value"><?php echo esc_html( (string) ( $hc_row['value'] ?? '' ) ); ?></span>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</div>
