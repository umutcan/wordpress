<?php
/**
 * The template for displaying search forms in Voyage
 *
 * @package Voyage
 * @subpackage Voyage
 * @since Voyage 1.0
 */
?>
	<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="s" class="assistive-text"><?php _e( 'Search', 'voyage' ); ?></label>
		<input type="text" class="search-query" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'voyage' ); ?>" />
		<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'voyage' ); ?>" />
	</form>
