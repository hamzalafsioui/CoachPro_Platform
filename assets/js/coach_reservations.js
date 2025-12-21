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
          // simple animation
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
  const confirmMsg =
    action === "delete"
      ? "Are you sure you want to permanently delete this reservation?"
      : `Are you sure you want to ${action} this reservation?`;

  if (confirm(confirmMsg)) {
    const card = document.querySelector(`.reservation-card[data-id="${id}"]`);

    // Choose endpoint based on action
    let endpoint = "../../actions/reservations/update.php";
    let body = { id: id, action: action };

    if (action === "delete") {
      endpoint = "../../actions/reservations/delete.php";
      body = { id: id };
    }

    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(body),
      });

      const result = await response.json();

      if (result.success) {
        if (action === "decline" || action === "cancel") {
          if (card) {
            const badge = card.querySelector(".status-badge");
            if (badge) {
              badge.className = "status-badge status-cancelled";
              badge.textContent = "Cancelled";
            }
            
          }
        } else if (action === "accept") {
          if (card) {
            const badge = card.querySelector(".status-badge");
            if (badge) {
              badge.className = "status-badge status-confirmed";
              badge.textContent = "Confirmed";
            }
            const actionsDiv = card.querySelector(".flex.items-center.gap-2");
            if (actionsDiv) {
              actionsDiv.innerHTML = `
                            <button onclick="handleAction('cancel', ${id})" class="flex-1 md:flex-none px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm font-medium transition-colors">
                                Cancel
                            </button>
                         `;
            }
          }
        } else if (action === "delete") {
          if (card) card.remove();
        }

       
      } else {
        alert("Error: " + result.message);
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred while processing the request.");
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
