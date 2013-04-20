<?php
if ( !defined('ABSPATH')) exit;
/**
 * Voyage Theme functions: Content
 *
 * @package voyage
 * @subpackage voyage
 * @since Voyage 1.1.3
 */

if ( ! function_exists( 'voyage_screen_reader' ) ) :
// Display screen reader text
function voyage_screen_reader() {
	printf( '<div class="screen-reader-text"><a href="#content" title="%1$s">%1$s</a></div>',
		 __( 'Skip to content', 'voyage' ) );
}
endif; 

if ( ! function_exists( 'voyage_single_post_link' ) ) :
/* This function echo the link to single post view for the following:
- Aside Post
- Post without title
------------------------------------------------------------------------- */
function voyage_single_post_link() {
	if (!is_single()) {
		if ( has_post_format('aside') || has_post_format('quote') || '' == the_title_attribute( 'echo=0' ) ) { 
			printf ('<a class="single-post-link" href="%1$s" title="%1$s"><i class="icon-chevron-right"></i></a>',
				get_permalink(),
				get_the_title()	);
		} 
	}
}
endif;

if ( ! function_exists( 'voyage_display_post_thumbnail' ) ) :
// Display Large Post Thumbnail on top of the post
function voyage_display_post_thumbnail( $post_id) {
	global $voyage_options;
//	if ( has_post_thumbnail() ) { has_post_thumbnail has bug 
	$image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'full' );	
	if ( $image[1] >= ( $voyage_options['grid_pixel'] * 0.7  ) 
			&& $image[2] < 1000 ) {
		if ( !is_single() ) {
			printf ('<a href="%1$s" title="%2$s">', 
				get_permalink(),
				get_the_title()	);	
			the_post_thumbnail( 'full', array( 'class'	=> 'img-polaroid', 'title' => get_the_title() ) );
			echo '</a>';
		}
		else
			the_post_thumbnail( 'full', array( 'class'	=> 'img-polaroid', 'title' => get_the_title() ) );
	}
}
endif;

if ( ! function_exists( 'voyage_post_title' ) ) :
// Display Post Title
function voyage_post_title() {
	if (is_single()) {
		printf('<h1 class="entry-title">%1$s</h1>',
			get_the_title()	);		
	}
	else {
		printf('<h2 class="entry-title"><a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></h2>',
		get_permalink(),
		sprintf( esc_attr__( 'Permalink to %s', 'voyage' ), the_title_attribute( 'echo=0' )),
		get_the_title()	);
	}
}
endif;

if ( ! function_exists( 'voyage_author_info' ) ) :
/************************************************
Display Author Info on single post view 
 and author has filled out their description
 and showauthor option checked 
************************************************/ 
function voyage_author_info() {
	global $voyage_options;
	if ( is_single() && get_the_author_meta( 'description' ) && ( $voyage_options['showauthor'] == '1' ) ) { ?>
		<div id="author-info">
			<div id="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'voyage_author_bio_avatar_size', 64 ) ); ?>
			</div><!-- #author-avatar -->
			<div id="author-description">
				<h2><?php printf( __( 'About %s', 'voyage' ), get_the_author() ); ?></h2>
				<?php the_author_meta( 'description' ); ?>
				<div id="author-link">
					<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php printf( __( 'View all posts by %s <span class="meta-nav"></span>', 'voyage' ), get_the_author() ); ?></a>
				</div>
			</div>
		</div>
<?php 
	}
}
endif;

if ( ! function_exists( 'voyage_top_menu' ) ) :	
/************************************************
Display Top Menu
************************************************/ 
function voyage_top_menu() {
	global $voyage_options;
	
  if (has_nav_menu('top-menu') || ( $voyage_options['sociallink_top'] == 1 )) {
	if ($voyage_options['fixed_menu'] == 1)
		$class = "navbar-inverse navbar-fixed-top";
	else
		$class = "navbar-no-background";
	echo '<div id="access" class="navbar ' . $class. ' clearfix">';
	echo '<div class="navbar-inner">';
	echo '<div class="'	. voyage_container_class() . '">';	
	if ( $voyage_options['sociallink_top'] == 1 )
		voyage_social_connection('top');
	if ( has_nav_menu('top-menu') ) {
		echo '<nav id="top-navigation" class="top-menu">';
		wp_nav_menu( array(	'container' => '',
							'container_class' => '',
							'theme_location'  => 'top-menu',
							'menu_class'      => 'menu',
							'fallback_cb' 	  => false,				
							 ) );
		echo '</nav>';
	}
	echo '</div></div></div>';
  }
}
endif;

