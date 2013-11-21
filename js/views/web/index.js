require([
	'jquery',
	'plugins/jquery/jquery.scrollto',
	'plugins/jquery/jquery.touchswipes',
	   ], function($) {
	   	
	   	// Full Page, basic get all the slide
	   	// Set the full height for them.
	   	var FP = function (elem) {
		   	
		   	this.opts = {
		   		elem: elem,
		   		slides: elem.length,
		   		pos: 0
		   	};
	   		
	   		this.init = function ( FP ){
	   				   			
	   			// Window resize event
			   	$(window).resize(function(){
				   var wHeight = $(this).height();
				   $(this.opts.elem).each(function(){
				   		$(this).find(".container .row").height(wHeight);
				   });
			   	});
			   	
			   	
			   	// Key Mapping event
			   	$(document).keydown(function(e){
				    
				    switch( e.keyCode ){
				    	// Left
				    	// case 37:
				    	// break;
				    	// Up
				    	case 38:
				    		FP.showPrev(FP.opts);
				    		return false;
				    	break;
				    	// Right
				    	// case 39:
				    	// break;
				    	// Down
				    	case 40:
				    		FP.showNext(FP.opts);
				    		return false;
				    	break;
				    }
				});
				
				// Scroll mapping
			    //adding the event listerner for Mozilla
			    var timeout = null,
			    	lastTop = null;
			    function scrollHandle(event){
			    	var delta = 0,
			    		d = 0;
 
				    if (!event) event = window.event;
				 
				    // normalize the delta
				    if (event.wheelDelta) {
				 
				        // IE and Opera
				        delta = event.wheelDelta / 60;
				        d = event.wheelDelta;
				 
				    } else if (event.detail) {
				 
				        // W3C
				        delta = -event.detail / 2;
				        d = event.detail;
				    }
				    
				    if(typeof timeout != "undefined"){
				    	clearTimeout(timeout);
				    }
				    
				    timeout = setTimeout(function(){
					    if(d > 0){
					    	FP.showPrev(FP.opts);
					    }else if( delta < 0){
					    	FP.showNext(FP.opts);
					    }
				    }, 40);
				    
				    return false;
				 
			    };
			    
			    if(window.addEventListener){
			        document.addEventListener('DOMMouseScroll', scrollHandle, false);
			    }
			    	
			    //for IE/OPERA etc
			    document.onmousewheel = scrollHandle;
			    
			     $(window).swipe( {
			        //Generic swipe handler for all directions
			        swipe:function(event, direction, distance, duration, fingerCount) {
			        
				    
				    if(typeof timeout != "undefined"){
				    	clearTimeout(timeout);
				    }
				    
				    timeout = setTimeout(function(){
			          switch( direction ){
			          	case 'up':
					    	FP.showNext(FP.opts);
			          	break;
			          	case 'down':
					    	FP.showPrev(FP.opts);
			          	break;
			          }
			          
				    }, 40);
			        },
			        //Default is 75px, set to 0 for demo so any distance triggers swipe
			         threshold:0
			      });
			   
			    
			    $('body').bind('touchmove', function(e) {
			    	var top = $(this).scrollTop();
			    	
				    console.log($(this).scrollTop()); // Replace this with your code.
				});
			 
	   		}
	   		
	   		this.resizeBoxes = function ( wHeight ){
	   		}
	   		
	   		this.showNext = function (opts){
				if( opts.pos < opts.slides ){
					opts.pos++;
				}else{
					opts.pos = 0;
				}
				var i = opts.pos,
					e = opts.elem.eq(i);
				
				console.log(e);				
				$(window).scrollTo( e, 800 );
	   		}
	   		
	   		this.showPrev = function (opts){
				if( opts.pos > 0 ){
					opts.pos--;
				}else{
					opts.pos = opts.slides;
				}
				var i = opts.pos,
					e = opts.elem.eq(i);
					
				console.log(e);
				
				$(window).scrollTo( e, 800 );
	   		}
	   		
	   		this.init(this);
	   		
		   	$(window).resize();
	   		
	   	};
	   	
	   	FP($(".section"));
		    // $.fn.fullpage();
	   	// $(document).ready(function() {
		// });

}); 