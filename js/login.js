$(document).ready(() => {
    $("#button").click((event) => {
        event.preventDefault();
        //fetching form data
        var form = $("#loginForm").serialize();
        $.ajax({
            type: 'POST',
            url: '../php/login.php',
            data: form,
            success: function (response) {
                //successful login
                if (response.success) {
                    localStorage.setItem("sessionId", response.sessionId);
                    var sessionId = localStorage.getItem('sessionId');
                    //authenticating to profile page
                    window.location.href = "./profile.html";
                    // Check if the session ID exists
                    if (!sessionId) {
                        console.log('Session ID not found in local storage');
                    }
                }
            },
            error: function (xhr, status, error) {
                // Handle error response from PHP
                console.error(xhr.responseText);
                alert('An error occurred while logging in.');
            }
        });
    })
})