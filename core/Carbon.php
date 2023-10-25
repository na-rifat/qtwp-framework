<?php
namespace qtwp\core;

defined( 'ABSPATH' ) or exit;

use qtwp\lib\FUNC;
use Carbon_Fields\Block;
use Carbon_Fields\Field;

abstract class Carbon
{
    use FUNC;

    public $blocks = [];

    public function __construct()
    {
        add_action( 'carbon_fields_register_fields', [$this, 'register'] );
    }

    /**
     * Creats a repeater button field
     *
     * @param  string  $name
     * @return mixed
     */
    public function buttons( $name = 'buttons' )
    {
        return $this->repeater(
            $name,
            [
                $this->image( 'img_icon' ),
                $this->text( 'icon' ),
                $this->text( 'title' ),
                $this->text( 'link' ),
                $this->select(
                    'type',
                    [
                        'primary'           => __( 'Primary Blue', $this->textdomain() ),
                        'secondary'         => __( 'Secondary Bordered', $this->textdomain() ),
                        'transparent-black' => __( 'Transparent Black', $this->textdomain() ),
                        'transparent-white' => __( 'Transparent White', $this->textdomain() ),
                        'link'              => __( 'Link With Arrow', $this->textdomain() ),
                        'round'             => __( 'Rounded Shape', $this->textdomain() )
                    ]
                ),
                $this->checkbox( 'new tab' )
            ],
            'vertical'
        );
    }

    /**
     * Returns a list of categories by taxonomoy
     *
     * @param string $taxonomy
     * @return  mixed
     */
    public function category_list( $taxonomy )
    {
        $options    = [];
        $categories = get_categories(
            [
                'taxonomy'   => $taxonomy,
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => 0
            ]
        );

        foreach ( $categories as $category )
        {
            $options[$category->term_id] = $category->name;
        }

        return $options;
    }

    /**
     * Returns list of contact form 7 forms
     *
     * @return mixed
     */
    public function cf7_list()
    {
        $forms = [];
        $posts = get_posts(
            [
                'post_type'      => 'wpcf7_contact_form',
                'posts_per_page' => -1
            ]
        );

        foreach ( $posts as $post )
        {
            $forms[$post->ID] = $post->post_title;
        }

        return $forms;
    }

    /**
     * Creates a checkbox
     *
     * @param  string  $name
     * @return mixed
     */
    public function checkbox( $name )
    {
        return self::field( 'checkbox', $name );
    }

    /**
     * Creats a color selector
     *
     * @param  string  $name
     * @return mixed
     */
    public function color( $name, $default = null )
    {

        if ( $default !== null )
        {
            return self::field( 'color', $name )->set_default_value( $default );
        }

        return self::field( 'color', $name );
    }

    /**
     * Creates a Gutenberg block
     *
     * @param  string  $name
     * @param  array   $fields
     * @return mixed
     */
    public function create_block( $name, $fields, $template_path = '' )
    {
        $name     = ucfirst( $name );
        $template = strtolower( str_replace( ' ', '-', sprintf( '/%s/%s', $template_path, $name ) ) );

        $this->blocks[] = str_replace( '//', '/', $template );
        add_filter( 'qtwp_gutenberg_blocks', [$this, 'register_templates'] );

        $fields[] = Field::make( 'select', 'template', '' )
            ->add_options( [$template => $template] )
            ->set_default_value( $template )

// ->set_value( '1' )

// ->set_attribute( 'value', sprintf( '/%s%s', $template_path, $name ) )

// ->set_attribute( 'readOnly', true )
        // ->set_attribute( 'type', 'hidden' )
            ->set_classes( 'hidden' );

        return Block::make(
            __( $name, $this->textdomain() )
        )
            ->set_description( __( $name . ' Section (Inument block)', $this->textdomain() ) )
            ->set_keywords( explode( ' ', $name ) )
            ->set_icon( 'block-default' )
            ->set_render_callback( function ( $fields, $atts, $inner_block )
        {
                $template = $fields['template'];
                setData( $fields );

                get_template_part( 'inc/views/' . $template );
            } )
            ->add_fields( $fields );
    }

    /**
     * Creates a column based section
     *
     * @param  string  $name
     * @param  array   $fields
     * @param  array   $columns
     * @param  string  $template_path
     * @return mixed
     */
    public function create_columns( $name, $columns, $fields = [], $template_path = '' )
    {
        $block = $this->create_block( $name, $fields, $template_path );

        foreach ( $columns as $title => $column_fields )
        {
            $block->add_tab(
                __( ucfirst( $title ), $this->textdomain() ),
                $column_fields
            );
        }

        return $block;
    }

