<?php
/*
Plugin Name: Extra Feed Links
Version: 0.6
Description: Adds appropriate feed links to the header of posts, pages, categories, tags, search and author pages.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/download/extra-feed-links.html
*/

class extraFeedLink {
	var $title;
	var $url;

	function __construct(){
		add_action('wp_head', array(&$this, 'head_link'));
	}

	function head_link(){
		$this->generate();

		if( !$this->url || !$this->title)
			return;

		echo "\n" . '<link rel="alternate" type="application/rss+xml" title="' . $this->title . '" href="' . $this->url . '" />' . "\n";
	}

	function theme_link($input){
		$this->generate();

		if( !$this->url )
			return;

		if ( substr($input, 0, 4) == 'http' )
			echo '<a href="' . $this->url . '" title="' . $this->title . '"><img src="' . $input . '" alt="rss icon" /></a>';
		elseif ( $input == '' )
			echo '<a href="' . $this->url . '" title="' . $this->title . '">' . $input . '</a>';
		else
			echo '<a href="' . $this->url . '" title="' . $this->title . '">' . $this->title . '</a>';
	}

	function generate(){
		$before = array(
			'comments' => 'Comments: ',
			'category' => 'Category: ',
			'tag' => 'Tag: ',
			'search' => 'Search: ',
			'author' => 'Author: '
		);

		/*if( is_home() ) {
			$this->url = get_bloginfo('comments_rss2_url');
			$this->title = 'All Comments';
		}
		else*/
		if( is_single() || is_page() ){
			global $post;
			if ( 'open' == $post->comment_status ){
				$this->url = get_post_comments_feed_link($post->ID);
				$this->title = $before['comments'] . $post->post_title;
			}
		}
		elseif ( is_category() ){
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$this->url = get_category_feed_link($cat_obj->term_id);
			$this->title = $before['category'] . $cat_obj->name;
		}
		elseif ( is_tag() ){
			global $wp_query;
			$tag_obj = $wp_query->get_queried_object();
			$this->url = $this->get_tag_feed_link($tag_obj->term_id);
			$this->title = $before['tag'] . $tag_obj->name;
		}
		elseif ( is_search() ){
			$search = attribute_escape(get_search_query());
			$this->url = get_search_feed_link($search);
			$this->title = $before['search'] . $search;
		}
		elseif ( is_author() ){
			global $wp_query;
			$author_obj = $wp_query->get_queried_object();
			$this->url = get_author_feed_link($author_obj->ID);
			$this->title = $before['author'] . $author_obj->user_nicename;
		}
	}

	function get_tag_feed_link($tag_id, $feed = '') {
		//for WP lower than 2.5.2
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
}

global $extraFeedLink;
$extraFeedLink = new extraFeedLink();

function extra_feed_link($input = ''){
	global $extraFeedLink;

	$extraFeedLink->theme_link($input);
}

?>
