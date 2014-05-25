/*
 * 	Character Count Plugin - jQuery plugin
 * 	Dynamic character count for text areas and input fields
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/7161/jquery-plugin-simplest-twitterlike-dynamic-character-count-for-textareas
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
(function($) {

	$.fn.charCount = function(options){
	  
		// default configuration properties
		var defaults = {	
			allowed: 140,		
			warning: 25,
			css: 'counter',
			counterElement: 'span',
			cssWarning: 'warning',
			cssExceeded: 'exceeded',
			counterText: ' / '
		}; 
			
		var options = $.extend(defaults, options); 
		if (options.allowed == 0)
			$('.'+options.css).remove();	
		
		function calculate(obj){
			var count = $(obj).val().length;
			var available = options.allowed - count;
			if(available <= options.warning && available >= 0){
				$(obj).next().addClass(options.cssWarning);
			} else {
				$(obj).next().removeClass(options.cssWarning);
			}
			if(available < 0){
				$(obj).next().addClass(options.cssExceeded);
			} else {
				$(obj).next().removeClass(options.cssExceeded);
			}
			$(obj).next().html(count + options.counterText + options.allowed);
		};
				
		this.each(function() {
			$('.'+options.css).remove();	
			if (options.allowed != 0) {
				$(this).after('<'+ options.counterElement +' class="' + options.css + '">'+ options.counterText + '</'+ options.counterElement +'>');
				calculate(this);
				$(this).keyup(function(){calculate(this)});
				$(this).change(function(){calculate(this)});
			}
		});
	  
	};

})(jQ);
