<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 */
?>
<?php get_header(); ?>
<div class="clear"></div>
<div class="page-content">
    <h1 class="page_title"><?php the_title(); ?></h1>
    <div class="grid_16 alpha">
        <div class="content-bar">  
            <?php if (have_posts()) : the_post(); ?>
                <?php the_content(); ?>	
                <?php wp_link_pages(array('before' => '<div class="clear"></div><div class="page-link"><span>' . __('Pages:', 'andrina-lite') . '</span>', 'after' => '</div>')); ?>
            <?php endif; ?>
            <div class="clear"></div>
            <!--Start Comment box-->
            <?php comments_template(); ?>
            <!--End Comment box-->
        </div>
    </div>
    <div class="grid_8 omega">
        <!--Start Sidebar-->
        <?php get_sidebar(); ?>
        <!--End Sidebar-->
    </div>
</div>
</div>
<?php get_footer(); ?>