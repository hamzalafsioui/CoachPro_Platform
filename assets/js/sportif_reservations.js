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

let currentReservationId = null;
let selectedRating = 0;

function openReviewModal(reservationId, coachName) {
  currentReservationId = reservationId;
  selectedRating = 0;
  
  console.log('Opening review modal for reservation:', reservationId, 'Coach:', coachName);
  
  document.getElementById('reviewCoachName').textContent = coachName;
  document.getElementById('reviewComment').value = '';
  
  const stars = document.querySelectorAll('#starRating i');
  stars.forEach(star => {
    star.classList.remove('text-yellow-400');
    star.classList.add('text-gray-600');
  });
  
  document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
  document.getElementById('reviewModal').style.display = 'none';
  currentReservationId = null;
  selectedRating = 0;
}

document.addEventListener('DOMContentLoaded', function() {
  console.log('Sportif reservations page loaded');
  
  const stars = document.querySelectorAll('#starRating i');
  console.log('Found', stars.length, 'star elements');
  
  stars.forEach(star => {
    star.addEventListener('click', function() {
      selectedRating = parseInt(this.dataset.rating);
      console.log('Star clicked, rating:', selectedRating);
      
      stars.forEach((s, index) => {
        if (index < selectedRating) {
          s.classList.remove('text-gray-600');
          s.classList.add('text-yellow-400');
        } else {
          s.classList.remove('text-yellow-400');
          s.classList.add('text-gray-600');
        }
      });
    });
    
    star.addEventListener('mouseenter', function() {
      const rating = parseInt(this.dataset.rating);
      stars.forEach((s, index) => {
        if (index < rating) {
          s.classList.add('text-yellow-400');
        }
      });
    });
    
    star.addEventListener('mouseleave', function() {
      stars.forEach((s, index) => {
        if (index < selectedRating) {
          s.classList.add('text-yellow-400');
          s.classList.remove('text-gray-600');
        } else {
          s.classList.remove('text-yellow-400');
          s.classList.add('text-gray-600');
        }
      });
    });
  });
});

async function submitReview() {
  const comment = document.getElementById('reviewComment').value.trim();
  
  if (selectedRating === 0) {
    alert('Please select a rating');
    return;
  }
  
  if (comment === '') {
    alert('Please write a review comment');
    return;
  }
  
  try {
    const response = await fetch('../../actions/reviews/create.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        reservation_id: currentReservationId,
        rating: selectedRating,
        comment: comment
      })
    });
    
    const result = await response.json();
    console.log('Review submission result:', result);
    
    if (result.success) {
      alert(result.message);
      closeReviewModal();
      location.reload();
    } else {
      console.error('Review submission failed:', result);
      alert('Error: ' + result.message + (result.debug ? '\n\nDebug info in console' : ''));
    }
  } catch (error) {
    console.error('Error submitting review:', error);
    alert('An unexpected error occurred while submitting your review. Check console for details.');
  }
}

