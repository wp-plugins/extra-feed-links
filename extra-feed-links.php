<?php
/*
Plugin Name: Extra Feed Links
Version: 1.1.3a
Description: (<a href="options-general.php?page=extra-feed-links"><strong>Settings</strong></a>) Adds appropriate feed auto-discovery links to posts, pages, category pages, tag pages, search pages and author pages.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/projects/extra-feed-links.html
*/

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

define( 'EFL_PLUGIN_URL', WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) );

class extraFeedLink {
	var $format_name;
	var $format;
	var $url;
	var $title;
	var $text;

	function __construct() {
		$this->format = get_option('efl-format');
		add_action('wp_head', array($this, 'head_link'));
	}

	function head_link() {
		$this->generate();

		if( !$this->url || !$this->text)
			return;

		echo "\n" . '<link rel="alternate" type="application/rss+xml" title="' . $this->text . '" href="' . $this->url . '" />' . "\n";
	}

	function theme_link($input) {
		$this->generate(TRUE);

		if( !$this->url )
			return;

		if ( substr($input, 0, 4) == 'http' )
			echo '<a href="' . $this->url . '" title="' . $this->text . '"><img src="' . $input . '" alt="rss icon" /></a>';
		elseif ( $input == '' )
			echo '<a href="' . $this->url . '" title="' . $this->text . '">' . $this->text . '</a>';
		elseif ( $input == 'raw' )
			echo $this->url;
		else
			echo '<a href="' . $this->url . '" title="' . $this->text . '">' . $input . '</a>';
	}

	function generate($for_theme = FALSE) {
		$this->title = $this->url = NULL;

		if ( is_home() && ($this->format['home'][0] || $for_theme) ) {
			$this->url = get_bloginfo('comments_rss2_url');
			$this->format_name = 'home';
		}
		elseif ( (is_single() || is_page()) && ($this->format['comments'][0] || $for_theme) ) {
			global $post;
			if ( $post->comment_status == 'open' ) {
				$this->url = get_post_comments_feed_link($post->ID);
				$this->title = $post->post_title;
				$this->format_name = 'comments';
			}
		}
		elseif ( is_category() && ($this->format['category'][0] || $for_theme) ) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();

			$this->url = get_category_feed_link($cat_obj->term_id);
			$this->title = $cat_obj->name;
			$this->format_name = 'category';
		}
		elseif ( is_tag() && ($this->format['tag'][0] || $for_theme) ) {
			global $wp_query;
			$tag_obj = $wp_query->get_queried_object();

			$this->url = $this->get_tag_feed_link($tag_obj->term_id);
			$this->title = $tag_obj->name;
			$this->format_name = 'tag';
		}
		elseif ( is_search() && ($this->format['search'][0] || $for_theme) ) {
			$search = attribute_escape(get_search_query());

			$this->url = get_search_feed_link($search);
			$this->title = $search;
			$this->format_name = 'search';
		}
		elseif ( is_author() && ($this->format['author'][0] || $for_theme) ) {
			global $wp_query;
			$author_obj = $wp_query->get_queried_object();

			$this->url = get_author_feed_link($author_obj->ID);
			$this->title = $author_obj->user_nicename;
			$this->format_name = 'author';
		}

		// Set the appropriate format
		$this->text = $this->format[$this->format_name][1];

		// Convert substitution tags
		$this->text = str_replace('%title%', $this->title, $this->text);
		$this->text = str_replace('%site_title%', get_option('blogname'), $this->text);
	}

	// Fixes bug in WP lower than 2.6
	function get_tag_feed_link($tag_id, $feed = '') {
		$tag_id = (int) $tag_id;

		$tag = get_tag($tag_id);

		if ( empty($tag) || is_wp_error($tag) )
			return false;

		$permalink_structure = get_option('permalink_structure');

		if ( empty($feed) )
			$feed = get_default_feed();

		if ( '' == $permalink_structure )
			$link = get_option('home') . "?feed=$feed&amp;tag=" . $tag->slug;
		else {
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
}

// Init
global $extraFeedLink;

if ( is_admin() ) {
	require_once ('inc/admin.php');
	$extraFeedLink = new extraFeedLinkAdmin();
}
else
	$extraFeedLink = new extraFeedLink();

// Template tag
function extra_feed_link($input = '') {
	global $extraFeedLink;

	$extraFeedLink->theme_link($input);
}

