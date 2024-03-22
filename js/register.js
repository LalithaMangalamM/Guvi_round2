$(document).ready(() => {
    $("#butt").click((event) => {
        event.preventDefault();
        var form = $("#registrationForm").serialize();
        $.ajax({
            type: "POST",
            url: "php/register.php",
            data: form,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    console.log(response)
                    // Registration successful
                    $(".output").text(response.message);
                    alert(response.message);
                    window.location.href = "../login.html";
                } else {
                    // Registration failed
                    $(".output").empty();
                    for (var field in response.errors) {
                        $(".output").append(response.errors[field]);
                    }
                }
            },
            error: function (xhr, status, error) {
                alert('An error occurred while processing the registration.');
            }
        });
    });
});
