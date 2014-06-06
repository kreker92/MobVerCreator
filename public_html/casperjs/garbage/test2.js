var casper = require('casper').create({
	clientScripts: ["jquery.min.js"]
}), system = require('system');
// console.log(system.args); // 4 по умолчанию

function getLinks() {
	var links = document.querySelectorAll('a');
	return Array.prototype.map.call(links, function(e) {
		return e.getAttribute('href');
	});
}

/*  ---- Basic Methods ----  */

//main site Object
var site = { 
	url: system.args[4],
	webPages: { //main object for all pages
		url: '',
		links: new Array(),
		content: ''
	},
	menus: { //processed menus are here
		identificator: '',
		items: new Array
	},
	socBtns: new Array,
	sitemap: new Array,
	bottom: {
		Identificator: '',
		content: ''
	}
};

function processContent(obj) {
	links = obj.evaluate(getLinks);
}

casper.start(site.url, function() {
	processContent(this);
	// links = this.evaluate(getLinks);
});

casper.run(function() {
	this.echo(links.length + ' links found:');
	this.echo(' - ' + links.join('\n - '));
	this.echo(' ').exit();
});