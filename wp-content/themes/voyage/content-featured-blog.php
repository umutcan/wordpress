<?php
	global $more, $voyage_options;
	$more = 0;	
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	
<?php	
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
	if ( $image[1] > 0 ) {  // has_post_thumbnail() has bug - this is the wordaround.	
?>
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'voyage' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_post_thumbnail( 'full', array( 'class' => 'carousel-image-blog', 'title' => get_the_title() ) ); ?></a>
		<header class="carousel-caption">
		<h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'voyage' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>	
    	<?php the_excerpt(); ?>			
		</header>	
<?php
	} elseif (has_post_format('image') ) {
		the_content( '' );	
?>
		<header class="carousel-caption">
			<h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'voyage' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		</header>
<?php
	}
	else { ?>
		<header class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'voyage' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		</header>
		<div class="entry-content clearfix">							
			<?php the_content( '' ); ?>		
		</div>	
<?php
	} ?>
	
</article>
