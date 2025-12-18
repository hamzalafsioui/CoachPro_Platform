document.addEventListener("DOMContentLoaded", function () {
  const editBtn = document.getElementById("editProfileBtn");
  const saveBtn = document.getElementById("saveProfileBtn");
  const cancelBtn = document.getElementById("cancelProfileBtn");
  const formInputs = document.querySelectorAll(".form-input");
  const actionButtons = document.getElementById("actionButtons");

  // Store original values to restore on cancel
  let originalValues = {};

  editBtn.addEventListener("click", function () {
    // Enable inputs
    formInputs.forEach((input) => {
      if (input.name !== "email") {
        // Optional: Keep email read-only
        originalValues[input.name] = input.value;
        input.disabled = false;
      }
    });

    // Toggle buttons
    editBtn.classList.add("hidden");
    actionButtons.classList.remove("hidden");

    // Focus first input
    formInputs[0].focus();
  });

  cancelBtn.addEventListener("click", function () {
    // Restore values
    formInputs.forEach((input) => {
      if (originalValues[input.name] !== undefined) {
        input.value = originalValues[input.name];
      }
      input.disabled = true;
    });

    // Toggle buttons
    actionButtons.classList.add("hidden");
    editBtn.classList.remove("hidden");
  });

  // Handle form submission (Mock)
  const profileForm = document.getElementById("profileForm");
  profileForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Show loading state or similar feedback here

    // Mock save
    setTimeout(() => {
      alert("Profile updated successfully!");

      // Disable inputs
      formInputs.forEach((input) => {
        input.disabled = true;
      });

      // Toggle buttons
      actionButtons.classList.add("hidden");
      editBtn.classList.remove("hidden");
    }, 500);
  });
});

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");

  if (sidebar.classList.contains("-translate-x-full")) {
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
  } else {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
  }
}
