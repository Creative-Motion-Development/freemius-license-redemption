<?php
/** @var array $args */
$plugin_id = $args['plugin_id'] ?? 0;
?>
<script>
    var wflr_ajaxurl = '<?= admin_url( 'admin-ajax.php' );?>';
    var wflr_nonce = '<?= wp_create_nonce( 'wflr' );?>';
</script>
<script src="<?php echo WFLR_PLUGIN_URL . "/assets/wflr_shortcode.js"; ?>" type="application/javascript"></script>

<style>@import url("<?php echo WFLR_PLUGIN_URL . "/assets/wflr_style.css"; ?>");</style>

<div class="wflr-container">
    <div class="wflr-form-success" style="display: none;">
        <h1>Congratulations!</h1>
        <p>The license key was successfully redeemed.</p>
    </div>
    <div class="wflr-form">
        <p>Please fill up the details below to redeem your AppSumo Code.</p>
        <form name="wflr_form" id="wflr_form">
            <label>AppSumo Code <span class="field-required">*</span>
                <input type="text" name="wflr_code"
                       placeholder="Enter redemption code here"
                       required/></label>
            <div class="form-columns">
                <label>First Name <span class="field-required">*</span>
                    <input type="text" name="wflr_firstname"
                           placeholder="Benjamin" required/></label>
                <label>Last Name <span class="field-required">*</span>
                    <input type="text" name="wflr_lastname"
                           placeholder="Intal" required/></label>
            </div>
            <label>Email Address <span class="field-required">*</span>
                <input type="email" name="wflr_email"
                       placeholder="your@email.com"
                       required/></label>
            <label class="checkbox">
                <input type="checkbox" name="wflr_agree_email"> I want to receive emails about security updates, new
                features and offers</label>
            <input type="hidden" name="wflr_plugin_id" value="<?= $plugin_id; ?>"/>
            <button class="wflr-submit"><span>Redeem</span></button>
            <div class="wflr-message" style="display: none;"></div>

            <div class="wflr-loader" style="display: none;">&nbsp;</div>
        </form>
    </div>
</div>
