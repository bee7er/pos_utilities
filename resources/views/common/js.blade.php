
<script language="javascript" type="application/javascript">

    let MAX_PRODUCT_CODE_LENGTH = 5;

    /**
     * Successful response from the Ajax call
     * @param message
     * @param status
     */
    const displayMessage = function (message, status)
    {
        let apiMessage = $('#apiMessage');
        apiMessage.text(message);
        // Clear previous classes
        apiMessage.removeClass(['alert-success', 'alert-danger']);
        apiMessage.addClass('alert-' + status);
        apiMessage.fadeIn(1000).delay(3000).fadeOut(1000);
    };

    /**
     * Post request to api
     *
     * @param url
     * @param data
     * @param callback
     * @param params - any additional parameters to pass on to the callback
     */
    const ajaxCall = function (url, data, callback)
    {
        $.ajax({
            type: 'post',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            data: data,
            contentType: 'application/json',
            cache: false,
            processData: false,
            success: function (response)
            {
                callback(response);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                // Optionally alert the user of an error here...
                var textResponse = jqXHR.responseText;
                var jsonResponse = jQuery.parseJSON(textResponse);

                alert('An error was detected. Please contact IT support.');
                $.each(jsonResponse, function (n, elem) {
                    console.log(elem);
                });
            }
        });
    };

</script>