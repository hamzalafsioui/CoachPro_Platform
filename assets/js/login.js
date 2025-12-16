// Toggle Password Visibility
function togglePassword() {
  const passwordField = document.getElementById("password");
  const icon = document.getElementById("password-icon");

  if (passwordField.type === "password") {
    passwordField.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    passwordField.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}

// Form Validation
document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");

  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      let isValid = true;

      // Clear previous errors
      document.querySelectorAll(".error-message").forEach((el) => {
        el.classList.add("hidden");
        el.textContent = "";
      });

      // Validate email
      const email = document.getElementById("email");
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value.trim()) {
        showError("email", "Email is required");
        isValid = false;
      } else if (!emailRegex.test(email.value)) {
        showError("email", "Please enter a valid email address");
        isValid = false;
      }

      // Validate password
      const password = document.getElementById("password");
      if (!password.value.trim()) {
        showError("password", "Password is required");
        isValid = false;
      } else if (password.value.length < 6) {
        showError("password", "Password must be at least 6 characters");
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  }
});

// Helper function to show error messages
function showError(fieldId, message) {
  const errorEl = document.getElementById(fieldId + "-error");
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.classList.remove("hidden");

    // Add visual feedback to the input field
    const inputField = document.getElementById(fieldId);
    if (inputField) {
      inputField.classList.add("border-red-500");
      inputField.classList.add("focus:border-red-500");

      // Remove error styling when user starts typing
      inputField.addEventListener(
        "input",
        function () {
          this.classList.remove("border-red-500");
          this.classList.remove("focus:border-red-500");
          errorEl.classList.add("hidden");
        },
        { once: true }
      );
    }
  }
}

// Add input animation effects
document.addEventListener("DOMContentLoaded", function () {
  const inputs = document.querySelectorAll(".form-input");

  inputs.forEach((input) => {
    // Add focus animations
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("scale-[1.02]");
    });

    input.addEventListener("blur", function () {
      this.parentElement.classList.remove("scale-[1.02]");
    });
  });

  // Remember me checkbox animation
  const rememberCheckbox = document.getElementById("remember");
  if (rememberCheckbox) {
    rememberCheckbox.addEventListener("change", function () {
      if (this.checked) {
        this.parentElement.classList.add("scale-105");
        setTimeout(() => {
          this.parentElement.classList.remove("scale-105");
        }, 200);
      }
    });
  }
});

// Loading state for submit button
document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const submitButton = loginForm?.querySelector('button[type="submit"]');

  if (loginForm && submitButton) {
    loginForm.addEventListener("submit", function (e) {
      // Only show loading if validation passes
      const isValid = loginForm.checkValidity();

      if (isValid) {
        submitButton.disabled = true;
        const originalHTML = submitButton.innerHTML;
        submitButton.innerHTML = `
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Signing in...</span>
                `;

        // Re-enable after 5 seconds as failsafe
        setTimeout(() => {
          submitButton.disabled = false;
          submitButton.innerHTML = originalHTML;
        }, 5000);
      }
    });
  }
});
