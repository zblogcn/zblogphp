<?php
function api_category_get_function() {
	API::$IO->end();
}
API::$Route->get('/category/', 'api_category_get_function');
