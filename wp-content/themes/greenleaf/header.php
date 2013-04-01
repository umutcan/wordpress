<?php
/**
 * @package WordPress
 * @subpackage greenleaf_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?><?php if (get_bloginfo('description', 'display')) { echo " - ".get_bloginfo('description', 'display'); } ?></title>

<meta name="viewport" content="width=device-width; initial-scale=1.0">

<link id="main-stylesheet" rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_enqueue_script('jquery'); ?>
<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>

<div id="wrap">

	<div id="header">
		<div id="logo">
		<?php
			$options = get_option('greenleaf_theme_options_logo');
			if ($options != "") {
		?>
			<a href="<?php echo home_url(); ?>/" title="Home | <?php bloginfo('name'); ?>"><img src="<?php echo $options; ?>" /></a>
		<?php
			} else {
		?>
			<a href="<?php echo home_url(); ?>/" title="Home | <?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
		<?php
			}
		?>
		</div>
		<div id="search">
			<?php get_search_form(); ?>
		</div>
	</div>

	<div id="nav">
		<?php wp_nav_menu( array('theme_location' => 'greenleaf_nav', 'fallback_cb' => false) ); ?>
	</div>	
