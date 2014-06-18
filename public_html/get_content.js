var casper = require('casper').create({
	clientScripts: ["jquery.min.js", "process_page_content.js"]
}), system = require('system');
var utils = require('utils');

function getLinks() { // get all links from page
	var links = document.querySelectorAll('a');
	return Array.prototype.map.call(links, function(e) {
		return e.getAttribute('href');
	});
}

function getAbsoluteLinks(links) { // filter links for catching soc Btns
	var l = new Array();
	for(linkHref in links) {
		href = links[linkHref];
		if(!href.match(/^\//) && !href.match(site.url)) {
			l.push(href);
		}
	}
	return l;
}

function emptyObject(obj) { // checking for empty object
	for (var i in obj) {
		return false;
	}
	return true;
}

function array_diff() { // find and return difference between 2 arr
	var arr1 = arguments[0], retArr = {};
	var k1 = '', i = 1, k = '', arr = {};
 
	arr1keys:
	for (k1 in arr1) {
		for (i = 1; i < arguments.length; i++) {
			arr = arguments[i];
			for (k in arr) {
				if (arr[k].url === arr1[k1].url) {
					continue arr1keys; 
				}
			}
			retArr[k1] = arr1[k1];
		}
	}
	return retArr;
}

function processMenu(arr) {
	for(i in arr) {
		arr[i].sort();
	}
	arr.sort(function(a, b) {
		return a.length - b.length;
	});
	for(i in arr) {
		for(j in arr) {
			if(j == i) continue;
			if(emptyObject(array_diff(arr[i], arr[j]))) arr.splice(i, 1);
		}
	}
	return arr.reverse();
}

function getMenu(menus) {
	var result = {
		identifier: '',
		items: new Array()
	};
	for(t in menus) {
		if($(menus[t]).length) {
			result.identifier = menus[t];
			menus = $(result.identifier);
			break;
		}
	}
	menus.each(function(i, e) {
		result.items[i] = new Array();
		$(e).find('a').each(function(j, q) {
			result.items[i].push({
				url: $(q).attr('href'),
				text: $(q).text()
			});
		});
	});
	return result;
}

function getMenus(site) {
	var ident = ['.menu', 'nav', '[class$=menu]', '[class^=menu]'];
	this.each(site.mainPages, function(self, page) {
		this.thenOpen(site.url + '' + page.url, function() {
			if(site.menus.identifier.length) ident = [site.menus.identifier];
			menuContent = this.evaluate(getMenu, ident);
			
		});
		this.then(function() {
			site.menus.items.push({
				lang: page.lang,
				items: processMenu(menuContent.items)
			});
		});
	});
	this.then(function() {
		site.menus.identifier = menuContent.identifier;
	});
	
	return site;
}

function getTop(top) { // find top of site
	var result = {
		identifier: '',
		content: ''
	};
	for(t in top) {
		if($(top[t]).length) {
			result.identifier = top[t];
			result.content = $(result.identifier).find('img');
			break;
		}
	}
	result.content.each(function(i, e) {
		if($(e).attr('src').toLowerCase().match('logo')) {
			result.content = $(e).attr('src');
			return false;
		}
	});
	return result;
}

function getTops(site) {
	this.each(site.mainPages, function(self, page) {
		this.thenOpen(site.url + '' + page.url, function() {
			var ident = ['header', '#header', '[id$=header]', '[id^=header]', '.header', '[class$=header]', '[class^=header]'];
			if(site.top.identifier.length) ident = [site.top.identifier];
			topContent = this.evaluate(getTop, ident);
			if(!site.top.identifier.length) site.top.identifier = topContent.identifier;
			site.top.content.push({
				lang: page.lang,
				content: site.url + '' + topContent.content
			});
		});
	});
	return site;
}

function getFooter(footer) { // find site footer
	var result = {
		identifier: '',
		content: new Array()
	};
	for(t in footer) {
		if($(footer[t]).length) {
			result.identifier = footer[t];
			result.content = $(result.identifier).find('p');
			break;
		}
	}
	var text = '';
	result.content.each(function(i, e) {
		text = text + $(e).html() +"\n";
	});
	result.content = text;
	return result;
}

function getFooters(site) {
	this.each(site.mainPages, function(self, page) {
		this.thenOpen(site.url + '' + page.url, function() {
			var ident = ['footer', '#footer', '[id$=footer]', '[id^=footer]', '.footer', '[class$=footer]', '[class^=footer]'];
			if(site.footer.identifier.length) ident = [site.footer.identifier];
			footerContent = this.evaluate(getFooter, ident);
			if(!site.footer.identifier.length) site.footer.identifier = footerContent.identifier;
			site.footer.content.push({
				lang: page.lang,
				content: footerContent.content
			});
		});
	});
	return site;
}

function getSocBtns(site) { // find soc Btns
	var links = getAbsoluteLinks(this.evaluate(getLinks));
	var h = new Array();
	var socNet = [
		['blogger.com/', 		"/images/socBtns/blogger.png"],
		['blogs.mail.ru/', 		"/images/socBtns/mail.png"],
		['deviantart.com/', 	"/images/socBtns/devianart.png"],
		['facebook.com/', 		"/images/socBtns/facebook.png"],
		['flickr.com/', 		"/images/socBtns/flickr.png"],
		['free-lance.ru/', 		"/images/socBtns/free-lance.png"],
		['habrahabr.ru/', 		"/images/socBtns/habrahabr.png"],
		['linkedin.com/', 		"/images/socBtns/linkedin.png"],
		['livejournal.com/', 	"/images/socBtns/livejournal.png"],
		['myspace.com/', 		"/images/socBtns/myspace.png"],
		['odnoklassniki.ru/', 	"/images/socBtns/odnoklassniki.png"],
		['pinterest.com/', 		"/images/socBtns/pinterest.png"],
		['plus.google.com/', 	"/images/socBtns/googleplus.png"],
		['rutube.ru/', 			"/images/socBtns/rutube.png"],
		['tumblr.com/', 		"/images/socBtns/tumblr.png"],
		['twitter.com/', 		"/images/socBtns/twitter.png"],
		['vimeo.com/', 			"/images/socBtns/vimeo.png"],
		['vk.com/', 			"/images/socBtns/vk.png"],
		['youtube.com/',		"/images/socBtns/youtube.png"]
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
	site.socBtns = h;
	return site;
}

	
function getContent(site, page) {
	/* determ lang */
	for(var i=1; i<site.mainPages.length; i++) {
		if(page.substr(0, site.mainPages[i].url.length) == site.mainPages[i].url) {
			var lang = i;
		} else {
			var lang = 0;
		}
	}
	var callUs = [['Позвоните нам', 'Имя', 'Сброс', 'Отправить', 'Телефон', 'Закрыть'], ['Call us', 'Name', 'Reset', 'Submit', 'Phone', 'Close']];
	if(lang) {
		t = 1
	} else {
		t = 0;
	}
	/* delete garbage */
	$('script').remove();
	$(site.footer.identifier).remove();
	$(site.menus.identifier).remove();
	$(site.top.identifier).remove();
	$('form').remove();
	$('noscript').remove();
	$('base').attr('href', 'http://diplom.kreker92.tmweb.ru/site/'+ site.url.substr(7, site.url.length-1) +'');
	$('div').each(function() {
		if($.trim($(this).text()) == '') {
			$(this).remove();
		}
	});
	$("#wrapper").replaceWith(function() {
		return $(this).contents(); 
	});
	
	/* set paths to css */
	var href = '';
	$('link[rel="stylesheet"]').each(function() {
		href = $(this).attr('href');
		if(href.match(/^http/) == null && href.match(/^\/\//) == null) {
			href = site.url + '' + href;
			link=document.createElement('link');
			link.href = href;
			link.rel = 'rel';
			$(this).remove();
			document.getElementsByTagName('head')[0].appendChild(link);
		}
	});
	/* set path to imgs */
	$('img').each(function() {
		img = $(this);
		src = img.attr('src');
		if(src.match(/^http/) == null && src.match(/^\/\//) == null) {
			src = site.url + '' + src;
			img.attr('src', src);
		}
	});
	
	/* add elements for jquery mobile */
	$('head').append('<link rel="stylesheet" href="http://diplom.kreker92.tmweb.ru/templates/protostar/css/jquery.mobile-1.4.2.min.css" />');
	$('head').append('<link rel="stylesheet" href="http://diplom.kreker92.tmweb.ru/templates/protostar/css/basic-site-style.css" />');
	var script = document.createElement('script');
	script.setAttribute('src','http://diplom.kreker92.tmweb.ru/templates/protostar/js/jquery-1.11.1.min.js');
	document.head.appendChild(script);
	var script = document.createElement('script');
	script.setAttribute('src','http://diplom.kreker92.tmweb.ru/templates/protostar/js/jquery.mobile-1.4.2.min.js');
	document.head.appendChild(script);
	$('body').wrapInner('<div data-role="page"></div>');
	$('div[data-role="page"]').attr('id', 'wrapper');
	 /* adding menu */
	$('div[data-role="page"]').prepend('<div data-role="panel" data-position="left" data-display="overlay" data-theme="b" id="mainMenu">');
	$('#mainMenu').append('<ul data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-icon="delete"><a class="ui-btn ui-btn-icon-right ui-icon-delete" href="#" data-rel="close">' + callUs[t][5] + '</a></li></ul>');
	if(site.menus.items[lang].items.length) {

		for(i = 0; i < site.menus.items[lang].items.length; i++) {
			
			var menuItems = '';
			for(j=0; j < site.menus.items[lang].items[i].length; j++) {
				menuItems = menuItems + '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="'+ site.menus.items[lang].items[i][j].url +'">'+ site.menus.items[lang].items[i][j].text +'</a></li>';
			}
			
			$('#mainMenu').append('<ul data-menu-id="menu'+ i +'" data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-role="list-divider"></li>'+ menuItems +'</ul>');
		}
	}
	$('#mainMenu').append('</div>');
	/* adding callback */
	
	$('<div data-role="panel"  data-position="right" data-display="overlay" data-theme="b" id="callbackForm"><ul data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-icon="delete"><a class="ui-btn ui-btn-icon-right ui-icon-delete" href="#" data-rel="close">' + callUs[t][5] + '</a></li></ul>').insertAfter('#mainMenu');
	$('#callbackForm').append('<a data-icon="phone" href="tel:8005551212" class="ui-shadow ui-btn ui-corner-all ui-icon-phone ui-btn-icon-left ui-btn-a">' + callUs[t][0] + '</a>');
	$('#callbackForm').append('<form><input placeholder="' + callUs[t][1] + '" 	type="text" 	name="name" 	id="form-name" value=""><input placeholder="E-mail" type="email" 	name="email" 	id="form-email" value=""><input placeholder="' + callUs[t][4] + '" 	type="tel" 		name="tel" 			id="form-tel" 		value=""><input type="submit" value="' + callUs[t][3] + '"><input type="reset" value="' + callUs[t][2] + '"></form></div>');
	$('<header data-role="header" role="banner" class="ui-header ui-bar-inherit"><div id="logo" role="heading"><a href="/"><img src="'+ site.top.content[lang].content +'" alt="Logo" /></a></div><a href="#mainMenu" data-icon="bars" data-iconpos="notext" class="ui-link ui-btn-left ui-btn ui-icon-bars ui-btn-icon-notext ui-shadow ui-corner-all"></a><a href="#callbackForm" data-icon="mail" data-iconpos="notext" class="ui-link ui-btn-right ui-btn ui-icon-mail ui-btn-icon-notext ui-shadow ui-corner-all"></a></header>').insertAfter('#callbackForm');
	function setClass(tag, className) {
		if(!$(tag).is(site.footer.identifier) && $(tag).attr('id') != site.footer.identifier && !$(tag).hasClass(site.footer.identifier) && $(tag).length) {
			$(tag).addClass(className);
			$(tag).attr('role', 'main');
			setClass($(tag).next(), className);
		}
	}
	setClass($('header.ui-header').next(), 'ui-content');
	$('div[data-role="page"]').append('<footer data-role=footer>'+ site.footer.content[lang].content +'</footer>');
	$('a').each(function() {
		href = $(this).attr('href')
		if(!href.match(/^http/)) $(this).attr('href', site.dirUri + '' + href);
	});
	
	var doc = document.documentElement.outerHTML;
	return doc;
}

function getContents(site) {
	this.each(site.sitemap, function(self, page) {
		if(site.multilang && page == '/') page = page + '/' + site.mainPages[0].lang + '/';
		if(page == '/ru/') page = '/'; 
		casper.thenOpen(site.url + '' + page,function() {
			site.webPages.push({
				url: page,
				content: this.evaluate(getContent, site, page)
			});
		});
	});
	return site;
}

json_data = JSON.parse(system.args[5]);

var site = {
	url: system.args[4],
	mainPages: json_data.mainPages, // [ { lang: 'ru', url: '' }, { lang: 'en', url: '' } ]
	multilang: json_data.multilang,
	sitemap: json_data.sitemap, // ['', '', '']
	webPages: new Array(), // [ url: '', content: '' ]
	menus: { //processed menus
		identifier: '',
		items: new Array()// [{ lang: 'ru', items: [ [{ url: '', text: '' }, ... ], ... ] }, { ... }, ... ]
	},
	top: { // img logo with link to main page
		identifier: '',
		content: new Array() // [{ lang: 'ru', content: '' }, { lang: 'en', content: '' }]
	},
	footer: { // parse only text
		identifier: '',
		content: new Array() // [{ lang: 'ru', content: '' }, { lang: 'en', content: '' }]
	},
	socBtns: new Array(), //gets on main page; [ ['', '*.jpg'], ['', '*.jpg'] ]
	dirUri: system.args[6]
}

casper.start(site.url, function() {
	//test data
	// site.mainPages = [{ lang: 'ru', url: '/'}, { lang: 'en', url: '/en/' }];
	// site.sitemap = [ "/", "/biography", "/contacts", "/en/", "/gallery", "/gallery/birthday", "/gallery/corporative", "/gallery/teambuilding", "/gallery/wedding", "/services", "/site-map", "/en/biography", "/en/contacts", "/en/gallery", "/en/gallery/birthday", "/en/gallery/corporative", "/en/gallery/teambuilding", "/en/gallery/wedding", "/en/services", "/en/site-map" ];
	// site.dirUri = 'site/nbilko.com/';
	
	if(site.multilang) {
		this.each(site.mainPages, function(self, page) {
			if(page.url == '/') page.url = '/' + page.lang + '/';
		});
	}
	
	// getting menus
	site = getMenus.call(this, site);
	// getting top
	site = getTops.call(this, site);
	// getting footer
	site = getFooters.call(this, site);
	// getting socBtns
	site = getSocBtns.call(this, site);
	// getting content
	site = getContents.call(this, site);
});

casper.run(function() {
	utils.dump(site);
	// utils.dump(site.menus);
	// utils.dump(site.top);
	// utils.dump(site.footer);
	// utils.dump(site.socBtns);
	// utils.dump(site.webPages[0]);
	// utils.dump(site.webPages[3]);
	this.exit();
});