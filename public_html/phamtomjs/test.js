// сбор всех страниц из карты сайта.
// сбор всех страниц сайта в массив с разделением ее составляющих и параметров 

var page = require('webpage').create(),
	system = require('system'), 
	address;

page.onConsoleMessage = function (msg, line, source) {
	console.log(msg);
};
page.onAlert = function (msg) {
	console.log('alert!!> ' + msg);
};

if (system.args.length < 3) {
  console.log('Enter some url and sitemap address with script launch like args');
  phantom.exit();
}

var site = {
	url: system.args[1],
	sitemap: system.args[2],
	webPages: new Array //pages from sitemap
}

page.open(site.sitemap, function(status) {
	if(status != 'success') {
		console.log('FAIL to load the address');
	} else {
		page.injectJs('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
		site.webPages = page.evaluate(function() {
			var webPagesArr = new Array;
			jQuery('ul li a').each(function() {
				var name = jQuery(this).html();
				var link = jQuery(this).attr('href');
				if(!link.match(/^\//) && !link.match(site.url)) link = ''; //filter crossdomain links /^http\:\/\/nbilko\.com/
				if(name && link && jQuery.inArray(link, webPagesArr != -1)) { //adding link to arr
					webPagesArr.push({
						name: name,
						link: link
					});
				}
			});
			return webPagesArr;
		});
		//for(i in webPages) console.log(i +':'+ webPages[i].name +', '+webPages[i].link); //checking output
		
	}
});

for(page in site.webPages) { //проход по всем страницам
	page.open(site.sitemap, function(status) {
		if(status != 'success') {
			console.log('FAIL to load page'+ page.link);
		} else {
			page.injectJs('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
			page.evaluate(function() {
				site.webPages.push({
					content: jQuery('html')
				});
			});
		}
	});
}

console.log(site.webPages.content);

phantom.exit();