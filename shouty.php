<?php
/*
 * Plugin Name: Shouty
 * Plugin URI: http://www.gungorbudak.com/shouty
 * Description: Shouty is a shoutbox that makes your Wordpress site more shoutable.
 * Version: 0.1.2
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
add_action( 'init', 'shouty_taxonomy_init');
add_action( 'wp_enqueue_scripts', 'shouty_enqueue' );
add_action( 'wp_ajax_shouty_share', 'shouty_share' );
add_action( 'wp_ajax_shouty_show_more', 'shouty_show_more' );
add_shortcode( 'shouty', 'shouty_short_code' );
add_filter('widget_text', 'do_shortcode');

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
        'rewrite'            => array( 'slug' => 'shout', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'taxonomies'         => array('shout_category'),
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'          => 'dashicons-testimonial',
    );

    register_post_type( 'shout', $args );
}

/*
* Shouty create custom taxonomy
*/

function shouty_taxonomy_init() {
    register_taxonomy(
        'shout_category',
        'shout',
        array(
            'hierarchical'        => true,
            'label'               => __('Categories'),
            'query_var'           => true,
            'show_admin_column'   => true,
            'rewrite'             => array(
                'slug'            => 'shout-category',
                'with_front'      => true
                )
            )
    );
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
        'category' => '',
        'look' => 'post',
        'user' => 'show',
        'user_avatar_size' => 64,
        'form' => 'show',
        'messages' => 'show',
        'messages_title' => 'show',
        'messages_number' => 10,
        'messages_users_avatar_size' => 32,
        'show_more_button' => 'show'
        ), $atts, 'shouty' );

    // Don't show user and form if not logged in
    if ( is_user_logged_in() ) {

        global $current_user;
        get_currentuserinfo();
        $shouty = '<div class="shouty"><span class="shouty-options shouty-hidden" data-category="'. $a['category'] .'" data-look="'. $a['look'] .'" data-messages-number="'. $a['messages_number'] .'" data-messages-users-avatar-size="'. $a['messages_users_avatar_size'] .'" data-messages="'. $a['messages'] .'" data-count="1"></span>';
        if ($a['user'] == 'show') {
            $shouty .= '<p class="shouty-user"><span class="shouty-user-avatar">' . get_avatar($current_user->ID, $a['user_avatar_size']) . '</span><span class="shouty-user-display-name">' . $current_user->display_name . '</span></p>';
        }
        if ($a['form'] == 'show') {
            $shouty .= '<form class="group"><input class="shouty-email shouty-hidden" type="email" name="email" value=""><textarea class="shouty-textarea" name="text" placeholder="' . __('Enter your shout here', 'shouty') . '..."></textarea><input class="shouty-button" type="submit" value="' . __('Share', 'shouty') . '"></form>';
        }

    } else {
        $shouty .= ($a['form'] == 'show') ? '<p><a href="'. get_bloginfo('url') . '/wp-login.php" target="_blank">' . __('Log in or create a new account to share shouts', 'shouty') . '</a></p>' : '';
    }

    if ($a['messages_title'] == 'show') {
        $shouty .= '<h2 class="shouty-messages-title">' . __('Shouts', 'shouty') . '</h2>';
    }

    if ($a['messages'] == 'show') {
        $shouty .= '<div class="shouty-messages">';
        $shouts = shouty_get($a['category'], $a['messages_number'], 0, $a['messages_users_avatar_size'], $a['look']);
        if (empty($shouts[0]) === false) {
            $shouty .= $shouts[0];
        } else {
            $shouty .= __('No shouts found.', 'shouty');
        }
        $shouty .= '</div>';
    }

    if ($a['show_more_button'] == 'show' && $shouts[1] > $a['messages_number']) {
        $shouty .= '<a href="#" class="shouty-button-more">'. __('Show more', 'shouty') .'</a>';
    }

    $shouty .= '</div>';

    return $shouty;
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
        $category = trim(sanitize_text_field(html_entity_decode($_POST['category'], ENT_QUOTES, 'UTF-8')));
        $title = wptexturize($title);
        $content = wptexturize($content);
        $category = wptexturize($category);
        // If content is given, proceed
        if (empty($content) === false) {
            $content = shouty_make_links(nl2br($content));
            if (empty($category) === false) {
                $category_object = get_term_by( 'slug', $category, 'shout_category' );
                $category_id = (int)$category_object->term_id;
            }

            $new_shout = array(
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_type' => 'shout',
                'post_author' => $current_user->ID
                );
            $shout_id = wp_insert_post( $new_shout );
            $cat_ids = wp_set_object_terms( $shout_id, array($category_id), 'shout_category', true );
        }

        $messages_number = (int)$_POST['messages_number'];
        $messages_users_avatar_size = (int)$_POST['messages_users_avatar_size'];
        $look = trim(sanitize_text_field(html_entity_decode($_POST['look'], ENT_QUOTES, 'UTF-8')));
        $look = wptexturize($look);

        $latest_shouts = shouty_get($category, $messages_number, 0, $messages_users_avatar_size, $look);
        echo json_encode(array($latest_shouts[0], $latest_shouts[1], '<a href="#" class="shouty-button-more">'. __('Show more', 'shouty') .'</a>'));
        die();
    }
}

