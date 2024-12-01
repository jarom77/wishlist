document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    var old_password = document.getElementById('old_password').value;
    var new_password = document.getElementById('new_password').value;
    var confirm_password = document.getElementById('confirm_password').value;

    // Send the form data via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "password.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            
            if (response.success) {
                // If login is successful, redirect to the specified page
                window.location.href = response.redirect;
            } else {
                // Show error message if login fails
                document.getElementById('error-message').style.display = 'block';
            }
        }
    };
    
    // Send the data
    xhr.send("old_password=" + encodeURIComponent(old_password) + "&new_password=" + encodeURIComponent(new_password) + "&confirm_password=" + encodeURIComponent(confirm_password));
});

