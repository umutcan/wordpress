<?php
/**
 * The template for displaying all pages.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
get_header(); ?>
<div id="content" class="<?php echo voyage_grid_class(); ?>" role="main">

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' ); ?>

			<?php comments_template( '', true ); ?>

		<?php endwhile; // end of the loop. ?>
</div>
<?php get_sidebar(); ?>	
<?php get_footer(); ?>
