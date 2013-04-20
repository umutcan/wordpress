<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to voyage_comment() which is
 * located in the functions.php file.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>

	<div id="comments" class="comments-area clearfix">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'voyage' ); ?></p>
	</div>
	<?php
			return;
		endif;
	?>

	<?php if ( have_comments() ) : ?>
		<h4 class="comments-title">
		<?php
			printf( _n( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'voyage' ),
			number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );	?>
		</h4>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Comments navigate ?>
		<nav id="comment-nav-above" class="site-navigation comment-navigation">
			<h5 class="assistive-text"><?php _e( 'Comment navigation', 'voyage' ); ?></h5>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'voyage' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'voyage' ) ); ?></div>
		</nav>
		<?php endif; ?>

		<ol class="commentlist">
			<?php
				wp_list_comments( array( 'callback' => 'voyage_comment' ) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Comments Navigation  ?>
		<nav id="comment-nav-below" class="site-navigation comment-navigation">
			<h5 class="assistive-text"><?php _e( 'Comment navigation', 'voyage' ); ?></h5>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'voyage' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'voyage' ) ); ?></div>
		</nav>
		<?php endif;?>

	<?php endif; ?>

	<?php
		// Comments closed
		global $voyage_options;
		if ( ! comments_open() && 'post' == get_post_type() && $voyage_options['pp_commoff'] == 0) : ?>
			<p class="nocomments"><?php _e( 'Comments are closed.' , 'voyage' ); ?></p>
		<?php endif; ?>

	<?php comment_form( ); ?>

</div>