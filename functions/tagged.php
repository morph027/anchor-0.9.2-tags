<?php

/**
    Theme functions for tagged
*/
function has_tagged_results() {
    return Registry::get('total_posts', 0) > 0;
}

function total_tagged_results() {
    return Registry::get('total_posts', 0);
}

function tagged_results() {
    $posts = Registry::get('tagged_results');

    if($result = $posts->valid()) {
        // register single post
        Registry::set('article', $posts->current());

        // move to next
        $posts->next();
    }

    return $result;
}

function tagged_term() {
    return Registry::get('tagged_term');
}

function has_tagged_pagination() {
    return Registry::get('total_posts') > Config::meta('posts_per_page');
}

function tagged_prev($text = ' &larr; Previous', $default = '') {
    $per_page = Config::meta('posts_per_page');
    $page = Registry::get('page_offset');

    $offset = ($page - 1) * $per_page;
    $total = Registry::get('total_posts');

    $pages = floor($total / $per_page);

    $tagged_page = Registry::get('page');
    $next = $page + 1;
    $term = Registry::get('tagged_term');
    Session::put(slug($term), $term);

    $url = base_url($tagged_page->slug . '/' . $term . '/' . $next);

    if(($page - 1) < $pages) {
	return '<a href="' . $url . '" class="prev">' . $text . '</a>';
    }

    return $default;
}

function tagged_next($text = 'Next &rarr;', $default = '') {
    $per_page = Config::get('meta.posts_per_page');
    $page = Registry::get('page_offset');

    $offset = ($page - 1) * $per_page;
    $total = Registry::get('total_posts');

    $pages = ceil($total / $per_page);

    $tagged_page = Registry::get('page');
    $prev = $page - 1;
    $term = Registry::get('tagged_term');
    Session::put(slug($term), $term);

    $url = base_url($tagged_page->slug . '/' . $term . '/' . $prev);

    if($offset > 0) {
	return '<a href="' . $url . '" class="next">' . $text . '</a>';
    }

    return $default;
}

function tagged_url() {
    return base_url('tagged');
}

function tagged_form_input($extra = '') {
    return '<input name="term" type="text" ' . $extra . ' value="' . tagged_term() . '">';
}
