<?php
/**
 * @package WordPress
 * @subpackage greenleaf_Theme
 */

get_header(); ?>

	<?php
		$options = get_option('greenleaf_theme_options');
		if ( ($options['greenleaf_main_punchline'] != "") || ($options['greenleaf_headline'] != "")) {
	?>
		<div id="banner">
			<?php
				if ($options['greenleaf_main_punchline'] != "") echo "<div id=\"banner-headline\">".stripslashes($options['greenleaf_main_punchline'])."</div>";
				if ($options['greenleaf_headline'] != "") echo "<div id=\"banner-secondary\">".stripslashes($options['greenleaf_headline'])."</div>";
			?>
		</div>
	<?php } ?>
	<br />

	<?php if (have_posts()) : ?>
	
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

	<?php else : ?>

		<h1 class="acenter">Not Found</h1>
		<p class="acenter">Sorry, but you are looking for something that isn't here.</p>
		<?php get_search_form(); ?>

	<?php endif; ?>

<?php get_footer(); ?>
