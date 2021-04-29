<?php
/** @var array $args */
?>
<style>
    .wflr-settings input[type="text"]{
        width: 300px;
    }
</style>
<div class="wrap">
    <div class="wflr-settings">
        <h2><?php echo get_admin_page_title() ?></h2>
        <form action="<?= admin_url( 'options.php' ) ?>" method="POST">
			<?php
			settings_fields( WFLR_PLUGIN_PREFIX . '_settings_group' );
			do_settings_sections( WFLR_PLUGIN_PREFIX . '_settings_page' );
			submit_button();
			?>
        </form>
        Shortcode:
        <pre>[wflr_form plugin_id='1234']</pre>
    </div>
</div>
