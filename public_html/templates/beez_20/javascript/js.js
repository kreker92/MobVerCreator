jQuery( document ).ready(function( $ ) {
	var form = $('form');
	$('form input[type="submit"]').click(function() {
		var site = new Object;
		
		site = {
			url: form.find('input[name="text1"]').val(),
			sitemapUrl: form.find('input[name="text3"]').val(),
		}
		
		//creat ierarchical array of sitemap
		site.getSitemap = function(url) {
			$.ajax({
				url: url,
				beforeSend: function(msg) {
					console.log('sending request...');
					console.log(msg);
				},
				complete: function(msg) {
					console.log('request was finished.');
				},
				success: function(msg) {
					console.log('request was finished SUCCESSFULLY.');
					console.log(msg);
				},
				/*error: function(msg) {
					console.log('request failed.');
					console.log(msg);
				},*/
				statusCode: {
					404: function() {
						console.log( "404 page not found" );
					}
				},
				timeout: 15000,
				context: document.body,
				crossDomain : true,
				dataType: "html"
			})
			  .done(function( data ) {
				console.log('Getted data:');
				console.log(data);
			  })
			  .fail(function( data ) {
				console.log('Request failed!');
				console.log(data);
			  });
		}
		
		console.log(site.sitemapUrl);
		if(site.sitemapUrl) site.getSitemap(site.sitemapUrl);
		return false;
  });
});