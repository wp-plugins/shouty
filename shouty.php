<?php
/*
 * Plugin Name: Shouty
 * Plugin URI: http://www.gungorbudak.com/shouty
 * Description: Shouty is a shoutbox that makes your Wordpress site more shoutable.
 * Version: 0.0.4
 * Author: Gungor Budak
 * Author URI: http://www.gungorbudak.com
 * License: GPL2
 */

 /*
 * Copyright 2014  Gungor Budak  (email : gngrbdk@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

add_action( 'init', 'shouty_localization_init' );
add_action( 'init', 'shouty_post_type_init' );
add_action( 'wp_enqueue_scripts', 'shouty_enqueue' );
add_action( 'wp_ajax_shouty_share', 'shouty_share' );
add_action( 'wp_ajax_shouty_show_more', 'shouty_show_more' );
add_shortcode( 'shouty', 'shouty_short_code' );

$allowed_html = array();

/*
 * Localization
 */
function shouty_localization_init() {
	load_plugin_textdomain('shouty', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}

/*
* Shouty create custom posty type: shout
*/
function shouty_post_type_init() {
	$labels = array(
		'name'               => _x( 'Shouts', 'post type general name', 'shouty' ),
		'singular_name'      => _x( 'Shout', 'post type singular name', 'shouty' ),
		'menu_name'          => _x( 'Shouts', 'admin menu', 'shouty' ),
		'name_admin_bar'     => _x( 'Shout', 'add new on admin bar', 'shouty' ),
		'add_new'            => _x( 'Add New', 'book', 'shouty' ),
		'add_new_item'       => __( 'Add New Shout', 'shouty' ),
		'new_item'           => __( 'New Shout', 'shouty' ),
		'edit_item'          => __( 'Edit Shout', 'shouty' ),
		'view_item'          => __( 'View Shout', 'shouty' ),
		'all_items'          => __( 'All Shouts', 'shouty' ),
		'search_items'       => __( 'Search Shouts', 'shouty' ),
		'parent_item_colon'  => __( 'Parent Shouts:', 'shouty' ),
		'not_found'          => __( 'No shouts found.', 'shouty' ),
		'not_found_in_trash' => __( 'No shouts found in Trash.', 'shouty' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'shout' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'shout', $args );
}

/*
 * Enqueue scripts and styles
 */
function shouty_enqueue() {
	wp_enqueue_style( 'style', plugins_url( 'css/main.css' , __FILE__ ) );
	wp_enqueue_script( 'script', plugins_url( 'js/main.js' , __FILE__ ), false, '1.0', true );
	wp_localize_script( 'script', 'shouty_ajax', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' )));
}

/*
 * [shouty] short code
 */
function shouty_short_code( $atts ) {
	$a = shortcode_atts( array(
		'user' => 'show',
		'user_avatar_size' => 64,
		'form' => 'show',
		'messages_title' => 'show',
		'messages_number' => 10,
		'messages_users_avatar_size' => 32,
		'look' => 'post'
		), $atts, 'shouty' );
	// Don't show user and form if not logged in
	if ( is_user_logged_in() ) {
		global $current_user;
		get_currentuserinfo();
		$user = ($a['user'] == 'show') ? '<p class="shouty-user"><span class="shouty-user-avatar">' . get_avatar($current_user->ID, $a['user_avatar_size']) . '</span><span class="shouty-user-display-name">' . $current_user->display_name . '</span></p>' : '';
		$form = ($a['form'] == 'show') ? '<form class="group"><input class="shouty-email" type="email" name="email" value=""><textarea class="shouty-textarea" name="text" placeholder="' . __('Enter your shout here', 'shouty') . '"></textarea><input class="shouty-button" type="submit" value="' . __('Share', 'shouty') . '"></form>' : '';
		$sharing = $user . $form;
		} else {
			$sharing = ($a['form'] == 'show') ? '<p><a href="'. get_bloginfo('url') . '/wp-login.php" target="_blank">' . __('Log in or create a new account to share shouts', 'shouty') . '</a></p>' : '';
		}
	$messages_title = ($a['messages_title'] == 'show') ? '<h2 class="shouty-messages-title">' . __('Shouts', 'shouty') . '</h2>' : '';
	$messages = ( $a['look'] == 'post' ) ? '<div class="shouty-messages">' . $messages_title : '<div>';
	$shouts = shouty_get($a['messages_number'], 0, $a['messages_users_avatar_size'], $a['look']);
	$messages .= (empty($shouts) === false) ? $shouts . '</div>' : __('No shouts found.', 'shouty') . '</div>';
	if (empty($shouts) === false) if ( $a['look'] == 'post' ) $messages .= '<a href="#" class="shouty-button-more">' . __('Show more', 'shouty') . '</a>';
	$content = $sharing . $messages;
	return $content;
}

/*
 * Share a shout via AJAX call
 */
function shouty_share() {
	/*
	 * Honeypot SPAM protection
	 * email field will be filled by bots
	 * and it's hidden to the user
	 * so this func will proceed only if
	 * email field is empty
	 */
	$email = sanitize_text_field(html_entity_decode($_POST['email'], ENT_QUOTES, 'UTF-8'));
	$email = wptexturize($email);
	if (empty($email) === true) {
		// shouty_share goes on if the request is not a SPAM
		global $allowed_html;
		global $current_user;
		get_currentuserinfo();
		$title = trim(sanitize_text_field(html_entity_decode($_POST['textarea'], ENT_QUOTES, 'UTF-8')));
		$title = (strlen($title) > 50) ? substr($title, 0, 50).'...' : $title;
		$content = wp_kses((string)html_entity_decode(trim($_POST['textarea']), ENT_QUOTES, 'UTF-8'), $allowed_html);
		$title = wptexturize($title);
		$content = wptexturize($content);
		// If content is given, proceed
		if (empty($content) === false) {
			$content = shouty_make_links(nl2br($content));
			$new_shout = array(
				'post_title' => $title,
				'post_content' => $content,
				'post_status' => 'publish',
				'post_type' => 'shout',
				'post_author' => $current_user->ID
				);
			$shout_id = wp_insert_post( $new_shout );
			/*
			 * Gets user avatar and sets it as
			 * featured image to the shout
			 */
			$user_avatar_url = shouty_get_avatar_url($current_user->ID, 400);
			$user_avatar_id = shouty_set_attachment($user_avatar_url, $shout_id);
			set_post_thumbnail( $shout_id, $user_avatar_id );
		}

		/*
		 * Gets latest shouts and echoes
		 * messages_users_avatar_size from short code here
		 * is NOT dynamic
		 */
		$latest_shouts = shouty_get(10, 0, 32, 'post');
		echo $latest_shouts;
		die();		
	}
}

/*
 * Show more
 */

function shouty_show_more() {
	$offset = (int)$_POST['offset'];
	/*
	 * messages_users_avatar_size from shortcode here
	 * is NOT dynamic
	 */
	$shouts = shouty_get(10, $offset, 32, 'post');
	echo ( empty($shouts) === false ) ? $shouts : '<p>'. __('No more shouts', 'shouty') .'</p>';
	die();
}

/*
 * Get shouts
 */
function shouty_get( $posts_per_page, $offset, $messages_users_avatar_size, $look ) {
	$args = array(
		'posts_per_page'   => $posts_per_page,
		'offset'           => $offset,
		'category'         => '',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'shout',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'post_status'      => 'publish',
		'suppress_filters' => true
	);
	$shouts = get_posts( $args );

	if (empty($shouts) === false) {
		$return_shouts = '';
		foreach ($shouts as $shout) {
			$user = get_userdata( $shout->post_author );
			$return_shouts .= ($look == 'post') ? '<p class="shouty-message"><span class="shouty-user-avatar">' . get_avatar($user->ID, $messages_users_avatar_size) . '</span><strong>' . $user->display_name . '</strong><span class="shouty-messages-content">' . $shout->post_content . '</span>' . get_the_date('j M, H:i', $shout->ID) . ' &ndash; <a href="' . get_permalink( $shout->ID ) . '">' . __('Read more & comment', 'shouty') . ' &rarr;</a></p>' : '<div class="shouty-messages-content group"><div class="shouty-user-avatar-widget">' . get_avatar($user->ID, $messages_users_avatar_size) . '</div><div class="shouty-messages-content-title"><a href="' . get_permalink( $shout->ID ) . '">' . $shout->post_title . '</a><br>' . get_the_date('j M, H:i', $shout->ID) . '</div></div>';
		}
		return $return_shouts;
	} else {
		return '';
	}

}

function shouty_get_avatar_url( $user_id, $size ) {
	/*
	 * Adapted from http://wordpress.stackexchange.com/a/139185
	 */
    $get_avatar = get_avatar( $user_id, $size );
    preg_match("/src='(.*?)'/i", $get_avatar, $matches);
    $pieces = explode('&', $matches[1]);
    $subpieces = explode('?', $pieces[0]);
    return ( $subpieces[0].'.jpg?'.$subpieces[1] );
}

function shouty_set_attachment($user_avatar_url, $shout_id) {
	/*
	 * Adapted from http://www.wpexplorer.com/wordpress-featured-image-url/
	 */
	$image_url  = $user_avatar_url;
	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents($image_url);
	$filename   = basename($image_url);
	$filename_pieces = explode('?', $filename);
	$filename = $filename_pieces[0]; // This is done to get rid of '?s=$size' from the URL
	$wp_filetype = wp_check_filetype( $filename, null );

	if ( wp_mkdir_p( $upload_dir['path'] ) ) {
    	$file = $upload_dir['path'] . '/' . $filename;
	} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
	}

	file_put_contents( $file, $image_data );

	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $file, $shout_id );

	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;
}

function shouty_make_links( $url ) {
	return preg_replace('"\b(https?://[a-zA-Z0-9-./_+?=&#!]+)"', '<a href="$1" target="_blank">$1</a>', $url);
}

?>