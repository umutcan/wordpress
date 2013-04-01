<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 *
 * @subpackage New life
 * @since New life
 */
?>

<div id="sidebar">
	<ul id="widgets">
		<?php
		 if ( !function_exists('dynamic_sidebar') || ! dynamic_sidebar( 'primary-widget-area' ) ) { 
			if ( ! dynamic_sidebar( ) ) { ?>

				<li id="archives" class="widget-container">
					<h3 class="widgettitle"><?php _e( 'Archives', 'newlife' ); ?></h3>
					<?php wp_get_archives( 'type=monthly' ); ?>
				</li>
				<li id="meta" class="widget-container">
					<h3 class="widgettitle"><?php _e( 'Meta', 'newlife' ); ?></h3>
					<ul>
						<li><?php wp_register(); ?></li>
						<li><?php wp_loginout(); ?></li>
						<li><?php wp_meta(); ?></li>
					</ul>
				</li>
		<?php } }; // end primary widget area ?>
	</ul>
	<?php
	// A second sidebar for widgets, just because.
	if ( is_active_sidebar( 'secondary-widget-area' ) ) : ?>
		<div id="widgets">
			<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
		</div><!-- #secondary .widget-area -->
	<?php endif; ?>
</div>	

