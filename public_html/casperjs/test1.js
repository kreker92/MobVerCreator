var casper = require('casper').create({
	clientScripts: ["jquery.min.js"]
}), system = require('system');
// console.log(system.args); // 4 по умолчанию

var site = {
	url: system.args[4],
	webPages: new Array, //pages from sitemap
	menus: new Array, //founded site menus
	socBtns: new Array
}

function getLinks() {
	var links = document.querySelectorAll('a');
	return Array.prototype.map.call(links, function(e) {
		return e.getAttribute('href');
	});
}

function getLocalLinks(links) {
	var wP = new Array;
	for(linkHref in links) {
		href = links[linkHref];
		if(!href.match(/^\//) && !href.match(site.url) || href == '/') {
			
		} else {
			wP.push(href);
		}
	}
	return wP;
}

function getAbsoluteLinks(links) {
	var l = new Array;
	for(linkHref in links) {
		href = links[linkHref];
		if(!href.match(/^\//) && !href.match(site.url)) {
			l.push(href);
		}
	}
	return l;
}

function getSocBtns(links) {
	var h = new Array;
	var socNet = [
		['livejournal.com/', 	"/images/socBtns/"], 
		['twitter.com/', 		"/images/socBtns/"], 
		['vk.com/', 			"/images/socBtns/"], 
		['facebook.com/', 		"/images/socBtns/"], 
		['blogger.com/', 		"/images/socBtns/"], 
		['youtube.com/', 		"/images/socBtns/"], 
		['odnoklassniki.ru/', 	"/images/socBtns/"], 
		['blogs.mail.ru/', 		"/images/socBtns/"], 
		['lastfm.ru/', 			"/images/socBtns/"], 
		['wow.ya.ru/', 			"/images/socBtns/"], 
		['diary.ru/', 			"/images/socBtns/"], 
		['moikrug.ru/', 		"/images/socBtns/"], 
		['blog.ru/', 			"/images/socBtns/"], 
		['myspace.com/', 		"/images/socBtns/"], 
		['habrahabr.ru/', 		"/images/socBtns/"], 
		['picasa.com/', 		"/images/socBtns/"], 
		['mywishlist.ru/', 		"/images/socBtns/"], 
		['flickr.com/', 		"/images/socBtns/"], 
		['tumblr.com/', 		"/images/socBtns/"], 
		['fotki.yandex.ru/', 	"/images/socBtns/"], 
		['mirtesen.ru/', 		"/images/socBtns/"], 
		['memori.ru/', 			"/images/socBtns/"], 
		['wordpress.com/', 		"/images/socBtns/"], 
		['rutube.ru/', 			"/images/socBtns/"], 
		['deviantart.com/', 	"/images/socBtns/"], 
		['delicious.com/', 		"/images/socBtns/"], 
		['moemesto.ru/', 		"/images/socBtns/"], 
		['bobrdobr.ru/', 		"/images/socBtns/"], 
		['free-lance.ru/', 		"/images/socBtns/"], 
		['mmm-tasty.ru/', 		"/images/socBtns/"], 
		['privet.ru/', 			"/images/socBtns/"], 
		['toodoo.ru/', 			"/images/socBtns/"], 
		['plurk.com/', 			"/images/socBtns/"], 
		['foto.mail.ru/', 		"/images/socBtns/"], 
		['lookatme.ru/', 		"/images/socBtns/"], 
		['linkedin.com/', 		"/images/socBtns/"], 
		['news2.ru/', 			"/images/socBtns/"], 
		['imhonet.ru/', 		"/images/socBtns/"], 
		['lj.russia.org/', 		"/images/socBtns/"], 
		['beon.ru/', 			"/images/socBtns/"], 
		['vimeo.com/', 			"/images/socBtns/"], 
		['photofile.ru/', 		"/images/socBtns/"], 
		['planeta.rambler.ru/', "/images/socBtns/"], 
		['rpod.ru/', 			"/images/socBtns/"], 
		['smi2.ru/', 			"/images/socBtns/"], 
		['gallery.ru/', 		"/images/socBtns/"], 
		['ru.wikipedia.org/', 	"/images/socBtns/"], 
		['loveplanet.ru/', 		"/images/socBtns/"], 
		['smotri.com/', 		"/images/socBtns/"], 
		['livelib.ru/', 		"/images/socBtns/"], 
		['video.mail.ru/', 		"/images/socBtns/"], 
		['mamba.ru/', 			"/images/socBtns/"], 
		['jaiku.com/', 			"/images/socBtns/"], 
		['weblancer.net/', 		"/images/socBtns/"], 
		['orkut.com/', 			"/images/socBtns/"], 
		['photosight.ru/', 		"/images/socBtns/"], 
		['autokadabra.ru/', 	"/images/socBtns/"], 
		['dirty.ru/', 			"/images/socBtns/"], 
	];
	for(linkHref in links) {
		href = links[linkHref];
		for(i in socNet) {
			sN = socNet[i][0];
			if(href.match(sN)) {
				h.push([href, socNet[i][1]]);
				break;
			}
		}
	}
	return h;
}

function getMenus() {
	var result = new Array();
	$('.menu').each(function(i, e) {
		result[i] = new Array();
		$(e).find('a').each(function(j, q) {
			result[i].push({
				url: $(q).attr('href'),
				text: $(q).text()
			});
		});
	});
	return result;
}

casper.start(site.url, function() {
	links = this.evaluate(getLinks);
	site.webPages.push({
		url: site.url,
		links: getLocalLinks(links),
		content: this.getPageContent()
	});
	site.menus = this.evaluate(getMenus);
	site.socBtns = getSocBtns(getAbsoluteLinks(links));
});

casper.then(function() {
	i = -1;
	var pages = site.webPages[0].links;
	this.each(pages, function(response) {
		i++;
		casper.thenOpen(site.url +''+ pages[i], function() {
			links = this.evaluate(getLinks);
			links = getLocalLinks(links);
			site.webPages.push({
				url: this.getCurrentUrl(), 
				links: links,
				content: this.getPageContent()
			});
		});
	});
});

casper.run(function() {
	// this.echo(links.length + ' links found:');
	// this.echo(' - ' + links.join('\n - '));
	
	this.echo('Sitemap:');
	for(i in site.webPages) {
		this.echo(site.webPages[i].url +': ');
		this.echo(site.webPages[i].links);
		if(site.webPages[i].content) {
			this.echo(true);
		} else {
			this.echo(false);
		}
		this.echo('');
	}
	
	this.echo('Menus:');
	i = -1;
	this.each(site.menus, function() {
		i++;
		this.echo('Menu '+ i +':');
		j = -1;
		this.each(site.menus[i], function() {
			j++;
			this.echo(site.menus[i][j].text +': '+ site.menus[i][j].url);
		});
	});
	
	this.echo('\n socBtns:');
	this.echo(' - ' + site.socBtns.join('\n - '));
	this.echo(' ').exit();
});