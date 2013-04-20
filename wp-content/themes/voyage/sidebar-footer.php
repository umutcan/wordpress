<?php
/**
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>

<div id="footer-widget-area" class="visible-desktop clearfix" role="complementary">
<?php
	global $voyage_options;	
	if ( is_active_sidebar( 'first-footer-widget-area' ) && $voyage_options['column_footer1'] > 0 ) : ?>
		<div id="first" class="<?php echo voyage_grid_columns($voyage_options['column_footer1']); ?> widget-area">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
			</ul>
		</div><!-- #first .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'second-footer-widget-area' ) && $voyage_options['column_footer2'] > 0) : ?>
		<div id="second" class="<?php echo voyage_grid_columns($voyage_options['column_footer2']); ?> widget-area">	
			<ul class="xoxo">
				<?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
			</ul>
		</div><!-- #second .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'third-footer-widget-area' ) && $voyage_options['column_footer3'] ) : ?>
		<div id="third" class="<?php echo voyage_grid_columns($voyage_options['column_footer3']); ?> widget-area">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'third-footer-widget-area' ); ?>
			</ul>
		</div><!-- #third .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'fourth-footer-widget-area' ) && $voyage_options['column_footer4'] ) : ?>
		<div id="fourth" class="<?php echo voyage_grid_columns($voyage_options['column_footer4']); ?> widget-area">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'fourth-footer-widget-area' ); ?>
			</ul>
		</div><!-- #fourth .widget-area -->
	<?php endif; ?>
</div><!-- #footer-widget-area -->
