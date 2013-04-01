<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @subpackage New life
 * @since New life
 */


get_header(); ?>
<?php get_sidebar(); ?>
	<div class="content">
		<div id="post-0" class="post error404 not-found">
			<h1 class="title_content"><?php _e( 'Page not Found', 'newlife' ); ?></h1>
			<div class="entry-content">
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'newlife' ); ?></p>
					
			</div><!-- .entry-content -->
		</div><!-- #post-0 -->
	</div><!-- #content -->
<?php get_footer(); ?>
