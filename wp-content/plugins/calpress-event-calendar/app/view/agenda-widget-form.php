<p>
	<label for="<?php echo $title['id'] ?>"><?php _e( 'Title:', CALP_PLUGIN_NAME ) ?></label>
	<input class="widefat" id="<?php echo $title['id'] ?>" name="<?php echo $title['name'] ?>" type="text" value="<?php echo $title['value'] ?>" />
</p>
<p>
	<label for="<?php echo $events_per_page['id'] ?>"><?php _e( 'Number of events to show:', CALP_PLUGIN_NAME ) ?></label>
	<input id="<?php echo $events_per_page['id'] ?>" name="<?php echo $events_per_page['name'] ?>" type="text" size="3" value="<?php echo $events_per_page['value'] ?>" />
</p>
<p class="calp-limit-by-container">
	Limit to:
	<br />
	<!-- Limit by Category -->
	<input id="<?php echo $limit_by_cat['id'] ?>" class="calp-limit-by-cat" name="<?php echo $limit_by_cat['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_cat['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_cat['id'] ?>"><?php _e( 'Events with these <strong>Categories</strong>', CALP_PLUGIN_NAME ) ?></label>
</p>
<div class="calp-limit-by-options-container" <?php if( ! $limit_by_cat['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Category Select box -->
	<select id="<?php echo $event_cat_ids['id'] ?>" class="calp-widget-cat-ids" name="<?php echo $event_cat_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $event_cat_ids['options'] as $event_cat ): ?>
			<option value="<?php echo $event_cat->term_id; ?>"<?php if( in_array( $event_cat->term_id, $event_cat_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_cat->name; ?></option>
		<?php endforeach ?>
	</select>
</div>
<p class="calp-limit-by-container">
	<!-- Limit by Tag -->
	<input id="<?php echo $limit_by_tag['id'] ?>" class="calp-limit-by-tag" name="<?php echo $limit_by_tag['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_tag['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_tag['id'] ?>"><?php _e( '<strong>Or</strong> events with these <strong>Tags</strong>', CALP_PLUGIN_NAME ) ?></label>
</p>
<div class="calp-limit-by-options-container" <?php if( ! $limit_by_tag['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Tag Select box -->
	<select id="<?php echo $event_tag_ids['id'] ?>" class="calp-widget-tag-ids" name="<?php echo $event_tag_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $event_tag_ids['options'] as $event_tag ): ?>
			<option value="<?php echo $event_tag->term_id; ?>"<?php if( in_array( $event_tag->term_id, $event_tag_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_tag->name; ?></option>
		<?php endforeach ?>
	</select>
</div>
<p class="calp-limit-by-container">
	<!-- Limit by Event -->
	<input id="<?php echo $limit_by_post['id'] ?>" class="calp-limit-by-event" name="<?php echo $limit_by_post['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_post['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_post['id'] ?>"><?php _e( '<strong>Or</strong> any of these <strong>Events</strong>', CALP_PLUGIN_NAME ) ?></label>
</p>
<div class="calp-limit-by-options-container" <?php if( ! $limit_by_post['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Event Select box -->
	<select id="<?php echo $event_post_ids['id'] ?>" class="calp-widget-event-ids" name="<?php echo $event_post_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $event_post_ids['options'] as $event_post ): ?>
			<option value="<?php echo $event_post->ID; ?>"<?php if( in_array( $event_post->ID, $event_post_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_post->post_title; ?></option>
		<?php endforeach ?>
	</select>
</div>
<br />
<p>
	<input id="<?php echo $show_calendar_button['id'] ?>" name="<?php echo $show_calendar_button['name'] ?>" type="checkbox" value="1" <?php if( $show_calendar_button['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $show_calendar_button['id'] ?>"><?php _e( 'Show <strong>View Calendar</strong> button', CALP_PLUGIN_NAME ) ?></label>
	<br />
	<input id="<?php echo $hide_on_calendar_page['id'] ?>" name="<?php echo $hide_on_calendar_page['name'] ?>" type="checkbox" value="1" <?php if( $hide_on_calendar_page['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $hide_on_calendar_page['id'] ?>"><?php _e( 'Hide this widget on calendar page', CALP_PLUGIN_NAME ) ?></label>
	<br />
	<input id="<?php echo $show_calendar_navigator['id'] ?>" name="<?php echo $show_calendar_navigator['name'] ?>" type="checkbox" value="1" <?php if( $show_calendar_navigator['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $show_calendar_navigator['id'] ?>"><?php _e( 'Show mini calendar navigator', CALP_PLUGIN_NAME ) ?></label>
</p>	
<script type="text/javascript">
	/* <![CDATA[ */
	// To be run every time the widget form is rendered, sets up the Multi-select boxes with bsmSelect
	jQuery( function( $ ) {
		$( 'select.calp-widget-cat-ids' ).bsmSelect( {
			title: '<?php _e( "Categories", CALP_PLUGIN_NAME ) ?>:',
			highlight: 'highlight',
			removeLabel: '<img src="<?php echo CALP_IMAGE_URL ?>/icon-close.png" alt="x" />',
			selectClass: 'calp-widget-option-ids',
			listClass: 'calp-category-selected-items',
			listItemClass: 'calp-category-selected-item',
			listItemLabelClass: 'calp-category-selected-item-label',
			removeClass: 'calp-category-selected-item-remove',
		} );

		$( 'select.calp-widget-tag-ids' ).bsmSelect( {
			title: '<?php _e( "Tags", CALP_PLUGIN_NAME ) ?>:',
			highlight: 'highlight',
			removeLabel: '<img src="<?php echo CALP_IMAGE_URL ?>/icon-close.png" alt="x" />',
			selectClass: 'calp-widget-option-ids',
			listClass: 'calp-category-selected-items',
			listItemClass: 'calp-category-selected-item',
			listItemLabelClass: 'calp-category-selected-item-label',
			removeClass: 'calp-category-selected-item-remove',
		} );

		$( 'select.calp-widget-event-ids' ).bsmSelect( {
			title: '<?php _e( "Events", CALP_PLUGIN_NAME ) ?>:',
			highlight: 'highlight',
			removeLabel: '<img src="<?php echo CALP_IMAGE_URL ?>/icon-close.png" alt="x" />',
			selectClass: 'calp-widget-option-ids',
			listClass: 'calp-category-selected-items',
			listItemClass: 'calp-category-selected-item',
			listItemLabelClass: 'calp-category-selected-item-label',
			removeClass: 'calp-category-selected-item-remove',
		} );
	} );
	/* ]]> */
</script>