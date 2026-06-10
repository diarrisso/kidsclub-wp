<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Mittwald's nginx proxy caches 404 responses for static files with a long TTL.
// Filter wp_get_attachment_image_src (fires AFTER image_downsize assembles the final sized URL)
// NOT wp_get_attachment_url (which would corrupt image_downsize's str_replace basename logic).
// Version key is tied to theme version so cache busts on each deploy.
add_filter( 'wp_get_attachment_image_src', 'kc_bust_image_src_cache', 999, 4 );
add_filter( 'wp_calculate_image_srcset', 'kc_bust_srcset_cache', 999, 5 );

function kc_bust_image_src_cache( $image, $attachment_id, $size, $icon ) {
    if ( $image && ! empty( $image[0] ) && false !== strpos( $image[0], '/wp-content/uploads/' ) ) {
        $image[0] = add_query_arg( 'v', wp_get_theme()->get( 'Version' ), $image[0] );
    }
    return $image;
}

function kc_bust_srcset_cache( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
    $v = wp_get_theme()->get( 'Version' );
    foreach ( $sources as &$source ) {
        if ( false !== strpos( $source['url'], '/wp-content/uploads/' ) ) {
            $source['url'] = add_query_arg( 'v', $v, $source['url'] );
        }
    }
    return $sources;
}
