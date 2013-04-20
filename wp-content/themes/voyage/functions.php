<?php
/**
 * Voyage Theme functions and definitions
 *
 * @package voyage
 * @subpackage voyage
 * @since Voyage 1.0
 */
/** Run voyage_setup() after the 'after_setup_theme' hook */
	
add_action( 'after_setup_theme', 'voyage_setup' );

if ( ! function_exists( 'voyage_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override voyage_setup() in a child theme, add your own voyage_setup to your child theme's functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails, custom headers and backgrounds, and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Voyage 1.0
 */

function voyage_setup() {

	/* Set the content width based on the theme's design and stylesheet. */
	global $content_width;
	if ( ! isset( $content_width ) )
		$content_width = 960;
	// Post Format support
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'quote', 'image' ) );
	//add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image', 'video', 'audio', 'chat' ) );
	// This theme uses post thumbnails i.e. Feathered Image
	add_theme_support( 'post-thumbnails' );
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );
	// Voyage supports woocommerce
	add_theme_support( 'woocommerce' );
	// Make theme available for translation
	load_theme_textdomain( 'voyage', get_template_directory() . '/languages' );

	// style the visual editor.
	add_editor_style();
	// This theme uses wp_nav_menu() in thee locations.
	register_nav_menus( array(
		'top-menu' => __( 'Top Menu', 'voyage' ),
		'section-menu' => __( 'Section Menu', 'voyage' ),
		'subsection-menu' => __( 'Subsection Menu', 'voyage' ),
		'footer-menu' => __( 'Footer Menu', 'voyage' ),		
	) );

	// This theme allows users to set a custom background.
	add_theme_support( 'custom-background', array(
		'default-color' => '', //Default background color
	) );
	$options = voyage_get_options();
	if ($options['logopos'] == 1) {
		$voyage_logo_width = 140;
		$voyage_logo_height = 36;
	}
	else {
		$voyage_logo_width = 300;
		$voyage_logo_height = 100;		
	}
	// The custom header business starts here.
	$custom_header_support = array(
		'default-image'		=> get_template_directory_uri() . '/images/vbc-logo.png',
		'flex-width'        => true,
		'flex-height'		=> true,
	    'header-text'		=> true,
		'default-text-color' => '000000',		
		// The height and width of our custom header.
		'width' 			=> apply_filters( 'voyage_header_image_width', $voyage_logo_width ),
		'height' 			=> apply_filters( 'voyage_header_image_height', $voyage_logo_height	 ),
		// Callback for styling the header.
		'wp-head-callback' => 'voyage_header_style',
		// Callback for styling the header preview in the admin.
		'admin-head-callback' => 'voyage_admin_header_style',
		// Callback used to display the header preview in the admin.
		'admin-preview-callback' => 'voyage_admin_header_image',
	);
	
	add_theme_support( 'custom-header', $custom_header_support );
	
/*	Will not support Deprecated function 			
	if ( ! function_exists( 'get_custom_header' ) ) {
		// This is all for compatibility with versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR', $custom_header_support['default-text-color'] );
		define( 'HEADER_IMAGE', $custom_header_support['default-image'] );
		define( 'HEADER_IMAGE_WIDTH', $custom_header_support['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $custom_header_support['height'] );
		add_custom_image_header( '', $custom_header_support['admin-head-callback'] );
		add_custom_background();
	} */

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'header' => array(
			'url' => '%s/images/vbc-logo.png',
			'thumbnail_url' => '%s/images/vbc-logo.png',
			/* translators: header image description */
			'description' => __( 'Logo', 'voyage' )
		),
	) );
}
endif;

if ( ! function_exists( 'voyage_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 * @since Voyage 1.0
 */
function voyage_header_style() {
	$text_color = get_header_textcolor();
	if ( $text_color == HEADER_TEXTCOLOR ) //Default Text Color. Doing Nothing
		return;
?>
<style type="text/css">
<?php
	if ( 'blank' == $text_color ) : // Blog Text is unchecked
?>
#site-title,
#site-description {
	position: absolute !important;
	clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
	clip: rect(1px, 1px, 1px, 1px);
}
<?php
	else : // Custom color
?>
#site-title a,
#site-description {
	color: #<?php echo $text_color; ?> !important;
}
<?php
	endif;
?>
</style>
<?php
}
endif; // voyage_header_style
/**
 * Get wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since Voyage 1.0
 */
