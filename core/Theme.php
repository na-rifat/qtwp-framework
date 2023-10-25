<?php
namespace qtwp\core;

use qtwp\lib\FUNC;
use Carbon_Fields\Container;

defined( 'ABSPATH' ) or exit;

abstract class Theme extends Carbon
{
    use FUNC;

    public function __construct()
    {

        if (  !  defined( '_S_VERSION' ) )
        {
            // Replace the version number of the theme on each release.
            define( '_S_VERSION', '1.0.0' );
        }

        // theme configurations
        add_action( 'after_setup_theme', [$this, 'setup'] );
        add_action( 'after_setup_theme', [$this, 'content_width'], 0 );
        add_action( 'widgets_init', [$this, 'init_widgets'] );

    }

    /**
     * Register widget area.
     *
     * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
     */
    public function init_widgets()
    {
        register_sidebar(
            [
                'name'          => esc_html__( 'Sidebar', 'qtwp' ),
                'id'            => 'sidebar-1',
                'description'   => esc_html__( 'Add widgets here.', 'qtwp' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>'
            ]
        );
    }

    /**
     * Set the content width in pixels, based on the theme's design and stylesheet.
     *
     * Priority 0 to make it available to lower priority callbacks.
     *
     * @global int $content_width
     */
    public function content_width()
    {
        $GLOBALS['content_width'] = apply_filters( 'qtwp_content_width', 640 );
    }

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    public function setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on qtwp, use a find and replace
         * to change 'qtwp' to the name of your theme in all the template files.
         */
        load_theme_textdomain( 'qtwp', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script'
            ]
        );

        // Set up the WordPress core custom background feature.
        add_theme_support(
            'custom-background',
            apply_filters(
                'qtwp_custom_background_args',
                [
                    'default-color' => 'ffffff',
                    'default-image' => ''
                ]
            )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        add_theme_support(
            'custom-logo',
            [
                'height'      => 250,
                'width'       => 250,
                'flex-width'  => true,
                'flex-height' => true
            ]
        );
    }

    public function add_menu_meta( $fields )
    {
        return Container::make( 'nav_menu_item', __( 'Menu Settings' ) )
            ->add_fields( $fields );
    }

    public function add_navigation_menu_area( $location, $title )
    {
        return register_nav_menu( $location, __( $title, $this->textdomain() ) );
    }

    public function add_theme_option( $page, $tabs )
    {
        $container = Container::make( 'theme_options', __( $page, $this->textdomain() ) )
            ->set_icon( 'dashcions-admin-generic' )
            ->set_page_menu_position( 0 );

        foreach ( $tabs as $title => $fields )
        {
            $container->add_tab(
                __( $title, $this->textdomain() ),
                $fields
            );
        }

    }

    public static function get_settings( $key )
    {
        return carbon_get_theme_option( self::format_name( $key ) );
    }

    public static function set_settings( $id, $key, $value )
    {
        return carbon_set_theme_option( self::format_name( $key ), $value );
    }

}
