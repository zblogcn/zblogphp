//'use strict';

(function(){
	var fs = require("fs"),
		arguments = process.argv.splice(2),
		tag = require('./list-tag.js').func();


	console.log(tag);
})();