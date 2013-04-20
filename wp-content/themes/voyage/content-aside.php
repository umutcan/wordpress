<?php
/**
 * The default template for displaying content
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.1.2
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	
	<header class="entry-header">
<?php
		voyage_posted_category();
		voyage_post_title(); ?>
	</header>
	<div class="entry-content clearfix">
<?php
		voyage_posted_on();
		the_content( __( '<span class="more-link btn btn-small">read more<span class="meta-nav"></i></span></span>', 'voyage' ) ); ?>
	</div>
<?php	voyage_single_post_link(); ?>
	<footer class="entry-footer clearfix">
<?php
		voyage_posted_in();
		voyage_author_info();
?>
	</footer>
</article>
