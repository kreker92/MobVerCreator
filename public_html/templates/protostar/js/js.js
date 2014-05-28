jQuery(document).ready(function(){
	jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
	
	$(this).find('.btn').toggleClass('active');  
    
    if ($(this).find('.btn-primary').size()>0) {
    	$(this).find('.btn').toggleClass('btn-primary');
    }
	$(this).find('.btn').toggleClass('btn-default');
});