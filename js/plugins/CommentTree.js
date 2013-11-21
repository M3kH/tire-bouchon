define(function(){

var CommentTree = function (txt, options) {
	// console.log(txt);
    this.multyLine =  /\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/g;
    this.singleLine =  /((\/\/).*?\n|(\/\/).*?(.*))/g;
    this.blocks =  /((\/\/).*?\n|(\/\/).*?(.*))/g;
    // this.editor =  editor;
  		
    // The SetType function set the correct regEx
    this.SetType = function( ){
        var multyLine, blocks, singleLine;
        switch(options){
            case "javascript":
                multyLine = /\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/g;
                singleLine = /((\/\/).*?\n|(\/\/).*?(.*))/g;
                blocks = /.\/\/.*START.*|.\/\/.*END.*/g;
            break;
                
            // This are the best solution for now.
            default:
                multyLine = /\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/g;
                singleLine = /((\/\/).*?\n|(\/\/).*?(.*))/g;
                blocks = /.\/\/.*START.*|.\/\/.*END.*/g;
            break;
        }
        
        // Here I redeclare all
        this.multyLine = multyLine; 
        this.singleLine = singleLine; 
        this.blocks = blocks;
    };
  		
        
    // This function return the last position of a certain block
    // str is the string, substring is what you are searching for an n is the times
    this.GetSubstringIndex = function (str, substring, n) {
        var times = 0, index;
    
        while (times<n && index !== -1) {
            index = str.indexOf(substring, index+1);
            times++;
        }
    
        return index;
    };
	
    // This function remove the line of the childs(passed as array) and return a 
    this.GetDetails = function(txt, childs){
        
        
        // And here his where we remove the childs.
        if(typeof childs != "undefined"){
            for(var k in childs){
                var child  = childs[k];
                if(typeof child.start != "undefined" && typeof child.end != "undefined" ){
                    var _txtStart = this.GetRows(txt, 0, child.start),
                        _txtEnd = this.GetRows(txt, child.end);
                        txt = _txtStart+_txtEnd;
                }
            }
        }
        
        // Here I declare the match rules on txt.
        var multyLine = txt.match(this.multyLine),
            singleLine = txt.match(this.singleLine),
            props = [];
        
        // console.log("---- MULTYLINE ----","",multyLine,"","");
        
        for(var k in multyLine){
            var _props = multyLine[k].split("\n");
            
            for(var y in _props){
            	
	            var val = _props[y].split(":"),
	            	val = val[1];
	            
                if(_props[y].indexOf("Todo") > 0){
                    props[props.length] = {value: val, type: "todo"};
                }else if(_props[y].indexOf("Desc") > 0){
                    props[props.length] = {value: val, type: "desc"};
                }else if(_props[y].indexOf("Author") > 0){
                    props[props.length] = {value: val, type: "author"};
                }else{
                    // props[props.length] = {value: _props[y], type: "generic"};
                	
                }
            }
        }
        
        for(var z in singleLine){
            var val = singleLine[z].split(":"),
            	val = val[1];
            	
            if(singleLine[z].indexOf("Todo") > 0){
                props[props.length] = {value: val, type: "todo"};
            }else if(singleLine[z].indexOf("Desc") > 0){
                props[props.length] = {value: val, type: "desc"};
            }else if(singleLine[z].indexOf("Author") > 0){
                props[props.length] = {value: val, type: "author"};
            }else{
                // props[props.length] = {value: singleLine[z], type: "generic"};
            }
        }
        
        // console.log(props);
        return props;
    };
		
    // This is a function where you can ask for get the row passing from to;
    this.GetRows = function(txt, from, to){
        // console.group("GetRows");
        var _txt = '';
        	// console.log(txt);
        txt = txt.split("\n");
        if( typeof to == "undefined" ){
            to = txt.length;
        }
        
        if(to > txt.length){
            to = txt.length;
        }
        
        
        if( typeof from != "undefined" ){
        	from = parseInt(from);
        	// console.log(from);
        }
        
        if( to < from ){
        	to += from;
        }
        // console.groupEnd();
        
        
        for(var i = from; i < to; i++){
            _txt += txt[i]+"\n";
        }
        
        return _txt;
    };
        
    this.GetTypeBlock = function (block){
        var type = false;
        if ( block.indexOf("START") > -1 ){
            type = "start";
        }else if( block.indexOf("END") > -1 ){
            type = "end";
        }
        return type;
    }
    
    /*
     * This is a recursive function and it return an array
     * Search for this.GetComments to see the recursive call
     * @params txt is mandatory, string. 
     * @params comments is optional, array. 
     */    
    this.GetComments = function (txt, comments){
        // Comments is the array of the already comment found.
        var comments = comments || [];
        
        // This define the blocks inside the code, 
        // basic simple single line comment with the word START or END
        var blocks = txt.match(this.blocks),
        	
        	// This define the index
            blockIndexStart = 0,
            blockIndexEnd = 0,
            
            // This define the Row start
            blockRowStart = 0,
            blockRowEnd = 0,
            
			// This define how many blocks was found
            blockCount = 0,
			
			// These are the rows  and his total          
            rows = txt.split("\n"),
            totalRows = rows.length,
            
            // Level is how many level of concatenation we have
            level = 0,
            
            // With lastIndex and the lastRow we register the last position of the cursor
            lastIndex = 0,
            lastRow = 0,
            
            lastEnd = 0,
            maxLevel = 0,
            
            // Thisis the local instance for txt
            _txt = txt,
            
            // This is the check for child elements
            childCheck = false,
            
            // This is the array of childs found ( just first level )
            childs = [],
            blockComment = {};
        console.log(blocks);
        // Then here is the loop for each block found.
        // @Todo []: 
        for( var k in blocks ){
            
            // Here is defined the block
            // block is the string found with match
            var block = blocks[k],
                blockLength = block.length,
                blockIndexOf = _txt.indexOf(block),
                blockEnd = blockIndexOf+blockLength,
                
                // This is the text before the block
                TXTPrevious = _txt.slice(0, blockEnd),
                
                // This is the row position
                row = TXTPrevious.split("\n").length,
                
                // This get clean type of the block start or end
                type =  this.GetTypeBlock( block ),
                
                // This count the elment of the child
                cLenght = childs.length;
                
                // ---- START | Simple a bit of debugging
				// console.group("blockIndexOf");
				// console.log("BLOCK:", block);
				// console.log(blockIndexOf);
				// console.log(TXTPrevious);
				// console.log("\n\n------------\n\n");
				// console.log(_txt);
				// console.groupEnd();
                // ---- END
                
            // If is the end would check based end-LEVEL
            if( type === "end" ){ type = type+"-"+level; };
            
            // ---- START | Simple a bit of debugging
            // console.log("---- TYPE ----");
            // console.log("",type,"");
            // ---- END
            
            switch( type ){
            	
            		// If is the start block
                    case "start":
                    
                    	// Make level higher
                        level++;
                        
                        // If the level 2
                        if( level > 1 && childCheck == false ){
                            childCheck = true;
                            childs[cLenght] = { start: row, end: 0, startIndex: (lastIndex+blockEnd-blockIndexOf), endIndex: 0};
                            
                        // If is level 1 start to register the block
                        }else if( level == 1 ){
			                
			                // ---- START | Simple a bit of debugging
                            //childs[cLenght-1].child = { start: row, end: 0};
                            //blockRowStart = row;
                            // console.log(row);
        					// console.group("TxtPrevious");
        					// console.log(TXTPrevious);
        					// console.groupEnd();
	                    	// console.log("BlockStart Checking", row, lastRow, "\nBlock\n", block,  "\n\n_TXT: \n\n", _txt);
				            // ---- END
				            
				            // Here is register the block
	                        blockRowStart = row;
	                        blockIndexStart = lastIndex+blockEnd;
                            blockComment = { start: row, end: 0, childs: [], elems : [] };
                            
                        // This is a sub children
                        }else{
                            // childs[cLenght] = { start: row, end: 0};
                        }
                    
                    break;
                    
                    // The block close the 1st level
                    case "end-1":
                    
                    	// Make level lower
                        level--;
                        
                        // Check for the lenght of the array comments
                        var comLen = comments.length,
                        	// Define blockText an empty string and the start and the end of the block.
                        	blockText = '',
                        	startIndex = blockIndexStart,
                        	endIndex = lastIndex+blockEnd,
                        	start = blockRowStart+lastEnd,
                        	end = row+start;
                        
                        blockCount++;
                    	
		                // ---- START | Simple a bit of debugging
                        // console.log("Row",row, "Last Row", lastRow, "Last End", lastEnd, "Block Start", blockRowStart, "Start", start, "End", end);
			            // ---- END
                        
                        // If block is 1 make it row higher at level of the last row
                    	if(blockCount == 1){
	                        row += lastRow;
                    	}
                    	var _row = row+lastRow;
                    	
                        blockComment.end = row-1;
                        
                        // if as a children
                        if( childCheck == true ){
                        	
                        	// This is the looping for the childs
                            // for(var z in childs){
                            	// var childStart = start + childs[z].start,
                            		// childEnd = childStart+childs[z].end;
//                             		
                            	// blockText = this.GetRows( txt, childStart, childEnd );
                                // blockComment.childs = blockComment.childs.concat(this.GetComments( blockText ));
                            
                                // // console.log( "THIS Are the row blocks\n\n",childs[z].start, childs[z].end );
                                // console.log( "THIS IS THE BLOCK TEXT PASSED AS CHILD\n\n",blockText, childStart, childEnd );
                            
                            // }
							
							// Here is a test for check if I can pass all the comments in the recursive function
                        	// blockText = this.GetRows( txt, start, end-1 );
                        	endIndex = endIndex-blockLength;
                        	blockText = txt.slice( startIndex, endIndex );
                            // blockComment.childs = blockComment.childs.concat(this.GetComments( blockText ));
                            
                            // What would be great is remove all the children comment and pass a clean blockComment for get all details here.
                            for(var z in childs){
                            	var childStart = childs[z].startIndex,
                            		childEnd = childStart+childs[z].endIndex,
                            		chidText = '',
                            		beforeBlock = '',
                            		afterBlock = '',
                            		textForBlock = '',
                            		chidTextLength = 0;
                            		
                            		
                            	childText = txt.slice(childStart, childEnd);
                            	childTextLength = childText.length;
                            	
	                            blockComment.childs = blockComment.childs.concat(this.GetComments( childText ));
	                            
	                            beforeBlock = blockText.slice(0, childStart);
	                            afterBlock = blockText.slice(childEnd, -1);
	                            blockText = beforeBlock+afterBlock;
                            }
                            
                            // console.log( "THIS IS CHILD\n\n",childs );
                       
                        // Here if the block don't has children
                        }else{
                        	
                        	blockText = txt.slice( startIndex, endIndex );
                            // blockText = this.GetRows( txt, start, end );
	                        
                        }
                        
                        console.log( "THIS is the txt\n\n",blockText, startIndex, endIndex );
                        
                    	// Try to get the block text detail; and passing the child for register it
                        blockComment.elems = this.GetDetails( blockText, blockComment.childs );
                        
                        //console.log(blockComment);
                    
                    	// Add the element to the comments array
                        comments[comLen] = blockComment;
                    
                        // Resetting rules
                        lastEnd = end-1;
                        childCheck = false;
                        // txt = this.GetRows( txt, row );
                        // lastRow = 0;
                        childs = [];
                    break;
                    
                    // The last end
                    case "end-2":
                        level--;
                    // console.log(blockComment);
                        if( childCheck == true ){
                            childs[cLenght-1].end = row;
                            childs[cLenght-1].endIndex = lastIndex+blockEnd;
                        }
                    
                    break;
                    // The rest of end
                    default:
                        // console.log(block);
                        level--;
                    break;
            }
            
            // For each loop in blocks slice the text at the position fo the block.
            _txt = _txt.slice( blockEnd, -1 );
            lastRow += row;
            lastIndex += blockEnd;
            
            // console.log(row);
            
        }
        
        //console.log(comments);
        return comments;
        
    };
        console.group("Comments", comments);
        this.SetType();
        var comments = this.GetComments(txt);
        
        this.comments = comments;
  		//this.comments = this.GetCleanComment(txt, []);
  		console.log(comments);
        console.groupEnd();
  		return this.comments;
  };
  
	return CommentTree;
	
});