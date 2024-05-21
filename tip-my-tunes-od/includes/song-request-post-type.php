<?php
function create_song_request_post_type() {
    register_post_type('song_request',
        array(
            'labels' => array(
                'name' => __('Song Requests'),
                'singular_name' => __('Song Request')
            ),
            'public' => false,
            'show_ui' => true,
            'has_archive' => false,
            'supports' => array('title', 'author'),
        )
    );
}
add_action('init', 'create_song_request_post_type');
?>
