<?php

/*
Plugin Name: Post Date Options
Plugin URI:
Description: Plugin for change date of posts
Author: Anton Litovchenko
Version: 2.2.2
Author URI: https://www.upwork.com/fl/antonlitovchenko
*/

add_action('genesis_entry_content', 'pdo_remove_post_meta');

function pdo_remove_post_meta()
{
    if (is_singular('page')) {
        return;
    }

    remove_action('genesis_entry_footer', 'genesis_entry_footer_markup_open', 5);
    remove_action('genesis_entry_footer', 'genesis_entry_footer_markup_close', 15);
    remove_action('genesis_entry_footer', 'genesis_post_meta');
}

add_filter('genesis_post_info', 'pdo_post_info_filter', 20);
    function pdo_post_info_filter($post_info)
    {
        global $post;

        $key = 'my_meta_box_select';
        $meta_values = get_post_meta($post->ID, $key, true);
        if (isset($meta_values)) {
            $author = get_the_author();
            switch ($meta_values) {
                case 'current':
                    $current_date = current_time('F d, Y');
                    $post_info = $current_date . ' By ' . $author;
                    return $post_info;
                    break;
                case 'create':
                    $current_date = get_the_date() . ' By ' . $author;
                    return $current_date;
                    break;
                case 'last':
                    $current_date = '[post_modified_date] By ' . $author;
                    return $current_date;
                    break;
                case 'default':
                    $current_date = get_the_date() . ' By ' . $author;
                    return $current_date;
                    break;
                default:
                    $current_date = get_the_date() . ' By ' . $author;
                    return $current_date;
                    break;
            }
        }
        $current_date = get_the_date() . ' By ' . $author;
        return $current_date;
    }
if( !has_action('genesis_post_info') ){
    add_filter('get_the_date', 'pdo_filter_get_the_date', 10);
    function pdo_filter_get_the_date($the_date)
    {
        global $post;
        
        $key = 'my_meta_box_select';
        $meta_values = get_post_meta($post->ID, $key, true);

        if (isset($meta_values)) {
            $author = get_the_author();
            switch ($meta_values) {
                case 'current':
                    $current_date = current_time('F d, Y');
                    $post_info = $current_date . ' By ' . $author;
                    return $post_info;
                    break;
                case 'create':
                    $current_date = get_the_time('F d, Y', $post->ID) . ' By ' . $author;
                    return $current_date;
                    break;
                case 'last':
                    $current_date = get_post_modified_time('F d, Y', $post->ID) . ' By ' . $author;

                    return $current_date;
                    break;
                case 'default':
                    $current_date = get_the_time('F d, Y', $post->ID) . ' By ' . $author;
                    return $current_date;
                    break;
                default:
                    $current_date = get_the_time('F d, Y', $post->ID) . ' By ' . $author;
                    return $current_date;
                    break;
            }
        }
        $current_date = get_the_time('Y-m-d') . ' By ' . $author;
        return $current_date;
    }
}


function pdo_posts_meta_box()
{
    add_meta_box(
        'posts_meta_box',
        'Post Date Options',
        'pdo_show_posts_metabox',
        'post',
        'side',
        'high');
}

add_action('add_meta_boxes', 'pdo_posts_meta_box');

function pdo_show_posts_metabox($post)
{
    $values = get_post_custom($post->ID);
    $selected = isset($values['my_meta_box_select']) ? $values['my_meta_box_select'][0] : '';
    wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
    ?>

    <p>
        <label for="my_meta_box_select">Post Date</label>
        <select name="my_meta_box_select" id="my_meta_box_select">
            <option value="default" <?php selected($selected, 'default'); ?>>Default</option>
            <option value="create" <?php selected($selected, 'create'); ?>>Create Date</option>
            <option value="current" <?php selected($selected, 'current'); ?>>Current Date</option>
            <option value="last" <?php selected($selected, 'last'); ?>>Last Updated Post Date</option>
        </select>
    </p>

    <?php
}

add_action('save_post', 'pdo_meta_box_save');
function pdo_meta_box_save($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post')) return;
    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array()
        )
    );
    if (isset($_POST['my_meta_box_select']))
        update_post_meta($post_id, 'my_meta_box_select', sanitize_text_field($_POST['my_meta_box_select']));
}
