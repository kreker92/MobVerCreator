var casper = require('casper').create({
	clientScripts: ["jquery.min.js", "process_page_content.js"]
}), system = require('system');
var utils = require('utils');

var url = system.args[4];
var imgUrl = 'site/';
var cssUrl = ;

casper.start(url, function() {
	img = this.evaluate(function() {
		var src = $('img').first().attr('src');
		var name = src.split('/');
		img = {
			path: src,
			name: name[name.length-1]
		};
		return img;
	});
	this.download(url + '' + img.path, 'site/' + img.name + '');
});

casper.run(function() {
    this.echo('Done.').exit();
});