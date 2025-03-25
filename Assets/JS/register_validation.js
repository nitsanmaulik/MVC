document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registerEmployeeForm");
    const registrationbutton = document.getElementById('registrationbutton');


    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phone");
    const qualificationInput = document.getElementById("qualification");
    const passwordInput = document.getElementById("password");
    const photoInput = document.getElementById("photo");

    function validateField(input, errorId, regex, errorMessage) {
        const value = input.value.trim();
        if (!regex.test(value)) {
            document.getElementById(errorId).innerText = errorMessage;
            return false;
        } else {
            document.getElementById(errorId).innerText = "";
            return true;
        }
    }

    function validateName() {
        return validateField(nameInput, "nameError", /^[A-Za-z\s]{3,}$/, "Enter a valid name (letters only, min 3 characters).");
    }

    function validateEmail() {
        return validateField(emailInput, "emailError", /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, "Enter a valid email.");
    }

    function validatePhone() {
        return validateField(phoneInput, "phoneError", /^[0-9]{10}$/, "Enter a valid 10-digit phone number.");
    }

    function validateQualification() {
        return validateField(qualificationInput, "qualificationError", /^[A-Za-z\s]{2,}$/, "Enter a valid qualification (letters only).");
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

    nameInput.addEventListener("input", validateName);
    emailInput.addEventListener("input", validateEmail);
    phoneInput.addEventListener("input", validatePhone);
    qualificationInput.addEventListener("input", validateQualification);
    passwordInput.addEventListener("input", validatePassword);

    

    form.addEventListener("submit", function (event) {
        if (!validateName() || !validateEmail() || !validatePassword() || !validatePhone() || !validateQualification()) {
            alert("Please fill out all fields correctly before submitting.");
            event.preventDefault(); // Prevent form submission
        } else {
            alert("Congratulations! Your registration was successful.");
        }
    });
});
