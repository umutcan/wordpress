<?php 
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 * @subpackage New life
 * @since New life
 */

get_header(); ?>
<?php get_sidebar(); ?>
<div class="content">
	<div id="page_child">
		<?php	if ( have_posts() ) the_post();?>
			<div class="title_content_post">
				<?php if ( is_day() ) : ?>
					<?php printf( __( 'Daily Archives:', 'newlife').' <span>%s</span>', get_the_date() ); ?>
					<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Monthly Archives:', 'newlife').' <span>%s</span>', get_the_date( _x( 'F Y', 'monthly archives date format', 'newlife' ) ) ); ?>
						<?php elseif ( is_year() ) : ?>
							<?php printf( __( 'Yearly Archives:', 'newlife').' <span>%s</span>' , get_the_date( _x( 'Y', 'yearly archives date format', 'newlife' ) ) ); ?>
								<?php else : ?>
									<?php _e( 'Blog Archives', 'newlife' ); ?>
				<?php endif; ?>
			</div>
		<?php
			rewind_posts();
		?>
		<?php while ( have_posts() ) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="date_back">
					<div class="date_post">
						<p class='month'><?php the_date('M');?></p>
						<p class='day'><?php the_time('j');?></p>
					</div>		
				</div>
				<div class="title_content_small"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
				<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
					<div class="entry-summary">
						<?php the_excerpt(); ?>
					</div><!-- .entry-summary -->
				<?php else : ?>
					<div class="entry-content">
						<?php the_content( __( 'Continue reading', 'newlife' ).' <span class="meta-nav">&rarr;</span>' ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'newlife' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
				<?php endif; ?>
				<div class="entry-utility">
					<?php if ( count( get_the_category() ) ) : ?>
						<span class="cat-links">
							<?php printf( '<span class="%1$s">'.__( 'Posted in', 'newlife' ).'</span> %2$s', 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
						</span>
						<span class="meta-sep">|</span>
					<?php endif; ?>
					<?php
						$tags_list = get_the_tag_list( '', ', ' );
						if ( $tags_list ):
					?>
					<span class="tag-links">
						<?php printf( '<span class="%1$s">'.__( 'Tagged', 'newlife' ).'</span> %2$s', 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					</span>
					<span class="meta-sep">|</span>
						<?php endif; ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'newlife' ), __( '1 Comment', 'newlife' ),'% '. __( 'Comments', 'newlife' ), __( 'Comments Off', 'newlife' ) ); ?></span>
					<?php edit_post_link( __( 'Edit', 'newlife' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry-utility -->
			</div><!-- #post-## -->
			<div class='clear'></div>	
			<?php comments_template( '', true ); ?>
		<?php endwhile;?>
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			<?php previous_posts_link( '<span class="read_more">'.__( 'Newer', 'newlife' ).' &rarr;</span>' ); ?>
			<?php next_posts_link( '<span class="read_more">&larr; '.__( 'Older', 'newlife' ).'</span>' ); ?>
		<?php endif; ?>
	</div><!-- #page_child -->
</div><!-- #content -->
<?php get_footer(); ?>
