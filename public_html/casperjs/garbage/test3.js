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

var site = { // main site Object
	url: system.args[4],
	menus: {
		identificator: '',
		items: {
			url: '',
			text: ''
		}
	}
}

function getLinks() {
	var links = document.querySelectorAll('a');
	return Array.prototype.map.call(links, function(e) {
		return e.getAttribute('href');
	});
}

function getMenus() {
	var result = {
		identificator: '',
		items: new Array()
	};
	var menus = ['.menu', 'nav', '[class$=menu]', '[class^=menu]'];
	/*for(t in menus) {
		if(menus[t].length) {
			result.identificator = menu[t];
			menus = $(result.identificator);
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
	});*/
	result.identificator = menus[0];
	$(menus[0]).each(function(i, e) {
		$(e).find('a').each(function(j, q) {
			result.items[i] = new Array();
			result.items[i].push({
				url: $(q).attr('href'),
				text: $(q).text()
			});
		});
	});
	return result;
}

function getProcessedMenu(body) {
	var result = body.evaluate(getMenus);
	console.log(result);
	return result;
}

casper.start(site.url, function() {
	// site.menus = getProcessedMenu(this);
	console.log(this.evaluate(getMenus).items[0].url);
});

casper.run(function() {
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
	this.echo(' ').exit();
});