if ( ! function_exists( 'voyage_nav_menu' ) ) :
function voyage_nav_menu() {
	global $voyage_options;
	
	voyage_header_before_navbar();	
?>
<div id="mainmenu" class="navbar <?php if ($voyage_options['navbarcolor'] == 1) echo 'navbar-inverse ';  ?>clearfix">
  <div class="<?php echo voyage_container_class(); ?>">
<?php
	if ($voyage_options['nonavbar'] != '1') {
?>
  	<div class="navbar-inner">
		<nav id="section-menu" class="section-menu">	
<?php
		$header_image = get_header_image();
		if ( !empty( $header_image ) ) {
			if(function_exists('get_custom_header')) {
				$header_width = get_custom_header() -> width;
				$header_height = get_custom_header() -> height;
			}
			else {
				$header_width = HEADER_IMAGE_WIDTH;
				$header_height = HEADER_IMAGE_HEIGHT;				
			}
		}
		if ( ! empty( $header_image ) )
		  if ($voyage_options['logopos'] == 1 ) {
?>
          	<a class="brand" href="<?php echo home_url('/'); ?>"><img src="<?php header_image(); ?>" width="<?php echo $header_width; ?>" height="<?php echo $header_height; ?>" alt="<?php bloginfo('name'); ?>" /></a>
<?php	  }
		  elseif ($header_width <= 300 && $header_height <= 100) {
			$ratio = 36 / $header_height;
			$header_height = (int)$header_height * $ratio;
			$header_width = (int)$header_width  * $ratio;				 
?>
            <a class="brand visible-phone" href="<?php echo home_url('/'); ?>"><img src="<?php header_image(); ?>" width="<?php echo $header_width; ?>" height="<?php echo $header_height; ?>" alt="<?php bloginfo('name'); ?>" /></a>
<?php	  }
?>
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></a>
		<div class="nav-collapse">
<?php		if ($voyage_options['searchform'] == 1 || $voyage_options['searchform'] == 2) {
				voyage_top_search_form();
			}				
			if (has_nav_menu('section-menu')) {
				wp_nav_menu( array( 'container_class' => 'section-menu-container', 
									'theme_location' => 'section-menu',
									'menu_class'     => 'nav',
									'walker'         => new voyage_walker_nav_menu,
									'fallback_cb' 	  => false,	
					 		) );					
					
			}
	 		voyage_subsection_menu(); ?>
		</div><?php //nav-collapse ?>
	</nav>
    </div><?php //nav-inner ?>
<?php
	}
	get_sidebar('navigation'); ?>
  </div><?php //container ?>
</div><?php //navbar ?>
<?php
}
endif;

if ( ! function_exists( 'voyage_top_search_form' ) ) :	
function voyage_top_search_form() {
	global $voyage_options;
	$phone_class = '';
	if ($voyage_options['searchform'] == 2)
		$phone_class ="visible-phone"; 
?>
    <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="navbar-search pull-right <?php echo $phone_class; ?>">
    	<input type="text" class="search-query" name="s" id="s1" placeholder="<?php esc_attr_e( 'Search', 'voyage' ); ?>">
		<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'voyage' ); ?>" />
    </form>
<?php
}
endif;

if ( ! function_exists( 'voyage_subsection_menu' ) ) :	
/************************************************
Display Subsection Menu and wp_page_menu fallback
if no menu assigned
************************************************/ 
function voyage_subsection_menu() {
	if ( has_nav_menu('subsection-menu') 
			|| ( !has_nav_menu('top-menu') && !has_nav_menu('section-menu') )) {
		if (has_nav_menu('section-menu'))
			echo '<div class="clear"></div>';
		echo '<div id="subsection-menu" class="subsection-menu">';
		wp_nav_menu( array(	'container' => '',
							'container_class' => '',
							'theme_location'  => 'subsection-menu',
							'menu_class'      => 'menu',			
							 ) );
		echo '</div>';
	}
}
endif;

