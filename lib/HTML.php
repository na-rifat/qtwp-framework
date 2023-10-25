<?php
/**
 * Name: HTML renderer
 *
 */
namespace qtwp\lib;

defined( 'ABSPATH' ) or exit;

class HTML
{
    use FUNC;

// To use the function, you can call it in your WordPress theme files like this:
    // generate_share_links();

    public static function _( $content )
    {
        return self::generate_content( $content );
    }

    public function __construct()
    {

    }

    public static function anim( $type = 'fade-up', $delay = 0, $duration = 1000 )
    {
        return [
            'data-aos'          => $type,
            'data-aos-delay'    => $delay,
            'data-aos-duration' => $duration
        ];
    }

    /**
     * Renders a single button
     *
     * @param array $data
     * @param array $atts
     * @return string
     */
    public static function button( $data, $atts = [] )
    {
        return sprintf(
            '<a href="%s" target="%s" class="btn %s" %s>%s%s%s</a>',
            $data['link'],
            $data['new_tab'] ? '_blank' : '_self',
            $data['type'],
            self::render_atts( $atts ),
             !  empty( $data['img_icon'] ) ? wp_get_attachment_image( $data['img_icon'], 'original' ) : '',
            $data['icon'],
            $data['title']
        );
    }

    /**
     * Renders group of buttons | Can render single button also
     * Contains group wrapper
     *
     * @param array $data
     * @param string $key
     * @param array $atts
     * @return string
     */
    public static function button_group( $data, $key = 'buttons', $atts = [] )
    {

        if ( empty( $data[$key] ) )
        {
            return;
        }

        $buttons = sprintf( '<div class="btn-grp %s" %s>',  !  empty( $atts['class'] ) ? $atts['class'] : '', self::render_atts( $atts ) );

        foreach ( $data[$key] as $button )
        {
            $buttons .= self::button( $button );
        }

        $buttons .= '</div>';

        return $buttons;
    }

    public static function class_name( $name )
    {
        return strtolower( str_replace( ['.', '_', ' '], '-', $name ) );
    }

    /**
     * Render a wrapper which can hold columns
     *
     * @param string|array $content
     * @return string
     */
    public static function column_wrapper( $content )
    {
        return sprintf( '<div class="col-wrapper">%s</div>', self::generate_content( $content ) );
    }

    /**
     * Renders columns
     *
     * Each columns contains it's content
     *
     * @param array $columns
     * @param array $atts
     * @return string
     */
    public static function columns( $columns = [], $atts = [] )
    {
        $el = '';

        foreach ( $columns as $name => $content )
        {
            $el .= sprintf(
                '<div class="col %s" %s>%s</div>',
                self::class_name( $name ),
                self::render_atts( $atts ),
                self::generate_content( $content ) );
        }

        return $el;
    }

    public static function condition( $condition, $content )
    {

        if ( $condition )
        {
            return self::generate_content( $content );
        }

        return '';
    }

    public static function container( $content )
    {
        return sprintf( '<div class="container">%s</div>', self::generate_content( $content ) );
    }

    /**
     * Renders content
     * Accepts textual contents
     *
     * @param array $data
     * @param array $atts
     * @return string
     */
    public static function content( $data, $atts = [] )
    {

        if ( empty( $data['content'] ) )
        {
            return;
        }

        return sprintf( '<div class="content" %s>%s</div>', self::render_atts( $atts ), $data['content'] );
    }

    /**
     * Customized wrapper for any element
     *
     * @param string $name
     * @param string|mixed $content
     * @param array $atts
     * @return string
     */
    public static function custom_wrapper( $name, $content, $atts = [] )
    {
        return sprintf(
            '<div class="%s" %s>%s</div>',
            self::class_name( $name ),
            self::render_atts( $atts ),
            self::generate_content( $content )
        );
    }

    /**
     * Renders description
     *
     * @param array $data
     * @param array $atts
     * @return string
     */
    public static function description( $data, $atts = [] )
    {

        if ( empty( $data['description'] ) )
        {
            return;
        }

        return sprintf( '<p class="description" %s>%s</p>', self::render_atts( $atts ), $data['description'] );
    }

    public static function func( $func, $args = [] )
    {

        if ( is_callable( $func ) )
        {
            return call_user_func_array( $func, $args );
        }

    }

