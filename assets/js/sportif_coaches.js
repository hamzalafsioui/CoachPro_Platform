document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("coachSearch");
  const specialtyFilter = document.getElementById("specialtyFilter");
  const coachCards = document.querySelectorAll(".coach-card");

  function filterCoaches() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedSpecialty = specialtyFilter.value.toLowerCase();

    coachCards.forEach((card) => {
      const name = card.getAttribute("data-name").toLowerCase();
      const specialties = card.getAttribute("data-specialties").toLowerCase();

      const matchesSearch = name.includes(searchTerm);
      const matchesSpecialty =
        selectedSpecialty === "all" || specialties.includes(selectedSpecialty);

      if (matchesSearch && matchesSpecialty) {
        card.style.display = "block";
        // Add simple entrance animation
        card.style.animation = "fadeIn 0.5s ease forwards";
      } else {
        card.style.display = "none";
      }
    });
  }

  searchInput.addEventListener("input", filterCoaches);
  specialtyFilter.addEventListener("change", filterCoaches);

  // Book button handler (Mock)
  window.handleBooking = function (coachId) {
    // Redirect to the detailed profile page
    window.location.href = "coach_profile.php?id=" + coachId;
  };
});

// Add keyframes for fade animation dynamically
const style = document.createElement("style");
style.innerHTML = `
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
`;
document.head.appendChild(style);

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
