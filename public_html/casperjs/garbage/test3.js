/*
  --------------  ������ �� �����  -----------------  
  1. ���� ����� �����
	* ���� ���� ��������� ������ � ����� ������ - ��� ����� ������ �� ���� ��������
	* ���� ������ �� ��������.� ������ ������: ����� ����� ������, ������ ������ �� ��������, ������������ ������� �������� - �� ������� ������ �� ���� ���������, ������� ���� �� ��������������
  2. ��������� ��������: (��� ������, �� ������ �����������, ��������� � ������� ��������. �������� � �� ����)
	* ������� �� �� head, ����� css-������, title, keywords, description, ico
	* ���� ���� ���� � ������ +
	* ����������/����������� ���� +
	* ����� �������� � ���������
	* ����� ����� ����, �������� ����������, ������� �� ��������
	* ����� ����� � ����������� ����������� ����� (���, �������, e-mail). ���� ��� - ���������� �������� �� ������ �������. ������� ��� ����� �� ��������
	* ����� ���. ������, ����������, ������� �� ��������
	* �� ������ ����� �������� �������, ���� �� �����, �� �����. ������� �� �������� ��� ������.
	* �� ������� ����� �������� ��������� ������, ����, �����, ���, e-mail, �������. ��������� �������
	* ������� ������.�������, ���� ����������, ������������
	* �������, �� ����������� � ��������� ����� �� ����� ������ ���� �����, ����� ��������� �����. � ������ ��������� ����� ������� ������� �������� ������� ������� �� ������������ ���������.
	* ������� �������� (����� 1��) �� ���������. ��� ������������
	* ����� � ���������� ����, ���� ����, ����� jQuery Mobile
  5. ��������� ���������������
	* ����� ���������� ����������� ���� �����
  6. ������ ������
	* �������� ������ ��� ���� ��� ����� +
	
	� ��������� ���������� ������ ��� ����
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