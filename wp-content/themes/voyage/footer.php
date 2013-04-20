<?php
/**
 * The template for displaying the footer.
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>
</div><!-- #container -->
</div><!-- #main -->
<?php global $voyage_options; ?>
<div id="footer" role="contentinfo">
	<div class="<?php echo voyage_container_class(); ?> clearfix">
		<?php get_sidebar( 'footer' ); ?>
		<div id="footer-menu" class="<?php echo voyage_grid_full(); ?>" role="complementary">		
		<?php if (has_nav_menu('footer-menu')) {
			wp_nav_menu( array( 'container_class' => 'footer-menu', 'theme_location' => 'footer-menu' ) );
       	} ?>	
		<?php if ( $voyage_options['sociallink_bottom'] == 1 ) {
			voyage_social_connection('bottom');
		} ?>			
		</div>

		<div id="site-info" class="<?php echo voyage_grid_half(); ?>">
		<?php esc_attr_e('&copy;', 'voyage'); ?> <?php _e(date('Y')); ?><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<?php bloginfo( 'name' ); ?></a>
		</div><!-- #site-info -->

		<div id="site-generator" class="<?php echo voyage_grid_half(); ?>">
			<?php _e('Powered By ', 'voyage'); ?> 
            <a href="<?php echo esc_url(__('http://wordpress.org/','voyage')); ?>" title="<?php esc_attr_e('WordPress', 'voyage'); ?>"><?php esc_attr_e('WordPress', 'voyage'); ?></a>
			<?php _e(' | ', 'voyage') ;?>
			<a href="<?php echo esc_url(__('http://www.voyagebc.com/voyagetheme/','voyage')); ?>" title="<?php esc_attr_e('Voyage Theme by Stephen Cui', 'voyage'); ?>"><?php esc_attr_e('Voyage Theme', 'voyage'); ?></a>		
		</div><!-- #site-generator -->
	</div><!-- #footer-container -->		
	<div class="back-to-top"><a href="#branding"><span class="icon-chevron-up"></span><?php _e(' TOP','voyage'); ?></a></div>
</div><!-- #footer -->
</div><!-- #wrapper -->
<?php 
	wp_footer();
?>
</body>
</html>
