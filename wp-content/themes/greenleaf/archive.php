<?php
/**
 * @package WordPress
 * @subpackage greenleaf_Theme
 */

get_header();
?>

	<?php if (have_posts()) : ?>

		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
		<?php /* If this is a category archive */ if (is_category()) { ?>
			<h1 class="acenter">Archive for the <?php single_cat_title(); ?> Category</h1>
		<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
			<h1 class="acenter">Posts Tagged <?php single_tag_title(); ?></h1>
		<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			<h1 class="acenter">Archive for <?php the_time('F jS, Y'); ?></h1>
		<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<h1 class="acenter">Archive for <?php the_time('F, Y'); ?></h1>
		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<h1 class="acenter">Archive for <?php the_time('Y'); ?></h1>
		<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			<h1 class="acenter">Author Archive</h1>
		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<h1 class="acenter">Blog Archives</h1>
		<?php } ?>

		<?php $counter = 0; ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="col3">
				<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
					<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

					<div class="entry">
						<?php the_excerpt('Read the rest of this entry &raquo;'); ?>
						<div id="morepage-list"><?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?></div>
						<p><br /><a href="<?php the_permalink() ?>" class="button">Read more &rarr;</a></p>
					</div>

					<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted on <?php the_time('F jS, Y') ?> in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments&nbsp;&#187;', '1 Comment&nbsp;&#187;', '% Comments&nbsp;&#187;'); ?></p>
				</div>
			</div>
			<?php
				$counter++;
				if ($counter % 3 == 0) echo '<div class="cboth"></div>' ;
			?>
		<?php endwhile; ?>

		<div class="cboth pagination">
			<?php greenleaf_pagenavi(); ?>
		</div>

	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
		} else {
			echo("<h2 class='center'>No posts found.</h2>");
		}
		get_search_form();

	endif;
?>

<?php get_footer(); ?>
