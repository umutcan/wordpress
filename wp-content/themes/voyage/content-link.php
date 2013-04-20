<?php
/**
 * The default template for displaying link post
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.1.2
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content clearfix">
<?php
		the_content();
?>
	</div><!-- .entry-content -->
	<footer class="entry-footer clearfix">
<?php
		global $voyage_entry_meta;
		$voyage_entry_meta = 1;
		voyage_post_summary_meta(); ?>
	</footer>
</article>