    /**
     * Renders an image gallery
     *
     * @param array $items
     * @return string
     */
    public static function gallery( $items )
    {
        $el = '<div class="image-gallery">';

        foreach ( $items as $column )
        {
            $el .= '<div class="gallery-col">';

            foreach ( $column['child_columns'] as $image )
            {
                $el .= self::image( $image );
            }

            $el .= '</div>';
        }

        $el .= '</div>';

        return $el;
    }

    public static function generate_content( $content = '' )
    {

        if ( is_string( $content ) )
        {
            return $content;
        }

        if ( is_array( $content ) )
        {
            return implode( '', $content );
        }

        return $content;
    }

    /**
     * Renders a single image
     *
     * @param array $data
     * @param string $key
     * @param array $attr
     * @return string
     */
    public static function image( $data, $key = 'image', $attr = [] )
    {

        if ( empty( $data[$key] ) )
        {
            return;
        }

        return wp_get_attachment_image( $data[$key], 'original', false, $attr );
    }

    /**
     * Renders an image overlay
     *
     * @param string $shapename
     * @param string $path
     * @return string
     */
    public static function image_overlay( $shapename, $path )
    {
        return sprintf( '<div class="%s">%s</div>', $shapename, get_img( $path ) );
    }

    /**
     * Renders HTML nav menu
     *
     * @param string $location
     * @param string $class
     * @return string
     */
    public static function nav_menu( $location, $class = '' )
    {
        wp_nav_menu( [
            'theme_location'  => $location,
            'depth'           => 3,
            'container'       => 'nav',
            'container_class' => 'collapse navbar-collapse ' . $class,
            'container_id'    => 'bs-example-navbar-collapse-1',
            'menu_class'      => 'nav navbar-nav',
            'fallback_cb'     => 'qtwp\lib\Navwalker::fallback',
            'walker'          => new Navwalker()
        ] );
    }

    /**
     * Creates a blank overlay
     *
     * @param string $class
     * @return string
     */
    public static function overlay( $class = 'overlay' )
    {
        return sprintf( '<div class="%s"></div>', $class );
    }

    public static function render_anim( $type = 'fade-up', $delay = 0, $duration = 1000 )
    {
        return self::render_atts( self::anim( $type, $delay, $duration ) );
    }

    public static function render_atts( $atts )
    {
        ob_start();

        foreach ( $atts as $key => $val )
        {
            printf( ' %s="%s" ', $key, $val );
        }

        return ob_get_clean();
    }

    public static function render_background( $background )
    {

        if ( empty( $background ) )
        {
            return;
        }

        if ( preg_match( '/^#[a-f0-9]{6}$/i', $background ) )
        {
            return sprintf( 'style="background-color: %s;" ', $background );
        }

        return sprintf( 'style="background-image: url(%s); background-size: cover; background-repeat: no-repeat; background-position: center;" ', wp_get_attachment_image_src( $background, 'original', false )[0] );
    }

    /**
     * Renders contact options with wrapper
     *
     * @param array $contacts
     * @return string
     */
    public static function render_contacts( $contacts )
    {
        $contacts_el = '';

        foreach ( $contacts as $contact )
        {
            $contacts_el .= sprintf( '<a href="%s">%s<span>%s</span></a>', $contact['link'], $contact['icon'], $contact['title'] );
        }

        return sprintf( '<div class="contacts">%s</div>', $contacts_el );
    }

    public static function render_resource_type( $type, $atts  = [])
    {
        return self::view( sprintf( 'resources/%s', $type ), $atts  );
    }

    static function render_webinar_modal($post){
        ob_start();

        include QTWP_VIEW . '/modals/webinar.php';

        return ob_get_clean();
    }

    /**
     * Renders social media links with wrapper and icon
     *
     * @param array $socials
     * @return string
     */
    public static function render_socials( $socials )
    {
        $socials_el = '';

        foreach ( $socials as $social )
        {
            $socials_el .= sprintf( '<a target="_blank" href="%s" aria-label="%s">%s</a>', $social['link'], $social['title'], wp_get_attachment_image( $social['icon'], 'original' ) );
        }

        return sprintf( '<div class="socials">%s</div>', $socials_el );

    }

    public static function render_text_color( $color )
    {
        return ['style' => sprintf( 'color: %s;', $color )];
    }

    public static function section( $name, $content, $background = '' )
    {
        printf(
            '<section class="%s" %s >%s</section>',
            self::class_name( $name ),
            self::render_background( $background ),
            self::generate_content( $content )
        );
    }

