// MODULE 1 - Client-side Validation
const form = document.querySelector("#loginForm");
const emailField = form.querySelector("#email");
const passwordField = form.querySelector("#password");
const roleField = form.querySelector("#role");

form.addEventListener("submit", (e) => {
    e.preventDefault();

    const emailValue = emailField.value.trim();
    const passwordValue = passwordField.value.trim();
    const roleValue = roleField.value.trim();

    let valid = true;

    if (!validateEmail(emailValue)) {
        alert("Please enter a valid email address.");
        valid = false;
    }

    if (!validatePassword(passwordValue)) {
        alert("Password cannot be empty.");
        valid = false;
    }

    if (!validateRole(roleValue)) {
        alert("Please select a valid role.");
        valid = false;
    }

    if (valid) {
        form.submit();
    }
});

function validateEmail(email) {
    const pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    return pattern.test(email);
}

function validatePassword(password) {
    return password !== "";
}

function validateRole(role) {
    return role !== ""; // Ensures a role is selected
}
