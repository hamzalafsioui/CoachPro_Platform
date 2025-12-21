document.addEventListener("DOMContentLoaded", function () {
  const days = [
    "monday",
    "tuesday",
    "wednesday",
    "thursday",
    "friday",
    "saturday",
    "sunday",
  ];

  days.forEach((day) => {
    const toggle = document.getElementById(`${day}-toggle`);
    const slotsContainer = document.getElementById(`${day}-slots`);
    const addBtn = document.getElementById(`${day}-add-btn`);

    if (toggle) {
      toggle.addEventListener("change", function () {
        if (this.checked) {
          slotsContainer.classList.remove("opacity-50", "pointer-events-none");
          addBtn.disabled = false;
          addBtn.classList.remove("opacity-50", "cursor-not-allowed");
        } else {
          slotsContainer.classList.add("opacity-50", "pointer-events-none");
          addBtn.disabled = true;
          addBtn.classList.add("opacity-50", "cursor-not-allowed");
        }
      });
    }

    if (addBtn) {
      addBtn.addEventListener("click", function () {
        addTimeSlot(day);
      });
    }
  });
});

function addTimeSlot(day) {
  const container = document.getElementById(`${day}-slots-container`);
  const newSlot = document.createElement("div");
  newSlot.className = "flex items-center gap-2 mb-2 time-slot animate-fade-in";
  newSlot.innerHTML = `
        <input type="time" name="${day}_start[]" class="time-input rounded-lg px-3 py-2 text-sm w-32">
        <span class="text-gray-500">-</span>
        <input type="time" name="${day}_end[]" class="time-input rounded-lg px-3 py-2 text-sm w-32">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 p-2 transition-colors">
            <i class="fas fa-trash-alt"></i>
        </button>
    `;
  container.appendChild(newSlot);
}

// Save availability
async function saveAvailability() {
  const days = [
    "monday",
    "tuesday",
    "wednesday",
    "thursday",
    "friday",
    "saturday",
    "sunday",
  ];
  const schedule = {};

  try {
    days.forEach((day) => {
      const toggle = document.getElementById(`${day}-toggle`);
      const container = document.getElementById(`${day}-slots-container`);
      const slots = [];

      if (toggle && toggle.checked && container) {
        const timeSlots = container.querySelectorAll(".time-slot");
        timeSlots.forEach((slot) => {
          const startInput = slot.querySelector(`input[name="${day}_start[]"]`);
          const endInput = slot.querySelector(`input[name="${day}_end[]"]`);

          if (startInput && endInput) {
            const start = startInput.value;
            const end = endInput.value;
            if (start && end) {
              slots.push([start, end]);
            }
          }
        });
      }

      schedule[day] = {
        active: toggle ? toggle.checked : false,
        slots: slots,
      };
    });

    // UI state - be more robust in finding the button
    const saveBtn =
      document.querySelector(".fa-save")?.closest("button") ||
      document.querySelector("button[onclick*='saveAvailability']");

    let originalText = "Save Changes";
    if (saveBtn) {
      originalText = saveBtn.innerHTML;
      saveBtn.disabled = true;
      saveBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>';
    }

    const response = await fetch("../../actions/availability/save.action.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ schedule: schedule }),
    });

    if (!response.ok) {
      throw new Error(`Server responded with status ${response.status}`);
    }

    const result = await response.json();

    if (result.success) {
      alert("Availability settings saved successfully!");
    } else {
      alert("Error: " + result.message);
    }

    if (saveBtn) {
      saveBtn.disabled = false;
      saveBtn.innerHTML = originalText;
    }
  } catch (error) {
    console.error("Error saving availability:", error);
    alert("An unexpected error occurred: " + error.message);
    const saveBtn =
      document.querySelector(".fa-save")?.closest("button") ||
      document.querySelector("button[onclick*='saveAvailability']");
    if (saveBtn) {
      saveBtn.disabled = false;
      saveBtn.innerHTML =
        '<i class="fas fa-save"></i> <span>Save Changes</span>';
    }
  }
}

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
