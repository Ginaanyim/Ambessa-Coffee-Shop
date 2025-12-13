document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);

  //Olika alerts för olika actions
  if (params.get("success") === "registered") {
    alert("Registration successful! You can now log in.");
  } else if (params.get("error") === "email_exists") {
    alert("The email address is already registered. Please log in.");
  } else if (params.get("error") === "password_mismatch") {
    alert("Passwords do not match. Please try again.");
  } else if (params.get("error") === "password") {
    alert("Incorrect password. Please try again.");
  } else if (params.get("error") === "email") {
    alert("The email address is not registered.");
  } else if (params.get("error") === "subscribed") {
    alert("This email is already subscribed.");
  } else if (params.get("error") === "invalid_email") {
    alert("Invalid email address.");
  } else if (params.get("success") === "1") {
    alert("E-mail sent!");
  } else if (params.get("error") === "1") {
    alert("Something went wrong.");
  }

  //Lösenordskontroll för formuläret för registreringsf
  const form = document.getElementById('register-form');
  if (form) {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    form.addEventListener('submit', function(event) {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;

      if (password.length < 6) {
        alert('Password must be at least 6 characters long.');
        event.preventDefault();
        return;
      }
      if (password !== confirmPassword) {
        alert('Passwords do not match. Please try again.');
        event.preventDefault();
        return;
      }
    });
  }
});