    /**
     * Creates a carbon fields input
     *
     * @param  string  $type
     * @param  string  $name
     * @return mixed
     */
    public function field( $type = 'text', $name )
    {
        return Field::make( $type, self::name( $name ), self::title( $name ) );
    }

    /**
     * Gallery repeater item
     *
     * @param  string  $name
     * @return mixed
     */
    public function gallery_repeater( $name )
    {
        return $this->repeater(
            $name,
            [
                $this->text( 'title' ),
                $this->repeater( 'child columns', [
                    $this->image( 'image' )
                ], 'vertical' )
            ],
        );
    }

    public function html( $name, $content )
    {
        return Field::make( 'html', $this->name( $name ) )
            ->set_html( $content );
    }

    /**
     * Creates an image filed
     *
     * @param  string  $name
     * @return mixed
     */
    public function image( $name = 'image' )
    {
        return self::field( 'image', $name );
    }

    /**
     * Creates multiple selection option
     *
     * @param  string  $name
     * @param  array   $options
     * @return mixed
     */
    public function multiselect( $name, $options = [] )
    {
        return Field::make( 'multiselect', self::name( $name ), self::title( $name ) )->set_options( $options );
    }

    /**
     * Converts name to field formatted name
     *
     * @param  string   $name
     * @return string
     */
    public function name( $name )
    {
        return strtolower( str_replace( [' ', '-', '.'], '_', $name ) );
    }

    /**
     * Returns list of posts | filtered by post type
     *
     * @param string $type
     * @return mixed
     */
    public function post_list( $type = 'post' )
    {
        $results = [];
        $posts   = get_posts(
            [
                'post_type'      => $type,
                'posts_per_page' => -1
            ]
        );

        foreach ( $posts as $post )
        {
            $results[$post->ID] = $post->post_title;
        }

        return $results;
    }

    /**
     * Register a template to the templates list
     *
     * @param array $templates
     * @return array
     */
    public function register_templates( $templates )
    {
        return array_merge( $templates, $this->blocks );
    }

    /**
     * Creates a repeater field
     *
     * @param  string  $name
     * @param  array   $fields
     * @param  string  $layout
     * @return mixed
     */
    public function repeater( $name, $fields, $layout = 'horizontal' )
    {
        $layout = 'tabbed-' . $layout;
        $name   = ucfirst( str_replace( ['.', '-', '_'], ' ', $name ) );

        return Field::make( 'complex', strtolower( str_replace( ' ', '_', $name ) ), $name )->set_layout( $layout )->add_fields( $fields );
    }

    /**
     * Creates a rich_text box
     *
     * @param  string  $name
     * @return mixed
     */
    public function rich_text( $name )
    {
        return self::field( 'rich_text', $name );
    }

    /**
     * Creates a field for section subtitle
     *
     * @return mixed
     */
    public function section_subtitle()
    {
        return $this->textarea( 'subtitle' );
    }

    /**
     * Creates a field for section tagline
     *
     * @return void
     */
    public function section_tagline()
    {
        return $this->text( 'tagline' );
    }

    /**
     * Creates a field for section title
     *
     * @return mixed
     */
    public function section_title()
    {
        return $this->text( 'title' );
    }

    /**
     * Creates a select option
     *
     * @param  string  $name
     * @param  array   $options
     * @return mixed
     */
    public function select( $name, $options = [] )
    {
        return Field::make( 'select', self::name( $name ), self::title( $name ) )->set_options( $options );
    }

    /**
     * Returns a template from inc/views folder
     *
     * @param string $name
     * @return mixed
     */
    public function template( $name )
    {
        $template_name = strtolower( str_replace( ['.', '-', '_'], '-', $name ) ) . '.qtwpt.php';

        return get_template_part( 'inc/views/' . $template_name );
    }

    /**
     * Creates a text field
     *
     * @param  string  $name
     * @return mixed
     */
    public function text( $name )
    {
        return self::field( 'text', $name );
    }

    /**
     * Creates a textarea field
     *
     * @param  string  $name
     * @return mixed
     */
    public function textarea( $name )
    {
        return self::field( 'textarea', $name );
    }

    /**
     * Converts name to title
     *
     * @param  string   $name
     * @return string
     */
    public function title( $name )
    {
        return ucfirst( strtolower( str_replace( ['-', '_', '.'], ' ', $name ) ) );
    }

}
