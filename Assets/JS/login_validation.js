document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");

    function validateEmail() {
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!emailInput.value.match(emailPattern)) {
            emailError.innerText = "⚠️ Enter a valid email address!";
            return false;
        } else {
            emailError.innerText = "";
            return true;
        }
    }

    function validatePassword() {
        if (passwordInput.value.length < 6) {
            passwordError.innerText = "⚠️ Password must be at least 6 characters!";
            return false;
        } else {
            passwordError.innerText = "";
            return true;
        }
    }

    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", validatePassword);

    form.addEventListener("submit", function (event) {
        if (!validateEmail() || !validatePassword()) {
            alert("Please fill out all fields correctly before submitting.");
            event.preventDefault();
        }
    });
});
