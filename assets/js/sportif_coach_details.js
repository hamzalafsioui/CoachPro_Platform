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
  let selectedSlotTime = null;
  let selectedSlotId = null;

  timeSlots.forEach((slot) => {
    slot.addEventListener("click", () => {
      if (slot.classList.contains("disabled")) return;

      // Deselect others
      timeSlots.forEach((s) => s.classList.remove("selected"));

      // Select clicked
      slot.classList.add("selected");
      selectedSlotTime = slot.getAttribute("data-time");
      selectedSlotId = slot.getAttribute("data-id");

      // Enable book button
      if (bookBtn) {
        bookBtn.disabled = false;
        bookBtn.classList.remove("opacity-50", "cursor-not-allowed");
        bookBtn.textContent = `Book for ${selectedSlotTime}`;
      }
    });
  });

  // Book Button Handler
  if (bookBtn) {
    bookBtn.addEventListener("click", async (e) => {
      e.preventDefault();
      if (!selectedSlotId) return;

      const urlParams = new URLSearchParams(window.location.search);
      const coachId = urlParams.get("id");

      if (!coachId) {
        alert("Coach ID not found.");
        return;
      }

      bookBtn.disabled = true;
      bookBtn.textContent = "Processing...";

      try {
        const response = await fetch("../../actions/reservations/create.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            coach_id: parseInt(coachId),
            availability_id: parseInt(selectedSlotId),
            price: 50.0, // Hardcoded for now as per dashboard
          }),
        });

        const result = await response.json();

        if (result.success) {
          alert("Reservation successful!");
          window.location.href = "reservations.php";
        } else {
          alert("Error: " + result.message);
          bookBtn.disabled = false;
          bookBtn.textContent = `Book for ${selectedSlotTime}`;
        }
      } catch (error) {
        console.error("Error booking session:", error);
        alert("An unexpected error occurred.");
        bookBtn.disabled = false;
        bookBtn.textContent = `Book for ${selectedSlotTime}`;
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
