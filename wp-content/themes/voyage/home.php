<?php
/**
 * Default Home Page
 * 
 * @package Voyage
 * @subpackage Voyage
 * @since 1.2.0
 */
	get_header();
	
    global $voyage_options;
	if ( 'page' == get_option( 'show_on_front' ) || $voyage_options['homepage'] == 3) {
?>  
	<div id="content" class="<?php echo voyage_grid_class(); ?>" role="main">
		<?php if ( have_posts() ) :
			voyage_content_nav( 'nav-above' );
			while ( have_posts() ) : the_post();
				get_template_part( 'content', get_post_format() );
			endwhile;				
			voyage_content_nav( 'nav-below' );
		elseif ( current_user_can( 'edit_posts' ) ) :
			get_template_part( 'content-none', 'index' );
		endif; ?>						
	</div>
<?php get_sidebar(); ?>
<?php
	}
	elseif ($voyage_options['homepage'] == 1) {
 		get_template_part( 'page-templates/featured'  ); 		
	}
	elseif ($voyage_options['homepage'] == 2) {
 		get_template_part( 'page-templates/landing'  ); 		
	}	
	elseif ($voyage_options['homepage'] == 4) {
		get_template_part( 'page-templates/portfolio' );
	}
	elseif ($voyage_options['homepage'] == 5) {
		get_template_part( 'page-templates/blog-sticky' );
	}
	get_footer();
?>


