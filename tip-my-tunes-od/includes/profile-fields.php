<?php
// Add PayPal email field to user profiles
function add_paypal_email_field($user) {
    ?>
    <h3>PayPal Email</h3>
    <table class="form-table">
        <tr>
            <th><label for="paypal_email">PayPal Email</label></th>
            <td>
                <input type="email" name="paypal_email" id="paypal_email" value="<?php echo esc_attr(get_the_author_meta('paypal_email', $user->ID)); ?>" class="regular-text" />
                <p class="description">Please enter your PayPal email address for receiving payments.</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'add_paypal_email_field');
add_action('edit_user_profile', 'add_paypal_email_field');

// Save PayPal email field
function save_paypal_email_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'paypal_email', sanitize_email($_POST['paypal_email']));
}
add_action('personal_options_update', 'save_paypal_email_field');
add_action('edit_user_profile_update', 'save_paypal_email_field');
?>
