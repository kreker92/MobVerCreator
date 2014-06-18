var casper = require('casper').create({
	clientScripts: ["jquery.min.js", "process_page_content.js"]
}), system = require('system');
var utils = require('utils');
// console.log(system.args); // 4 по умолчанию

/*  ----  Additional Methods  ----  */

function getLinks() {
	var links = document.querySelectorAll('a');
	return Array.prototype.map.call(links, function(e) {
		return e.getAttribute('href');
	});
}

function getLocalLinks(links) { // filter links for parsing
	var wP = new Array();
	for(linkHref in links) {
		href = links[linkHref];
		if(!href.match(/^\//) && !href.match(site.url) || href == '/' || href.match(/(gif|GIF|png|PNG|jpg|JPG|jpeg|JPEG|pdf|PDF|doc|DOC|docx|DOCX|xls|XLS|xlsx|XLSX)$/)) {
		} else {
			wP.push(href);
		}
	}
	return wP;
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

function emptyObject(obj) { // checking for empty object
	for (var i in obj) {
		return false;
	}
	return true;
}

function DiffArrays(A,B) { // find and return difference between 2 arr B in A
    var M = A.length, N = B.length, c = 0, C = [];
    for (var i = 0; i < M; i++)
     { var j = 0, k = 0;
       while (B[j] !== A[ i ] && j < N) j++;
       while (C[k] !== A[ i ] && k < c) k++;
       if (j == N && k == c) C[c++] = A[ i ];
     }
   return C;
}

/*  ----  Methods For All Pages  ---- */

function addLinksToSitemap(sitemap, pages) { // add local links to sitemap
	for(i in pages) {
		for(j in links) {
			sitemap.push(pages[i].links[j]);
		}
	}
	sitemap.sort();
	for (var j = sitemap.length - 1; j > 0; j--) {
		if (sitemap[j] == sitemap[j - 1]) sitemap.splice(j, 1);
	}
	return sitemap.sort();
}

/*  ----  Second Pages Methods  ----  */

function filterLinks(links) { // filter double local links
	for(i in links) {
		for(j in site.menus.items) {
			for(g in site.menus.items[j]) {
				if(links[i] == site.menus.items[j][g].url) {
					links.splice(i, 1);
				}
			}
		}
	}
	links.sort();
	for (i = links.length - 1; i > 0; i--) {
		if (links[i] == links[i - 1]) links.splice( i, 1);
	}
	return links;
}

function getElseContent(site) {
	this.then(function() {
		var arr = new Array();
		this.each(site.webPages, function(self, page) {
			arr.push(page.url.substr(site.url.length, page.url.length));
		});
		arr = DiffArrays(site.sitemap, arr);
		var arr2 = new Array();
		this.each(arr, function(self, link) {
			if(link != null) {
				arr2.push( site.url +''+ link );
			}
		});
		this.each(arr2,function(self, link) {
			casper.thenOpen(link, function() {
				site.webPages.push({
					url: this.getCurrentUrl(),
					links: filterLinks(getLocalLinks(this.evaluate(getLinks))),
					content: contenti
				});
			});
			site.sitemap = addLinksToSitemap(site.sitemap, site.webPages);
		});
	});
	return site;
}

/*  ----  Main Page Methods  ----  */

function addContentFirst(site) {
	i = -1;
	var pages = site.webPages[0].links;
	this.each(pages, function(response) {
		i++;
		casper.thenOpen(site.url +''+ pages[i], function() {
			if(site.multilang.accept) {
				var l = site.multilang.data[1].url.substr(site.url.length, site.multilang.data[1].url.length-site.url.length);
			
				if(site.multilang.accept && pages[i] == l) {
					// site.menus = site.multiMenus[0];
				}
			}
			contenti = this.evaluate(function(site) {
				//deleting garbage
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
				//add elements for jquery mobile
				$('head').append('<link rel="stylesheet" href="http://diplom.kreker92.tmweb.ru/templates/protostar/css/jquery.mobile-1.4.2.min.css" />');
				$('head').append('<link rel="stylesheet" href="http://diplom.kreker92.tmweb.ru/templates/protostar/css/basic-site-style.css" />');
				var script = document.createElement('script');
				script.setAttribute('src','http://diplom.kreker92.tmweb.ru/templates/protostar/js/jquery-1.11.1.min.js');
				document.head.appendChild(script);
				var script = document.createElement('script');
				script.setAttribute('src','http://diplom.kreker92.tmweb.ru/templates/protostar/js/jquery.mobile-1.4.2.min.js');
				document.head.appendChild(script);
				$('body').wrapInner('<div data-role="page"></div>');
				// adding menu
				$('div[data-role="page"]').prepend('<div data-role="panel" data-position="left" data-display="overlay" data-theme="b" id="mainMenu">');
				$('#mainMenu').append('<ul data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-icon="delete"><a class="ui-btn ui-btn-icon-right ui-icon-delete" href="#" data-rel="close">Закрыть меню</a></li></ul>');
				if(site.menus != null) {
					for(i = 0; i < site.menus.items.length; i++) {
						var menuItems = '';
						for(j=0; j < site.menus.items[i].length; j++) {
							menuItems = menuItems + '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="'+ site.menus.items[i][j].url +'">'+ site.menus.items[i][j].text +'</a></li>';
						}
						$('#mainMenu').append('<ul data-menu-id="menu'+ i +'" data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-role="list-divider"></li>'+ menuItems +'</ul>');
					}
				}
				$('#mainMenu').append('</div>');
				// adding callback
				$('<div data-role="panel"  data-position="right" data-display="overlay" data-theme="b" id="callbackForm"><ul data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-icon="delete"><a class="ui-btn ui-btn-icon-right ui-icon-delete" href="#" data-rel="close">Закрыть</a></li></ul>').insertAfter('#mainMenu');
				$('#callbackForm').append('<a data-icon="phone" href="tel:8005551212" class="ui-shadow ui-btn ui-corner-all ui-icon-phone ui-btn-icon-left ui-btn-a">Позвоните нам</a>');
				$('#callbackForm').append('<form><input placeholder="Name" 	type="text" 	name="text-basic" 	id="text-basic" value=""><input placeholder="E-mail" type="email" 	name="text-basic" 	id="text-basic" value=""><input placeholder="Tel" 	type="tel" 		name="tel" 			id="tel" 		value=""><input type="submit" value="Отправить"><input type="reset" value="Сброс"></form></div>');
				$('<header data-role="header" role="banner" class="ui-header ui-bar-inherit"><div id="logo" role="heading"><a href="/"><img src="'+ site.top.content +'" alt="Logo" /></a></div><a href="#mainMenu" data-icon="bars" data-iconpos="notext" class="ui-link ui-btn-left ui-btn ui-icon-bars ui-btn-icon-notext ui-shadow ui-corner-all"></a><a href="#callbackForm" data-icon="mail" data-iconpos="notext" class="ui-link ui-btn-right ui-btn ui-icon-mail ui-btn-icon-notext ui-shadow ui-corner-all"></a></header>').insertAfter('#callbackForm');
				// adding attrs to content
				function setClass(tag, className) {
					if(!$(tag).is(site.footer.identifier) && $(tag).attr('id') != site.footer.identifier && !$(tag).hasClass(site.footer.identifier) && $(tag).length) {
						$(tag).addClass(className);
						$(tag).attr('role', 'main');
						setClass($(tag).next(), className);
					}
				}
				setClass($(site.top.identifier).next(), 'ui-content');
				// adding footer
				$('div[data-role="page"]').append('<footer data-role=footer>'+ site.footer.content +'</footer>');
			}, site);
			contenti = this.evaluate(function() {
				var doc = document.documentElement.outerHTML;
				return doc;
			});
			site.webPages.push({
				url: this.getCurrentUrl(), 
				links: filterLinks(getLocalLinks(this.evaluate(getLinks))),
				content: contenti
			});
			site.sitemap = addLinksToSitemap(site.sitemap, site.webPages);
		});
	});
	return site;
}

function getMenus() {
	var result = {
		identifier: '',
		items: new Array()
	};
	var menus = ['.menu', 'nav', '[class$=menu]', '[class^=menu]'];
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

function getSocBtns(links) { // returns finded soc Btns
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
	return h;
}

function getTop() { // find top of site
	var result = {
		identifier: '',
		content: ''
	};
	var top = ['header', '#header', '[id$=header]', '[id^=header]', '.header', '[class$=header]', '[class^=header]'];
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

function getFooter() { // find site footer
	var result = {
		identifier: '',
		content: new Array()
	};
	var footer = ['footer', '#footer', '[id$=footer]', '[id^=footer]', '.footer', '[class$=footer]', '[class^=footer]'];
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

function getMultilang(site) { // using detectlanguage.com to determine lang
	if(site.menus != null) {
	var menus = new Array();
	this.each(site.menus.items, function(self, links) {
		this.each(links, function(self2, link) {
			menus.push(link.url);
		});
	});
	var arr = DiffArrays(site.webPages[0].links, menus); //filter links to find multilang
	} else {
		arr = site.webPages[0].links;
	}
	// this.echo(arr);
	var langs = new Array();
	this.each(arr, function(self, link) {
		this.thenOpen(site.url +''+ link, function() {
			langs.push({
				url: link,
				lang: this.evaluate(getP)
			});
			// this.echo(this.evaluate(getP));
		});
	});
	this.then(function() {
		this.each(langs, function(self, lang) {
			this.thenOpen('http://ws.detectlanguage.com/0.2/detect?q='+ lang.lang +'&key=70f6941e2574dbd52010cf3dce79aeb7', {
				method: 'get',
				headers: {
					'Accept': 'application/json'
				}
			});
			this.then(function() {
				lang2 = JSON.parse(this.getPageContent());
					if(lang2.data.detections.length > 1) {
						// many langs
					} else {
						var exist = false;
						this.each(site.multilang.data, function(self3, d) {
							if(d.language == lang2.data.detections[0].language) {
								exist = true;
							}
						});
						if(!exist) {
							site.multilang.accept = true;
							site.multilang.data.push({
								url: site.url +''+ lang.url,
								language: lang2.data.detections[0].language
							});
						}
					}
			});
		});
	});
	return site;
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

function getSiteLang(site) {
	var longest = '';
	this.thenOpen(site.url, function() {
		longest = this.evaluate(getP);
	});
	this.then(function() {
		this.open('http://ws.detectlanguage.com/0.2/detect?q='+ longest +'&key=70f6941e2574dbd52010cf3dce79aeb7', {
			method: 'get',
			headers: {
				'Accept': 'application/json'
			}
		});
		this.then(function() {
			lang = JSON.parse(this.getPageContent());
			// this.echo(lang.data.detections[0].language);
			site.multilang.data.push({
				url: site.url,
				language: lang.data.detections[0].language
			});
		});
	});
	return site;
}

/*  ----  Basic Methods & Objects  ----  */

var site = {
	url: system.args[4],
	mainPages: new Array(), // [ { lang: 'ru', url: '' }, { lang: 'en', url: '' } ]
	webPages: new Array(), // [ url: '', links: ['', ''], content: '' ]
	menus: { //processed menus
		identifier: '',
		items: new Array()// [{ lang: 'ru', items: '' }, { lang: 'en', items: '' }]
	},
	top: { // img logo with link to main page
		identifier: '',
		content: new Array() // [{ lang: 'ru', content: '' }, { lang: 'en', content: '' }]
	},
	footer: { // parse only text
		identifier: '',
		content: new Array() // [{ lang: 'ru', content: '' }, { lang: 'en', content: '' }]
	},
	socBtns: new Array(), //gets on main page; [ ['', '.jpg'], ['', '.jpg'] ]
	sitemap: new Array(), // ['', '', '']
	multilang: false,
	images: new Array(), // ['', '', '']
	css: new Array() // ['', '', '']
}

casper.start(site.url, function() {
	links = this.evaluate(getLinks);
	
	site.menus 			= this.evaluate(getMenus);
	if(site.menus != null) {
		site.menus.items 	= processMenu(site.menus.items);
	}
	
	site.top 			= this.evaluate(getTop);
	site.footer 		= this.evaluate(getFooter);
	site.socBtns 		= getSocBtns(getAbsoluteLinks(links));
	footer = 'footer';
	contenti = this.evaluate(function(site) {
		//deleting garbage
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
		//add elements for jquery mobile
		$('head').append('<link rel="stylesheet" href="http://diplom.kreker92.tmweb.ru/templates/protostar/css/jquery.mobile-1.4.2.min.css" />');
		$('head').append('<link rel="stylesheet" href="http://diplom.kreker92.tmweb.ru/templates/protostar/css/basic-site-style.css" />');
		var script = document.createElement('script');
		script.setAttribute('src','http://diplom.kreker92.tmweb.ru/templates/protostar/js/jquery-1.11.1.min.js');
		document.head.appendChild(script);
		var script = document.createElement('script');
		script.setAttribute('src','http://diplom.kreker92.tmweb.ru/templates/protostar/js/jquery.mobile-1.4.2.min.js');
		document.head.appendChild(script);
		$('body').wrapInner('<div data-role="page"></div>');
		// adding menu
		$('div[data-role="page"]').prepend('<div data-role="panel" data-position="left" data-display="overlay" data-theme="b" id="mainMenu">');
		$('#mainMenu').append('<ul data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-icon="delete"><a class="ui-btn ui-btn-icon-right ui-icon-delete" href="#" data-rel="close">Закрыть меню</a></li></ul>');
		if(site.menus != null) {
			for(i = 0; i < site.menus.items.length; i++) {
				var menuItems = '';
				for(j=0; j < site.menus.items[i].length; j++) {
					menuItems = menuItems + '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="'+ site.menus.items[i][j].url +'">'+ site.menus.items[i][j].text +'</a></li>';
				}
				$('#mainMenu').append('<ul data-menu-id="menu'+ i +'" data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-role="list-divider"></li>'+ menuItems +'</ul>');
			}
		}
		$('#mainMenu').append('</div>');
		// adding callback
		$('<div data-role="panel"  data-position="right" data-display="overlay" data-theme="b" id="callbackForm"><ul data-role="listview" class="ui-listview ui-corner-all ui-shadow ui-group-theme-b"><li data-icon="delete"><a class="ui-btn ui-btn-icon-right ui-icon-delete" href="#" data-rel="close">Закрыть</a></li></ul>').insertAfter('#mainMenu');
		$('#callbackForm').append('<a data-icon="phone" href="tel:8005551212" class="ui-shadow ui-btn ui-corner-all ui-icon-phone ui-btn-icon-left ui-btn-a">Позвоните нам</a>');
		$('#callbackForm').append('<form><input placeholder="Name" 	type="text" 	name="text-basic" 	id="text-basic" value=""><input placeholder="E-mail" type="email" 	name="text-basic" 	id="text-basic" value=""><input placeholder="Tel" 	type="tel" 		name="tel" 			id="tel" 		value=""><input type="submit" value="Отправить"><input type="reset" value="Сброс"></form></div>');
		$('<header data-role="header" role="banner" class="ui-header ui-bar-inherit"><div id="logo" role="heading"><a href="/"><img src="'+ site.top.content +'" alt="Logo" /></a></div><a href="#mainMenu" data-icon="bars" data-iconpos="notext" class="ui-link ui-btn-left ui-btn ui-icon-bars ui-btn-icon-notext ui-shadow ui-corner-all"></a><a href="#callbackForm" data-icon="mail" data-iconpos="notext" class="ui-link ui-btn-right ui-btn ui-icon-mail ui-btn-icon-notext ui-shadow ui-corner-all"></a></header>').insertAfter('#callbackForm');
		// adding attrs to content
		function setClass(tag, className) {
			if(!$(tag).is(site.footer.identifier) && $(tag).attr('id') != site.footer.identifier && !$(tag).hasClass(site.footer.identifier) && $(tag).length) {
				$(tag).addClass(className);
				$(tag).attr('role', 'main');
				setClass($(tag).next(), className);
			}
		}
		setClass($(site.top.identifier).next(), 'ui-content');
		// adding footer
		$('div[data-role="page"]').append('<footer data-role=footer>'+ site.footer.content +'</footer>');
	}, site);
	contenti = this.evaluate(function() {
		var doc = document.documentElement.outerHTML;
		return doc;
	});
	
	site.webPages.push({
		url: site.url,
		links: getLocalLinks(links),
		content: contenti
	});
	site.sitemap = addLinksToSitemap(site.sitemap, site.webPages);
	site = getSiteLang.call(this, site);
	site = getMultilang.call(this, site);
});

casper.then(function() {
	site = addContentFirst.call(this, site);
	// site = addContentSecond.call(this, site;)
	site = getElseContent.call(this, site);
	// utils.dump(site.multilang);
	for(var i = 1;i <site.multilang.data.length;i++) {
		casper.thenOpen(site.url +''+ site.multilang.data[i].url, function() {
			site.multiMenus.push( this.evaluate(getMenus) );
			/* // site.multiMenus[0].items = processMenu(site.menus.items); */
		});
	}
});

casper.run(function() {
	// utils.dump(site);
	// utils.dump(site.multiMenus);
	// utils.dump(site.multilang.data);
	// utils.dump(site.webPages[2].content);
	// this.echo(links.length + ' links found:');
	// this.echo(' - ' + links.join('\n - '));
	
	// this.echo('Sitemap:');
	// this.echo(site.sitemap);
	// this.echo('Pages:');
	// this.echo(site.webPages[0].content);
	// for(i in site.webPages) {
		// this.echo(site.webPages[i].url);
		// this.echo(site.webPages[i].links);
		// if(site.webPages[i].content) {
			// this.echo('Content is loaded: '+ true);
		// } else {
			// this.echo('Content is loaded: '+ false);
		// }
		// this.echo('');
	// }
	// this.echo('Site top:');
	// this.echo(site.top.content);
	// this.echo('Site footer:');
	// this.echo(site.footer.content);
	// this.echo('');
	// this.echo('Menus:');
	// i = -1;
	// this.each(site.menus.items, function() {
		// i++;
		// this.echo('Menu '+ i +':');
		// j = -1;
		// this.each(site.menus.items[i], function() {
			// j++;
			// this.echo(site.menus.items[i][j].text +': '+ site.menus.items[i][j].url);
		// });
	// });
	
	// this.echo('\n socBtns:');
	// this.echo(' - ' + site.socBtns.join('\n - '));
	
	
	this.exit();
});