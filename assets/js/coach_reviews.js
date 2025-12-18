document.addEventListener("DOMContentLoaded", function () {
  // Initial animations if any
  const bars = document.querySelectorAll(".progress-bar-fill");
  bars.forEach((bar) => {
    const width = bar.style.width;
    bar.style.width = "0";
    setTimeout(() => {
      bar.style.width = width;
    }, 300);
  });
});

function toggleReply(reviewId) {
  const replyForm = document.getElementById(`reply-form-${reviewId}`);
  if (replyForm) {
    replyForm.classList.toggle("hidden");
  }
}

function submitReply(reviewId) {
  const textarea = document.querySelector(`#reply-form-${reviewId} textarea`);
  const content = textarea.value.trim();

  if (content) {
    alert(`Reply submitted for review ${reviewId}:\n${content}`);

    // Hide form and clear input
    toggleReply(reviewId);
    textarea.value = "";

    // Optimistic update => Show "Replied" badge or similar
    const card = document.getElementById(`review-card-${reviewId}`);
    const badge = document.createElement("div");
    badge.className =
      "mt-3 lg:mt-0 px-3 py-1 bg-blue-500/10 text-blue-400 text-xs rounded-full border border-blue-500/20 inline-block";
    badge.innerHTML = '<i class="fas fa-check mr-1"></i> Replied';

    // Append to actions area
    const actionsDiv = card.querySelector(".actions-area");
    if (actionsDiv) {
      actionsDiv.appendChild(badge);
    }
  } else {
    alert("Please write a reply first.");
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
