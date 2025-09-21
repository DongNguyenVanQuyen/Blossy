function startCountdown(targetDateStr) {
  const countdownEl = document.getElementById("countdown");
  const targetDate = new Date(targetDateStr);

  function updateCountdown() {
    const now = new Date();
    const diff = targetDate - now;

    if (diff <= 0) {
      countdownEl.innerHTML = "<strong>Offer ended</strong>";
      return;
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((diff / 1000 / 60) % 60);
    const seconds = Math.floor((diff / 1000) % 60);

    document.getElementById("days").textContent = String(days).padStart(2, "0");
    document.getElementById("hours").textContent = String(hours).padStart(
      2,
      "0"
    );
    document.getElementById("minutes").textContent = String(minutes).padStart(
      2,
      "0"
    );
    document.getElementById("seconds").textContent = String(seconds).padStart(
      2,
      "0"
    );
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
}

// Bắt đầu đếm ngược đến 7 ngày sau hôm nay
const countdownTarget = new Date();
countdownTarget.setDate(countdownTarget.getDate() + 7);
startCountdown(countdownTarget);

document
  .querySelector(".newsletter-form")
  ?.addEventListener("submit", function (e) {
    e.preventDefault();
    const email = this.querySelector("input").value;
    if (email) {
      alert(`✅ Subscribed with ${email}`);
      this.reset();
    }
  });
