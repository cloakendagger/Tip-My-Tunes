<?php
get_header();
$user = get_user_by('slug', get_query_var('author_name'));

if ($user) {
    echo '<h1>' . esc_html($user->display_name) . '\'s Song Queue</h1>';
    echo '<div id="song-queue">';

    if (isset($_POST['clear_queue']) && is_user_logged_in()) {
        // Clear the song queue for the logged-in user
        $args = array(
            'post_type' => 'song_request',
            'author' => $user->ID,
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
        );
        $song_requests = new WP_Query($args);

        while ($song_requests->have_posts()) : $song_requests->the_post();
            wp_delete_post(get_the_ID(), true);
        endwhile;

        wp_reset_postdata();
    }

    // Query user's requested songs
    $args = array(
        'post_type' => 'song_request',
        'author' => $user->ID,
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'ASC',
    );
    $song_requests = new WP_Query($args);

    if ($song_requests->have_posts()) {
        echo '<form method="post">';
        echo '<ul>';
        $count = 1;
        while ($song_requests->have_posts()) : $song_requests->the_post();
            $time_submitted = get_post_meta(get_the_ID(), 'time_submitted', true);
            echo '<li>' . $count . '. ' . get_the_title() . ' - ' . esc_html($time_submitted) . '</li>';
            $count++;
        endwhile;
        echo '</ul>';

        if (is_user_logged_in() && $user->ID == get_current_user_id()) {
            echo '<input type="submit" name="clear_queue" value="Clear Queue" class="button">';
        }

        echo '</form>';
    } else {
        echo '<p>No requested songs found.</p>';
    }

    wp_reset_postdata();
    echo '</div>';
} else {
    echo '<p>User not found.</p>';
}

get_footer();
?>