if ( ! function_exists( 'voyage_branding' ) ) :
function voyage_branding() {
	global $voyage_options;
?>
<div id="branding" class="<?php echo voyage_container_class(); ?> clearfix">
  <div class="<?php echo voyage_grid_full(); ?> clearfix">
<?php
	voyage_header_branding();
	get_sidebar('header');
	if ($voyage_options['searchform'] == 4)
		voyage_top_search_form();
	$header_image = get_header_image();
	if ( !empty( $header_image ) ) {
		if(function_exists('get_custom_header')) {
			$header_width = get_custom_header() -> width;
			$header_height = get_custom_header() -> height;
		}
		else {
			$header_width = HEADER_IMAGE_WIDTH;
			$header_height = HEADER_IMAGE_HEIGHT;				
		}
		$header_class = "";
		if 	($header_width <= 300 && $header_height <= 100){
			$header_class = "hidden-phone";
		}
	}

	if (! empty( $header_image ) && $voyage_options['logopos'] == 2) { ?>
		<div id="logo" class="<?php echo $header_class; ?>">
          <a href="<?php echo home_url('/'); ?>"><img src="<?php header_image(); ?>" width="<?php echo $header_width; ?>" height="<?php echo $header_height; ?>" alt="<?php bloginfo('name'); ?>" /></a>
		  </div>
<?php
	} else { ?>
		<hgroup>
		  <h3 id="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h3>
		  <h3 id="site-description"><?php bloginfo( 'description' ); ?></h3>
		</hgroup>	
<?php
	}
?>	  
  </div>
</div>
<?php
}
endif;

if ( ! function_exists( 'voyage_default_widgets' ) ) :	
/************************************************
Display defaul tabbed widgets
************************************************/ 
function voyage_default_widgets() {
?>
<li class="widget-container voyage_navigation">
	<ul id="vntTab" class="nav nav-tabs">
		<li class="active"><a href="#category" data-toggle="tab"><?php _e('Categories','voyage'); ?></a></li>
		<li><a href="#archive" data-toggle="tab"><?php _e('Archives','voyage'); ?></a></li>
        <li><a href="#tag" data-toggle="tab"><?php _e('Tags','voyage'); ?></a></li>
    </ul>
	<div id="vntTabContent" class="tab-content">
        <div class="widget_categories tab-pane fadein active" id="category">
			<ul>
			<?php
				$cat_args = array();
				$cat_args['show_count'] = 1;
				$cat_args['title_li'] = '';
				$cat_args['exclude'] = 1;
				wp_list_categories( $cat_args ); ?>
			</ul>
        </div>
        <div class="widget_archive tab-pane fade" id="archive">
			<ul>
			<?php
				$arc_args = array();
				$arc_args['type'] = 'monthly';
				$arc_args['limit'] = 10;
				wp_get_archives( $arc_args ); ?>
			</ul>
        </div>
        <div class="widget_tag_cloud tab-pane fade" id="tag">
			<ul>
			<?php
				$tag_args = array();
				wp_tag_cloud( $tag_args ); 
			?>
			</ul>
        </div>
	</div>
</li>
<li id="recent_post" class="widget-container widget_recent_entries">
	<h4 class="widget-title"><?php _e( 'Recent Posts', 'voyage' ); ?></h4>
	<ul>
<?php	$args = array( 'post_status' => 'publish' );
		$recent_posts = wp_get_recent_posts($args);
		foreach( $recent_posts as $recent ){
			echo '<li><a href="' . get_permalink($recent["ID"]) . '" title="Look '.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </li> ';
		}
?>
	</ul>
</li>
<?php
}
endif;

if ( ! function_exists( 'voyage_carousel_controls' ) ) :	
/************************************************
Display carousel_controls
************************************************/ 
function voyage_carousel_controls($count) {
?>
	<a id="featured-prev" class="carousel-control left">&lsaquo;</a>
	<a id="featured-next" class="carousel-control right">&rsaquo;</a>
	<div class="carousel-nav clearfix">
<?php
		for ($i=0;$i<$count;$i++) {
			if ($i == 0) 
			  echo '<a href="#" class="active" data-to="' . ($i+1) . '"></a>';
			else
			  echo '<a href="#" data-to="' . ($i+1) . '"></a>';
		}
?>	  	
	</div>
<?php
}
endif;

if ( ! function_exists( 'voyage_title_bar' ) ) :	
function voyage_title_bar() {
	global $voyage_options;
	if ($voyage_options['sociallink_middle'] == 1) {
?>
	<div id="title" class="titlebar clearfix">
	  <div class="<?php echo voyage_container_class(); ?>">
		<div class="<?php echo voyage_grid_full(); ?>">
<?php
		if ($voyage_options['sociallink_middle'] == 1)
			voyage_social_connection('middle');
?>
	  	</div>
	  </div>
	</div>
<?php
	}
}
endif;
?>
