<?php
/**
 * Awesome One functions and definitions
 *
 * @package Awesomeone
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */

if ( ! function_exists( 'awesomeone_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function awesomeone_setup() {

	if ( ! isset( $content_width ) )
		$content_width = 640; /* pixels */

	load_theme_textdomain( 'awesomeone', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'title-tag' );
	add_image_size('awesomeone-homepage-thumb',240,145,true);
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'awesomeone' ),
		'footer'	=> __('Footer Menu', 'awesomeone'),
	) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff'
	) );
	add_editor_style( 'editor-style.css' );
}
endif; // awesomeone_setup
add_action( 'after_setup_theme', 'awesomeone_setup' );


function awesomeone_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'awesomeone' ),
		'description'   => __( 'Appears on blog page sidebar', 'awesomeone' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'awesomeone' ),
		'description'   => __( 'Appears on page sidebar', 'awesomeone' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Header Widget', 'awesomeone' ),
		'description'   => __( 'Appears on header of the page', 'awesomeone' ),
		'id'            => 'sidebar-3',
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '<h4 class="header-title">',
		'after_title'   => '</h4>',
	) );

}
add_action( 'widgets_init', 'awesomeone_widgets_init' );

function awesomeone_font_url(){
		$font_url = '';

		/* Translators: If there are any character that are
		* not supported by PT Sans, translate this to off, do not
		* translate into your own language.
		*/
		$ptsans = _x('on', 'PT Sans font:on or off','awesomeone');

		/* Translators: If there are any character that are
		* not supported by Oswald, translate this to off, do not
		* translate into your own language.
		*/
		$oswald = _x('on', 'Oswald font:on or off','awesomeone');

		/* Translators: If there are any character that are
		* not supported by Roboto Condensed, translate this to off, do not
		* translate into your own language.
		*/
		$roboto_cond = _x('on', 'Roboto Condensed font:on or off','awesomeone');

		if('off' !== $ptsans || 'off' !==  $oswald || 'off' !== $roboto_cond){
			$font_family = array();

			if('off' !== $ptsans){
				$font_family[] = 'PT Sans:300,400,600,700,800,900';
			}

			if('off' !== $oswald){
				$font_family[] = 'Oswald:400,700';
			}

			if('off' !== $roboto_cond){
				$font_family[] = 'Roboto Condensed:400,700';
			}

			$query_args = array(
				'family'	=> urlencode(implode('|',$font_family)),
			);

			$font_url = add_query_arg($query_args,'//fonts.googleapis.com/css');
		}

	return $font_url;
	}

function awesomeone_scripts() {
	wp_enqueue_style( 'awesomeone-font', awesomeone_font_url(), array() );
	wp_enqueue_style( 'awesomeone-basic-style', get_stylesheet_uri() );
	wp_enqueue_style( 'awesomeone-editor-style', get_template_directory_uri().'/editor-style.css' );
	wp_enqueue_style( 'awesomeone-responsive-style', get_template_directory_uri().'/css/theme-responsive.css' );
	wp_enqueue_style( 'awesomeone-nivo-style', get_template_directory_uri().'/css/nivo-slider.css' );
	if ( is_home() || is_front_page() ) {
		wp_enqueue_script( 'awesomeone-nivo-slider', get_template_directory_uri() . '/js/jquery.nivo.slider.js', array('jquery') );
	}
	wp_enqueue_script( 'awesomeone-customscripts', get_template_directory_uri() . '/js/custom.js', array('jquery') );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'awesomeone_scripts' );

function awesomeone_ie_stylesheet(){
	global $wp_styles;

	/** Load our IE-only stylesheet for all versions of IE.
	*   <!--[if lt IE 9]> ... <![endif]-->
	*
	*  Note: It is also possible to just check and see if the $is_IE global in WordPress is set to true before
	*  calling the wp_enqueue_style() function. If you are trying to load a stylesheet for all browsers
	*  EXCEPT for IE, then you would HAVE to check the $is_IE global since WordPress doesn't have a way to
	*  properly handle non-IE conditional comments.
	*/
	wp_enqueue_style('awesomeone-ie', get_template_directory_uri().'/css/ie.css', array('awesomeone-ie-style'));
	$wp_styles->add_data('awesomeone-ie','conditional','IE');
	}
add_action('wp_enqueue_scripts','awesomeone_ie_stylesheet');


function awesomeone_pagination() {
	global $wp_query;
	$big = 12345678;
	$page_format = paginate_links( array(
	    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	    'format' => '?paged=%#%',
	    'current' => max( 1, get_query_var('paged') ),
	    'total' => $wp_query->max_num_pages,
	    'type'  => 'array'
	) );
	if( is_array($page_format) ) {
		$paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
		echo '<div class="pagination"><div><ul>';
		echo '<li><span>'. $paged . ' of ' . $wp_query->max_num_pages .'</span></li>';
		foreach ( $page_format as $page ) {
			echo "<li>$page</li>";
		}
		echo '</ul></div></div>';
	}
}

define('pro_theme_url','http://flythemes.net/wordpress-themes/awesomeone-corporate-wordpress-theme/');
define('theme_doc','http://flythemesdemo.net/documentation/awesomeone-doc/');
define('site_url','http://flythemes.net/');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


function awesomeone_custom_blogpost_pagination( $wp_query ){
	$big = 999999999; // need an unlikely integer
	if ( get_query_var('paged') ) { $pageVar = 'paged'; }
	elseif ( get_query_var('page') ) { $pageVar = 'page'; }
	else { $pageVar = 'paged'; }
	$pagin = paginate_links( array(
		'base' 			=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' 		=> '?'.$pageVar.'=%#%',
		'current' 		=> max( 1, get_query_var($pageVar) ),
		'total' 		=> $wp_query->max_num_pages,
		'prev_text'		=> '&laquo; Prev',
		'next_text' 	=> 'Next &raquo;',
		'type'  => 'array'
	) );
	if( is_array($pagin) ) {
		$paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
		echo '<div class="pagination"><div><ul>';
		echo '<li><span>'. $paged . ' of ' . $wp_query->max_num_pages .'</span></li>';
		foreach ( $pagin as $page ) {
			echo "<li>$page</li>";
		}
		echo '</ul></div></div>';
	}
}

// get slug by id
function awesomeone_get_slug_by_id($id) {
	$post_data = get_post($id, ARRAY_A);
	$slug = $post_data['post_name'];
	return $slug;
}

function awesomeone_credit_link(){
		return "Powered by WordPress. AwesomeOne theme by <a href=".esc_url(site_url)." target='_blank'>Flythemes</a>";
	}
