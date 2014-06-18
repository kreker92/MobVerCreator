var casper = require('casper').create({
	clientScripts: ["jquery.min.js", "process_page_content.js"]
}), system = require('system');
var utils = require('utils');

var response = system.args[4];
var site = JSON.parse(response);

casper.start().then(function() {
	this.echo('1');
	this.echo(site.url);
});

casper.run(function() {
	//utils.dump(site);
	this.exit();
});
