document.addEventListener("DOMContentLoaded", function () {
  // Filter
  const filterBtns = document.querySelectorAll(".filter-btn");
  const reservations = document.querySelectorAll(".reservation-card");

  filterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      // Update active state
      filterBtns.forEach((b) =>
        b.classList.remove("active", "bg-blue-600", "text-white")
      );
      filterBtns.forEach((b) =>
        b.classList.add("bg-gray-800", "text-gray-400")
      );

      btn.classList.remove("bg-gray-800", "text-gray-400");
      btn.classList.add("active", "bg-blue-600", "text-white");

      const filter = btn.dataset.filter;

      // Filter items
      reservations.forEach((card) => {
        if (filter === "all" || card.dataset.status === filter) {
          card.style.display = "block";
          card.classList.add("animate-fade-in");
        } else {
          card.style.display = "none";
          card.classList.remove("animate-fade-in");
        }
      });
    });
  });
});

function handleAction(action, id) {
  if (confirm(`Are you sure you want to ${action} this reservation?`)) {
    // AJAX Not Implemented Yet

    // Visual feedback
    const card = document.querySelector(`.reservation-card[data-id="${id}"]`);
    if (card && action === "cancel") {
      card.style.opacity = "0.5";
      card.style.pointerEvents = "none";
      const badge = card.querySelector(".status-badge");
      badge.className = "status-badge status-cancelled";
      badge.textContent = "Cancelled";
    }

    alert(`Reservation ${action}led successfully!`);
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
