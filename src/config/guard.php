<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| The JavaScripts Path
	|--------------------------------------------------------------------------
	|
	| This is where you can specify a custom path to your JavaScripts
	| directory. We've set a sensible default, but feel free to update it.
	|
	*/
	'js_path' => 'public/js',

	/*
	|--------------------------------------------------------------------------
	| The Stylesheets Path
	|--------------------------------------------------------------------------
	|
	| This is where you can specify a custom path to your Stylesheets
	| directory. We've set a sensible default, but feel free to update it.
	|
	*/
	'css_path' => 'public/css',

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
	'assets_path' => 'app/assets',

	/*
	|--------------------------------------------------------------------------
	| JavaScript Concatenation
	|--------------------------------------------------------------------------
	|
	| By default, we're going to concat all files from your JavaScript directory,
	| but that's probably not what you want. When you need to set a specific order
	| for concatenation, set this value to an array of paths that are relative
	| to what you have set in the `js_path` option, above. So, for `public/js/main.js`,
	| you'd simply add `array('main')` (the extension may be left off).
	|
	*/
	'js_concat' => \File::files('public/js'),

	/*
	|--------------------------------------------------------------------------
	| CSS Concatenation
	|--------------------------------------------------------------------------
	|
	| By default, we're going to concat all files from your CSS directory,
	| but that's probably not what you want. When you need to set a specific order
	| for concatenation, set this value to an array of paths that are relative
	| to what you have set in the `js_path` option, above. So, for `public/css/buttons.css`,
	| you'd simply add `array('buttons')` (the extension may be left off).
	|
	*/
	'css_concat' => \File::files('public/css')
);