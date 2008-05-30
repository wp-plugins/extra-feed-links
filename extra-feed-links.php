<?php
/*
Plugin Name: Extra Feed Links
Version: 0.5
Description: Adds appropriate feed links to the header of posts, pages, categories, tags, search and author pages.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/downloads/extra-feed-links.html
*/

/*
Copyright (C) 2008 scribu.net (scribu AT gmail DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function extra_feed_links(){
	#Translatable text
	$before = array(
		'comments' => 'Comments: ',
		'categoty' => 'Category: ',
		'tag' => 'Tag: ',
		'search' => 'Search: ',
		'author' => 'Author: '
	);

	#For adding the "All comments" feed to the home page
	/*if( is_home() ) {
		$url = get_bloginfo('comments_rss2_url');
		$title = 'All Comments';
	}
	else*/

	if( is_single() || is_page() ){
		global $post;
		if ( 'open' == $post->comment_status ){
			$url = get_post_comments_feed_link($post->ID);
			$title = $before['comments'] . $post->post_title;
		}
	}
	elseif ( is_category() ){
		global $wp_query;
		$cat_obj = $wp_query->get_queried_object();
		$url = get_category_feed_link($cat_obj->term_id);
		$title = $before['category'] . $cat_obj->name;
	}
	elseif ( is_tag() ){
		global $wp_query;
		$tag_obj = $wp_query->get_queried_object();
		$url = get_fixed_tag_feed_link($tag_obj->term_id);
		$title = $before['tag'] . $tag_obj->name;
	}
	elseif ( is_search() ){
		$search = attribute_escape(get_search_query());
		$url = get_search_feed_link($search);
		$title = $before['search'] . $search;
	}
	elseif ( is_author() ){
		global $wp_query;
		$author_obj = $wp_query->get_queried_object();
		$url = get_author_feed_link($author_obj->ID);
		$title = $before['author'] . $author_obj->user_nicename;
	}

	if($url && $title)
		echo "\n" . '<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . $url . '" />' . "\n";
}

function get_fixed_tag_feed_link($tag_id, $feed = '') {
	#for WP lower than 2.5.2
	$tag_id = (int) $tag_id;

	$tag = get_tag($tag_id);

	if ( empty($tag) || is_wp_error($tag) )
		return false;

	$permalink_structure = get_option('permalink_structure');

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' == $permalink_structure ) {
		$link = get_option('home') . "?feed=$feed&amp;tag=" . $tag->slug;
	} else {
		$link = get_tag_link($tag->term_id);
		if ( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";
		$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
	}

	$link = apply_filters('tag_feed_link', $link, $feed);

	return $link;
}

add_action('wp_head', 'extra_feed_links');
?>