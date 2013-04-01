(function($) {
	$(document).ready(function(){
			$('#menu ul:first-child > li').children('a').before('<div class="image"><img class="menu_image" src="'+theme_url+'/image/menu.jpg"></div>');
			$('.image').css("visibility","hidden");
			content=$('.content').css("height");
			bar=$('#sidebar').css("height");
			leg=$('#legend').css("height");
			research=$('#researches').css("height");
			content_home=$('.content_home').css("height");
			if (content) {content = content.split('px');}
			if (leg) {leg = leg.split('px');}
			if (research) {research = research.split('px');}
			if (content_home) {content_home = content_home.split('px');}
			bar = bar.split('px');

			if (content) {if (parseInt(content[0]) > parseInt(bar[0])) {
				$('#sidebar').css('height', function(index) {return content[0];});
			} 
			else {$('.content').css('height', function(index) {return bar[0];});
			}};
			if (content_home) {if (parseInt(content_home[0])<parseInt(bar[0])) {
				$('.line_home').css('height', function(index) {return bar[0];});
			}
			else {
				$('#sidebar').css('height', function(index) {return content_home[0];});
				$('.line_home').css('height', function(index) {return $('.content_home').css("height");});
			}};
			if (leg) {if (parseInt(leg[0]) > parseInt(research[0])) {
				$('#researches').css('height', function(index) {return leg[0];});
			}};
			$("input").focus(function () {
				document.getElementById('s').value="";
			});
			$("input").blur(function () {
				document.getElementById('s').value=" Search ...";
			});

			$(".title_researches p").hover(function () {
				$(this).addClass("hilite");}, function () {
					$(this).removeClass("hilite");
			});
	
			jQuery("#menu li").hover(function(){
				
				jQuery(this).find('ul:first').css({visibility: "visible",display: "block"}).show(250);
				jQuery(this).find('ul:first>li a').css({background:"#A9D046"});
					},function(){
						jQuery(this).find('ul:first').css({"visibility": "hidden"});
			});
			
			if (navigator.userAgent.indexOf('MSIE') !=-1) {
				$("#menu  ul:first-child > li").each(function(){
					$(this).width($(this).width());
				});
			};
			$("#menu li").hover(function () {
				$(this).children("a").css({"position":"relative","color":"white"});
				$(this).find('.image').css("visibility","visible");
				$(this).find('ul').css("background","#A9D046");},function(){
					      	$(this).find('a').css("color","#515151");
						$(this).find('.image').css("visibility","hidden");}
			);

	});
})(jQuery);
