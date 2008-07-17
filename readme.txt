=== Extra Feed Links ===
Contributors: scribu
Donate link: http://scribu.net/downloads
Tags: archive, comments, feed, rss
Requires at least: 2.6
Tested up to: 2.5.1
Stable tag: trunk

Adds appropriate feed links to the header of posts, pages, categories, tags, search and author pages.

== Description ==

This plugin adds additional feed links besides the "All posts" feed added by default to every theme.

== Installation ==

1. Unzip the archive and put the folder into your "plugins" folder (/wp-content/plugins/).
1. Activate the plugin from the Plugins admin menu.

== Usage ==

You can use extra_feed_link() inside your theme to display a link to the feed corresponding to the type of page.

`<?php extra_feed_link('http://url/of/image'); ?>` (creates a link with a feed icon that you choose)

`<?php extra_feed_link('Link Text'); ?>` (creates a link with the text you choose)

`<?php extra_feed_link(); ?>` (creates a link with the default text)
