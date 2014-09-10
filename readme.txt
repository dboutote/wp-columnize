=== WP Columnize ===
Contributors: dbmartin
Tags: posts, columns, layout, magazine-style, newspaper-style, multi-column, pages
Requires at least: 2.5.1
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2

Easily create multiple columns within posts and pages.

== Description ==
Easily create multiple columns within your posts and pages for a newspaper/magazine-style layout.  Post and page columns are easily styled with two CSS classes and two custom quicktags which are created automatically upon plugin installation.


== Installation ==
1. Upload the `wp_columnize` folder to the your plugins directory, typically `/wp-content/plugins/`.
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= Does it work using the Visual Editor? =

Yes, except you'll have to manually type the column quicktags, `[col-sect]` and `[column]`
and their corresponding closing tags.

= Do you provide support for the plugin? =

Yes!  Just drop a line on the plugin site in the comments section.

= I don't know much about CSS... =

The plugin homepage provides an example of styling to use.

== Changelog ==
= 1.0 =
* encapsulate code within `mishWPColumnize()` Class
* added ability to add custom `class` and `id` attribites to columns
* fixed erroneous auto-formatting around columns
* updated quicktags to work with the new [Quicktag API](https://codex.wordpress.org/Quicktags_API)

== Upgrade Notice ==
Fixes issues with buttons not appearing on the Text Editor