<?php
/**
 * Script to generate taxonomy terms.
 *
 * This script generates a specified number of terms for each given taxonomy.
 * Each term will be named using the pattern: "{taxonomy_name}-{index}"
 *
 * Configuration:
 * - $taxonomies: Comma-separated list of taxonomies.
 * - $terms_count: Number of terms to generate for each taxonomy.
 */

require_once('../wp-load.php');

echo "Generating taxonomy terms...\n";

// Configuration
$taxonomies = 'locations_country,locations_region,locations_topic';
$terms_count = 5;
$delete = false;

$taxonomy_array = array_map('trim', explode(',', $taxonomies));

if(!$delete) {
    foreach ($taxonomy_array as $taxonomy) {
        $taxonomy_object = get_taxonomy($taxonomy);

        if (!$taxonomy_object) {
            echo "Taxonomy $taxonomy does not exist. Skipping...\n";
            continue;
        }

        $readable_name_base = $taxonomy_object->labels->name;

        echo "Generating terms for taxonomy: $taxonomy ({$readable_name_base})\n";

        for ($i = 1; $i <= $terms_count; $i++) {
            $term_name = "{$readable_name_base} {$i}";

            if (!term_exists($term_name, $taxonomy)) {
                $result = wp_insert_term($term_name, $taxonomy);
                if (is_wp_error($result)) {
                    echo "Error inserting term '$term_name' into '$taxonomy': " . $result->get_error_message() . "\n";
                } else {
                    echo "Inserted term: '$term_name' in taxonomy: '$taxonomy'\n";
                }
            } else {
                echo "Term '$term_name' already exists in taxonomy '$taxonomy'\n";
            }
        }
    }
} else {
    foreach ($taxonomy_array as $taxonomy) {
        if (!taxonomy_exists($taxonomy)) {
            echo "Taxonomy '$taxonomy' does not exist. Skipping...\n";
            continue;
        }

        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            echo "No terms found for taxonomy: $taxonomy\n";
            continue;
        }

        echo "Found " . count($terms) . " terms in taxonomy: $taxonomy. Deleting...\n";

        foreach ($terms as $term) {
            $deleted = wp_delete_term($term->term_id, $taxonomy);
            if (is_wp_error($deleted)) {
                echo "Error deleting term '{$term->name}' (ID: {$term->term_id}): " . $deleted->get_error_message() . "\n";
            } else {
                echo "Deleted term '{$term->name}' (ID: {$term->term_id}) from taxonomy '$taxonomy'\n";
            }
        }
    }
}

echo "Done.\n";