    public static function section_subtitle( $data, $atts = [] )
    {

        if ( empty( $data['subtitle'] ) )
        {
            return;
        }

        return sprintf( '<p class="subtitle" %s>%s</p>', self::render_atts( $atts ), $data['subtitle'] );
    }

    public static function section_title( $data, $atts = [] )
    {

        if ( empty( $data['title'] ) )
        {
            return;
        }

        return sprintf(
            '<h2 class="title %s" %s>%s</h2>',
             !  empty( $atts['class'] ) ? $atts['class'] : '',
            self::render_atts( $atts ),
            $data['title'] );
    }

    public static function section_titleh1( $data, $atts = [] )
    {

        if ( empty( $data['title'] ) )
        {
            return;
        }

        return sprintf( '<h1 class="title" %s>%s</h1>', self::render_atts( $atts ), $data['title'] );
    }

    public static function section_titleh3( $data, $atts = [] )
    {

        if ( empty( $data['title'] ) )
        {
            return;
        }

        return sprintf( '<h3 class="title" %s>%s</h3>', self::render_atts( $atts ), $data['title'] );
    }

    /**
     * Renders website logo
     *
     * @return void
     */
    public static function site_logo()
    {
        the_custom_logo();
    }

    public static function social_share_links( $medias = [] )
    {

        if ( is_string( $medias ) )
        {
            $medias = explode( ', ', $medias );
        }

        // Get the current post ID
        $post_id = get_the_ID();

        // Get the current post title and URL
        $post_title = get_the_title();
        $post_url   = get_permalink( $post_id );

        // Create a mailto link for email sharing
        $email_subject    = "Check out this post: {$post_title}";
        $email_body       = "I found this interesting article and wanted to share it with you: {$post_title}\n{$post_url}";
        $email_share_link = "mailto:?subject={$email_subject}&body={$email_body}";

        // Encode the post title and URL for use in URLs
        $encoded_title = urlencode( $post_title );
        $encoded_url   = urlencode( $post_url );

        $links = [
            'facebook'  => "https://www.facebook.com/sharer/sharer.php?u={$encoded_url}",
            'linked_in' => "https://www.linkedin.com/shareArticle?mini=true&url={$encoded_url}&title={$encoded_title}",
            'twitter'   => "https://twitter.com/intent/tweet?url={$encoded_url}&text={$encoded_title}",
            'medium'    => "https://medium.com/m/share?url={$encoded_url}&title={$encoded_title}",
            'email'     => $email_share_link,
            'instagram' => "https://www.instagram.com/share?url={$encoded_url}",
            'pinterest' => "https://pinterest.com/pin/create/button/?url={$encoded_url}&description={$encoded_title}",
            'youtube'   => "https://www.youtube.com/addtoany?linkurl={$encoded_url}&linkname={$encoded_title}",
            'whatsapp'  => "https://api.whatsapp.com/send?text={$encoded_title}%20{$encoded_url}",
            'messenger' => "fb-messenger://share/?link={$encoded_url}&app_id=",
            'discord'   => "https://discord.com/api/share?client_id=&title={$encoded_title}&description={$encoded_url}&url={$encoded_url}",
            'reddit'    => "https://www.reddit.com/submit?url={$encoded_url}&title={$encoded_title}",
            'tiktok'    => "https://www.tiktok.com/share?url={$encoded_url}&title={$encoded_title}"
        ];

        $el = '';

        foreach ( $medias as $media )
        {
            $el .= sprintf( '<a href="%s" aria-label="%s" target="_blank">%s</a>', $links[$media], ucfirst( $media ), get_img( sprintf( 'social-shares/%s.svg', $media ) ) );
        }

        printf( '<div class="social-shares">%s</div>', $el );
    }

    public static function tagline( $data, $key = 'tagline', $atts = [] )
    {

        if ( empty( $data[$key] ) )
        {
            return;
        }

        return sprintf( '<p class="tagline">%s</p>', $data[$key] );
    }

    /**
     * Renders title of any element
     *
     * @param array $data
     * @param array $atts
     * @return string
     */
    public static function title( $data, $atts = [] )
    {

        if ( empty( $data['title'] ) )
        {
            return;
        }

        return sprintf( '<p class="title" %s>%s</p>', self::render_atts( $atts ), $data['title'] );
    }

    /**
     * Elements wrapper
     *
     * @param string|array $content
     * @return string
     */
    public static function wrapper( $content )
    {
        return sprintf( '<div class="wrapper">%s</div>', self::generate_content( $content ) );
    }

}
