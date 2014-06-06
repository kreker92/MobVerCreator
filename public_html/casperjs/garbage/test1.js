/*
  --------------  Работы по сайту  -----------------  
  1. Сбор карты сайта
	* сбор всех локальных ссылок в общий массив - нет сбора ссылок со всей иерархии
	* сбор ссылок по иерархии.в массив входит: адрес самой ссылки, массив ссылок на странице, обработанный контент страницы - не доделан проход по всем иерархиям, контент пока не обрабатывается
  2. Обработка контента: (все поиски, за редким исключением, относятся к главной странице. удаления — ко всем)
	* удалить всё из head, кроме css-файлов, title, keywords, description, ico
	* сбор всех меню в массив +
	* фильтрация/объединение меню +
	* найти телефоны и сохранить
	* найти блоки меню, отдельно обработать, удалить из контента
	* найти форму с минимальным количеством полей (имя, телефон, e-mail). если нет - предложить отправку со своего скрипта. удалить все формы из контента
	* найти соц. кнопки, обработать, удалить из контента
	* из головы сайта вырезать логотип, если не нашел, то текст. удалить из контента всю голову.
	* из подвала сайта вырезать текстовые данные, типо, адрес, инн, e-mail, телефон. остальное удалить
	* удалить яндекс.метрики, гугл аналитиксы, ливинтернеты
	* столбцы, не относящиеся к основному блоку на сайте залить вниз сайта, после основного блока. в ручной настройке сайта сделать функцию удаления боковых колонок на определенных страницах.
	* тяжелые картинки (свыше 1Мб) не загружать или даунскейлить.
	* найти и обработать блог, если есть, через jQuery Mobile
  5. Обработка мультиязычности
	* найти библиотеку определения англ языка
  6. Прочие работы
	* добавить иконки для всех соц сетей +
	
	в обработке зафигачить пункты как фичи
*/

var casper = require('casper').create({
	clientScripts: ["jquery.min.js"]
}), system = require('system');
// console.log(system.args); // 4 по умолчанию

/*  ----  Additional Methods  ----  */

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
		if(!href.match(/^\//) && !href.match(site.url) || href == '/' || href.match(/(gif|GIF|png|PNG|jpg|JPG|jpeg|JPEG|pdf|PDF|doc|DOC|docx|DOCX|xls|XLS|xlsx|XLSX)$/)) {
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

function array_diff() {
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

function emptyObject(obj) {
	for (var i in obj) {
		return false;
	}
	return true;
}

/*  ----  Methods For All Pages  ---- */

function addLinksToSitemap(sitemap, pages) {
	for(i in pages) {
		for(j in links) {
			sitemap.push(pages[i].links[j]);
		}
	}
	// sitemap = sitemap.concat(links);
	sitemap.sort();
	for (var j = sitemap.length - 1; j > 0; j--) {
		if (sitemap[j] == sitemap[j - 1]) sitemap.splice(j, 1);
	}
	return sitemap.sort();
}

function processContent() {
	// processContentHead
	// processContentBody
	// clearPageBottom(cutForms(cutSocBtns(cutMenus(content))))
	return document.html();
}

/*  ----  Second Pages Methods  ----  */

function filterLinks(links) {
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

/*  ----  Main Page Methods  ----  */

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

function getSocBtns(links) {
	var h = new Array;
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

function getTop() {
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

function getFooter() {
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

/*  ----  Basic Methods & Objects  ----  */

var site = {
	url: system.args[4],
	webPages: new Array(),
	menus: { //processed menus
		identifier: '',
		items: new Array
	},
	top: { // it must be a link to main page with logo
		identifier: '',
		content: ''
	},
	footer: { // parse only text
		identifier: '',
		content: ''
	},
	socBtns: new Array,
	sitemap: new Array
}

casper.start(site.url, function() {
	links = this.evaluate(getLinks);
	
	site.menus 			= this.evaluate(getMenus);
	site.menus.items 	= processMenu(site.menus.items);
	site.top 			= this.evaluate(getTop);
	site.footer 		= this.evaluate(getFooter);
	site.socBtns 		= getSocBtns(getAbsoluteLinks(links));
	site.webPages.push({
		url: site.url,
		links: getLocalLinks(links),
		content: this.evaluate(processContent)
	});
	site.sitemap = addLinksToSitemap(site.sitemap, site.webPages);
});

casper.then(function() {
	i = -1;
	var pages = site.webPages[0].links;
	this.each(pages, function(response) {
		i++;
		casper.thenOpen(site.url +''+ pages[i], function() {
			site.webPages.push({
				url: this.getCurrentUrl(), 
				links: filterLinks(getLocalLinks(this.evaluate(getLinks))),
				content: this.evaluate(processContent)
			});
			site.sitemap = addLinksToSitemap(site.sitemap, site.webPages);
		});
	});
});

casper.run(function() {
	// this.echo(links.length + ' links found:');
	// this.echo(' - ' + links.join('\n - '));
	
	this.echo('Sitemap:');
	this.echo(site.sitemap);
	this.echo('Pages:');
	for(i in site.webPages) {
		this.echo(site.webPages[i].url +': ');
		this.echo(site.webPages[i].links);
		// this.echo(site.webPages[i].content);
		if(site.webPages[i].content) {
			this.echo('Content is loaded: '+ true);
		} else {
			this.echo('Content is loaded: '+ false);
		}
		this.echo('');
	}
	this.echo('Site top:');
	this.echo(site.top.content);
	this.echo('Site footer:');
	this.echo(site.footer.content);
	this.echo('');
	this.echo('Menus:');
	i = -1;
	this.each(site.menus.items, function() {
		i++;
		this.echo('Menu '+ i +':');
		j = -1;
		this.each(site.menus.items[i], function() {
			j++;
			this.echo(site.menus.items[i][j].text +': '+ site.menus.items[i][j].url);
		});
	});
	
	this.echo('\n socBtns:');
	this.echo(' - ' + site.socBtns.join('\n - '));
	this.echo(' ').exit();
});