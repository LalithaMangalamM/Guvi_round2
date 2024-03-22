$(document).ready(() => {
    // Function to fetch user profile data 
    function fetchProfile() {
        let id = localStorage.getItem('sessionId');
        $.ajax({
            type: "GET",
            url: "/php/profile.php", 
            dataType: 'json',
            headers:{
                'sessionid': id
            },
            success: function (response) {
                console.log(response.username);
                $("#username").val(response.username);
                $("#password").val(response.password);
                $("#age").val(response.age);
                $("#dob").val(response.dob);
                $("#contact").val(response.contact);
                $("#email").val(response.email);
            },
            error: function (xhr, status, error) {
                alert('An error occurred while fetching user profile.');
            }
        });
    }
    fetchProfile();
    $("#editBtn").click(() => {
        $(".form-control").removeAttr("readonly");
        $("#editBtn").text("Save");
        $("#editBtn").removeClass("btn-primary").addClass("btn-warning");
        $("#editBtn").off("click").click(saveProfile);
    });

    // Function to save edited profile data
    function saveProfile() {
        // Disable all form fields for editing
        $(".form-control").attr("readonly", true);

        // Change save button text back to Edit
        $("#editBtn").text("Edit");

        // Change save button class back to btn-primary
        $("#editBtn").removeClass("btn-warning").addClass("btn-primary");
        
        // Change save button click event back to editProfile function
        $("#editBtn").off("click").click(editProfile);

        $.ajax({
            type: "POST",
            url: "php/save_profile.php", 
            data: $("#profileForm").serialize(), 
            success: function (response) {
                alert(response.message); 
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                alert('An error occurred while saving user profile.');
            }
        });
    }

    // Logout button click event handler
    $("#logoutBtn").click(() => {
        // Clear local storage
        localStorage.clear();
        // Redirect to logout page
        window.location.href = "logout.php"; // Replace with your logout page URL
    });
});
