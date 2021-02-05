$(document).ready(function() {
    $('#forgot-password').click(() => {
        var email = prompt("Please enter your email");
        $.ajax({
            type: 'POST',
            url: BASE_URL + "/User/forgotPassword",
            data: {email: email},
            dataType: 'json',
            error: function() {
                alert('Invalid email');
            },
            success: function(data) {
                alert('A password reset link was sent to ' + email);
            }
        });
    });
});