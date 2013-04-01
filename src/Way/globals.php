<?php

if ( ! function_exists('stylesheet'))
{
	/**
	 * Generates the necessary HTML for a
	 * stylesheet link. Path should be relative
	 * to the css_path config option for Guard.
	 *
	 * @param  string $path
	 * @return string
	 */
	function stylesheet($path = 'style.min.css')
	{
		$path = \Config::get('guard-laravel::guard.css_path') . "/$path";
		$path = str_replace('public', '', $path);

		return "<link rel='stylesheet' href='{$path}'>";
	}
}

if ( ! function_exists('script'))
{
	/**
	 * Generates the necessary HTML for a
	 * script link. Path should be relative
	 * to the js_path config option for Guard.
	 *
	 * @param  string $path
	 * @return string
	 */
	function script($path = 'scripts.min.css')
	{
		$path = \Config::get('guard-laravel::guard.js_path') . "/$path";
		$path = str_replace('public', '', $path);

		return "<script src='$path'></script>";
	}
}