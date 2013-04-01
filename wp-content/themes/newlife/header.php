<?php /**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @subpackage New life
 * @since New life
 */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php wp_title( '|', true, 'left' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head();?>
</head>
<body <?php body_class(); ?>>

<div id="wrap">
	<div id="header">
		<div id="strip_brown"></div>
		<div id="strip_green"></div>
		<div id="main">
			<div id="logo">
				<!--<a href="<?php echo home_url(); ?>"><img src="<?php echo get_template_directory_uri();?>/image/logo.jpg" alt="" /></a>-->
					<h1 id="site-title">
							<span>
								<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
							</span>
					</h1>
					<div id="site-description"><?php bloginfo( 'description' ); ?></div>
			</div>
			<div id="menu">
				<?php wp_nav_menu( array( 'theme_location' => 'menu' ) ); ?>
			</div><!-- #menu -->
			<div class="clear"></div>
			<div id="tips">
				<?php if (!is_home() ) { get_search_form();} ?>
			</div>
		</div><!-- #main -->	
	</div><!-- #header -->
	<div id="conteiner">
		
		
