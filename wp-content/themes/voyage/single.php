<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
	get_header();
	$po_layout = get_post_meta($post->ID, '_voyage_layout', true);
?>
	<div id="content" class="<?php echo $po_layout ? voyage_grid_full() : voyage_grid_class(); ?>" role="main">
<?php	while ( have_posts() ) :
			the_post();
			get_template_part( 'content', get_post_format() ); ?>

			<nav id="nav-single" class="clearfix">
				<h3 class="assistive-text"><?php _e( 'Post navigation', 'voyage' ); ?></h3>
				<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '<i class="icon-chevron-left"></i>', 'Previous post link', 'voyage' ) . '</span> %title' ); ?></span>
				<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '<i class="icon-chevron-right"></i>', 'Next post link', 'voyage' ) . '</span>' ); ?></span>
			</nav>
			<?php comments_template( '', true ); ?>
<?php	endwhile; // end of the loop. ?>
	</div>
<?php if (empty($po_layout)) get_sidebar(); ?>
<?php get_footer(); ?>
