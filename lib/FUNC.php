<?php

namespace qtwp\lib;

defined( 'ABSPATH' ) or exit;

trait FUNC
{
    public static function view( $name, $atts = [] )
    {
        ob_start();

        include QTWP_VIEW . $name . '.php';

        return ob_get_clean();
    }

    function var ( $name, $type = 'string' ) {
        return  !  empty( $_REQUEST[$name] ) ? $_REQUEST[$name] : '';
    }

    public static function get_var( $name, $type = 'string' )
    {
        return  !  empty( $_REQUEST[$name] ) ? $_REQUEST[$name] : '';
    }

    public static function textdomain()
    {
        return wp_get_theme()->get( 'TextDomain' );
    }

    /**
     * Receieves a list of texts separated by new lines and it converts each line of string to an array element
     *
     * @param string $filename
     * @return array|object
     */
    public static function fileToArray( $filename )
    {
        $filename = get_template_directory() . '/resources/' . $filename;
        $cities   = [];

        // Open the file
        $handle = fopen( $filename, "r" );

// Loop through the file line by line
        while (  ( $name = fgets( $handle ) ) !== false ) {
            // Add the city to the array
            $cities[$name] = $name;
        }

        // Close the file
        fclose( $handle );

        return $cities;
    }

    /**
     * Undocumented function
     *
     * @param string $file
     * @return string
     */
    public static function fileToDropdown( $file )
    {
        $arr = self::fileToArray( $file );

        ob_start();

        foreach ( $arr as $key => $value ) {
            printf( '<option value="%s">%s</option>', $key, $value );
        }

        return ob_get_clean();
    }

    public static function col_to_data( $cols, $data )
    {
        $result = [];

        foreach ( $cols as $col ) {

            if ( isset( $data[$col] ) ) {
                $result[$col] = $data[$col];
            }

        }

        return $result;
    }

    public static function col_to_sanitize( $cols )
    {
        $result = [];

        foreach ( $cols as $col ) {
            $result[] = '%s';
        }

        return $result;
    }

    public static function format_name( $name )
    {
        return str_replace( ['-', ' '], '_', strtolower( $name ) );
    }

    /**
     * Removes all files inside of a folder/path
     *
     * @param string $path
     * @return void
     */
    public static function removeFilesAtPath( $path )
    {

// Check if the path exists
        if (  !  is_dir( $path ) ) {
            return false;
        }

        // Open the directory
        $dir = opendir( $path );

        if (  !  $dir ) {
            return false;
        }

// Loop through the directory
        while (  ( $file = readdir( $dir ) ) !== false ) {

// Skip current and parent directory entries
            if ( $file == '.' || $file == '..' ) {
                continue;
            }

            $filePath = $path . '/' . $file;

// Check if the file is a regular file
            if ( is_file( $filePath ) ) {

// Attempt to remove the file
                if (  !  unlink( $filePath ) ) {
                    // If unable to remove the file, return false
                    closedir( $dir );

                    return false;
                }

            }

        }

        // Close the directory handle
        closedir( $dir );

        return true;
    }

    public function convertYoutubeEmbed( $videoLink )
    {
        // Check if the link is a valid YouTube URL
        $pattern = '/^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})$/';

        if ( preg_match( $pattern, $videoLink, $matches ) ) {
            $videoId   = $matches[1];
            $embedCode = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

            return $embedCode;
        } else {
            // If the link is not a valid YouTube URL, return an error message or handle the situation accordingly.
            return 'Invalid YouTube video link.';
        }

    }

    public static function post_categories( $post_id, $taxonomy )
    {
        $categories = get_the_terms( $post_id, $taxonomy );

        if ( empty( $categories ) ) {
            return;
        }

        $category_names = [];

        foreach ( $categories as $category ) {
            $category_names[] = $category->name;
        }

        return implode( ', ', $category_names );
    }

}
