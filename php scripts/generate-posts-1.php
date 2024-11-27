<?php
/**
 * Script to generate multiple posts with random taxonomy terms and featured image.
 *
 * This script is used to programmatically create multiple posts of a specified post type.
 * Each post is assigned a random term from specified taxonomies and a featured image.
 * The taxonomies can be specified as a comma-separated list, and each will be used to assign a term to the created posts.
 *
 * Configuration variables:
 * - \$posts_count: Number of posts to create.
 * - \$post_type: The type of posts to create.
 * - \$taxonomies: A comma-separated list of taxonomies from which terms will be randomly assigned.
 * - \$post_title_prefix: Prefix for the generated post titles.
 * - \$featured_image: Placeholder image to be used as the featured image for each post.
 */

require_once('../wp-load.php');

echo 'Adding posts...' . "\n";

// Configuration variables
$posts_count = 30;
$post_type = 'post_type';
$taxonomies = 'post_type_category,post_type_category_2'; // Specify multiple taxonomies separated by commas
$post_title_prefix = 'Post Title - ';
$featured_image = get_field('image_placeholder', 'options');

// Split the taxonomies into an array
$taxonomy_array = array_map('trim', explode(',', $taxonomies));

// Create posts
for ($i = 1; $i <= $posts_count; $i++) {
    $current_post_title = "$post_title_prefix $i";

    // Prepare post data
    $new_post = [
        'post_title'   => $current_post_title,
        'post_content' => "This is the content for $current_post_title",
        'post_status'  => 'publish',
        'post_type'    => $post_type,
        'post_author'  => 1,
    ];

    // Insert post
    $post_id = wp_insert_post($new_post);
    if (is_wp_error($post_id)) {
        echo "Error inserting post: " . $post_id->get_error_message() . "\n";
        continue;
    }
    echo "Inserted post with ID: $post_id\n";

    // Set featured image if available
    if ($featured_image) {
        set_post_thumbnail($post_id, $featured_image['id']);
        echo "Set featured image for post ID: $post_id\n";
    }

    // Assign random terms from each taxonomy
    foreach ($taxonomy_array as $taxonomy) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);

        if (!is_wp_error($terms) && !empty($terms)) {
            $random_term = $terms[array_rand($terms)];
            wp_set_post_terms($post_id, [$random_term->term_id], $taxonomy);
            echo "Assigned term ID: {$random_term->term_id} to post ID: $post_id for taxonomy: $taxonomy\n";
        } else {
            echo "No terms found for taxonomy: $taxonomy\n";
        }
    }
}
