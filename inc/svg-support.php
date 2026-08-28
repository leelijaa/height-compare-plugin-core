<?php
/**
 * SVG upload support — enables SVG in the WP media library and sanitizes
 * uploaded files to strip XSS vectors.
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow SVG MIME type in the media uploader.
 *
 * @param array<string,string> $mimes Allowed MIME types.
 * @return array<string,string>
 */
function hc_allow_svg_upload( array $mimes ): array {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'hc_allow_svg_upload' );

/**
 * Sanitize SVG immediately after upload.
 *
 * @param array{file: string, url: string, type: string} $upload Upload data.
 * @return array{file: string, url: string, type: string}
 */
function hc_sanitize_svg_on_upload( array $upload ): array {
	if ( 'image/svg+xml' === $upload['type'] ) {
		hc_sanitize_svg_file( $upload['file'] );
	}
	return $upload;
}
add_filter( 'wp_handle_upload', 'hc_sanitize_svg_on_upload' );

/**
 * Read, sanitize, and re-write an SVG file on disk.
 *
 * @param string $path Absolute filesystem path to the SVG.
 */
function hc_sanitize_svg_file( string $path ): void {
	$content = file_get_contents( $path );
	if ( false === $content || '' === $content ) {
		return;
	}
	$clean = hc_sanitize_svg_string( $content );
	file_put_contents( $path, $clean );
}

/**
 * Remove known XSS vectors from SVG markup.
 *
 * Strips <script>, <foreignObject>, PHP tags, and on* event attributes.
 * The SVG coordinate / styling structure is intentionally preserved so that
 * Illustrator exports (with embedded <style> blocks and <defs>) remain valid.
 *
 * @param string $svg Raw SVG string.
 * @return string Sanitized SVG string.
 */
function hc_sanitize_svg_string( string $svg ): string {
	// Remove PHP processing instructions.
	$svg = preg_replace( '/<\?php.*?\?>/si', '', $svg ) ?? $svg;

	// Remove <script> blocks (including CDATA-wrapped).
	$svg = preg_replace( '/<script[\s\S]*?<\/script>/si', '', $svg ) ?? $svg;

	// Remove <foreignObject> (can embed arbitrary HTML).
	$svg = preg_replace( '/<foreignObject[\s\S]*?<\/foreignObject>/si', '', $svg ) ?? $svg;

	// Remove on* event attributes (double- and single-quoted).
	$svg = preg_replace( '/\s+on\w+="[^"]*"/i', '', $svg ) ?? $svg;
	$svg = preg_replace( "/\\s+on\\w+='[^']*'/i", '', $svg ) ?? $svg;

	// Remove javascript: href / xlink:href values.
	$svg = preg_replace( '/\s+(?:xlink:)?href\s*=\s*["\']javascript:[^"\']*["\']/i', '', $svg ) ?? $svg;

	// Remove data: URIs inside href/src (potential XSS vector for non-images).
	$svg = preg_replace( '/\s+(?:xlink:)?href\s*=\s*["\']data:(?!image)[^"\']*["\']/i', '', $svg ) ?? $svg;

	return $svg;
}

/**
 * Fix WP's SVG thumbnail — WP can't generate a raster thumb for SVGs,
 * so return the raw file URL when WP would otherwise return false.
 *
 * The filter value is array{ url, width, height, is_intermediate }|false.
 *
 * @param array<int, mixed>|false $image         Existing src data or false.
 * @param int                     $attachment_id Attachment post ID.
 * @param string|int[]            $size          Requested size (unused).
 * @param bool                    $icon          Whether to use an icon (unused).
 * @return array<int, mixed>|false
 */
function hc_svg_thumbnail( $image, int $attachment_id, $size, bool $icon ) {
	unset( $size, $icon ); // unused — required by filter signature

	if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
		return $image;
	}
	if ( false !== $image ) {
		return $image; // WP already has something — don't override
	}
	$url = wp_get_attachment_url( $attachment_id );
	if ( ! $url ) {
		return false;
	}
	return array( $url, 0, 0, false );
}
add_filter( 'wp_get_attachment_image_src', 'hc_svg_thumbnail', 10, 4 );
