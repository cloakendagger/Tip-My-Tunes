<?php
// Shortcode to display the user songs list
function display_user_songs_frontend($atts) {
    $atts = shortcode_atts(array(
        'user_id' => get_current_user_id(),
    ), $atts, 'user_songs');

    $user_id = $atts['user_id'];

    $args = array(
        'post_type' => 'song',
        'author' => $user_id,
        'posts_per_page' => -1,
    );
    $songs = new WP_Query($args);

    $output = '<div class="user-songs">';
    if ($songs->have_posts()) {
        $output .= '<ol>'; // Use ordered list instead of unordered list
        while ($songs->have_posts()) : $songs->the_post();
            $song_title = get_the_title();
            $song_id = get_the_ID();
            $output .= '<li>' . $song_title;
            $output .= ' <button class="request-song-button" data-song-id="' . $song_id . '" data-user-id="' . $user_id . '">Request Song</button></li>';
        endwhile;
        $output .= '</ol>'; // Close the ordered list
    } else {
        $output .= '<p>No songs found.</p>';
    }
    $output .= '</div>';

    $output .= '
    <script>
    jQuery(document).ready(function($) {
        $(".request-song-button").on("click", function() {
            var songId = $(this).data("song-id");
            var userId = $(this).data("user-id");
            $.ajax({
                url: "' . admin_url('admin-ajax.php') . '",
                type: "POST",
                data: {
                    action: "tip_my_tunes_create_payment",
                    song_id: songId,
                    user_id: userId
                },
                success: function(response) {
                    console.log("Response from server:", response); // Log the response for debugging
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.url) {
                            window.location.href = jsonResponse.url;
                        } else if (jsonResponse.error) {
                            alert("Error: " + jsonResponse.error);
                        } else {
                            alert("Unexpected response format. Check console for details.");
                        }
                    } catch (e) {
                        console.error("Error parsing JSON response:", e);
                        console.error("Raw response:", response);
                        alert("Error processing response. Check console for details.");
                    }
                },
                error: function(xhr, status, error) {
                    alert("AJAX error: " + error);
                }
            });
        });
    });
    </script>';

    wp_reset_postdata();

    return $output;
}
add_shortcode('user_songs', 'display_user_songs_frontend');
?>
