<?php get_header(); ?>
	<div id="home_head">
		<div id="block_head"></div>
		<div id="block_researches">
			<?php if ( get_header_image() ) { ?>
			<div id="legend_no_background">
				<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" />
			</div>				
			<?php } else { ?>
			<div id="legend_background">
			</div>
			<?php } ?>
			<div id="researches">
				<?php $i=0;
				$number_of_posts=3;
				while (have_posts() && $i<$number_of_posts) : the_post(); ?>
					<div class="title_researches"><p><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></p></div>
					<div class="content_researches"><?php echo the_excerpt(); ?></div>
					<?php $i++;
				endwhile; ?>
			</div><!-- #researches -->
			<div class='clear'></div>
		</div><!-- #block_researches -->
		<div id="search_block">
			<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<p><input type="text" class="field" name="s" id="s" value=" <?php _e('Search ...','newlife');?>"/></p>
			</form>
		</div><!-- #search_block -->
		<div id="angle"></div>
	</div><!-- #home_head-->
	<?php get_sidebar(); ?>
	<div class="content_home">
		<div id="page_child">
			<?php 
			$i=0;
			$number_of_posts=2;
			while ( have_posts() && $i < $number_of_posts) : the_post();?>
				<div class="page_content<?php if($i+1 == $number_of_posts) echo "_last"; ?>">
					<div class="title_content_post" ><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
					<?php
						if ( has_post_thumbnail() )
						{
							the_post_thumbnail('thumbnail');
						}
						//the_excerpt('<span class="read_more">'.__( 'read more', 'newlife' ).'</span>'); 
						the_content();
					?>
				</div><!-- #page_content -->
				<?php if ($i==0):?> <div class="line_home"></div><?php endif;?>
			<?php $i++;endwhile; //end of The Loop ?>
		<div class="clear"></div>
		<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'newlife' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'newlife' ) ); ?></div>
				</div><!-- #nav-below -->
		<?php endif; ?>
		</div><!-- #page_child -->
	</div><!-- #content -->
<?php get_footer(); ?>
