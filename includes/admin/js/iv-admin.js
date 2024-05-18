jQuery(document).ready(function($) {
    $('#apply-verification-status').on('click', function() {
        var status = $('#bulk_verification_status').val();
        if (!status) {
            alert(ivAdmin.status_missing);
            return;
        }

        var user_ids = [];
        $('tbody .check-column input[type="checkbox"]:checked').each(function() {
            user_ids.push($(this).val());
        });

        if (user_ids.length === 0) {
            alert(ivAdmin.select_users);
            return;
        }

        $.ajax({
            url: ivAdmin.ajax_url,
            method: 'POST',
            data: {
                action: 'iv_bulk_update_verification_status',
                user_ids: user_ids,
                status: status,
                nonce: ivAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });
});
