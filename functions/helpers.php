<?php

/**
 * Theme helpers functions
 */
function full_url($url = '') {
	return Uri::full($url);
}

function base_url($url = '') {
	return Uri::to($url);
}

function theme_url($file = '') {
	$theme_folder = Config::meta('theme');
	$base = 'themes' . '/' . $theme_folder . '/';

	return asset($base . ltrim($file, '/'));
}

function theme_include($file) {
	$theme_folder = Config::meta('theme');
	$base = PATH . 'themes' . DS . $theme_folder . DS;

	if(is_readable($path = $base . ltrim($file, DS) . EXT)) {
		return require $path;
	}
}

function asset_url($extra = '') {
	return asset('anchor/views/assets/' . ltrim($extra, '/'));
}

function current_url() {
	return Uri::current();
}

function rss_url() {
	return base_url('feeds/rss');
}

//  Custom function helpers
function bind($page, $fn) {
	Events::bind($page, $fn);
}

function receive($name = '') {
	return Events::call($name);
}

function body_class() {
	$classes = array();

	//  Get the URL slug
	$parts = explode('/', Uri::current());
	$classes[] = count($parts) ? trim(current($parts)) : 'index';

	//  Is it a posts page?
	if(is_postspage()) {
		$classes[] = 'posts';
	}

	//  Is it the homepage?
	if(is_homepage()) {
		$classes[] = 'home';
	}

	return implode(' ', array_unique($classes));
}

// page type helpers
function is_homepage() {
	return Registry::prop('page', 'id') == Config::meta('home_page');
}

function is_postspage() {
	return Registry::prop('page', 'id') == Config::meta('posts_page');
}

function is_article() {
	return Registry::get('article') !== null;
}

function is_page() {
	return Registry::get('page') !== null;
}

// tag cloud
function tag_cloud() {
	$query = Query::table(Base::table('post_meta'))
		->join(Base::table('extend'), Base::table('extend.id'), '=', Base::table('post_meta.extend'))
		->where(Base::table('extend.key'), '=', 'tags');
	$tags_all = $query->get(array(Base::table('post_meta.data')));
	$tags_arr = array();

	foreach(array_keys($tags_all) as $tags_index) {
		$tags_this = '';
		$tags_this = Json::decode($tags_all[$tags_index] ? $tags_all[$tags_index]->data : '{}');
		if( ! empty($tags_this->text)) {
			$tag_current = $tags_this->text;
			$tag_current_arr = explode(",", $tag_current);
			foreach ($tag_current_arr as $taggy) {
				if(!isset($tags_arr[$taggy])) {
					$tags_arr[$taggy] = 0;
				}
				$tags_arr[$taggy] = $tags_arr[$taggy] + 1;
			}
		}
	}

	//want to sort the results?
	//uncomment one of these
	//ksort($tags_arr); //by the tag name
	arsort($tags_arr); //by the count

	//or want to pull in a set amount of random results
	//10 in this example
	//uncomment the 4 lines below
	//$tags_arr = array_slice($tags_arr, 0, 10, true);
	//$tags_arrshuffle = array_keys($tags_arr);
	//shuffle($tags_arrshuffle);
	//$tags_arr = array_merge(array_flip($tags_arrshuffle), $tags_arr);

	return $tags_arr;
}
