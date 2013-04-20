jQuery(document).ready(function($){
	
	$(".toggle-off div").hide();

	$("p.toggle-title").click(function(){
		$(this).toggleClass("active").next().slideToggle("slow");
	});
	
/* Carousel Controls */
	var currentPage =0;
	var pages = $(".carousel-nav a");
	var total_pages = pages.length;

	$(".carousel-control.left").click(function(){
		if(currentPage>0){
			currentPage=((currentPage-2)%total_pages);
			$('.carousel').carousel('prev');
		} else {
			currentPage=total_pages-2;
			$('.carousel').carousel('prev');
		}
		return false;
	});

	$(".carousel-control.right").click(function(){
		$(".carousel").carousel('next');
		return false;
	});

	$('.carousel-nav a').click(function(q){
		q.preventDefault();
		clickedPage = $(this).attr('data-to')-1;
		currentPage = clickedPage-1;
		$('.carousel').carousel(clickedPage);
		return false;
	});

	
	$('.carousel').on('slide', function(evt) {
		$(pages).removeClass("active");
		currentPage++;
		currentPage=(currentPage%total_pages);
		$(pages[currentPage]).addClass("active");
		$('body').scrollTop();
	});
/*End of Carousel Controls */
	
	//Add Pretty Print class
	$("#content pre").addClass("prettyprint");
	
	// Back-to-top Script
	$(".back-to-top").hide();
	// fade in back-to-top 
	$(window).scroll(function () {
		if ($(this).scrollTop() > 500) {
			$('.back-to-top').fadeIn();
		} else {
			$('.back-to-top').fadeOut();
		}
	});
	
	// scroll body to 0px on click
	$('.back-to-top a').click(function () {
		$('body,html,header').animate({
			scrollTop: 0
		}, 800);
		return false;
	});
	// Colorbox
	$('.voyage-colorbox .format-gallery a[href$=".jpg"],.voyage-colorbox .format-gallery a[href$=".jpeg"],.voyage-colorbox .format-gallery a[href$=".png"],.voyage-colorbox .format-gallery a[href$=".gif"]').colorbox({
		slideshow:'true',
		slideshowSpeed:'5000',
		maxWidth:'100%',
		maxHeight:'100%',
		rel:'gallery',
		fixed:'true'
	});
	  	
});
	
(function($) {
	$(function(){
	    var $window = $(window)
		// make code pretty
	    window.prettyPrint && prettyPrint()
	});
})(jQuery);
