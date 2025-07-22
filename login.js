// login.js
document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector(".container");
    const registerButton = document.getElementById("register");
    const loginButton = document.getElementById("login");

    // When the "Sign Up" button is clicked, add the "active" class to the container
    registerButton.addEventListener("click", () => {
        container.classList.add("active");
    });

    // When the "Sign In" button is clicked, remove the "active" class from the container
    loginButton.addEventListener("click", () => {
        container.classList.remove("active");
    });

    // Optional: Prevent default form submission (if you want to handle with JS)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            // event.preventDefault(); // Uncomment this if needed
            // Additional JavaScript code...
        });
    });
});
