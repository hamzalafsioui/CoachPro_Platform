document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".tab-btn");
  const sections = document.querySelectorAll(".content-section");
  const timeSlots = document.querySelectorAll(".time-slot");
  const bookBtn = document.getElementById("bookSessionBtn");

  // Tab Switching
  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const target = tab.getAttribute("data-target");

      // Update tabs
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      // Update sections
      sections.forEach((s) => s.classList.add("hidden"));
      document.getElementById(target).classList.remove("hidden");
    });
  });

  // Time Slot Selection
  let selectedSlot = null;

  timeSlots.forEach((slot) => {
    slot.addEventListener("click", () => {
      if (slot.classList.contains("disabled")) return;

      // Deselect others
      timeSlots.forEach((s) => s.classList.remove("selected"));

      // Select clicked
      slot.classList.add("selected");
      selectedSlot = slot.getAttribute("data-time");

      // Enable book button
      if (bookBtn) {
        bookBtn.disabled = false;
        bookBtn.classList.remove("opacity-50", "cursor-not-allowed");
        bookBtn.textContent = `Book for ${selectedSlot}`;
      }
    });
  });

  // Book Button Handler
  if (bookBtn) {
    bookBtn.addEventListener("click", () => {
      if (!selectedSlot) return;

      // Redirect to reservation flow
      alert(`Proceeding to book session at ${selectedSlot}`);
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
