jQuery( document ).ready(function( $ ) {
	jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
	
	//$(this).find('.btn').toggleClass('active');


	// if ($(this).find('.btn-primary').size()>0) {
		// $(this).find('.btn').toggleClass('btn-primary');
	// }
	$(this).find('.btn').toggleClass('btn-default');
	$( "#chronoform-mainPage" ).submit(function( event ) {
		// Stop form from submitting normally
		event.preventDefault();

		// Get some values from elements on the page:
		var $form = $( this ),
		term = $form.find( "input[name='new_site']" ).val(),
		url = 'casperjs/get_site_data.php';

		// Send the data using post
		var posting = $.post( url, { s: term } );

		// Put the results in a div
		posting.done(function( data ) {
			// var content = $( data ).find( "#tryit" );
			$( "#result" ).append( data );
			console.log(data);
			console.log(term);
		});
		posting.fail(function() {
			console.log('error');
		})
		posting.always(function() {
			// console.log('show always');
		});
	});
});