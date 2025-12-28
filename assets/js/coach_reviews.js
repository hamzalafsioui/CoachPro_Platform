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

async function submitReply(reviewId) {
  const textarea = document.querySelector(`#reply-textarea-${reviewId}`);
  const content = textarea.value.trim();

  if (!content) {
    alert("Please write a reply first.");
    return;
  }

  // Disable button during submission
  const submitBtn = document.querySelector(`#reply-form-${reviewId} button[onclick*="submitReply"]`);
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = "Sending...";

  try {
    const response = await fetch("../../actions/reviews/reply.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        review_id: reviewId,
        reply_text: content,
      }),
    });

    const data = await response.json();

    if (data.success) {
      // Hide form
      toggleReply(reviewId);

      // Reload page to show the reply
      location.reload();
    } else {
      alert(data.message || "Failed to submit reply. Please try again.");
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  } catch (error) {
    console.error("Error submitting reply:", error);
    alert("An error occurred. Please try again.");
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
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
