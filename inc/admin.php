<?php
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
		add_option('efl-display', $this->defaults);

		add_action('admin_menu', array(&$this, 'page_init'));
	}

	// Options page
	function page_init() {
		if ( current_user_can('manage_options') ) {
			$page = add_options_page('Extra Feed Links', 'Extra Feed Links', 8, 'extra-feed-links', array(&$this, 'page'));
			add_action("admin_print_scripts-$page", array(&$this, 'page_head'));
		}
	}

	function page_head() {
		$plugin_url = $this->get_plugin_url();

		wp_enqueue_script('form_js', $plugin_url . '/inc/functions.js');
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

	function get_plugin_url() {
		if ( function_exists('plugins_url') )
			return plugins_url( plugin_basename( dirname(dirname(__FILE__)) ) );
		else
			// Pre-2.6 compatibility
			return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename( dirname(dirname(__FILE__)) );
	}
}

$extraFeedLinkAdmin = new extraFeedLinkAdmin();
?>
