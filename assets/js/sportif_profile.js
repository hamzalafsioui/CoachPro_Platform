document.addEventListener("DOMContentLoaded", function () {
  const editBtn = document.getElementById("editProfileBtn");
  const saveBtn = document.getElementById("saveProfileBtn");
  const cancelBtn = document.getElementById("cancelProfileBtn");
  const formInputs = document.querySelectorAll(".form-input");
  const actionButtons = document.getElementById("actionButtons");

  // Store original values to restore on cancel
  let originalValues = {};

  editBtn.addEventListener("click", function () {
    
    formInputs.forEach((input) => {
      if (input.name !== "email") {
        
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

  // Handle form submission (AJAX)
  const profileForm = document.getElementById("profileForm");
  profileForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(profileForm);
    const saveBtn = document.getElementById("saveProfileBtn");
    const originalText = saveBtn.innerHTML;

    try {
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

      const response = await fetch("../../actions/sportif/update_profile.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        alert("Profile updated successfully!");

        // Update the display name in the header area
        const headerName = document.querySelector(".text-3xl.font-outfit");
        if (headerName) {
          headerName.textContent =
            formData.get("firstname") + " " + formData.get("lastname");
        }

        // Disable inputs
        formInputs.forEach((input) => {
          input.disabled = true;
        });

        // Toggle buttons
        actionButtons.classList.add("hidden");
        editBtn.classList.remove("hidden");
      } else {
        alert("Error: " + result.message);
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An unexpected error occurred.");
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = originalText;
    }
  });

  // Handle Account Deletion
  const deleteBtn = document.querySelector(".bg-red-500\\/10");
  if (deleteBtn) {
    deleteBtn.addEventListener("click", async function () {
      if (
        confirm(
          "Are you SURE you want to delete your account? This action cannot be undone and all your reservations will be cancelled."
        )
      ) {
        try {
          deleteBtn.disabled = true;
          deleteBtn.textContent = "Deleting...";

          const response = await fetch(
            "../../actions/sportif/delete_account.php",
            {
              method: "POST",
            }
          );

          const result = await response.json();

          if (result.success) {
            alert("Account deleted. We're sorry to see you go!");
            window.location.href = "../../index.php";
          } else {
            alert("Error: " + result.message);
            deleteBtn.disabled = false;
            deleteBtn.textContent = "Delete Account";
          }
        } catch (error) {
          console.error("Error:", error);
          alert("An unexpected error occurred.");
          deleteBtn.disabled = false;
          deleteBtn.textContent = "Delete Account";
        }
      }
    });
  }
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
