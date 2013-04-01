<form method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
    <div><label class="screen-reader-text" for="s">Search for:</label>
        <input type="text" name="s" id="s" value="Search <?php bloginfo('name'); ?>" onfocus="if (this.value=='Search <?php bloginfo('name'); ?>') this.value='';" onblur="if (this.value=='') this.value='Search <?php bloginfo('name'); ?>';" />
        <input type="submit" id="b-search" value="Search" />
    </div>
</form>
