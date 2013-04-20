<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	global $more; //WordPress global variable
	global $voyage_thumbnail, $voyage_display_excerpt, $voyage_entry_meta;
	
	if (! isset($voyage_display_excerpt) ) {
		$voyage_display_excerpt = 1;
	}
	if (! isset($voyage_thumbnail) ) {
		$voyage_thumbnail = 'thumbnail';
	}
	if (! isset($voyage_entry_meta) ) {
		$voyage_entry_meta = 1;
	}
	$displayed_thumnnail = 0;
	if ( has_post_thumbnail() && ($voyage_thumbnail != 'none') ) {
		$displayed_thumnnail = 1;
?>	
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'voyage' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_post_thumbnail($voyage_thumbnail, array( 'class' => 'post-thumbnail', 'title' => get_the_title() ) ); ?></a>
    <?php
		if ( is_sticky() ) {
			echo '<div class="featured-container">';
			if (has_action('voyage_featured_logo') )
				do_action('voyage_featured_logo');
			else
				echo '<p><i class="icon-star"></i></p>';
			echo '</div>';
		}	
	}
	?>
	<header class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'voyage' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	</header>
	<div class="entry-summary clearfix">
<?php 
		if (has_post_format('aside'))
			voyage_posted_on();
		if (has_post_format('link') || 
				has_post_format('aside') ||
				has_post_format('quote') ) {
			$more = 0;
			the_content('');			
		}
		elseif ( $voyage_display_excerpt == 1) {
			the_excerpt();	
		}
		elseif ($voyage_display_excerpt == 2) {
			$more = 0;
			if ($displayed_thumnnail)
				add_filter( 'the_content', 'remove_images', 100 );
			the_content( '' );		
			if ($displayed_thumnnail)
				remove_filter( 'the_content', 'remove_images', 100 );
		}
?>
	</div>
<?php
	voyage_single_post_link();
	voyage_post_summary_meta();
?>
</article>
