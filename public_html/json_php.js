var casper = require('casper').create({
	clientScripts: ["jquery.min.js", "process_page_content.js"]
}), system = require('system');
var utils = require('utils');

var json_data = JSON.parse(system.args[4]);

casper.start(json_data.url, function() {
	
});

casper.run(function() {
	utils.dump(json_data);
	// utils.dump(site.accept);
	this.exit();
});