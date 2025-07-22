// student.js

function validateSignup() {
    const id = document.getElementById('id').value;
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const course = document.getElementById('course').value;
    const password = document.getElementById('password').value;

    if (!id || !name || !email || !course || !password) {
        document.getElementById('error-msg').innerText = 'Please fill all fields.';
        return false;
    }

    // Create FormData object
    const formData = new FormData(document.getElementById('signupForm'));

    // Submit form data via AJAX
    fetch('signup.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Signup successful!');
                // Optionally, redirect to login page or another page
                window.location.href = 'login.html';
            } else {
                document.getElementById('error-msg').innerText = data.error;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('error-msg').innerText = 'An error occurred.';
        });

    return false; // Prevent form submission
}
