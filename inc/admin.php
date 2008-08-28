<?php
class extraFeedLinkAdmin extends extraFeedLink {
	var $default_format = array(
			'home' => array(TRUE, 'All comments for %site_title%'),
			'comments' => array(TRUE, 'Comments: %title%'),
			'category' => array(TRUE, 'Category: %title%'),
			'tag' => array(TRUE, 'Tag: %title%'),
			'search' => array(TRUE, 'Search: %title%'),
			'author' => array(TRUE, 'Author: %title%')
		);

	function __construct() {
		add_option('efl-format', $this->default_format);

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
		$this->format = get_option('efl-format');

		// Update options
		if ( $_POST['submit-format'] ) {
			foreach ($this->format as $name => $value) {
				$this->format[$name][0] = $_POST['show-' . $name];
				$this->format[$name][1] = $_POST['format-' . $name];
			}

			update_option('efl-format', $this->format);
			echo '<div class="updated"><p>Options <strong>saved</strong>.</p></div>';
		}

		// Reset options
		if ( $_POST['submit-reset'] ) {
			update_option('efl-format', $this->default_format);
			$this->format = $this->default_format;
			echo '<div class="updated"><p>Options <strong>reset</strong>.</p></div>';
		}
?>
<div class="wrap">
<h2>Extra Feed Links</h2>

<p>The table below allows you to select which page types get an extra header link and the format of the link text.</p>

<div class="alignleft" style="width:auto">
<form id="efl-format" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<table class="widefat" style="width:auto">
		<thead>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" /></th>
			<th scope="col">Page type</th>
			<th scope="col">Text format</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($this->format as $name => $value) { ?>
		<tr>
			<th scope='row' class='check-column'><input type="checkbox" name="show-<?php echo $name ?>" value="1" <?php if ( $this->format[$name][0] ) echo 'checked="checked"' ?> /></th>
			<td><?php echo ucfirst($name) ?></td>
			<td><input style="width: 250px" type="text" name="format-<?php echo $name ?>" value="<?php echo $this->format[$name][1] ?>" size="25" /></td>
		</tr>
		<?php } ?>
		</tbody>
		</table>

	<div class="tablenav" style="width:auto">
	<div class="alignleft">
	<input type="submit" name="submit-format" class="button-secondary" value="Save" />
	</div>
	<br class="clear">
	</div>

	</form>
	<br class="clear">
</div>

<div style="float:left; margin-left: 50px">
<p>Available formats are:</p>

<em>%title%</em> - displays the corresponding title for each page type<br />
<em>%site_title%</em> - displays the title of the site

</div>

<br class="clear" />

<h2>Reset</h2>
<p>This will revert to default options.</p>
<form id="efl-reset" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<p class="submit">
		<input type="submit" name="submit-reset" class="button" value="Reset" />
	</p>
</form>

</div>
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
