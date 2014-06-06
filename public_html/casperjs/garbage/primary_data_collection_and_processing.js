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
	* тяжелые картинки (свыше 1Мб) не загружать. или даунскейлить
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

/*  ----  Additional Methods  ----  */

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

/*  ----  Methods For All Pages  ---- */

function getLinks() { //getting all links from page
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

function addToSitemap(pages) {
	var result = new Array();
	for(i in pages) {
		result.push(pages[i].links);
	}
	return result;
}

/* -- Process Page Content -- */

function processContentHead(content) {
	//watch plan on top
	return content;
}

function cutMenus(content, obj) {
	$(content).find(obj.menus.identificator).remove();
	return content;
}

function cutSocBtns(content, obj) {
	for(i in obj.socBtns) {
		href = obj.socBtns[i][0];
		$(content).find('a[href="'+ href +'"]').remove();
	}
	return content;
}

function cutForms(content) {
	$(content).find('form').remove();
	return content;
}

function clearFooter(content, obj) {
	$(content).find(obj.footer.identificator).remove();
	return content;
}

function processPageDisplaying(content, obj) {
	return content;
}

function processContentBody(content, obj) {
	content = cutMenus(content, obj);
	content = cutSocBtns(content, obj);
	content = cutForms(content);
	content = clearFooter(content, obj);
	content = processPageDisplaying(content, obj); //not ready
	return content;
}

function processContent(content, obj) {
	content = processContentHead(content);
	content = processContentBody(content, obj);
	return content;
}

/*  ----  Second Pages methods  ----  */

function transferDataFromSecondPage(obj) {
	
}

function processContentFromSecondPage() {
	var links = getLinks(document);
	site.webPages.push({
		url: this.getCurrentUrl(), 
		links: getLocalLinks(links),
		content: processContent(this.getPageContent())
	});
	site.sitemap = addLinksToSitemap(site.sitemap, data.webPages);
	return data;
}

function addLinksToSitemap(sitemap, links) {
	sitemap.concat(links);
	for (i = sitemap.length - 1; i > 0; i--) {
		if (sitemap[i] == sitemap[i - 1]) sitemap.splice( i, 1);
	}
	return sitemap;
}

/*  ----  Main Page Methods  ----  */

function getMenus(obj) { // getting menus
	var result = {
		identificator: '',
		items: new Array()
	};
	var menus = ['.menu', 'nav', '[class$=menu]', '[class^=menu]'];
	for(t in menus) {
		if($(obj).find(menus[t]).length) {
			result.identificator = menu[t];
			menus = $(obj).find(result.identificator);
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

function processMenus(arr) { // menu's filtering
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
	return arr.reverse(); // restore sequence
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

function getTop(obj) {
	var result = {
		identificator: '',
		content: ''
	};
	var top = ['header', '#header', '[id$=header]', '[id^=header]', '.header', '[class$=header]', '[class^=header]'];
	for(t in top) {
		if($(obj).find(top[t]).length) {
			result.identificator = top[t];
			top = $(obj).find(result.identificator);
			break;
		}
	}
	result.content = top; // process top
	return result;
}

function getFooter(obj) {
	var result = {
		identificator: '',
		content: new Array()
	};
	var footer = ['footer', '#footer', '[id$=footer]', '[id^=footer]', '.footer', '[class$=footer]', '[class^=footer]'];
	for(t in footer) {
		if($(obj).find(footer[t]).length) {
			result.identificator = footer[t];
			footer = $(obj).find(result.identificator);
			break;
		}
	}
	result.content = footer; //delete all unnecessary content
	return result;
}

function processContentFromMainPage() {
	var dataFromMainPage = {
		webPages: {}
	};
	var links = getLinks(document);
	dataFromMainPage.menus = getMenus(document);
	dataFromMainPage.menus.items = processMenus(dataFromMainPage.menus.items);
	dataFromMainPage.socBtns = getSocBtns(getAbsoluteLinks(links));
	dataFromMainPage.top = getTop(document);
	dataFromMainPage.footer = getFooter(document);
	dataFromMainPage.webPages.push({
		url: site.url,
		links: getLocalLinks(links),
		content: processContent(document, dataFromMainPage)
	});
	dataFromMainPage.sitemap = addToSitemap(dataFromMainPage.webPages);
	
	return dataFromMainPage;
}

function transferDataFromMainPageToSiteObj(site, obj) {
	// console.log(obj);
	site.menus 		= obj.menus;
	site.socBtns 	= obj.socBtns;
	site.top 		= obj.top;
	site.footer 	= obj.footer;
	site.webPages 	= obj.webPages;
	site.sitemap 	= obj.sitemap;
}
	
/*  ----  Basic Methods & Objects  ----  */

var site = { // main site Object
	url: system.args[4],
	menus: {
		identificator: '',
		items: {
			url: '',
			text: ''
		}
	},
	top: { // it must be a link to main page with logo
		identificator: '',
		content: ''
	},
	footer: { // parse only text
		Identificator: '',
		content: ''
	},
	socBtns: new Array,
	sitemap: new Array,
	webPages: { // main object for all pages
		url: '',
		links: new Array(),
		content: ''
	}
};

casper.start(site.url, function() {
	transferDataFromMainPageToSiteObj(site, this.evaluate(processContentFromMainPage));
});

casper.then(function() {
	i = -1;
	var pages = site.webPages[0].links;
	this.each(pages, function(response) {
		i++;
		casper.thenOpen(site.url +''+ pages[i], function() {
			transferDataFromSecondPage(this.evaluate(processContentFromSecondPage));
		});
	});
});

casper.run(function() {
	// this.echo(links.length + ' links found:');
	// this.echo(' - ' + links.join('\n - '));
	
	this.echo('Sitemap:');
	this.echo(site.sitemap);
	this.echo('\n Pages:');
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