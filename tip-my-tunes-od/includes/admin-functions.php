<?php
// Add Songs menu item to the user profile page
function add_songs_menu_item() {
    add_menu_page(
        __('Your Songs', 'tip-my-tunes'),
        __('Your Songs', 'tip-my-tunes'),
        'read',
        'user-songs',
        'display_user_songs_page',
        'dashicons-format-audio',
        6
    );
}
add_action('admin_menu', 'add_songs_menu_item');

// Display the user's songs in the admin area
function display_user_songs_page() {
    $user_id = get_current_user_id();

    echo '<div class="wrap">';
    echo '<h1>' . __('Your Songs', 'tip-my-tunes') . '</h1>';
    if (current_user_can('publish_posts')) {
        echo '<a href="' . admin_url('post-new.php?post_type=song') . '" class="page-title-action">' . __('Add New') . '</a>';
    }
    echo '<hr>';

    // Output the table of songs
    $args = array(
        'post_type' => 'song',
        'author' => $user_id,
        'posts_per_page' => -1,
    );
    $songs = new WP_Query($args);

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Title') . '</th>';
    echo '<th>' . __('Price') . '</th>';
    echo '<th>' . __('Actions') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if ($songs->have_posts()) {
        while ($songs->have_posts()) : $songs->the_post();
            echo '<tr>';
            echo '<td>' . get_the_title() . '</td>';
            echo '<td>' . get_post_meta(get_the_ID(), 'song_price', true) . '</td>';
            echo '<td>';
            echo '<a href="' . get_edit_post_link() . '">' . __('Edit') . '</a> | ';
            echo '<a href="' . get_delete_post_link() . '">' . __('Delete') . '</a>';
            echo '</td>';
            echo '</tr>';
        endwhile;
    } else {
        echo '<tr><td colspan="3">' . __('No songs found') . '</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';

    echo '</div>';

    wp_reset_postdata();
}
?>
