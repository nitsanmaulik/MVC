document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");

    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const phoneInput = document.getElementById("phone");
    const qualificationInput = document.getElementById("qualification");

    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");
    const phoneError = document.getElementById("phoneError");
    const qualificationError = document.getElementById("qualificationError");

    function validateName() {
        if (nameInput.value.trim() === "") {
            nameError.innerText = "Name is required.";
            return false;
        }
        const namePattern = /^[A-Za-z\s]{3,}$/;
        if (!namePattern.test(nameInput.value.trim())) {
            nameError.innerText = "Name must contain only letters and at least 3 characters.";
            return false;
        }
        nameError.innerText = "";
        return true;
    }

    function validateEmail() {
        if (emailInput.value.trim() === "") {
            emailError.innerText = "Email is required.";
            return false;
        }
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(emailInput.value.trim())) {
            emailError.innerText = "Enter a valid email format (e.g., user@example.com).";
            return false;
        }
        emailError.innerText = "";
        return true;
    }

    function validatePassword() {
        if (passwordInput.value === "") {
            passwordError.innerText = "Password is required.";
            return false;
        }
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!passwordPattern.test(passwordInput.value)) {
            passwordError.innerText = "Password must be at least 8 characters with uppercase, lowercase, number, and special character.";
            return false;
        }
        passwordError.innerText = "";
        return true;
    }

    function validatePhone() {
        if (phoneInput.value.trim() === "") {
            phoneError.innerText = "Phone number is required.";
            return false;
        }
        const phonePattern = /^[0-9]{10}$/;
        if (!phonePattern.test(phoneInput.value.trim())) {
            phoneError.innerText = "Enter a valid 10-digit phone number.";
            return false;
        }
        phoneError.innerText = "";
        return true;
    }

    function validateQualification() {
        if (qualificationInput.value.trim() === "") {
            qualificationError.innerText = "Qualification is required.";
            return false;
        }
        const qualificationPattern = /^[A-Za-z\s]{2,}$/;
        if (!qualificationPattern.test(qualificationInput.value.trim())) {
            qualificationError.innerText = "Enter a valid qualification (letters only, min 2 characters).";
            return false;
        }
        qualificationError.innerText = "";
        return true;
    }

    // Add event listeners to trigger validation on input
    nameInput.addEventListener("input", validateName);
    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", validatePassword);
    phoneInput.addEventListener("input", validatePhone);
    qualificationInput.addEventListener("input", validateQualification);

    // Form submit event
    form.addEventListener("submit", function (event) {
        if (!validateName() || !validateEmail() || !validatePassword() || !validatePhone() || !validateQualification()) {
            alert("Please fill out all fields correctly before submitting.");
            event.preventDefault(); // Prevent form submission
        } else {
            alert("Congratulations! Your registration was successful.");
        }
    });
});
