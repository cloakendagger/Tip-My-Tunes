<?php
function tip_my_tunes_settings_init() {
    register_setting('tip_my_tunes_settings', 'tip_my_tunes_settings');

    add_settings_section(
        'tip_my_tunes_section',
        __('Tip My Tunes Settings', 'tip_my_tunes'),
        'tip_my_tunes_section_callback',
        'tip_my_tunes_settings'
    );

    add_settings_field(
        'tip_my_tunes_mode',
        __('PayPal Mode', 'tip_my_tunes'),
        'tip_my_tunes_mode_render',
        'tip_my_tunes_settings',
        'tip_my_tunes_section'
    );

    add_settings_field(
        'paypal_client_id',
        __('PayPal Client ID', 'tip_my_tunes'),
        'paypal_client_id_render',
        'tip_my_tunes_settings',
        'tip_my_tunes_section'
    );

    add_settings_field(
        'paypal_client_secret',
        __('PayPal Client Secret', 'tip_my_tunes'),
        'paypal_client_secret_render',
        'tip_my_tunes_settings',
        'tip_my_tunes_section'
    );
}

function tip_my_tunes_section_callback() {
    echo __('Configure PayPal settings for transactions.', 'tip_my_tunes');
}

function tip_my_tunes_mode_render() {
    $options = get_option('tip_my_tunes_settings');
    ?>
    <select name='tip_my_tunes_settings[tip_my_tunes_mode]'>
        <option value='sandbox' <?php selected($options['tip_my_tunes_mode'], 'sandbox'); ?>>Sandbox</option>
        <option value='live' <?php selected($options['tip_my_tunes_mode'], 'live'); ?>>Live</option>
    </select>
    <?php
}

function paypal_client_id_render() {
    $options = get_option('tip_my_tunes_settings');
    $client_id = isset($options['paypal_client_id']) ? esc_attr($options['paypal_client_id']) : '';
    ?>
    <input type='text' name='tip_my_tunes_settings[paypal_client_id]' value='<?php echo $client_id; ?>'>
    <?php
}

function paypal_client_secret_render() {
    $options = get_option('tip_my_tunes_settings');
    $client_secret = isset($options['paypal_client_secret']) ? esc_attr($options['paypal_client_secret']) : '';
    ?>
    <input type='text' name='tip_my_tunes_settings[paypal_client_secret]' value='<?php echo $client_secret; ?>'>
    <?php
}

function tip_my_tunes_settings_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Tip My Tunes Settings</h2>
        <?php
        settings_fields('tip_my_tunes_settings');
        do_settings_sections('tip_my_tunes_settings');
        submit_button();
        ?>
    </form>
    <?php
}

function tip_my_tunes_settings_menu() {
    add_options_page(
        'Tip My Tunes Settings',
        'Tip My Tunes',
        'manage_options',
        'tip_my_tunes_settings',
        'tip_my_tunes_settings_page'
    );
}
add_action('admin_menu', 'tip_my_tunes_settings_menu');
add_action('admin_init', 'tip_my_tunes_settings_init');
?>
