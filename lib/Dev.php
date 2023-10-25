<?php

namespace qtwp\lib;

use qtwp\Ajax;

defined( 'ABSPATH' ) or exit;

class Dev
{
    use FUNC;
    const PHP     = 'php';
    const JS      = 'js';
    const SCSS    = 'scss';
    const CSS     = 'css';
    const enabled = true;

    public function __construct()
    {

        if (  !  self::enabled ) {
            return;
        }

        add_action( 'wp_ajax_qtwp_dev_opration', [$this, 'handle_ajax'] );

        add_action( 'admin_menu', [$this, 'register_to_admin'] );
    }

    public function register_to_admin()
    {
        add_menu_page( __( 'QTWP DEV TOOLS 1.0' ), __( 'QTWP DEV TOOLS' ), 'manage_options', 'qtwp-dev-tools', [$this, 'render_admin_page'], 'dashicons-admin-tools', 1 );
    }

    /**
     * Renders management page for admin dashboard
     *
     * @return void
     */
    public function render_admin_page()
    {
        include QTWP_DIR . '/inc/tools/dev.php';
    }

    /**
     * Handles AJAX requests from the frontend
     *
     * @return void
     */
    public function handle_ajax()
    {
        $operation = self::get_var( 'operation' );

        if ( empty( $operation ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'operation not found!' )
                ]
            );exit;
        }

        switch ( $operation ) {
            case 'create_php_views':
                $this->create_php_views();
                break;
            case 'create_js_views':
                $this->create_js_views();
                break;
            case 'create_scss_views':
                $this->create_scss_views();
                break;
            case 'write_import_scss':
                $this->write_import_scss();
                break;
            case 'write_import_js':
                $this->write_import_js();
                break;
            case 'generate_bulk':
                $this->generate_bulk();
                break;
            case 'clean_bulk':
                $this->clean_bulk();
                break;
            case 'clean_php':
                $this->clean_php();
                break;
            case 'clean_scss':
                $this->clean_scss();
                break;
            case 'clean_js':
                $this->clean_js();
                break;
            default:

                wp_send_json_error(
                    [
                        'msg' => 'operation isn\'t permitted!'
                    ]
                );exit;
                break;
        }

        // operation succesfull
        wp_send_json_success(
            [
                'msg'       => __( sprintf( 'operation completed successfully! id: %s', $operation ) ),
                'operation' => $operation
            ]
        );exit;

    }

    public function clean_bulk()
    {
        $this->clean_scss();
        $this->clean_js();
        $this->clean_php();
    }

    public function clean_scss()
    {
        return $this->clean_files( $this->scss_views_path(), 'scss' );
    }

    public function clean_js()
    {
        return $this->clean_files( $this->js_views_path(), 'js' );
    }

    public function clean_php()
    {
        return $this->clean_files( $this->php_views_path() );
    }

    /**
     * Returns the templates registered by Carbon
     *
     * @return array
     */
    public function get_templates()
    {
        return array_diff( apply_filters( 'qtwp_gutenberg_blocks', [] ), ['', '..', '.'] );
    }

    /**
     * Create .php views file in the specified path
     *
     * @return void
     */
    public function create_php_views()
    {
        $this->create_files( $this->php_views_path(), self::PHP );
    }

    /**
     * Create JS views file in the specified path
     *
     * @return void
     */
    public function create_js_views()
    {
        $this->create_files( $this->js_views_path(), self::JS );
    }

    /**
     * Creates SCSS files based on registered templates
     *
     * @return void
     */
    public function create_scss_views()
    {
        $this->create_files( $this->scss_views_path(), self::SCSS, '_' );
    }

    /**
     * Creates _all.scss file with including all scss files import command
     *
     * @return void
     */
    public function write_import_scss()
    {
        $templates = $this->get_templates();
        $buffer    = '';

        foreach ( $templates as $template ) {
            $buffer .= sprintf( "@import './%s';\n", substr( $template, 1 ) );
        }

        $file = fopen( $this->scss_views_path() . '/_all.scss', 'w' );
        fwrite( $file, $buffer );
        fclose( $file );
    }

    /**
     * Creates all.js file with including all js files import command
     *
     * @return void
     */
    public function write_import_js()
    {
        $templates = $this->get_templates();
        $buffer    = '';

        foreach ( $templates as $template ) {
            $buffer .= sprintf( "import './%s';\n", substr( $template, 1 ) );
        }

        $file = fopen( $this->js_views_path() . '/all.js', 'w' );
        fwrite( $file, $buffer );
        fclose( $file );
    }

    /**
     * Generates bulk amount of files for php, scss and js for registered templates
     * Includes registering import commands for registered templates
     *
     * @return void
     */
    public function generate_bulk()
    {
        $this->create_php_views();
        $this->create_js_views();
        $this->create_scss_views();
        $this->write_import_js();
        $this->write_import_scss();
    }

    /**
     * Create file(s) based on provided type
     *
     * @param string $path
     * @param string $type
     * @param string $prefix
     * @return void
     */
    public function create_files( $path, $type, $prefix = '' )
    {
        $views = $this->get_templates();

        foreach ( $views as $view ) {
            $file = $this->concat_file_name( $path, $prefix, $view, $type );

            if (  !  file_exists( $file ) ) {
                $file = fopen( $file, 'w' );
                fclose( $file );
            }

        }

    }

    /**
     * Clean files based on provided path
     * All files of the given path will be removed
     * Requires cleaning importer file, if type is provided
     *
     * @param string $path
     * @param string $type
     * @param string $prefix
     * @return void
     */
    public function clean_files( $path, $type = '' )
    {
        self::removeFilesAtPath( $path );

        if ( empty( $type ) ) {
            return true;
        }

        switch ( $type ) {
            case 'scss':
                $file = fopen( $this->scss_views_path() . '/_all.scss', 'w' );

                fwrite( $file, '' );
                fclose( $file );

                break;
            case 'js':
                $file = fopen( $this->js_views_path() . '/all.js', 'w' );

                fwrite( $file, '' );
                fclose( $file );

                break;
            default:
                return true;
                break;
        }

        return true;

    }

    /**
     * Contacts file name with prefix
     *
     * @param string $path
     * @param string $prefix
     * @param string $template
     * @param string $type
     * @return string
     */
    public function concat_file_name( $path, $prefix, $template, $type )
    {
        return $path . $this->push_template_prefix( $template, $prefix ) . '.' . $type;
    }

    /**
     * Extracts template name separated by slash(/)
     *
     * @param string $template
     * @return array
     */
    public function extract_template_name( $template )
    {
        return explode( '/', $template );
    }

    /**
     * Returns templates base file name
     *
     * @param string $template
     * @return string
     */
    public function get_template_base_name( $template )
    {
        $template = $this->extract_template_name( $template );

        return $template[count( $template ) - 1];
    }

    /**
     * Pushes a prefix before base template file name
     *
     * @param string $template
     * @param string $prefix
     * @return void
     */
    public function push_template_prefix( $template, $prefix )
    {
        $detail   = $this->extract_template_name( $template );
        $template = $this->get_template_base_name( $template );

        $detail[count( $detail ) - 1] = $prefix . $template;

        return implode( '/', $detail );
    }

    /**
     * Returns path of PHP templates files
     *
     * @return string
     */
    public static function php_views_path()
    {
        return get_template_directory() . '/inc/views';
    }

    /**
     * Returns path of js templates
     *
     * @return void
     */
    public static function js_views_path()
    {
        return get_template_directory() . '/src/js/views';
    }

    /**
     * Returns path of scss templates
     *
     * @return void
     */
    public static function scss_views_path()
    {
        return get_template_directory() . '/src/scss/views';
    }

}
