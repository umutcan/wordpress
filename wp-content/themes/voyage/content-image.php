<?php
/**
 * The default template for displaying image post
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
<?php
		voyage_posted_category();
		voyage_post_title();
		voyage_posted_on();
?>
	</header>
	<div class="entry-content clearfix">
<?php
		the_content( __( '<span class="more-link btn btn-small">read more<span class="meta-nav"></i></span></span>', 'voyage' ) ); ?>
	</div>
		
	<footer class="entry-footer">
<?php
		voyage_posted_in();
		voyage_social_post_bottom();
?>
	</footer>
</article>