/*
 * Show more
 */

function shouty_show_more() {
    $messages_number = (int)$_POST['messages_number'];
    $messages_users_avatar_size = (int)$_POST['messages_users_avatar_size'];
    $factor = (int)$_POST['factor'];
    $category = sanitize_text_field(html_entity_decode($_POST['category'], ENT_QUOTES, 'UTF-8'));
    $look = sanitize_text_field(html_entity_decode($_POST['look'], ENT_QUOTES, 'UTF-8'));
    $category = wptexturize($category);
    $look = wptexturize($look);

    $shouts = shouty_get($category, $messages_number, $factor*$messages_number, $messages_users_avatar_size, $look);
    if (empty($shouts[0]) === false) {
        echo json_encode($shouts);
    } else {
        echo '<p class="shouty-no-more">'. __('No more shouts', 'shouty') .'</p>';
    }
    die();
}

/*
 * Get shouts
 */
function shouty_get( $category, $posts_per_page, $offset, $messages_users_avatar_size, $look ) {
    $args = array(
        'posts_per_page'   => $posts_per_page,
        'offset'           => $offset,
        'shout_category'   => $category,
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

    $count_shouts = count(get_posts(array(
        'shout_category'   => $category,
        'post_type'        => 'shout',
        'post_status'      => 'publish',
        'numberposts'      => -1
        )));
    $shouts = get_posts( $args );

    if (empty($shouts) === false) {
        $shouts_list = '';
        foreach ($shouts as $shout) {
            $user = get_userdata( $shout->post_author );
            if ($look == 'post') {
                $shouts_list .= '<div class="shouty-message">';
                $shouts_list .= '<span class="shouty-user-avatar">'. get_avatar($user->ID, $messages_users_avatar_size) .'</span>';
                $shouts_list .= '<strong>'. $user->display_name .'</strong>';
                $shouts_list .= '<span class="shouty-message-content">'. $shout->post_content .'</span>';
                $shouts_list .= '<span>'. get_the_date('j M, H:i', $shout->ID) . ' &ndash; <a href="'. get_permalink( $shout->ID ) . '">' . __('Read more & comment', 'shouty') . ' &rarr;</a></span>';
                $shouts_list .= '</div>';
            } elseif ($look == 'widget') {
                $shouts_list .= '<div class="shouty-message-widget group">';
                $shouts_list .= '<span class="shouty-user-avatar-widget">'. get_avatar($user->ID, $messages_users_avatar_size) .'</span>';
                $shouts_list .= '<span class="shouty-message-content-widget"><a href="' . get_permalink( $shout->ID ) . '">' . $shout->post_title . '</a><br>' . get_the_date('j M, H:i', $shout->ID) . '</span>';
                $shouts_list .= '</div>';
            }
        }
        return array($shouts_list, $count_shouts);
    } else {
        return null;
    }
}

function shouty_make_links( $url ) {
    return preg_replace('"\b(https?://[a-zA-Z0-9-./_+?=&#!]+)"', '<a href="$1" target="_blank">$1</a>', $url);
}

?>