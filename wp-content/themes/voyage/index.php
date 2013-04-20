<?php
/**
 * The main template file.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since 1.0
 */
get_header(); ?>

<div id="content" class="<?php echo voyage_grid_class(); ?>" role="main">
<?php
	if ( have_posts() ) :
		voyage_content_nav( 'nav-above' );
		while ( have_posts() ) :
			the_post();
			get_template_part( 'content', get_post_format() );
		endwhile;				
		voyage_content_nav( 'nav-below' );
	elseif ( current_user_can( 'edit_posts' ) ) :
		get_template_part( 'content-none', 'index' );
	endif; ?>						
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