function voyage_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'voyage_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * @since Voyage 1.0
 * @return int
 */
function voyage_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'voyage_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Voyage 1.0
 * @return string "Continue Reading" link
 */
function voyage_continue_reading_link() {
	return ' <a class="more-link" href="'. get_permalink() . '">' . __( 'read more', 'voyage' ) . '<span class="meta-nav"></span></a>';			
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and voyage_continue_reading_link().
 *
 * @since Voyage 1.0
 */
function voyage_auto_excerpt_more( $more ) {
	return ' &hellip;';
}
add_filter( 'excerpt_more', 'voyage_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * @since Voyage 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function voyage_custom_excerpt_more( $output ) {
	if ( ! is_attachment() ) {
		$output .= voyage_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'voyage_custom_excerpt_more' );

/**
 * Tell Wordprsss not using default gallary style
 */
//add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Deprecated way to remove inline styles printed when the gallery shortcode is used.
 */
function voyage_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
// Backwards compatibility with WordPress 3.0.
if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) )
	add_filter( 'gallery_style', 'voyage_remove_gallery_css' );

if ( ! function_exists( 'voyage_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own voyage_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Voyage 1.0
 */
function voyage_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'voyage' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '[Edit]', 'voyage' ), ' ' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 40 ); ?>
					<?php printf( __( '%s <span class="says">says:</span>', 'voyage' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author .vcard -->
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em><?php _e( 'Your comment is awaiting moderation.', 'voyage' ); ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-meta commentmetadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'voyage' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					<?php edit_comment_link( __( '[Edit]', 'voyage' ), ' ' );
					?>
				</div>
			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div>
		</article>

	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widget area and widgets
 *
 * @since Voyage 1.0
 */
function voyage_widgets_init() {
	register_widget( 'Voyage_Recent_Post' );
	register_widget( 'Voyage_Navigation' );

	// First Sidebar - left or right
	register_sidebar( array(
		'name' => __( 'Blog Widget Area 1', 'voyage' ),
		'id' => 'first-widget-area',
		'description' => __( 'Blog Widget Area 1', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );

	// Second Sidebar - left or right
	register_sidebar( array(
		'name' => __( 'Blog Widget Area 2', 'voyage' ),
		'id' => 'second-widget-area',
		'description' => __( 'Blog Widget Area 2', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	
	// Header Widget Area
	register_sidebar( array(
		'name' => __( 'Header Widget Area', 'voyage' ),
		'id' => 'header-widget-area',
		'description' => __( 'Header Widget Area', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	
	// Home Widget Areas
	register_sidebar( array(
		'name' => __( 'Home Widget Area 1', 'voyage' ),
		'id' => 'first-home-widget-area',
		'description' => __( 'Home Widget Area 1', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array(
		'name' => __( 'Home Widget Area 2', 'voyage' ),
		'id' => 'second-home-widget-area',
		'description' => __( 'Home Widget Area 2', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array(
		'name' => __( 'Home Widget Area 3', 'voyage' ),
		'id' => 'third-home-widget-area',
		'description' => __( 'Home Widget Area 3', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array(
		'name' => __( 'Home Widget Area 4', 'voyage' ),
		'id' => 'fourth-home-widget-area',
		'description' => __( 'Home Widget Area 4', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	
	// Footer Widgets
	register_sidebar( array(
		'name' => __( 'Footer Widget Area 1', 'voyage' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'Footer Widget Area 1', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h5 class="widget-title">',
		'after_title' => '</h5>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Widget Area 2', 'voyage' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'Footer Widget Area 2', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h5 class="widget-title">',
		'after_title' => '</h5>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Widget Area 3', 'voyage' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'Footer Widget Area 3', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h5 class="widget-title">',
		'after_title' => '</h5>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Widget Area 4', 'voyage' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'Footer Widget Area 4', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h5 class="widget-title">',
		'after_title' => '</h5>',
	) );
	// Nav Widget Area
	register_sidebar( array(
		'name' => __( 'Navigation Widget Area', 'voyage' ),
		'id' => 'nav-widget-area',
		'description' => __( 'Navigation Widget Area', 'voyage' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
}
/** Register sidebars by running voyage_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'voyage_widgets_init' );

/**
 * Replace rel="category tag" with rel="tag"
 * For W3C validation purposes only.
 */
function voyage_replace_rel_category ($output) {
    $output = str_replace(' rel="category tag"', ' rel="tag"', $output);
    return $output;
}

add_filter('wp_list_categories', 'voyage_replace_rel_category');
add_filter('the_category', 'voyage_replace_rel_category');

if ( ! function_exists( 'voyage_meta_category' ) ) :
// Prints Post Categories
function voyage_meta_category() {
	$categories = wp_get_post_categories( get_the_ID() , array('fields' => 'ids'));
	if($categories) {
 		$sep = ' &bull; ';
 		$cat_ids = implode(',' , $categories);
 		$cats = wp_list_categories('title_li=&style=none&echo=0&include='.$cat_ids);
 		$cats = rtrim(trim(str_replace('<br />',  $sep, $cats)), $sep);
		echo '<i class="icon-bookmark meta-summary-icon"></i>';	
		echo '<span class="entry-category"><span class="meta-cat-prep">';
		echo __('Posted in','voyage') . '</span>';
 		echo  $cats;
		echo '</span>';
	}
}
endif;

if ( ! function_exists( 'voyage_meta_tag' ) ) :
// Prints Post Tags
function voyage_meta_tag() {
	$tags_list = get_the_tag_list( '', __( ' &bull; ', 'voyage' ) );
	if ( $tags_list ):
		echo '<i class="icon-tags meta-summary-icon"></i>';
		printf( '<span class="entry-tag"><span class="%1$s">%2$s </span>%3$s</span>',
		'meta-tag-prep',
		__('Tagged','voyage'),
		$tags_list );
	endif; // End if $tags_list 
}
endif;

if ( ! function_exists( 'voyage_meta_author' ) ) :
// Prints Author
function voyage_meta_author() {
	global $voyage_options;
	if ( $voyage_options['showauthor'] == '1' ):
		echo '<i class="icon-user meta-summary-icon"></i>';
		printf( '<span class="by-author"><span class="meta-author-prep">%4$s</span><span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'voyage' ), get_the_author() ) ),
		get_the_author(),
		__('By ', 'voyage') ); 
	endif;
}
endif;

if ( ! function_exists( 'voyage_meta_date' ) ) :
// Prints Post Date
function voyage_meta_date() {
	global $voyage_options;
	
	if ($voyage_options['showdate'] == 1) {
		echo '<i class="icon-calendar meta-summary-icon"></i>';
		printf( __( '<time class="entry-date" datetime="%1$s">%2$s</time>', 'voyage' ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ) );	
	}
}
endif;

function voyage_comment_prep() {
	return '<span class="meta-comment-prep">Comments:</span>';
}

if ( ! function_exists( 'voyage_meta_comment' ) ) :
// Prints Comments Link
function voyage_meta_comment() {
	if ( comments_open() && ! post_password_required() ) : 
		printf ('<span class="comments-link pull-right">');
		comments_popup_link( voyage_comment_prep(), voyage_comment_prep() . __( '1 Comment', 'voyage' ) , voyage_comment_prep() . __( '% Comments', 'voyage' ) );		
		printf('</span>');
	endif;
}
endif;

if ( ! function_exists( 'voyage_posted_category' ) ) :
// Prints Post Category on top of post title
function voyage_posted_category() {
	echo '<div class="entry-meta entry-meta-top">';	
//	if ( 'post' == get_post_type() && ! is_category() ) {
	if ( 'post' == get_post_type() ) {
		voyage_meta_category();		
	}
	// Display Post Rating using WP-Post Ratings
	if( function_exists('the_ratings') ) {
		the_ratings();
	}	
	echo '</div>';
}
endif;

if ( ! function_exists( 'voyage_posted_on' ) ) :
//Prints post format, date/time and author below post title
function voyage_posted_on() {
	global $voyage_options;

	if ( 'post' == get_post_type() ) {
		$icon_only = 0;	
		if ( $voyage_options['sharesocial'] == 1 
				&& ($voyage_options['share_top'] == 1 || $voyage_options['share_sum_top'] == 1 ) && ! has_post_format('aside') ) {
			$options = get_option('sharing-options');
			if (isset($options['global']['button_style'] ) && 
				$options['global']['button_style'] == 'icon')
				$icon_only = 1;
		}
		echo '<div class="entry-meta entry-meta-middle">';
		if ($icon_only)
			echo '<div class="sharing-icon-only">';			

		// Featured
		if ( is_sticky() ) {
			printf( '<span class="entry-featured">%1$s</span>', __( 'Featured ', 'voyage') );
		}
   		else {
			$postformat = get_post_format();
			if ($postformat != '') {
				printf( '<span class="entry-format">%1$s </span>', $postformat );
			}
		}
		voyage_meta_date();
		voyage_meta_author();
		if ($icon_only)	{
			echo '</div>';
			voyage_social_post_top();			
		}	
		voyage_meta_comment();
		echo '</div>';
		if ($icon_only == 0)
			voyage_social_post_top();
	}
	else {
		echo '<div class="entry-meta entry-meta-middle">';
		if ( $voyage_options['sharesocial'] == 1
				&& $voyage_options['share_top'] == 1
				&&  function_exists( 'sharing_display' ) )
			echo sharing_display();			
		voyage_meta_comment();
		echo '</div>';	
	}
}
endif;

if ( ! function_exists( 'voyage_posted_in' ) ) :
// Prints tages, edit link at the bottom of the post
function voyage_posted_in() {
	printf ('<div class="entry-meta entry-meta-bottom">');	
	if ( 'post' == get_post_type() )
		voyage_meta_tag();
	if ( is_singular() && ! is_home() )
		printf( __(' <a href="%1$s" title="Permalink to %2$s" rel="bookmark">Permalink</a>', 'voyage' ),
				esc_url( get_permalink() ),
				the_title_attribute( 'echo=0' )
			);
	edit_post_link( __( '[Edit]', 'voyage' ), '<span class="edit-link">', '</span>' );
	echo '</div>';	
}
endif;

if ( ! function_exists( 'voyage_post_summary_meta' ) ) :
// Prints meta info for Post Summary
function voyage_post_summary_meta() {
	global $voyage_entry_meta;
	if ( ($voyage_entry_meta == 1) && ('post' == get_post_type()) ) {
		echo '<div class="entry-meta entry-meta-summary clearfix">';
		voyage_meta_date();
		voyage_meta_author();
		voyage_meta_category();
		voyage_meta_tag();
		if ( comments_open() && ! post_password_required() ) {
			echo '<i class="icon-comment meta-summary-icon"></i><span class="meta-comment">';
			comments_popup_link( __('Reply','voyage'), __('1 Comment','voyage') , __('% Comments','voyage') );
			echo '</span>';
		}
		edit_post_link( __( '[Edit]', 'voyage' ), '<span class="edit-link">', '</span>' );	
		echo '</div>';	
	}
}
endif;

if ( ! function_exists( 'voyage_content_nav' ) ) :
/**
Pagination for main loop
 */
function voyage_content_nav( $nav_id ) {
	global $wp_query;
	voyage_content_nav_link($wp_query->max_num_pages, $nav_id );
}
endif; // voyage_content_nav

if ( ! function_exists( 'voyage_content_nav_link' ) ) :
/**
Pagination 
 */
function voyage_content_nav_link( $num_of_pages, $nav_id ) {
	if ( $num_of_pages > 1 ) {
		echo '<nav id="' . $nav_id . '">';
		echo '<div class="pagination pagination-centered">';

		$big = 999999999;
    	if ( get_query_var('paged') )
	    	$current_page = get_query_var('paged');
		elseif ( get_query_var('page') ) 
	   	 	$current_page = get_query_var('page');
		else 
			$current_page = 1;
		$links =  paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, $current_page ),
			'total' => $num_of_pages,
			'mid_size' => 3,
			'prev_text'    => '<i class="icon-chevron-left"></i>' ,
			'next_text'    => '<i class="icon-chevron-right"></i>' ,
			'type' => 'array',
									) );
		echo '<ul><li><span>' . __( 'Page', 'voyage' ) . '</span></li>';
		foreach ( $links as $link )
			printf( '<li>%1$s</li>', $link );
		echo '</ul></div></nav>';					
	}
}
endif; // voyage_content_nav

function voyage_body_classes( $classes ) {
	global $voyage_options;
			
	if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_singular() && ! is_home() )
		$classes[] = 'singular';

	if ( !is_single() )
		$classes[] = 'multi';
			
	if ($voyage_options['fixed_menu'] == 1 && (has_nav_menu('top-menu') || $voyage_options['sociallink_top'] == 1))
		$classes[] = 'fixed-top-menu';
	
	if ( $voyage_options['fp_image'] == 3 && (is_page_template('page-templates/featured.php') || (is_home() && $voyage_options['homepage'] == 1) ) ) {
		$classes[] = 'fullscreen';
	}
	
	if ($voyage_options['ao_colorbox'] == 1) {
		$classes[] = 'voyage-colorbox';		
	}	
	return $classes;
}
add_filter( 'body_class', 'voyage_body_classes' );

function voyage_scripts_method() {	
	global $voyage_options, $voyage_fonts;
	$voyage_options = voyage_get_options();
	$theme_uri = get_template_directory_uri();
	
	wp_enqueue_script( 'modernizr' , $theme_uri . '/js/modernizr.voyage.js', array( 'jquery'), null );
	wp_enqueue_script( 'bootstrap' , $theme_uri . '/js/bootstrap.min.js', array( 'jquery'), null );
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
	
	// Check if the fonts are webfont, if yes, load the font.
	$voyage_fonts = voyage_fonts_array();
	$font_elements = array(
			'bodyfont','headingfont','entrytitlefont',
			'sitetitlefont','sitedescfont', 'mainmenufont',
			'sidebarfont', 'widgettitlefont', 'footerfont'
	);
	$fonts = array();
	foreach ($font_elements as $element) {
		if ($voyage_options[$element] > 0
				&& !in_array($voyage_options[$element],$fonts) )
			$fonts[] = $voyage_options[$element];		
	}
	foreach ($fonts as $font) {
		if (!empty($voyage_fonts[$font]['url']))
			wp_enqueue_style(str_replace(' ','',$voyage_fonts[$font]['label']), $voyage_fonts[$font]['url'], false, '1.0');
	}
	
	wp_enqueue_style('bootstrap', $theme_uri . '/css/bootstrap.min.css', false, '2.2.2');
	wp_enqueue_style('fontawesome', $theme_uri . '/css/font-awesome.min.css', array('bootstrap'), '3.0.2');		
	if ($voyage_options['ao_colorbox'] == 1) {
		wp_enqueue_script( 'colorbox' , $theme_uri . '/js/jquery.colorbox-min.js', array( 'jquery'), null );
	}
	if ($voyage_options['ao_pprint'] == 1) {
		wp_enqueue_script( 'prettify' , $theme_uri . '/js/prettify.js', array( 'jquery'), null );     		
	}

	wp_enqueue_script( 'voyage-theme-script', $theme_uri . '/js/voyage.js', array( 'bootstrap' ), null );
	wp_enqueue_style('voyage', get_stylesheet_uri(), array( 'bootstrap','fontawesome' ), '1.1.6');
    
	if ($voyage_options['mobile_style'] == "1" ) {
		echo '<meta name="viewport" content="width=device-width initial-scale=1.0" />' . "\n";
		wp_enqueue_style('voyage-responsive', $theme_uri . '/css/responsive.css', array( 'voyage'), '1.2.6');
	}

	if ( !empty($voyage_options['schemecss']) ) {
		wp_enqueue_style('voyage-scheme', $voyage_options['schemecss'], array( 'voyage' ), '1.0');
	}
	
	if ( $voyage_options['sharesocial'] == 1) {
		wp_enqueue_style('voyage-sharing', $theme_uri . '/css/voyage-sharing.css', array( 'sharedaddy'));		
	}	
}
if (!is_admin())
	add_action('wp_enqueue_scripts', 'voyage_scripts_method'); 

// Load functions extends WP functions.
require( get_template_directory() . '/inc/lib-function.php' );
// Load grid functions.
require( get_template_directory() . '/inc/lib-layout.php' );
// Load content functions.
require( get_template_directory() . '/inc/lib-content.php' );
// Load Theme widgets
require( get_template_directory() . '/inc/widgets.php' );
// Load Social functuons
require( get_template_directory() . '/inc/social.php' );
// Action Hooks.
require( get_template_directory() . '/inc/hooks.php' );
if (is_admin()) {
	// Load Scheme
	require( get_template_directory() . '/scheme/scheme.php' );
	// Load theme options page and related code.
	require( get_template_directory() . '/inc/lib-admin.php' );	
	// Load theme options page and related code.
	require( get_template_directory() . '/inc/theme-options.php' );	
}
?>
