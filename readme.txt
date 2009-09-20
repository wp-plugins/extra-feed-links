=== Extra Feed Links ===
Contributors: scribu
Donate link: http://scribu.net/wordpress
Tags: archive, comments, feed, rss, aton
Requires at least: 2.5
Tested up to: 2.9-rare
Stable tag: 1.1.5.1

Lets you control extra feed auto-discovery links on various page types (categories, tags, search results etc.)

== Description ==

WordPress 2.8 introduced feed auto-discovery links for each page type: categories, tags, search results etc.

This plugin lets you control what these links look like and where they should appear.

It also has a template tag that you can use in your theme.

== Installation ==

1. Install the plugin through the admin or via FTP.
1. Activate it.
1. Customize the links in the settings page.

**Usage**

You can use `extra_feed_link()` inside your theme to display a link to the feed corresponding to the type of page:

* `<?php extra_feed_link(); ?>` (creates a link with the default text)
* `<?php extra_feed_link('Link Text'); ?>` (creates a link with the text you choose)
* `<?php extra_feed_link('http://url/of/image'); ?>` (creates an image tag linked to the feed URL)
* `<?php extra_feed_link('raw'); ?>` (just outputs the feed URL)

== Changelog ==

= 1.2 =
* updated admin page
* drop compatibility with WordPress older than 2.8
* [more info](http://scribu.net/wordpress/extra-feed-links/efl-1-1.html)

= 1.1.5 =
* WP 2.8 compatibility

= 1.1.1 =
* italian translation

= 1.1 =
* more flexible link text format
* [more info](http://scribu.net/wordpress/extra-feed-links/efl-1-1.html)

= 1.0 =
* added options page

= 0.6 =
* extra_feed_link() template tag

= 0.5 =
* initial release

