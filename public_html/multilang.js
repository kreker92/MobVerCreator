var casper = require('casper').create({
	clientScripts: ["jquery.min.js", "process_page_content.js"]
}), system = require('system');
var utils = require('utils');

function getLinks() {
	var links = document.querySelectorAll('a');
	return Array.prototype.map.call(links, function(e) {
		return e.getAttribute('href');
	});
}

function getLocalLinks(links) { // filter links for parsing
	var local = new Array();
	for(linkHref in links) {
		href = links[linkHref];
		if(!href.match(/^\//) && !href.match(site.url) || href == '/' || href.match(/(gif|GIF|png|PNG|jpg|JPG|jpeg|JPEG)$/)) {
		} else {
			local.push(href);
		}
	}
	return local;
}

function getP() { // get thelongest text in tag p (using for determine site lang)
	var longest = '';
	$('p').each(function() {
		if($(this).text().length > longest.length) {
			longest = $(this).text();
		}
	});
	return longest;
}

function findLangLinks(links) {
	var res = new Array();
	this.each(links, function(self, link) {
		if(link.substr(link.length-1, 1) == '/') {
			res.push(link);
		}
	});
	return res;
}

site = {
	url: system.args[4],
	mainPages: new Array(), // [ { lang: 'ru', url: '' }, { lang: 'en', url: '' } ]
	accept: false
}
casper.start(site.url, function() {
	links = getLocalLinks(this.evaluate(getLinks));
	links = findLangLinks.call(this, links);
	links.reverse();
	links.push('/');
	links.reverse();
});

function getMainPages(site, p) {
	this.each(p, function(self, i) {
		this.then(function() {
			var longest = i.text;
			this.open('http://ws.detectlanguage.com/0.2/detect?q='+ longest +'&key=70f6941e2574dbd52010cf3dce79aeb7', {
				method: 'get',
				headers: {
					'Accept': 'application/json'
				}
			});
			this.then(function() {
				var lang = JSON.parse(this.getPageContent());
				lang = lang.data.detections[0].language;
				var exist = false;
				
				this.each(site.mainPages, function(self, page) {
					if(page.lang == lang) {
						exist = true;
					}
				});
				if(!exist) {
					site.mainPages.push({
						lang: lang,
						url: i.url.substr(site.url.length, i.url.length)
					});
				}
			});
		});
	});
	return site;
}

casper.then(function() {
	var p = new Array();
	this.each(links, function(self, link) {
		this.thenOpen(site.url + '' + link, function() {
			p.push({
				url: site.url + '' + link,
				text: this.evaluate(getP)
			});
		});
	});
	this.then(function() {
		site = getMainPages.call(this, site, p);
		this.then(function() {
			if(site.mainPages.length > 1) {
				site.accept = true;
			}
		});
	});
});

casper.run(function() {
	utils.dump(JSON.stringify(site));
	// utils.dump(site.accept);
	this.exit();
});