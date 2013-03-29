<?php

return array(
	// What is the path to your JavaScripts folder?
	'js_path' 		=> 'public/js',

	// What is the path to your CSS folder?
	'css_path'  	=> 'public/css',

	/*
	|--------------------------------------------------------------------------
	| The Path To Your Assets Directory
	|--------------------------------------------------------------------------
	|
	| Here, you'll specify where you want your assets directory to go.
	| This will be the base directory, where sass, less, and coffee directories
	| will be inserted.
	|
	*/
	'assets_path'   => 'app/assets',

	/*
	|--------------------------------------------------------------------------
	| JavaScript Concatenation
	|--------------------------------------------------------------------------
	|
	| Here is where you'll specify the list of JavaScript files, in order,
	| that you want to have concatenated. Specify your paths relative to
	| what you have set in the `js_path` option, above. So, for `public/js/main.js`,
	| you'd simply add `array('main')` (the extension may be left off).
	|
	*/
	'js_concat'		=> array(),

	/*
	|--------------------------------------------------------------------------
	| CSS Concatenation
	|--------------------------------------------------------------------------
	|
	| Here is where you'll specify the list of CSS files, in order,
	| that you want to have concatenated. Specify your paths relative to
	| what you have set in the `css_path` option, above. So, for `public/css/buttons.css`,
	| you'd simply add `array('buttons')` (the extension may be left off).
	|
	*/
	'css_concat'	=> array()
);