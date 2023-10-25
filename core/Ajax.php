<?php

namespace qtwp\core;

use qtwp\lib\FUNC;

defined( 'ABSPATH' ) or exit;

abstract class Ajax {
    use FUNC;
    public $actions = [];
    public $action;

    /**
     * Register an action
     *
     * @param [type] $action
     * @return void
     */
    public function register( $action ) {
        add_action( "wp_ajax_nopriv_{$action}", [$this, $action] );
        add_action( "wp_ajax_{$action}", [$this, $action] );

        $this->actions[$action] = wp_create_nonce( $action );
        add_filter( 'qtwp_vars', [$this, 'hook_nonce'] );
    }

    public function hook_nonce( $vars ) {
        $vars['nonces'] = $this->actions;

        return $vars;
    }

    /**
     * Nonce verification
     *
     * @return void
     */
    public static function verify_nonce() {

        if ( !wp_verify_nonce( self::get_var( 'nonce' ), self::get_var( 'action' ) ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid token!', self::textdomain() ),
                ]
            );
            exit;
        }

    }
}
