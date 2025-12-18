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
function saveAvailability() {
  // collect data and send to server Not Implemented Yet

  alert("Availability settings saved!");
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
