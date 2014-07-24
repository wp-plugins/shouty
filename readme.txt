=== Shouty ===
Contributors: gungorbudak
Tags: AJAX, shoutbox, contact, comments, shortcode, widget, sidebar, page, Post, posts, honeypot
Donate link: http://www.gungorbudak.com
Requires at least: 2.7
Tested up to: 3.9.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shouty is a shoutbox powered by AJAX that you can add anywhere using shortcode which comes with some options to customize Shouty.

== Description ==

Shouty is a Wordpress plugin for you to accept messages from your users. It creates a custom post type called "shout" and you will be administer shouts from the Dashboard as you do for the default post types. You can add Shouty in a post, page or a widget using its shorcode. The shortcode has certain options for you to customize the look and function of the Shouty. Shouty shares messages using AJAX and protects your blog using Honeypot spam protection technique. Users must log in to share shouts. When a shout is shared, the user's avatar is set to be the featured image of the shout so that you can late make use of it. It doesn't accept any HTML but http and https links are converted to a tags when the shout is shared. You don't have to worry about unclickable links.

**Options you can set for the shortcode**

* user (show|hide; default: show; Displays user information at the top of the form)
* user_avatar_size (in px; default: 64px; Changes size of the user avatar in user information)
* form (show|hide; default: show; Displays Shouty form and the share button)
* messages_title (show|hide; default: show; Displays a title at the tops of the shouts)
* messages_number (a number; default: 10; The number of shouts to be viewed in one time)
* messages_users_avatar_size (in px; default: 32; Changes size of the user avatar on the left of the each shout)
* look (post|widget; default: post; Switches looks - widget look is more compact)

The plugin is written in English and has been translated into Turkish. You're welcomed if you want to translate it into your language. Use translations.pot, rename it as shouty_LANGCODE.po and open it in Poedit. Translate and save. There should be a MO files as well. Send PO and MO files to me, then I will add them in the next release.

== Installation ==

1. Decompress the package you downloaded and upload `/shouty/` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. And shout!

== Frequently Asked Questions ==

= Is this plugin bug free? =
It is the first release so, any bug report would be appreciated.

== Screenshots ==

1. Post/page look of Shouty without a shout
2. Post/page look of Shouty with a shout
3. Shouty shortcode when it's in the widget
4. Shouty widget look
5. Individual shout

== Changelog ==

= 0.0.1 =
First release

== Upgrade Notice ==

= 0.0.1 =
First release