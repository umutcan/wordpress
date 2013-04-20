// JavaScript Document
jQuery(document).ready(function($){	
	// Tabs
	$('#voyage-wrapper .voyage-pane').eq($('.voyage-current').index()).show();
		
	$('#voyage-tabs a').click(function() {
		$('#voyage-tabs a').removeClass('voyage-current');
		$(this).addClass('voyage-current');
		$('#voyage-wrapper .voyage-pane').hide();
		$('#voyage-wrapper .voyage-pane').eq($(this).index()).show();
		$('#currenttab').val($(this).index());
	});
});
jQuery(document).ready(function ($) {
    setTimeout(function () {
        $(".fade").fadeOut("slow", function () {
            $(".fade").remove();
        });

    }, 3000);
});
