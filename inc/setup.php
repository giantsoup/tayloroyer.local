<?php

/* ==========================================================================
   Admin Setup
   ========================================================================== */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
    $content_width = 640; /* pixels */





if ( ! function_exists( 'tayloroyer_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function tayloroyer_setup() {

    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     * If you're building a theme based on Tayloroyer, use a find and replace
     * to change 'tayloroyer' to the name of your theme in all the template files
     */
    load_theme_textdomain( 'tayloroyer', get_template_directory() . '/languages' );

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
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ) );

    /**
     * Enable support for Post Thumbnails on posts and pages
     *
     * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
     */
    add_theme_support( 'post-thumbnails' );
    add_image_size( '640x', 640 );

    /**
     * Add custom image sizes to display settings in Media Library
     * @param  array $sizes list of sizes
     * @return array
     */
    function tayloroyer_media_library_image_options( $sizes ) {

        $new_sizes = array_merge( $sizes, array(
            // name must be string, not integer
            '640x' => __( 'Main Content' ),
        ) );

        return $new_sizes;

    }
    add_filter( 'image_size_names_choose', 'tayloroyer_media_library_image_options' );

    /**
     * This theme uses wp_nav_menu() in one location.
     */
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'tayloroyer' ),
    ) );

}
endif; // tayloroyer_setup
add_action( 'after_setup_theme', 'tayloroyer_setup' );





/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function tayloroyer_widgets_init() {

    register_sidebar( array(
        'name'          => __( 'Sidebar', 'tayloroyer' ),
        'id'            => 'sidebar-1',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget__heading">',
        'after_title'   => '</h3>',
    ) );

}
add_action( 'widgets_init', 'tayloroyer_widgets_init' );





/**
 * Enqueue scripts and styles
 */
function tayloroyer_scripts() {

    if ( !is_admin() ) {

        // Styles
        wp_enqueue_style( 'googlefonts', add_query_arg( 'family', 'Open+Sans:400,300,700|Roboto:400,100,900italic', '//fonts.googleapis.com/css' ), array(), null );
        wp_enqueue_style( 'main', get_template_directory_uri() . '/assets/dist/css/main.min.css', array(), null );
        wp_enqueue_style( 'tayloroyer-style', get_stylesheet_uri(), array( 'main' ) );

        // IE Conditional
        wp_register_style( 'no-mq', get_template_directory_uri() . '/assets/dist/css/no-mq.css', array(), null );
        $GLOBALS['wp_styles']->add_data( 'no-mq', 'conditional', 'lte IE 8' );
        wp_enqueue_style( 'no-mq' );

        // Scripts
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/dist/js/plugins/modernizr-2.8.3.min.js', array(), null );
        wp_enqueue_script( 'plugins', get_template_directory_uri() . '/assets/dist/js/plugins.min.js', array( 'jquery' ), null, true );
        wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/dist/js/main.min.js', array( 'jquery', 'plugins' ), null, true );

        // values available to js
        wp_localize_script( 'main', 'ELEV', array(
                'siteUrl' => site_url(),
                'directoryUrl' => get_template_directory_uri()
            )
        );
    }
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'tayloroyer_scripts' );





/**
 * Add Admin Options menu item
 *
 * Managed by Advanced Custom Fields Plugin
 *
 */
function tayloroyer_acf_options_page_settings( $settings )
{
    $settings['title'] = 'Global Options';
    // $settings['pages'] = array('Header', 'Sidebar', 'Footer');

    return $settings;
}

add_filter('acf/options_page/settings', 'tayloroyer_acf_options_page_settings');





/**
 * Order Menu Items
 */
function tayloroyer_custom_menu_order( $menu_ord ) {
    if ( !$menu_ord ) return true;
    return array(
        'index.php', // Dashboard
        'edit.php?post_type=page', // Pages
        'edit.php', // Posts
        'gf_edit_forms', // Forms
        'upload.php' // Media
    );
}
add_filter('custom_menu_order', 'tayloroyer_custom_menu_order');
add_filter('menu_order', 'tayloroyer_custom_menu_order');





/**
 * Modify main query
 */
function tayloroyer_modify_main_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    // Events Taxonomy
    // if ( is_tax( 'type' ) ) {
        // $query->set( 'meta_key', 'date' );
        // $query->set( 'orderby', 'date' );
    // }
    // Volunteers
    // if ( is_post_type_archive( 'volunteers' ) ) {
    //  $query->set( 'posts_per_page', -1 );
    //  $query->set( 'orderby', 'menu_order' );
    //  $query->set( 'order', 'ASC' );
    //  $query->set( 'meta_key', 'active' );
    //  $query->set( 'meta_value', true );
    // }
}
add_action( 'pre_get_posts', 'tayloroyer_modify_main_query', 1 );





/**
 * Remove inline styling for recent comments widget
 */
function tayloroyer_remove_recent_comments_style() {

        global $wp_widget_factory;

        remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );

    }
add_action( 'widgets_init', 'tayloroyer_remove_recent_comments_style' );





/* ==========================================================================
   Project Specific
   ========================================================================== */

/**
* Add style dropdown to MCE editor
*/
function coop022901_mce_editor_buttons( $buttons ) {

   array_unshift( $buttons, 'styleselect' );
   return $buttons;
}
add_filter( 'mce_buttons_2', 'coop022901_mce_editor_buttons' );





/**
* Add styles/classes to the "Styles" drop-down
*/
function coop022901_mce_before_init( $settings ) {

   $style_formats = array(
        array(
            'title'    => 'Subheading',
            'selector' => 'h1,h2,h3,h4,h5,h6',
            'classes'  => 'subheading'
        ),
        array(
           'title'    => 'Button: Black',
           'selector' => 'a',
           'classes'  => 'btn'
        ),
        array(
           'title'    => 'Button: Primary',
           'selector' => 'a',
           'classes'  => 'btn primary'
        ),
        array(
           'title'    => 'Button: Secondary',
           'selector' => 'a',
           'classes'  => 'btn secondary'
        ),
        array(
           'title'    => 'Button: Small',
           'selector' => 'a',
           'classes'  => 'small'
        ),
        array(
           'title'    => 'Button: Large',
           'selector' => 'a',
           'classes'  => 'large'
        ),
        // Primary Text Colors
        // array(
        //    'title'   => 'Text: Green',
        //    'inline'  => 'span',
        //    'classes' => 'text-conifer'
        // ),
        // array(
        //    'title'   => 'Text: Gray',
        //    'inline'  => 'span',
        //    'classes' => 'text-corduroy'
        // ),
        array(
           'title'   => 'Text: Fine Print',
           'inline'  => 'span',
           'classes' => 'fine-print'
        ),
        array(
           'title'    => 'Text: Capitalize',
           'selector' => 'h1,h2,h3,h4,h5,h6,p,div,dt,dd,li,address',
           'classes'  => 'text-capitalize'
        ),
        array(
           'title'    => 'Text: Uppercase',
           'selector' => 'h1,h2,h3,h4,h5,h6,p,div,dt,dd,li,address',
           'classes'  => 'text-upper'
        )
   );

   $settings['style_formats'] = json_encode( $style_formats );

   return $settings;

}
add_filter( 'tiny_mce_before_init', 'coop022901_mce_before_init' );
