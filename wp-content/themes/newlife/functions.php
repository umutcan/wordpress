<?php

if ( ! isset( $content_width ) )
	$content_width = 1037;
	
if ( ! function_exists( 'newlife_setup' ) ):
	function newlife_setup() {
		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();
		// This theme uses post thumbnails
		add_theme_support( 'post-thumbnails' );
		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );
		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'newlife', get_template_directory() . '/languages' );
		// This theme uses wp_nav_menu() in one location.
		register_nav_menu( 'menu', __( 'Primary Menu', 'newlife' ) );

		// Add a way for the custom header to be styled in the admin panel that controls
		// custom headers. See newlife_admin_header_style(), below.
		$custom_header_support = array(
				// The default image to use.
				// The %s is a placeholder for the theme template directory URI.
				'default-image' => '%s/image/legend.jpg',
				// The height and width of our custom header.
				'width' => apply_filters( 'newlife_header_image_width', 732 ),
				'height' => apply_filters( 'newlife_header_image_height', 337 ),
				// Support flexible heights.
				'flex-height' => true,
				// Don't support text inside the header image.
				'header-text' => false,
				// Callback for styling the header preview in the admin.
				'wp-head-callback' => 'newlife_admin_header_style',
			);
	
		add_theme_support( 'custom-header', $custom_header_support );
	
	
		if ( ! function_exists( 'get_custom_header' ) ) {
				// This is all for compatibility with versions of WordPress prior to 3.4.
				define( 'HEADER_TEXTCOLOR', '' );
				define( 'NO_HEADER_TEXT', true );
				define( 'HEADER_IMAGE', $custom_header_support['default-image'] );
				define( 'HEADER_IMAGE_WIDTH', $custom_header_support['width'] );
				define( 'HEADER_IMAGE_HEIGHT', $custom_header_support['height'] );
				add_custom_image_header( '', $custom_header_support['wp-head-callback'] );
		}
			
		// Add a way for the custom background to be styled in the admin panel that controls
		// custom headers.
		add_theme_support( 'custom-background' );

		if ( ! function_exists( 'get_custom_background' ) ) {
				// This is all for compatibility with versions of WordPress prior to 3.4.
				add_custom_background();
		}

		// We'll be using post thumbnails for custom header images on home pages.
		// We want them to be 732 pixels wide by 337 pixels tall.
		// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
		set_post_thumbnail_size( $custom_header_support['width'], $custom_header_support['height'], true );
		// This theme uses sidebar.
		register_sidebar();
}
endif;

function newlife_widgets_init() {
		// Area 1, located at the top of the sidebar.
		register_sidebar( array(
			'name' => __( 'Primary Widget Area', 'newlife' ),
			'id' => 'primary-widget-area',
			'description' => __( 'The primary widget area', 'newlife' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );

		// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
		register_sidebar( array(
			'name' => __( 'Secondary Widget Area', 'newlife' ),
			'id' => 'secondary-widget-area',
			'description' => __( 'The secondary widget area', 'newlife' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
}



function newlife_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case '' :
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
				<?php echo get_avatar( $comment, 40 ); ?>
				<?php printf( __( '%s <span class="says">says:</span>', 'newlife' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
			</div><!-- .comment-author .vcard -->
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'newlife' ); ?></em>
				<br />
			<?php endif; ?>

			<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
				<?php
					/* translators: 1: date, 2: time */
					printf( __( '%1$s at %2$s', 'newlife' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'newlife' ), ' ' );
				?>
			</div><!-- .comment-meta .commentmetadata -->

			<div class="comment-body"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</div><!-- #comment-##  -->

		<?php
				break;
			case 'pingback'  :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'newlife' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'newlife' ), ' ' ); ?></p>
		<?php
				break;
		endswitch;
}


function newlife_custom_excerpt_length( $length ) {
		return 20;
}

if ( ! function_exists( 'newlife_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via custom-header hook in newlife_setup().
 *
 * @since New Life 1.2
 */
function newlife_admin_header_style() {
		?>
		<style type="text/css">
		/* Shows the same border as on front end */
		#headimg {
			border-bottom: 1px solid #000;
			border-top: 4px solid #000;
		}
		/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
			#headimg #name { }
			#headimg #desc { }
		*/
		</style>
		<?php
}
endif;


function newlife_script() {
		wp_enqueue_script('jquery'); ?>
		<script type="text/javascript">
				var search='<?php echo __('Search ...','newlife');?>';
				var url = theme_url = '<?php echo get_stylesheet_directory_uri(); ?>';
		</script>
		<?php wp_enqueue_script( 'newlife-main-script', get_stylesheet_directory_uri().'/js/main.js' ); 
		wp_enqueue_script( 'comment-reply' );
		wp_enqueue_style( 'newlife-style', get_stylesheet_uri() ); 
		wp_register_style( 'newlife-style-ie', get_stylesheet_directory_uri() . '/ie.css' );
		$GLOBALS['wp_styles']->add_data( 'newlife-style-ie', 'conditional', 'IE' );
		wp_enqueue_style( 'newlife-style-ie' );
}    

// filter function for wp_title
function newlife_filter_wp_title( $old_title, $sep, $sep_location ){
		// add padding to the sep
		$ssep = ' ' . $sep . ' ';
			
		// find the type of index page this is
		if( is_category() ) 
				$insert = $ssep . 'Category';
		elseif( is_tag() ) 
				$insert = $ssep . 'Tag';
		elseif( is_author() ) 
				$insert = $ssep . 'Author';
		elseif( is_year() || is_month() || is_day() ) 
				$insert = $ssep . 'Archives';
		else 
				$insert = NULL;
			
		// get the page number we're on (index)
		if( get_query_var( 'paged' ) )
				$num = $ssep . 'page ' . get_query_var( 'paged' );
			
		// get the page number we're on (multipage post)
		elseif( get_query_var( 'page' ) )
				$num = $ssep . 'page ' . get_query_var( 'page' );
			
		// else
		else $num = NULL;
			
		// concoct and return new title
return get_bloginfo( 'name' ) . $insert . $old_title . $num;
}

add_filter( 'wp_title', 'newlife_filter_wp_title', 10, 3 );
add_action( 'wp_enqueue_scripts', 'newlife_script' );
add_action( 'after_setup_theme', 'newlife_setup' );
add_filter( 'excerpt_length', 'newlife_custom_excerpt_length', 999 );
add_action( 'widgets_init', 'newlife_widgets_init' );
?>