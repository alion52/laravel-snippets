<?php

namespace App\Sync;

use WP_Query;

/**
 * Сбор данных для синхронизации.
 */
class CollectorExample
{
    public function collectPosts(array $excludedIds = []): array
    {
        $query = new WP_Query([
            'post_type' => ['post', 'page'],
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'post__not_in' => $excludedIds,
        ]);

        $items = [];

        foreach ($query->posts as $post) {
            $items[] = [
                'id' => $post->ID,
                'type' => $post->post_type,
                'title' => $post->post_title,
                'slug' => $post->post_name,
                'content' => $post->post_content,
                'excerpt' => $post->post_excerpt,
                'status' => $post->post_status,
                'date' => $post->post_date,
                'meta' => $this->collectMeta($post->ID),
                'acf' => $this->collectAcf($post->ID),
                'terms' => $this->collectTerms($post->ID),
            ];
        }

        return $items;
    }

    private function collectMeta(int $postId): array
    {
        return get_post_meta($postId);
    }

    private function collectAcf(int $postId): array
    {
        if (!function_exists('get_fields')) {
            return [];
        }
        return get_fields($postId) ?: [];
    }

    private function collectTerms(int $postId): array
    {
        $terms = [];
        $taxonomies = get_post_taxonomies($postId);

        foreach ($taxonomies as $taxonomy) {
            $terms[$taxonomy] = wp_get_post_terms($postId, $taxonomy, ['fields' => 'ids']);
        }

        return $terms;
    }
}
