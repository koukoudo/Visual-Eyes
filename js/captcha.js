function validateCaptcha() {
    var response = grecaptcha.getResponse();
    $.ajax({
        type: 'POST',
        url: BASE_URL + "/User/captcha",
        data: {response: response},
        dataType: 'json',
        error: function() {
            alert('Could not validate captcha. Please try again.');
            grecaptcha.reset();  
        },
        success: function(data) {  
            //if (data.success) {
                $('.submit-sign').prop('disabled', false);
           /* } else {
                alert('Could not validate captcha. Please try again.');  
                grecaptcha.reset();        
            } */
        }
    }) 
}