jQuery(document).ready(function ($) {
    $('.wflr-submit').on('click', function (e) {
        e.preventDefault();
        var self = $(this);
        var msg = $('.wflr-message');
        var form = $('.wflr-form');
        var text_success = $('.wflr-form-success');

        self.hide();
        $('.wflr-loader').show();
        msg.text('');
        msg.hide();


        $.post(wflr_ajaxurl, {
            action: 'wflr_redeem',
            code: $("#wflr_form input[name='wflr_code']").val(),
            firstname: $("#wflr_form input[name='wflr_firstname']").val(),
            lastname: $("#wflr_form input[name='wflr_lastname']").val(),
            email: $("#wflr_form input[name='wflr_email']").val(),
            agree_email: $("#wflr_form input[name='wflr_agree_email']").val(),
            plugin_id: $("#wflr_form input[name='wflr_plugin_id']").val(),
            nonce: wflr_nonce
        }, function (response) {
            if (response && response.success) {
                form.hide();
                text_success.show();
            } else {
                console.log(response.data);
                msg.text(response.data);
                msg.show();
            }

            self.show();
            $('.wflr-loader').hide();

        });
    })

});