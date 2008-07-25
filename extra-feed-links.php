<?php
/*
Plugin Name: Extra Feed Links
Version: 1.0
Description: Adds appropriate feed links to the header of posts, pages, categories, tags, search and author pages.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/download/extra-feed-links.html
*/

class extraFeedLink {
	var $display;
	var $title;
	var $url;

	function __construct() {
		$this->display = get_option('efl-display');
		add_action('wp_head', array(&$this, 'head_link'));
	}

	function head_link() {
		$this->generate();

		if( !$this->url || !$this->title)
			return;

		echo "\n" . '<link rel="alternate" type="application/rss+xml" title="' . $this->title . '" href="' . $this->url . '" />' . "\n";
	}

	function theme_link($input) {
		$this->generate(TRUE);

		if( !$this->url )
			return;

		if ( substr($input, 0, 4) == 'http' )
			echo '<a href="' . $this->url . '" title="' . $this->title . '"><img src="' . $input . '" alt="rss icon" /></a>';
		elseif ( $input == '' )
			echo '<a href="' . $this->url . '" title="' . $this->title . '">' . $this->title . '</a>';
		elseif ( $input == 'raw' )
			echo $this->url;
		else
			echo '<a href="' . $this->url . '" title="' . $this->title . '">' . $input . '</a>';
	}

	function generate($for_theme = FALSE) {
		$this->title = $this->url = NULL;

		if ( is_home() && ($this->display['home'][0] || $for_theme) ) {
			$this->url = get_bloginfo('comments_rss2_url');
			$this->title = $this->display['home'][1];
		}
		elseif ( (is_single() || is_page()) && ($this->display['comments'][0] || $for_theme) ) {
			global $post;
			if ( $post->comment_status == 'open' ) {
				$this->url = get_post_comments_feed_link($post->ID);
				$this->title = $this->display['comments'][1] . $post->post_title;
			}
		}
		elseif ( is_category() && ($this->display['category'][0] || $for_theme) ) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$this->url = get_category_feed_link($cat_obj->term_id);
			$this->title = $this->display['category'][1] . $cat_obj->name;
		}
		elseif ( is_tag() && ($this->display['tag'][0] || $for_theme) ) {
			global $wp_query;
			$tag_obj = $wp_query->get_queried_object();
			$this->url = $this->get_tag_feed_link($tag_obj->term_id);
			$this->title = $this->display['tag'][1] . $tag_obj->name;
		}
		elseif ( is_search() && ($this->display['search'][0] || $for_theme) ) {
			$search = attribute_escape(get_search_query());
			$this->url = get_search_feed_link($search);
			$this->title = $this->display['search'][1] . $search;
		}
		elseif ( is_author() && ($this->display['author'][0] || $for_theme) ) {
			global $wp_query;
			$author_obj = $wp_query->get_queried_object();
			$this->url = get_author_feed_link($author_obj->ID);
			$this->title = $this->display['author'][1] . $author_obj->user_nicename;
		}
	}

	//Fixes bug in WP lower than 2.5.2
	function get_tag_feed_link($tag_id, $feed = '') {
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

class extraFeedLinkAdmin extends extraFeedLink {
	var $defaults = array(
			'home' => array(TRUE, 'All comments'),
			'comments' => array(TRUE, 'Comments: '),
			'category' => array(TRUE, 'Category: '),
			'tag' => array(TRUE, 'Tag: '),
			'search' => array(TRUE, 'Search: '),
			'author' => array(TRUE, 'Author: ')
		);

	function __construct() {
		register_activation_hook(__FILE__, array(&$this, 'activate'));
		add_action('admin_menu', array(&$this, 'page_init'));
	}

	function activate() {
		add_option('efl-display', $this->defaults);
	}

	// Options page
	function page_init() {
		$page = add_options_page('Extra Feed Links', 'Extra Feed Links', 8, 'extra-feed-links', array(&$this, 'page'));
		add_action("admin_print_scripts-$page", array(&$this, 'page_head'));
	}

	function page_head() {
		// Set plugin url
		if ( function_exists('plugin_url') )
			$plugin_url = plugin_url();
		else
			$plugin_url = get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__)); // pre-2.6 compatibility

		wp_enqueue_script('form_js', $plugin_url . '/functions.js');
	}

	function page() {
		$this->display = get_option('efl-display');

		// Update display options
		if ( $_POST['efl-submit'] ) {
			foreach ($this->display as $name => $value) {
				$this->display[$name][0] = $_POST['show-' . $name];
				$this->display[$name][1] = $_POST['before-' . $name];
			}

			update_option('efl-display', $this->display);
			echo '<div class="updated"><p>Options saved.</p></div>';
		}
?>
<div class="wrap">
<h2>Extra Feed Links</h2>

<p>The table below allows you to select which page categories get an extra header link and what text to display before this link.</p>

<div class="alignleft" style="width:auto">
<form id="efl-display" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" width="200px">
<div class="tablenav" style="width:auto">
	<div class="alignleft">
	<input type="submit" class="button-secondary" name="efl-submit" value="Save" />
	</div>
	<br class="clear">
</div>
<br class="clear">

	<table class="widefat" style="width:auto">
		<thead>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" /></th>
			<th scope="col">Page type</th>
			<th scope="col">Text before</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($this->display as $name => $value) { ?>
		<tr>
			<th scope='row' class='check-column'><input type="checkbox" name="show-<?php echo $name ?>" value="1" <?php if ( $this->display[$name][0] ) echo 'checked="checked"' ?> /></th>
			<td><?php echo ucfirst($name) ?></td>
			<td><input type="text" name="before-<?php echo $name ?>" value="<?php echo $this->display[$name][1] ?>" size="25" /></td>
		</tr>
		<?php } ?>
		</tbody>
		</table>
	</form>
	<br class="clear">
</div></div>
<?php	}
}

global $extraFeedLink, $extraFeedLinkAdmin;

// Init
if ( is_admin() )
	$extraFeedLinkAdmin = new extraFeedLinkAdmin();
else
	$extraFeedLink = new extraFeedLink();

// Functions
function extra_feed_link($input = '') {
	global $extraFeedLink;

	$extraFeedLink->theme_link($input);
}
?>
