<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @subpackage New life
 * @since New life
 */
?>
	<div class='clear'></div>	
</div><!-- #conteiner -->
<div id="footer">
	<div id="colophon">
		<div id="menu_copy">
			<?php wp_nav_menu( array( 'theme_location' => 'menu','depth'=>1 ) ); ?>
			<div class="clear"></div>
		</div><!-- #menu_copy -->
	</div><!-- #colophon -->
</div><!-- #footer -->
</div><!-- #wrap -->
<?php wp_footer();?>
</body>
</html>
