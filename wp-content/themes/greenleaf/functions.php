<?php
/**
 * @package WordPress
 * @subpackage greenleaf_Theme
 */
 

// enable Theme Options page
require_once ( get_stylesheet_directory() . '/theme-options.php' );


// set content width
if ( ! isset( $content_width ) ) $content_width = 625;


// load JavaScript scripts
// excluding Superfish script for admin users
function greenleaf_scripts() {
	wp_enqueue_script('greenleaf_script', get_template_directory_uri() . '/js/greenleaf.js');
	wp_localize_script('greenleaf_script', 'greenleaf_vars', greenleaf_localize_vars());
	if (!is_admin())  {
		wp_enqueue_script('greenleaf_superfish', get_template_directory_uri() . '/js/superfish.js');
	}
}
add_action('wp_enqueue_scripts', 'greenleaf_scripts');


// load JavaScript scripts for Theme Options page
function greenleaf_theme_options_scripts() {
	wp_enqueue_script('greenleaf_ajaxupload', get_template_directory_uri() . '/js/ajaxupload.js');
	wp_enqueue_script('greenleaf_theme_options_scripts', get_template_directory_uri() . '/js/greenleaf-theme-options.js');
}
add_action('appearance_page_greenleaf_theme_options', 'greenleaf_theme_options_scripts');


// localize GreenLeaf JavaScript script variables
function greenleaf_localize_vars(){
	return array(
		'theme_url' => get_bloginfo('template_url')
    );
}

// load Google fonts
function greenleaf_stylesheets() {
	wp_register_style('greenleaf_stylesheet1', 'http://fonts.googleapis.com/css?family=Crafty+Girls');
	wp_register_style('greenleaf_stylesheet2', 'http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:regular,bold');
	wp_enqueue_style('greenleaf_stylesheet1');
	wp_enqueue_style('greenleaf_stylesheet2');
}
add_action('wp_enqueue_scripts', 'greenleaf_stylesheets');


// define the variable for the Google Analytics ID
function greenleaf_localize_ga_var(){
	$options = get_option('greenleaf_theme_options');
	return array(
		'ga_code' => $options['ga_code']
    );
}


// enqueue the script
function greenleaf_enqueue_ga_script() {
	$options = get_option('greenleaf_theme_options');
	// Only display the javascript if a Google Analytics ID has been defined in theme options page
	if ( isset($options['ga_code']) && $options['ga_code'] != "" ) {
		wp_enqueue_script('greenleaf_ga', get_template_directory_uri() .'/js/ga.js');
		wp_localize_script('greenleaf_ga', 'greenleaf_ga_var', greenleaf_localize_ga_var());
	}
}
add_action('wp_enqueue_scripts', 'greenleaf_enqueue_ga_script');


// enable feed links
add_theme_support('automatic-feed-links');


// enable dynamic sidebar
function greenleaf_sidebars_init() {
	register_sidebar(array(
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>',
	));
}
add_action('widgets_init', 'greenleaf_sidebars_init');


// enable WordPress menus
add_action('init', 'greenleaf_register_menu');
function greenleaf_register_menu() {
	register_nav_menu('greenleaf_nav', 'Main Navigation');
}


// redirect to theme options page after theme activation
if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
	echo("<script>self.location='".admin_url()."themes.php?page=greenleaf_theme_options';</script>");
}


// template for comments and pingbacks
function greenleaf_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( '%s <span class="says">says:</span>', sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation">Your comment is awaiting moderation.</em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( '%1$s at %2$s', get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( '(Edit)', ' ' );
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
		<p>Pingback: <?php comment_author_link(); ?><?php edit_comment_link( '(Edit)', ' ' ); ?></p>
	<?php
			break;
	endswitch;
}


// page pagination
function greenleaf_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	
	if(empty($prelabel)) {
		$prelabel  = '<strong>&laquo;</strong>';
	}
	if(empty($nxtlabel)) {
		$nxtlabel = '<strong>&raquo;</strong>';
	}
	$half_pages_to_show = round($pages_to_show/2);
	if (!is_single()) {
		if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);		
		} else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);		
		}
		$fromwhere = $matches[1];
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		if($max_page > 1 || $always_show) {
			echo "$before <div class='nav'>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">&laquo; First</a>';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='on'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo '<a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
			}
			echo "</div> $after";
		}
	}
}

?>
