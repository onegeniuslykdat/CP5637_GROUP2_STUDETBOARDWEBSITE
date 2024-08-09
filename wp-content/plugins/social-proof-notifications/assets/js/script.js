jQuery(document).ready(function($) {
    function fetchNotifications() {
        $.ajax({
            url: spn_ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'fetch_notifications'
            },
            success: function(response) {
                if (response.success) {
                    displayNotification(response.data);
                }
            }
        });
    }

    function displayNotification(notification) {
        var notificationElement = $('<div class="spn-notification">' + notification.message + '</div>');
        $('body').append(notificationElement);
        setTimeout(function() {
            notificationElement.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    fetchNotifications();
    setInterval(fetchNotifications, 10000);
});
