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

async function handleAction(action, id) {
  if (confirm(`Are you sure you want to ${action} this reservation?`)) {
    try {
      const response = await fetch("../../actions/reservations/update.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id: id,
          action: action,
        }),
      });

      const result = await response.json();

      if (result.success) {
        // Visual feedback
        const card = document.querySelector(
          `.reservation-card[data-id="${id}"]`
        );
        if (card && action === "cancel") {
          card.style.opacity = "0.5";
          card.style.pointerEvents = "none";
          card.setAttribute("data-status", "cancelled");
          const badge = card.querySelector(".status-badge");
          badge.className = "status-badge status-cancelled";
          badge.textContent = "Cancelled";

          // Hide cancel button
          const actionArea = card.querySelector(".flex.items-center.gap-2");
          if (actionArea) {
            actionArea.innerHTML = `
                <button class="flex-1 md:flex-none px-4 py-2 bg-gray-800 text-gray-500 rounded-lg text-sm font-medium cursor-not-allowed">
                    Details
                </button>
            `;
          }
        }
        alert(`Reservation ${action}ed successfully!`);
      } else {
        alert("Error: " + result.message);
      }
    } catch (error) {
      console.error("Error updating reservation:", error);
      alert("An unexpected error occurred.");
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
