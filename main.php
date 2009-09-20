<?php
/*
Plugin Name: Extra Feed Links
Version: 1.2
Description: Lets you control extra feed auto-discovery links on various page types (categories, tags, search results etc.)
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/wordpress/extra-feed-links

Copyright (C) 2009 scribu.net (scribu AT gmail DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

_efl_init();
function _efl_init()
{
	$options = new scbOptions('efl-format', __FILE__, array(
		'home' => array(FALSE, '%site_title% Comments'),
		'comments' => array(TRUE, 'Comments: %title%'),
		'category' => array(TRUE, 'Category: %title%'),
		'tag' => array(TRUE, 'Tag: %title%'),
		'author' => array(TRUE, 'Author: %title%'),
		'search' => array(TRUE, 'Search: %title%')
	));

	extraFeedLink::init($options->get());

	if ( is_admin() ) 
	{
		require_once dirname(__FILE__) . '/admin.php';
		new extraFeedLinkAdmin(__FILE__, $options);
	}
}

class ExtraFeedLink
{
	static $options;

	static $format;
	static $format_name;
	static $url;
	static $title;
	static $text;

	function init($format)
	{
		self::$format = $format;

		remove_action('wp_head', 'feed_links_extra', 3);
		add_action('wp_head', array(__CLASS__, 'head_link'));
	}

	function head_link() 
	{
		self::generate();

		if( !self::$url || !self::$text )
			return;

		echo "\n" . '<link rel="alternate" type="application/rss+xml" title="' . self::$text . '" href="' . self::$url . '" />' . "\n";
	}

	function theme_link($input) 
	{
		self::generate(TRUE);

		if( !self::$url )
			return;

		if ( substr($input, 0, 4) == 'http' )
			echo '<a href="' . self::$url . '" title="' . self::$text . '"><img src="' . $input . '" alt="rss icon" /></a>';
		elseif ( $input == '' )
			echo '<a href="' . self::$url . '" title="' . self::$text . '">' . self::$text . '</a>';
		elseif ( $input == 'raw' )
			echo self::$url;
		else
			echo '<a href="' . self::$url . '" title="' . self::$text . '">' . $input . '</a>';
	}

	function generate($for_theme = FALSE) 
	{
		self::$title = self::$url = NULL;

		if ( is_home() && (self::$format['home'][0] || $for_theme) )
		{
			self::$url = get_bloginfo('comments_rss2_url');
			self::$format_name = 'home';
		}
		elseif ( is_singular() && (self::$format['comments'][0] || $for_theme) ) 
		{
			global $post;

			if ( $post->comment_status == 'open' ) 
			{
				self::$url = get_post_comments_feed_link($post->ID);
				self::$title = $post->post_title;
				self::$format_name = 'comments';
			}
		}
		elseif ( is_category() && (self::$format['category'][0] || $for_theme) )
		{
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();

			self::$url = get_category_feed_link($cat_obj->term_id);
			self::$title = $cat_obj->name;
			self::$format_name = 'category';
		}
		elseif ( is_tag() && (self::$format['tag'][0] || $for_theme) )
		{
			global $wp_query;
			$tag_obj = $wp_query->get_queried_object();

			self::$url = get_tag_feed_link($tag_obj->term_id);
			self::$title = $tag_obj->name;
			self::$format_name = 'tag';
		}
		elseif ( is_author() && (self::$format['author'][0] || $for_theme) )
		{
			global $wp_query;
			$author_obj = $wp_query->get_queried_object();

			self::$url = get_author_feed_link($author_obj->ID);
			self::$title = $author_obj->user_nicename;
			self::$format_name = 'author';
		}
		elseif ( is_search() && (self::$format['search'][0] || $for_theme) )
		{
			$search = attribute_escape(get_search_query());

			self::$url = get_search_feed_link($search);
			self::$title = $search;
			self::$format_name = 'search';
		}

		// Set the appropriate format
		self::$text = self::$format[self::$format_name][1];

		// Convert substitution tags
		self::$text = str_replace('%title%', self::$title, self::$text);
		self::$text = str_replace('%site_title%', get_option('blogname'), self::$text);
	}
}

// Template tag
function extra_feed_link($input = '') 
{
	ExtraFeedLink::theme_link($input);
}

