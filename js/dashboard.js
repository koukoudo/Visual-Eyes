var emailCur;
var firstNameCur;
var lastNameCur;
var edit = false;

$(document).ready(() => {
    $('#btn-update-info').click(() => {
        var firstName = $('#dash-first-name').val();
        var lastName = $('#dash-last-name').val();
        var email = $('#dash-email').val();
        if (edit == false || (firstName == firstNameCur && lastName == lastNameCur && email == emailCur)) {
            $('#dash-profile input').each(function() {
                $(this).removeClass('edit');
                $(this).attr('disabled', true);
            }) 
        } else {
            $.ajax({
                type: 'POST',
                url: BASE_URL + "/User/updateUserInfo",
                data: {firstName: firstName, lastName: lastName, email: email},
                dataType: 'json',
                error: function() {
                    $('#dash-profile').append('<p class="update-msg error">Emaill address already exists. Please try again.</p>');
                    $('#dash-first-name').val(firstNameCur);
                    $('#dash-last-name').val(lastNameCur);
                    $('#dash-email').val(emailCur); 
                },
                success: function(data) {
                    if (data.emailUpdated) {
                        $('#dash-profile').append('<p class="update-msg success">User profile successfully updated. Please check your email inbox for a verification link.</p>');
                        $('#small-verified').removeClass('verified');
                        $('#small-verified').addClass('not-verified');
                        $('#small-verified').html('not verified');
                    } else {
                        $('#dash-profile').append('<p class="update-msg success">User profile successfully updated.</p>');
                    }
                    $('#dash-first-name').val(data.firstname);
                    $('#dash-last-name').val(data.lastname);
                    $('#dash-email').val(data.email); 
                },
                complete: function() {
                    $('#dash-profile input').each(function() {
                        $(this).removeClass('edit');
                        $(this).attr('disabled', true);
                    }) 
                    setTimeout(function(){
                        $('.update-msg').remove();
                    }, 4000);  
                }
            })
        }
    })

    $('#btn-dash-edit').click(() => {
        edit = true;
        emailCur = $('#dash-email').val();
        firstNameCur = $('#dash-first-name').val();
        lastNameCur = $('#dash-last-name').val();
        $('#dash-profile input').each(function() {
            $(this).toggleClass('edit');
            if ($(this).attr('disabled')) {
                $(this).attr('disabled', false);
            } else {
                $(this).attr('disabled', true);
            }
        })
    })

    $(document).on('click', '.rec-dataset', (e) => {
        var title = e.target.textContent;
        console.log(title);
        $.post(BASE_URL + "/Visualize/selectData", {title: title});
        $(location).attr('href', BASE_URL + "/Visualize/visualizeView"); 
    })

    $(document).on('click', '.img-history', (e) => {
        var src = e.target.getAttribute('src');
        $('#img-dash-popup').attr('src', src);
        var alt = e.target.getAttribute('alt');
        $('#img-dash-popup').attr('alt', alt);
        $('#dash-overlay').show();
    })

    $(document).on('click', '#btn-dash-download', () => {
        if ($('#chart-download-link').length == 0) {
            var src = $('#img-dash-popup').attr('src');
            var title = $('#img-dash-popup').attr('alt');
            var a = $('<a>')
            .attr('href', src)
            .attr('download', title + '.png')
            .appendTo('#dash-chart-popup');
        }

        a[0].click();
        a.remove();
    }) 

    $(document).mouseup((e) => {
        var container = $('#dash-chart-popup');

        if (!container.is(e.target) && container.has(e.target).length === 0) 
        {
            $('#dash-overlay').css('display', 'none');
        }
    })

    $('.logo').click(() => {
        $(location).attr('href', BASE_URL + "/User/index"); 
    })
})