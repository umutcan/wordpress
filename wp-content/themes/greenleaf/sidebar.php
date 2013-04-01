<?php
/**
 * @package WordPress
 * @subpackage greenleaf_Theme
 */
?>
	<div id="col-right">
			<?php 	/* Widgetized sidebar */
					if ( !dynamic_sidebar() ) : ?>

			<?php if ( is_404() || is_category() || is_day() || is_month() ||
						is_year() || is_search() || is_paged() ) {
			?> 
			<ul><li>

			<?php /* If this is a 404 page */ if (is_404()) { ?>
			<?php /* If this is a category archive */ } elseif (is_category()) { ?>
			<p><strong>You are currently browsing the archives for the <?php single_cat_title(''); ?> category.</strong></p>

			<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<p><strong>You are currently browsing the <a href="<?php echo home_url(); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the day <?php the_time('l, F jS, Y'); ?>.</strong></p>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<p><strong>You are currently browsing the <a href="<?php echo home_url(); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <?php the_time('F, Y'); ?>.</strong></p>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<p><strong>You are currently browsing the <a href="<?php echo home_url(); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the year <?php the_time('Y'); ?>.</strong></p>

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<p><strong>You have searched the <a href="<?php echo home_url(); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <strong>'<?php the_search_query(); ?>'</strong>. If you are unable to find anything in these search results, you can try one of these links.</strong></p>

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<p><strong>You are currently browsing the <a href="<?php echo home_url(); ?>/"><?php echo bloginfo('name'); ?></a> blog archives.</strong></p>

			<?php } ?>

			</li></ul>
		<?php } ?>
		<?php endif; ?>
	</div>
