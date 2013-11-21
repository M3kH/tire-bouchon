require([
	'jquery', 'plugins/CommentTree', 'vendor/codemirror/lib/codemirror',
	'plugins/jquery/sticky', 
	 'vendor/codemirror/mode/htmlmixed/htmlmixed',
	 'vendor/codemirror/mode/xml/xml',
	 'vendor/codemirror/mode/css/css',
	 'vendor/codemirror/mode/clike/clike',
	 'vendor/codemirror/mode/php/php',
	'vendor/codemirror/addon/selection/active-line', 'vendor/codemirror/addon/edit/matchbrackets',
	 ], function($, CommentTree) {
	 	
// <script src="../htmlmixed/htmlmixed.js"></script>
// <script src="../xml/xml.js"></script>
// <script src="../javascript/javascript.js"></script>
// <script src="../css/css.js"></script>
// <script src="../clike/clike.js"></script>
// <script src="php.js"></script>
	 	
		// <link rel="stylesheet" href="codemirror/theme/solarized.css">
// 		
		// <script src="codemirror/lib/codemirror.js"></script>
		// <script src="codemirror/mode/javascript/javascript.js"></script>
		// <script src="codemirror/keymap/extra.js"></script>
		// <script src="codemirror/addon/selection/active-line.js"></script>
		// <script src="codemirror/addon/edit/matchbrackets.js"></script>
	   
		function renderComments (comments){
		
				var line = 0,
					html = "";
	
				for(var k in comments){
					var block = comments[k],
						interval = block.end - block.start;
						if( line > 0 ){
							interval = block.end;
						}
						//console.log(block);
						
					if(typeof block.start != "undefined" && typeof block.end != "undefined"){
						for(var z=line; z<(line+block.start-1);z++){
							html += "<br/>";
						}
						html += "<div class=\"block\">";
							html += "<div class=\"cases\">";
							for(var y in block.elems){
								switch(block.elems[y].type){
									case "todo":
										html += "<div class=\"todo\">"+block.elems[y].value+"</div>";
									break;
									case "author":
										html += "<div class=\"author\">"+block.elems[y].value+"</div>";
									break;
									default:
										html += "<div class=\"elem\">"+block.elems[y].value+"</div>";
									break;
								} 
							}
							html+="</div>";
							
							
							if( block.childs.length > 0 ){
								var r = renderComments(block.childs);
								interval = interval - r.rows;
								html += r.html;
							}
						for(var c=0; c<(interval+1);c++){
							html += "<br/>";
						}
						html+="</div>";
						line = block.end--;
					}
				}
			return {html: html, rows: line};
		};
			
		  function render(txt){
		  	// module.exports = CommentTree;
			var textarea = $("#txt"),
				_txt = textarea.val(),
				_txtLine = '',
				_txtArr = [];
				
			if( typeof txt == "undefined"){
				txt = _txt;
			}else{
				textarea.val(txt);
			}
			
			// console.log(txt);
			
			var	comments = CommentTree(txt, "javascript"),
				redered = renderComments(comments).html;
			_txtArr = _txt.split("\n");
			for( var k in _txtArr ){
				var row = _txtArr[k];
				_txtLine += k+"\n";
			}
			
			textarea.hide();
			$("body").append("<div id=\"comments\"></div>");
			// $("#rows").html(_txtLine);
			// $("#code").html(txt);
			$("#comments").html(redered);
			
			
			var editor = CodeMirror.fromTextArea(document.getElementById("txt"), {
			        lineNumbers: true,
			        matchBrackets: true,
			        mode: "application/x-httpd-php",
			        indentUnit: 4,
			        indentWithTabs: true,
			        enterMode: "keep",
			        tabMode: "shift"
			});
			editor.setOption("theme", "solarized light");
			var input = document.getElementById("select");
			
			if(comments.length > 0){
				$(".CodeMirror").addClass("comment-tree");
			}else{
				$(".CodeMirror").removeClass("comment-tree");
			}
			// console.log( comments );
		  }
		  
		  $(document).on("blur", "#code", function(){
		  	render($(this).html());
		  });
		  render();
		  
		$('.cases').fixer({
            gap: 10
        });
        
        
		$('#file-browser ul').hide();
		$('#file-browser > ul').show();
    	$('#file-browser').find("li > a").on("click", function(){
    		
    		$(this).closest('li').find(' > ul').toggle();

    		if( $(this).attr("href") == "#" ){
	    		return false;
    		}
    	});
        
        // $('.CodeMirror').height( $('.CodeMirror-sizer').height());  
         // $(window).resize(function(){
	    // })
	    // $(window).resize();
		  
}); 