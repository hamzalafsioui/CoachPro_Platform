function openClientModal(id) {
  // Not Implement Yet
  alert(`Viewing details for client ID: ${id}`);
}

function messageClient(id) {
  alert(`Opening message thread for client ID: ${id}`);
  // Redirects to a chat page or opens a chat modal, Not Implemented Yet
}

document.addEventListener("DOMContentLoaded", function () {
  // Search
  const searchInput = document.getElementById("clientSearch");
  const clientCards = document.querySelectorAll(".client-card");

  if (searchInput) {
    searchInput.addEventListener("input", function (e) {
      const searchTerm = e.target.value.toLowerCase();

      clientCards.forEach((card) => {
        const name = card.dataset.name.toLowerCase();
        const plan = card.dataset.plan.toLowerCase();

        if (name.includes(searchTerm) || plan.includes(searchTerm)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
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
