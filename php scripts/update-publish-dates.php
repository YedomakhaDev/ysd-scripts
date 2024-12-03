<?php

/**
 * This script updates the publish dates of all posts for a specified custom post type.
 * You can configure the following parameters:
 * - \$post_type: The custom post type whose posts you want to update.
 * - \$start_date: The starting date and time for the first post.
 * - \$time_interval: The interval to add to each subsequent post's publish date.
 *
 * The script retrieves all posts of the given type, sorts them by date in ascending order,
 * and updates their publish dates starting from the specified start date, incrementing by the defined interval.
 */

//require_once('../wp-load.php');

$post_type = 'post_type_name';
$start_date = new DateTime('2024-11-25 12:00:00');
$time_interval = '+1 minute';

$args = array(
    'post_type'      => $post_type,
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
);
$posts = get_posts($args);

foreach ($posts as $post) {
    $new_post_date = $start_date->format('Y-m-d H:i:s');

    wp_update_post(array(
        'ID'            => $post->ID,
        'post_date'     => $new_post_date,
        'post_date_gmt' => get_gmt_from_date($new_post_date),
    ));

    $start_date->modify($time_interval);
}

echo 'Dates for all posts have been updated.';
die();