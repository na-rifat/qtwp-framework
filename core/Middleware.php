<?php

namespace qtwp\core;

defined( 'ABSPATH' ) or exit;

abstract class Middleware {

    /**
     * Hooks events while site loading
     *
     * @param string $action
     * @return void
     */
    public function register( $action ) {
        add_action( 'template_redirect', [$this, $action] );
    }

    /**
     * Detects if provided slug is a page
     *
     * @param string $slug
     * @return boolean
     */
    public static function is_page( $slug ) {
        // Get the page object by slug
        $page = get_page_by_path( $slug );

        // Check if the current page is the page with the desired slug
        if ( $page && is_page( $page->ID ) ) {
            // The current page matches the desired slug
            // Do something here
            return true;
        }

        return false;
    }

    /**
     * Redirects to passed url
     *
     * @param string $slug
     * @return void
     */
    public static function redirect( $slug ) {
        wp_redirect( site_url( $slug ), 301 );exit;
    }
}
