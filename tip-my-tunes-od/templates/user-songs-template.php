<?php
get_header();

$user = get_user_by('slug', get_query_var('author_name'));

if ($user) {
    echo '<h1>' . esc_html($user->display_name) . '\'s Songs</h1>';
    echo do_shortcode('[user_songs user_id="' . $user->ID . '"]');
} else {
    echo '<p>User not found.</p>';
}

get_footer();
?>
