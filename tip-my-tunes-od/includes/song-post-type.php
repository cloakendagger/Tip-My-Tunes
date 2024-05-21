<?php
// Create Song custom post type
function create_song_post_type() {
    register_post_type('song',
        array(
            'labels' => array(
                'name' => __('Songs'),
                'singular_name' => __('Song'),
                'add_new' => __('Add New'),
                'add_new_item' => __('Add New Song'),
                'edit_item' => __('Edit Song'),
                'new_item' => __('New Song'),
                'view_item' => __('View Song'),
                'search_items' => __('Search Songs'),
                'not_found' => __('No songs found'),
                'not_found_in_trash' => __('No songs found in Trash'),
                'all_items' => __('All Songs'),
                'archives' => __('Song Archives'),
                'insert_into_item' => __('Insert into song'),
                'uploaded_to_this_item' => __('Uploaded to this song'),
                'filter_items_list' => __('Filter songs list'),
                'items_list_navigation' => __('Songs list navigation'),
                'items_list' => __('Songs list'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'show_in_menu' => current_user_can('administrator'), // Only show in menu for administrators
        )
    );
}
add_action('init', 'create_song_post_type');

// Add custom fields for song details
function add_song_meta_boxes() {
    add_meta_box("song_meta", "Song Details", "display_song_meta_box", "song", "normal", "high");
}
add_action("add_meta_boxes", "add_song_meta_boxes");

function display_song_meta_box($song) {
    $song_price = get_post_meta($song->ID, 'song_price', true);
    ?>
    <label for="song_price">Price: </label>
    <input type="text" name="song_price" value="<?php echo $song_price; ?>" />
    <?php
}

function save_song_meta_box($post_id) {
    if (isset($_POST['song_price'])) {
        update_post_meta($post_id, 'song_price', $_POST['song_price']);
    }
}
add_action('save_post', 'save_song_meta_box');
?>
