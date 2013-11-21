require([
	'jquery'
	   ], function($) {
	   	$(window).resize(function(){
			   	var wHeight = $(this).height();
			   	console.log(wHeight);
			   	
			   $(".jumbotron").each(function(){
			   		$(this).height(wHeight);
			   });
	   			
	   	});
	   	$(window).resize();
}); 