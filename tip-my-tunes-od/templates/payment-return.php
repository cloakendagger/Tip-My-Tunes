<?php
get_header();

if (get_query_var('pagename') == 'payment-success') {
    ?>
    <div class="payment-success">
        <h1>Payment Successful</h1>
        <p>Thank you for your payment. Your song has been added to the queue.</p>
        <p><a href="<?php echo home_url('/username/songque'); ?>">View Your Song Queue</a></p>
    </div>
    <?php
} elseif (get_query_var('pagename') == 'payment-cancel') {
    ?>
    <div class="payment-cancel">
        <h1>Payment Cancelled</h1>
        <p>Your payment was cancelled. Please try again.</p>
        <p><a href="<?php echo home_url('/username/songs'); ?>">Go back to Songs</a></p>
    </div>
    <?php
}

get_footer();
?>
