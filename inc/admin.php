<?php
class extraFeedLinkAdmin extends extraFeedLink {
	var $default_format = array(
		'home' => array(TRUE, 'All comments for %site_title%'),
		'comments' => array(TRUE, 'Comments: %title%'),
		'category' => array(TRUE, 'Category: %title%'),
		'tag' => array(TRUE, 'Tag: %title%'),
		'author' => array(TRUE, 'Author: %title%'),
		'search' => array(TRUE, 'Search: %title%')
	);

	// PHP4 compatibility
	function extraFeedLinkAdmin() {
		$this->__construct();
	}

	function __construct() {
		add_action('admin_menu', array(&$this, 'page_init'));
	}

	function install() {
		if ( $old = get_option($this->key) )
			$this->default_format = array_merge($this->default_format, $old);

		   add_option($this->key, $this->default_format) or
		update_option($this->key, $this->default_format);
	}

	function handle_options() {
		$this->format = get_option($this->key);

		if ( !isset($_POST['action']) )
			return;

		$message = '<div class="updated"><p>Options <strong>%s</strong>.</p></div>';

		// Update options
		if ( 'Save Changes' == $_POST['action'] ) {
			foreach ($this->format as $name => $value) {
				$this->format[$name][0] = $_POST['show-' . $name];
				$this->format[$name][1] = $_POST['format-' . $name];
			}

			update_option($this->key, $this->format);
			printf($message, 'saved');
		}

		// Reset options
		if ( 'Reset' == $_POST['action'] ) {
			$this->format = $this->default_format;
			
			update_option($this->key, $this->format);
			printf($message, 'reset');
		}
	}

	// Options page
	function page_init() {
		if ( current_user_can('manage_options') ) {
			$page = add_options_page('Extra Feed Links', 'Extra Feed Links', 8, 'extra-feed-links', array(&$this, 'page'));
			add_action("admin_print_scripts-$page", create_function('', "wp_enqueue_script('admin-forms');"));	// deprecated since WP 2.7
		}
	}

	function page() {
		$this->handle_options();
?>
<div class="wrap">
<h2>Extra Feed Links</h2>

<p>The table below allows you to select which page types get an extra header link and the format of the link text.</p>

<div class="alignleft" style="width:auto">
<form method="post" action="">
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
			<input name="action" type="submit" class="button-primary button" value="Save Changes" />
			<input name="action" type="submit" class="button-secondary" onClick="return confirm('Are you sure you want to reset to defaults?')" value="Reset" />
		</div>
	</div>

	</form>
</div>

<div style="float:left; margin-left: 50px">
	<p>Available substitution tags:</p>
	<ul>
		<li><em>%title%</em> - displays the corresponding title for each page type</li>
		<li><em>%site_title%</em> - displays the title of the site</li>
	</ul>
</div>
<br class="clear" />
</div>
<?php	}
}

