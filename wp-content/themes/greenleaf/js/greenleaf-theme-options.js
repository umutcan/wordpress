
	jQuery(document).ready(function() {
		jQuery.fn.start = function() {
					
			//Check if element exists
			jQuery.fn.exists = function(){return jQuery(this).length;}
			  
			//AJAX upload
			jQuery('.gd_upload').each(function(){
				
				var the_button=jQuery(this);
				var image_input=jQuery(this).prev();
				var image_id=jQuery(this).attr('id');
				
				//alert(ajaxurl+image_input.value);
				new AjaxUpload(image_id, {
					action: ajaxurl,
					name: image_id,

					// Additional data
					data: {
						action: 'gd_ajax_upload',
						data: image_id
					},
					autoSubmit: true,
					responseType: false,
					onChange: function(file, extension){},
					onSubmit: function(file, extension) {
						the_button.html("Uploading...");
					},
					onComplete: function(file, response) {
						the_button.html("Upload Image");

						//if (response.search("Error") > -1) {
						if (response < 1) {
							alert("There was an error uploading: "+response);
						} else {
							image_input.val(response);
							var image_preview='<img src="' + response + '" class="gd_image_preview" />';							
							the_button.next().html(image_preview);

							var remove_button_id='remove_'+image_id;
							var rem_id="#"+remove_button_id;
							if(!(jQuery(rem_id).exists())){
								the_button.after('<span class="button gd_remove" id="'+remove_button_id+'">Remove Image</span>');
							}
						}
					}
				});
				jQuery.error = console.error;
				
			});
			
			//AJAX image remove
			jQuery('.gd_remove').click(function(){

				var remove_button=jQuery(this);
				var image_remove_id=jQuery(this).prev().attr('id');
				remove_button.html('Removing...');
				
				var data = {
					action: 'gd_ajax_remove',
					data: image_remove_id
				};
				
				jQuery.post(ajaxurl, data, function(response) {
					remove_button.prev().prev().val('');
					remove_button.next().html('');
					remove_button.remove();
					jQuery('.logoupload').show();
				});
				
			});
		};	
		jQuery(document).start();
	});
