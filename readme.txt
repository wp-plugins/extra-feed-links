=== Extra Feed Links ===
Contributors: scribu
Donate link: http://scribu.net/wordpress
Tags: archive, comments, feed, rss, aton
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: trunk

Adds extra feed auto-discovery links to various page types (categories, tags, search results etc.)

== Description ==

This plugin adds feed auto-discovery links to any page type:

* Category page
* Tag page
* Search page
* Author page
* Comments feed for single articles and pages

It also has a template tag that you can use in your theme.

== Installation ==

1. Unzip the archive and put the folder into your plugins folder (/wp-content/plugins/).
1. Activate the plugin from the Plugins admin menu.
1. Customize the links in the settings page.

**Usage**

You can use `extra_feed_link()` inside your theme to display a link to the feed corresponding to the type of page:

* `<?php extra_feed_link(); ?>` (creates a link with the default text)
* `<?php extra_feed_link('Link Text'); ?>` (creates a link with the text you choose)
* `<?php extra_feed_link('http://url/of/image'); ?>` (creates an image tag linked to the feed URL)
* `<?php extra_feed_link('raw'); ?>` (just displays the feed URL)
