(function($) {
    $.fn.doubleselect = function(options) { 
        
    	// default configuration properties
		var defaults = {	
				add_button : "add",
				remove_button : "remove",
				max_size: 10,
				max_selected : 0,
				max_selected_text : "You have reached the max selection",
				add_function: null,
				remove_function : null,
				before_add_function: null,
				before_remove_function : null
		}; 
			
		var options = $.extend(defaults, options); 
		
		name = $(this).attr('name');
		id = $(this).attr('id');
		
		not_selected_list = "not_selected_"+id;
		selected_list = "selected_"+id;
		add_button = "addbutton_"+id;
		remove_button = "removebutton_"+id;
		container = "container_"+id;
		
		size = $("option",this).size();
		if (size > options.max_size)
			size = options.max_size;
		
		
		$('<div id="'+container+'" class="doubleselect_container"> \
			   <div class="doubleselect_notselected"> \
			   	<select multiple="multiple" id="'+not_selected_list+'" name="'+not_selected_list+'" size="'+size+'" /> \
			   </div> \
			   <div class="doubleselect_action"> \
			   	<div> \
			   	<input id="'+add_button+'" name="'+add_button+'" value="'+options.add_button+'" type="button" class="button " /> \
			   	<input id="'+remove_button+'" name="'+remove_button+'" value="'+options.remove_button+'" type="button" class="button " /> \
			   	</div> \
			   </div> \
			   <div class="doubleselect_selected"> \
			   	<select multiple="multiple" id="'+selected_list+'" name="'+selected_list+'" size="'+size+'" /> \
			   </div> \
			   <div class="doubleselect_hidden"> \
			   </div> \
		   </div>').insertAfter(this);	
		
		not_selected_list = "#"+not_selected_list;
		selected_list = "#"+selected_list;
		add_button = "#"+add_button;
		remove_button = "#"+remove_button;
		container = "#"+container;
		$("option:selected",this).each(function(){
			$(this).appendTo($(selected_list));
		});
		$("option",this).each(function(){
			$(this).appendTo($(not_selected_list));
		});
		
		$(this).remove();
		
		html = "";
		$(selected_list+" option").each(function(){
			var v = $(this).val();
			html += '<input type="hidden" id="'+id+'" name="'+name+'" value="'+v+'">';
		});
		//$(container+" .doubleselect_hidden").append(html);
	
		$(add_button).click(function(event) {
			//options.addfunction();
			if (options.max_selected != 0) {
				nb_newcats = $(not_selected_list+" option:selected").size();
				nb_actualcats =  $(selected_list+" option").size();
				if (nb_newcats+nb_actualcats > options.max_selected) {
					alert(options.max_selected_text);
					event.stopImmediatePropagation();
					return;
				}
			}
			
			if (options.before_add_function != null) {
				options.before_add_function();
			}
			
			$(not_selected_list+" option:selected").each(function(){
				$(this).removeAttr('selected').appendTo($(selected_list));
			});
			html = "";
			$(selected_list+" option").each(function(){
				var v = $(this).val();
				html += '<input type="hidden" name="'+name+'" value="'+v+'">';
			});
			//$(container+" .doubleselect_hidden").html(html);
			if (options.add_function != null) {
				options.add_function();
			}
		});
		
		$(remove_button).click(function() {
			if (options.before_remove_function != null) {
				options.before_remove_function();
			}
			
			$(selected_list+" option:selected").each(function(){
				$(this).removeAttr('selected').appendTo($(not_selected_list));
			});
			html = "";
			$(selected_list+" option").each(function(){
				var v = $(this).val();
				html += '<input type="hidden" id="'+id+'" name="'+name+'" value="'+v+'">';
			});
			//$(container+" .doubleselect_hidden").html(html);
			if (options.remove_function != null) {
				options.remove_function();
			}
		});
    };
})(jQ);