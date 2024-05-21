<?php
get_header();
?>

<div class="payment-success">
    <h1>Payment Successful</h1>
    <p>Thank you for your payment. Your song has been added to the queue.</p>
    <p><a href="<?php echo home_url('/username/songque'); ?>">View Your Song Queue</a></p>
</div>

<?php
get_footer();
?>
