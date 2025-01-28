jQuery(document).ready(function($) {
    // Show modal when the page loads
    $('#sms-modal').fadeIn();

    // Handle form submission
    $('#sms-form').submit(function(event) {
        event.preventDefault();

        var name = $('input[name="name"]').val();
        var phone = $('input[name="phone"]').val();

        // Send data via AJAX to WordPress
        $.post({
            url: ajaxurl, 
            data: {
                action: 'sms_form_submission',
                name: name,
                phone: phone
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    alert('Thank you for your submission!');
                    $('#sms-modal').fadeOut();
                } else {
                    alert('Error submitting form. Please try again.');
                }
            }
        });
    });
});
