<?php
/**
 * The default template for displaying content
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	
<?php
	voyage_display_post_thumbnail($post->ID); ?>
	
	<header class="entry-header">
<?php
		voyage_posted_category();
		voyage_post_title();
		voyage_posted_on();
?>
	</header>
	<div class="entry-content clearfix">
<?php
		the_content( __( '<span class="more-link btn btn-small">read more<span class="meta-nav"></i></span></span>', 'voyage' ) );

		wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'voyage' ) . '</span>', 'after' => '</div>' ) );  ?>
	</div><!-- .entry-content -->
	<?php voyage_single_post_link(); ?>		
	<footer class="entry-footer clearfix">
<?php
		voyage_posted_in();
		voyage_social_post_bottom();
		voyage_author_info();
?>
	</footer>
</article>
