var casper = require('casper').create({
	clientScripts: ["jquery.min.js", "process_page_content.js"]
}), system = require('system');
var utils = require('utils');

function DiffArrays(A,B) { // find and return difference between 2 arr
    var M = A.length, N = B.length, c = 0, C = [];
    for (var i = 0; i < M; i++)
     { var j = 0, k = 0;
       while (B[j] !== A[ i ] && j < N) j++;
       while (C[k] !== A[ i ] && k < c) k++;
       if (j == N && k == c) C[c++] = A[ i ];
     }
   return C;
}

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
		if(!href.match(/^\//) && !href.match(site.url) || href == '/' || href.match(/(gif|GIF|png|PNG|jpg|JPG|jpeg|JPEG|pdf|PDF|doc|DOC|docx|DOCX|xls|XLS|xlsx|XLSX)$/)) {
		} else {
			local.push(href);
		}
	}
	return local;
}

function filterLinks(links) { // filter double local links
	links.sort();
	for (var i = links.length - 1; i > 0; i--) {
		if (links[i] == links[i - 1] || (links[i].indexOf('#')+1)) links.splice( i, 1);
	}
	return links;
}

function getSiteMap(site, links) {
	links = filterLinks(links);
	var newLinks = new Array();
	this.each(links, function(self, link) {
		this.thenOpen(site.url + '' + link, function() {
			ls = filterLinks(getLocalLinks(this.evaluate(getLinks)));
			ls.sort();
			// utils.dump(link);
			// utils.dump(ls);
			var diff = DiffArrays(ls, links);
			if(diff.length) {
				if(!(diff[0].indexOf('/ru/')+1)) {
					// utils.dump(diff[0]);
					newLinks = newLinks.concat(diff);
				}
			}
		});
	});
	this.then(function() {
		newLinks = filterLinks(newLinks);
		
		this.each(newLinks, function(self, link) {
			this.thenOpen(site.url + '' + link, function() {
				ls = filterLinks(getLocalLinks(this.evaluate(getLinks)));
				ls.sort();
				var diff = DiffArrays(ls, newLinks);
				if(diff.length) {
					if(!(diff[0].indexOf('/ru/')+1)) {
						// utils.dump(diff[0]);
						if(DiffArrays(ls, links).length) { 
							links = links.concat(diff);
						}
					}
				}
			});
		});
		this.then(function() {
			// utils.dump(site);
			site.sitemap = site.sitemap.concat(links.concat(newLinks));
			site.sitemap.pop()
			// utils.dump(links);
		});
	});
	return site;
}

json_data = JSON.parse(system.args[5]);

site = {
	url: system.args[4],
	sitemap: new Array(),
	mainPages: json_data.mainPages,
	multilang: json_data.accept // accept form multilang.js
}
casper.start(site.url, function() {
	links = filterLinks(getLocalLinks(this.evaluate(getLinks)));
	site.sitemap.push('/');
	// utils.dump(site.sitemap);
	site = getSiteMap.call(this, site, links);
});

casper.run(function() {
	utils.dump(JSON.stringify(site));
	// utils.dump(json_data);
	// utils.dump(json_data.url);
	// utils.dump(json_data.mainPages);
	this.exit();
});