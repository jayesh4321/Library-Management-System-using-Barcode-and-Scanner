// Add event listener to the form submit
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting and reloading the page

    // Logging to check if the form is submitted
    console.log('Form submitted');

    // Get the username and password from the form inputs
    const username = document.querySelector('.sign-in input[name="username"]').value;
    const password = document.querySelector('.sign-in input[name="password"]').value;

    // Logging for debugging
    console.log('Username:', username);
    console.log('Password:', password);

    // Fixed admin username and password (you can change these)
    const fixedUsername = 'admin';
    const fixedPassword = 'password'; // Set the fixed password here

    // Check if the entered credentials match the fixed username and password
    if (username === fixedUsername && password === fixedPassword) {
        console.log('Credentials match! Redirecting...'); // Logging

        // Redirect to the index.html page
        window.location.href = 'index.php'; // You can also use window.location.assign('index.html');

        // Force redirect after a delay in case the first one doesn't work
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 100); // Delay of 100ms for backup redirection
    } else {
        console.log('Invalid credentials'); // Logging for invalid credentials

        // Display an error message if login fails
        document.getElementById('error-msg').innerText = 'Invalid username or password.';
    }
});

// Add event listener for register button click to add "active" class
const registerBtn = document.getElementById('register');
if (registerBtn) {
    registerBtn.addEventListener('click', () => {
        document.getElementById('container').classList.add("active");
    });
}

// Add event listener for login button click to remove "active" class
const loginBtn = document.getElementById('login');
if (loginBtn) {
    loginBtn.addEventListener('click', () => {
        document.getElementById('container').classList.remove("active");
    });
}
