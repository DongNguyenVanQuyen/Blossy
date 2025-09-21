const overlay = document.querySelector(".auth-overlay");
const modal = document.querySelector(".auth-modal");
const loginForm = document.querySelector(".auth-form.login");
const registerForm = document.querySelector(".auth-form.register");
const btnClose = document.querySelector(".auth-modal .close-btn");

// ===== Open Modal =====
function openAuthForm(type = "login") {
  overlay.style.display = "block";
  modal.style.display = "block";

  if (type === "login") {
    loginForm.style.display = "flex";
    registerForm.style.display = "none";
  } else {
    loginForm.style.display = "none";
    registerForm.style.display = "flex";
  }
}

// ===== Close Modal =====
function closeAuthForm() {
  overlay.style.display = "none";
  modal.style.display = "none";
}

// ===== Toggle Forms =====
document.querySelectorAll(".toggle-link a").forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const to = link.getAttribute("data-to");
    if (to === "register") {
      loginForm.style.display = "none";
      registerForm.style.display = "flex";
    } else {
      loginForm.style.display = "flex";
      registerForm.style.display = "none";
    }
  });
});

// ===== Close when clicking overlay or close button =====
overlay.addEventListener("click", closeAuthForm);
btnClose.addEventListener("click", closeAuthForm);

// ===== Optional: Open from buttons =====
// document.querySelector("#btnLogin")?.addEventListener("click", () => openAuthForm("login"));
// document.querySelector("#btnRegister")?.addEventListener("click", () => openAuthForm("register"));
