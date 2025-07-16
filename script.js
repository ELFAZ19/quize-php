document.addEventListener("DOMContentLoaded", function () {

  const handleNavigation = (e) => {
    // Only prevent default for form submissions
    if (
      e.target.closest("form") &&
      !e.target.closest(".navigation-buttons a")
    ) {
      return;
    }

    // For navigation links, let them work normally
    if (e.target.closest(".navigation-buttons a")) {
      return;
    }

    e.preventDefault();
    // Your existing form handling code
  };
  // Smooth transitions between pages
  const animateElements = document.querySelectorAll(".animate-in");
  animateElements.forEach((el) => {
    el.style.opacity = "0";
    setTimeout(() => {
      el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
      el.style.opacity = "1";
      el.style.transform = "translateY(0)";
    }, 100);
  });

  // Handle choice selection styling
  const choices = document.querySelectorAll(".choice");
  choices.forEach((choice) => {
    choice.addEventListener("click", function () {
      const questionChoices =
        this.closest(".choices").querySelectorAll(".choice");
      questionChoices.forEach((c) => c.classList.remove("selected"));
      this.classList.add("selected");

      const radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!e.target.closest(".navigation-buttons a")) {
        e.preventDefault();
        this.submit();
      }
    });
  });

  // Animate score circle
  const scoreCircle = document.querySelector(".score-circle");
  if (scoreCircle) {
    const percent = scoreCircle.getAttribute("data-percent");
    const circleFill = scoreCircle.querySelector(".circle-fill");
    const scorePercent = scoreCircle.querySelector(".score-percent");

    setTimeout(() => {
      circleFill.style.strokeDasharray = `${percent}, 100`;

      let current = 0;
      const increment = percent / 50;
      const timer = setInterval(() => {
        current += increment;
        if (current >= percent) {
          current = percent;
          clearInterval(timer);
        }
        scorePercent.textContent = Math.round(current) + "%";
      }, 20);
    }, 500);
  }

  // Print certificate button
  const printBtn = document.querySelector(".print-btn");
  if (printBtn) printBtn.addEventListener("click", () => window.print());

  // Prevent form resubmission
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }

  // Add ripple effect to buttons
  const buttons = document.querySelectorAll(".btn");
  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      const x = e.clientX - e.target.getBoundingClientRect().left;
      const y = e.clientY - e.target.getBoundingClientRect().top;

      const ripple = document.createElement("span");
      ripple.classList.add("ripple");
      ripple.style.left = `${x}px`;
      ripple.style.top = `${y}px`;

      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 1000);
    });
  });

  // Auto-focus name input
  const nameInput = document.getElementById("name");
  if (nameInput) nameInput.focus();
});
