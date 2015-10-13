=== Shouty ===
Contributors: gungorbudak
Tags: AJAX, shoutbox, contact, comments, shortcode, widget, sidebar, page, Post, posts, honeypot
Donate link: http://www.gungorbudak.com/buy-me-a-coffee
Requires at least: 2.7
Tested up to: 4.3
Stable tag: 0.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shouty is a shoutbox powered by AJAX that you can add anywhere using shortcode which comes with some options to customize Shouty.

== Description ==

Shouty (shoutbox) is a Wordpress plugin for you to receive messages from your users. It creates a custom post type called "shout" which will allow you to administer shouts from the Dashboard as you do for the default post types. You can add Shouty in a post, page or a widget using its shortcode. The shortcode has certain options for you to customize the look and function of the Shouty. Shouty also creates a custom category option which can be used to create multiple shoutboxes on your website. To do that, you should first create a category, then set it in the shortcode. Shouty shares messages using AJAX and protects your blog using Honeypot spam protection technique. Users must log in to share shouts. It doesn't accept any HTML but http and https links are converted to `<a>` tags when the shout is shared. You don't have to worry about unclickable links.

**How to use Shouty**

Insert the shortcode below into any post/page or widget.

`[shouty category="home-page" look="widget" user="hide" form="hide" messages_number="5" messages_title="hide" messages_users_avatar_size="60" show_more_button="hide"]`

**Options you can set for the shortcode**

* category (category-slug; default: empty; Limits shout viewing and posting to a previously created shout category)
* look (post | widget; default: post; Switches looks - widget look is more compact)
* user (show | hide; default: show; Displays user information at the top of the form)
* user_avatar_size (in px; default: 64; Changes size of the user avatar in user information)
* form (show | hide; default: show; Displays Shouty form and the share button)
* messages_title (show | hide; default: show; Displays a title at the tops of the shouts)
* messages (show | hide; default: show; Displays shouts)
* messages_links (show | hide; default: show; Displays URLs in the shout)
* messages_number (a number; default: 10; The number of shouts to be viewed in one time)
* messages_users_avatar_size (in px; default: 32; Changes size of the user avatar on the left of the each shout)
* share_links (show | hide; default: show; Displays Facebook share and Twitter tweet links)
* show_more_button (show | hide; default: show; Displays show more button at the end of the shouts)

The plugin is written in English and has been translated into Turkish by [myself](http:/www.gungorbudak.com/ "Güngör Budak")
. German (by [Pascal Jordin](http://www.jordin.eu/ "Pascal Jordin")), Serbian (by [Ogi Djuraskovic](http://firstsiteguide.com/ "First Site Guide")) and Spanish (by [Andrew Kurtis](http://www.webhostinghub.com/ "WebHostingHub")) translations are also available. You're welcomed if you want to translate it into your language. Use translations.pot, rename it as shouty-LANGCODE.po (e.g. shouty-tr_TR) and open it in Poedit. Translate and save. There should be a MO file created by Poedit. Send PO and MO files to me, then I will add them in the next release.

== Installation ==

1. Decompress the package you downloaded and upload `/shouty/` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. And shout!

== Frequently Asked Questions ==

= Is this plugin bug free? =
It is the first release so, any bug report would be appreciated.

= May I request a new feature? =
Absolutely.

== Screenshots ==

1. Post/page look of Shouty without a shout
2. Post/page look of Shouty with a shout
3. Shouty shortcode when it's in the widget
4. Shouty widget look
5. Individual shout

== Changelog ==

= 0.2.1 =
Missing additional styles also added

= 0.2.0 =
* Shortcode configuration to allow or prevent URLs in shouts, defaults to allowing them.
* Facebook share and Twitter tweet links added to shouts and made configurable through shortcode, defaults to showing them.
* User display name in front of the shout in widget layout.
* Better widget layout by taking user display name, shout, share links and time on the right, and avatar on the left.
* Representation of time for shouts improved.
* More than two new lines in shouts now being prevented in the backend.
* Many improvements in the code.

= 0.1.3 =
Fixing extra div closing tag for not registered users. Tests for 4.3.

= 0.1.2 =
Many fixes for broken Shouty functionalities, new Dashicon, Spanish translations

= 0.1.1 =
In WP 4.0, the shortcode in text widgets was not working after 0.1.0, this has been fixed in this version.

= 0.1.0 =
Shouty no longer sets user avatar as featured image – this option will be reconsidered in next releases. Now shouts can be categorized and in this way you can have multiple shoutboxes on your website. Just don't forget to create a category first and include in the shortcode Sharing shouts and pagination have been improved. Sharing shouts from widgets is now possible. Every functionality is avaliable for post/page look as well as widget look. You can also now use all attributes of shortcode dynamically. You can also now hide show more button.

= 0.0.4 =
German translations have been added

= 0.0.3 =
WP 4.0 compatibility has been checked and Serbian translations have been added

= 0.0.2 =
Bug fixed: you could send an empty shout

= 0.0.1 =
First release

== Upgrade Notice ==

= 0.0.1 =
First release
