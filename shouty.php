<?php
/*
 * Plugin Name: Shouty
 * Plugin URI: http://www.gungorbudak.com/shouty
 * Description: Shouty is a shoutbox that makes your Wordpress site more shoutable.
 * Version: 0.2.1
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
add_shortcode( 'shouty', 'shouty_shortcode' );
add_filter('widget_text', 'do_shortcode');

$allowed_html = array();

/*
 * Localization
 */
function shouty_localization_init() {
    load_plugin_textdomain('shouty', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}

/*
* Shouty create custom post type: shout
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
 * [shouty] shortcode
 */
function shouty_shortcode($atts) {
    $a = shortcode_atts( array(
        'category' => '',
        'look' => 'post',
        'user' => 'show',
        'user_avatar_size' => 64,
        'form' => 'show',
        'messages' => 'show',
        'messages_links' => 'show',
        'messages_title' => 'show',
        'messages_number' => 10,
        'messages_users_avatar_size' => 32,
        'share_links' => 'show',
        'show_more_button' => 'show'
        ), $atts, 'shouty' );

    $shouty = '<div class="shouty">'
            . '<span class="shouty-options shouty-hidden"'
            . 'data-category="' . $a['category']
            . '" data-look="' . $a['look']
            . '" data-share-links="' . $a['share_links']
            . '" data-messages-links="' . $a['messages_links']
            . '" data-messages-number="' . $a['messages_number']
            . '" data-messages-users-avatar-size="' . $a['messages_users_avatar_size']
            . '" data-messages="' . $a['messages']
            . '" data-count="1"></span>';

    // Don't show user and form if not logged in
    if ( is_user_logged_in() ) {

        global $current_user;
        get_currentuserinfo();

        if ($a['user'] == 'show') {
            $shouty .= '<p class="shouty-user">'
                    . '<span class="shouty-user-avatar">'
                    . get_avatar($current_user->ID, $a['user_avatar_size'])
                    . '</span><span class="shouty-user-display-name">'
                    . $current_user->display_name . '</span></p>';
        }
        if ($a['form'] == 'show') {
            $shouty .= '<form class="group">'
                    . '<input class="shouty-email shouty-hidden" type="email" name="email" value="">'
                    . '<textarea class="shouty-textarea" name="text" placeholder="' . __('Enter your shout here', 'shouty') . '..."></textarea>'
                    . '<input class="shouty-button" type="submit" value="' . __('Share', 'shouty') . '"></form>';
        }

    } else {
        $shouty .= ($a['form'] == 'show') ? '<p><a href="'. get_bloginfo('url') . '/wp-login.php" target="_blank">' . __('Log in or create a new account to share shouts', 'shouty') . '</a></p>' : '';
    }

    if ($a['messages_title'] == 'show') {
        $shouty .= '<h2 class="shouty-messages-title">' . __('Shouts', 'shouty') . '</h2>';
    }

    if ($a['messages'] == 'show') {
        $shouty .= '<div class="shouty-messages">';
        $opts = array(
            'category' => $a['category'],
            'messages_number' => $a['messages_number'],
            'offset' => 0,
            'messages_users_avatar_size' => $a['messages_users_avatar_size'],
            'share_links' => $a['share_links'],
            'look' => $a['look']
            );
        $shouts = shouty_get($opts);
        if (empty($shouts['shouts']) === false) {
            $shouty .= $shouts['shouts'];
        } else {
            $shouty .= __('No shouts found.', 'shouty');
        }
        $shouty .= '</div>';
    }

    if ($a['show_more_button'] == 'show' && $shouts['count'] > $a['messages_number']) {
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

        $messages_links = trim(sanitize_text_field(html_entity_decode($_POST['messages_links'], ENT_QUOTES, 'UTF-8')));
        $messages_links = wptexturize($messages_links);

        $content = wp_kses((string)html_entity_decode(trim($_POST['textarea']), ENT_QUOTES, 'UTF-8'), $allowed_html);
        $content = wptexturize($content);

        $content = shouty_control_links($content, $messages_links);
        $content = shouty_control_breaks($content);

        $title = (strlen($content) > 50) ? substr($content, 0, 50).'...' : $content;

        $category = trim(sanitize_text_field(html_entity_decode($_POST['category'], ENT_QUOTES, 'UTF-8')));
        $category = wptexturize($category);

        // If content is given, proceed
        if (empty($content) === false) {
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

        $share_links = trim(sanitize_text_field(html_entity_decode($_POST['share_links'], ENT_QUOTES, 'UTF-8')));
        $share_links = wptexturize($share_links);

        $look = trim(sanitize_text_field(html_entity_decode($_POST['look'], ENT_QUOTES, 'UTF-8')));
        $look = wptexturize($look);

        $opts = array(
            'category' => $category,
            'messages_number' => $messages_number,
            'offset' => 0,
            'messages_users_avatar_size' => $messages_users_avatar_size,
            'share_links' => $share_links,
            'look' => $look
            );
        $shouts = shouty_get($opts);

        echo json_encode(array(
                            'shouts'   => $shouts['shouts'],
                            'count'    => $shouts['count'],
                            'btn_more' => '<a href="#" class="shouty-button-more">'. __('Show more', 'shouty') .'</a>'
                            ));
    }
    die();
}

/*
 * Show more functionality
 */
function shouty_show_more() {
    $category = sanitize_text_field(html_entity_decode($_POST['category'], ENT_QUOTES, 'UTF-8'));
    $category = wptexturize($category);

    $messages_number = (int)$_POST['messages_number'];
    $factor = (int)$_POST['factor'];

    $messages_users_avatar_size = (int)$_POST['messages_users_avatar_size'];

    $share_links = sanitize_text_field(html_entity_decode($_POST['share_links'], ENT_QUOTES, 'UTF-8'));
    $share_links = wptexturize($share_links);

    $look = sanitize_text_field(html_entity_decode($_POST['look'], ENT_QUOTES, 'UTF-8'));
    $look = wptexturize($look);

    $opts = array(
        'category' => $category,
        'messages_number' => $messages_number,
        'offset' => $factor * $messages_number,
        'messages_users_avatar_size' => $messages_users_avatar_size,
        'share_links' => $share_links,
        'look' => $look
        );
    $shouts = shouty_get($opts);

    if (empty($shouts['shouts']) === false) {
        echo json_encode($shouts);
    } else {
        echo '<p class="shouty-no-more">'. __('No more shouts', 'shouty') .'</p>';
    }
    die();
}

/*
 * Get shouts
 */
function shouty_get($opts) {
    $args = array(
        'posts_per_page'   => $opts['messages_number'],
        'offset'           => $opts['offset'],
        'shout_category'   => $opts['category'],
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
        'shout_category'   => $opts['category'],
        'post_type'        => 'shout',
        'post_status'      => 'publish',
        'numberposts'      => -1
        )));
    $shouts = get_posts( $args );

    if (empty($shouts) === false) {
        $shouts_list = '';
        foreach ($shouts as $shout) {
            $user = get_userdata( $shout->post_author );
            $share_links = '';
            if ($opts['share_links'] == 'show') {
                $share_links = '<a href="https://www.facebook.com/sharer.php?u=' . get_permalink( $shout->ID ) . '" title="' . __('Share this on Facebook', 'shouty') . '" target="_blank">' . __('Share', 'shouty') . '</a> &middot; '
                             . '<a href="https://twitter.com/share?text=' . $shout->post_title . '&url=' . get_permalink( $shout->ID ) . '" title="' . __('Tweet about this on Twitter', 'shouty') . '" target="_blank">' . __('Tweet', 'shouty') . '</a> &middot; ';
            }
            if ($opts['look'] == 'post') {
                $shouts_list .= '<div class="shouty-message">'
                             . '<span class="shouty-user-avatar">'. get_avatar($user->ID, $opts['messages_users_avatar_size']) .'</span>'
                             . '<strong>'. $user->display_name .'</strong>'
                             . '<span class="shouty-message-content">'. $shout->post_content .'</span>'
                             . '<span>'
                             . $share_links
                             . '<abbr title="'. get_the_date('D, d F Y H:i:s', $shout->ID) . '">'. get_the_date('j M, H:i', $shout->ID) . '</abbr> &middot; <a href="'. get_permalink( $shout->ID ) . '" title="' . __('Read more and comment about this', 'shouty') . '">' . __('Read more & comment', 'shouty') . ' &rarr;</a>'
                             . '</span>'
                             . '</div>';
            } elseif ($opts['look'] == 'widget') {
                $shouts_list .= '<div class="shouty-message-widget group">'
                             . '<div class="shouty-user-avatar-widget" title="'. $user->display_name .'">'. get_avatar($user->ID, $opts['messages_users_avatar_size']) .'</div>'
                             . '<div class="shouty-message-content-widget">'
                             . '<strong>'. $user->display_name .'</strong> '
                             . '<a href="' . get_permalink( $shout->ID ) . '" title="' . __('Read more and comment about this', 'shouty') . '">' . $shout->post_title . '</a><br>'
                             . $share_links
                             . '<abbr title="'. get_the_date('D, d F Y H:i:s', $shout->ID) . '">'. get_the_date('j M, H:i', $shout->ID) . '</abbr>'
                             . '</div>'
                             . '</div>';
            }
        }
        return array(
                     'shouts' => $shouts_list,
                     'count'  => $count_shouts
                     );
    } else {
        return null;
    }
}

/*
 * Makes sure there are not more than two breaks
 */
function shouty_control_breaks($content) {
    $break_pattern = '#(<br\s*\/?>\s*){3,}#i';
    return preg_replace($break_pattern, '<br /><br />', nl2br($content));
}

/*
 * Generates links from given URL
 * or if set removes them from the content
 */
function shouty_control_links($url, $messages_links='show') {
    $url_pattern = '"\b(https?://[a-zA-Z0-9-./_+?=&#!]+)"';
    return ($messages_links == 'show') ? preg_replace($url_pattern, '<a href="$1" target="_blank">$1</a>', $url): preg_replace($url_pattern, '', $url);
}

?>
