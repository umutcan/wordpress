
function adjustStyle(width) {
	if (width < 701) {
		jQuery("#main-stylesheet").attr("href", greenleaf_vars.theme_url+"/style-320.css");
	} else {
	   jQuery("#main-stylesheet").attr("href", greenleaf_vars.theme_url+"/style.css"); 
	}
}

jQuery(function() {
	adjustStyle(jQuery(window).width());
	jQuery(window).resize(function() {
		adjustStyle(jQuery(window).width());
	});
});
