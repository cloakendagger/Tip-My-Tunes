<?php
function enqueue_ajax_script() {
    // Commenting out or removing the line that enqueues the auto-refresh JavaScript
    // wp_enqueue_script('tip-my-tunes-ajax', plugin_dir_url(__FILE__) . 'js/tip-my-tunes-ajax.js', array('jquery'), null, true);

    wp_localize_script('tip-my-tunes-ajax', 'tipMyTunesAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'user_id' => get_current_user_id()
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_script');

function refresh_song_queue() {
    $user_id = intval($_POST['user_id']);
    if ($user_id > 0) {
        $args = array(
            'post_type' => 'song_request',
            'author' => $user_id,
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
        );
        $song_requests = new WP_Query($args);

        ob_start();
        if ($song_requests->have_posts()) {
            echo '<ul>';
            $count = 1;
            while ($song_requests->have_posts()) : $song_requests->the_post();
                echo '<li>' . $count . '. ' . get_the_title() . '</li>';
                $count++;
            endwhile;
            echo '</ul>';
        } else {
            echo '<p>No requested songs found.</p>';
        }
        wp_reset_postdata();
        $output = ob_get_clean();
        echo json_encode(array('html' => $output));
    }
    wp_die();
}
add_action('wp_ajax_refresh_song_queue', 'refresh_song_queue');
add_action('wp_ajax_nopriv_refresh_song_queue', 'refresh_song_queue');
?>
