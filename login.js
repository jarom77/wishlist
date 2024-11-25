document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    // Send the form data via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "auth.php", true);
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
    xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
});

