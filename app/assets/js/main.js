/**
 * Main JavaScript
 * CS425 Assignment Grading System - Starter Codebase
 *
 * Client-side functionality.
 * TODO for students: Add form validation, AJAX submissions, better UX
 */

// API Helper for clean asynchronous calls
const API = {
  baseUrl: "/api",
  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}/${endpoint}`;
    const defaultOptions = {
      headers: { "Content-Type": "application/json" },
    };
    const response = await fetch(url, { ...defaultOptions, ...options });
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || "API request failed");
    return data;
  },
};

// DOM Ready
/**
 * Main JavaScript - GradeSys
 */

document.addEventListener("DOMContentLoaded", function () {
  initTheme();
  initMobileMenu(); // Now correctly defined below
  initAlerts();
  initForms();
  initRubricForm();
  initConfirmDialogs();
});

/**
 * 1. Dark Mode Logic (Unified with PHP Cookies)
 */
function initTheme() {
  const toggle = document.getElementById("theme-toggle");
  const html = document.documentElement;

  if (!toggle) return;

  toggle.addEventListener("click", () => {
    const isDark = html.classList.toggle("dark");

    // Save to BOTH LocalStorage (for JS) and Cookies (for PHP)
    localStorage.setItem("theme", isDark ? "dark" : "light");
    document.cookie = `theme=${
      isDark ? "dark" : "light"
    };path=/;max-age=31536000;SameSite=Lax`;

    console.log("Theme updated to:", isDark ? "dark" : "light");
  });
}

/**
 * 2. Mobile Menu Logic
 */
function initMobileMenu() {
  const mobileBtn = document.getElementById("mobile-menu-button");
  const mobileMenu = document.getElementById("mobile-menu");

  if (!mobileBtn || !mobileMenu) return;

  mobileBtn.addEventListener("click", () => {
    const isHidden = mobileMenu.classList.toggle("hidden");
    const icon = mobileBtn.querySelector("i");

    if (icon) {
      if (isHidden) {
        icon.classList.replace("fa-times", "fa-bars");
      } else {
        icon.classList.replace("fa-bars", "fa-times");
      }
    }
  });
}

/**
 * 3. Form Validation
 */
function initForms() {
  const forms = document.querySelectorAll("form[data-validate]");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        showNotification("Please fill in all required fields.", "danger");
      }
      form.classList.add("was-validated");
    });
  });
}

/**
 * 4. Dynamic Rubric Form
 */
function initRubricForm() {
  const addRubricBtn = document.getElementById("add-rubric");
  const rubricContainer = document.getElementById("rubric-container");

  if (!addRubricBtn || !rubricContainer) return;

  addRubricBtn.addEventListener("click", function () {
    const index = rubricContainer.querySelectorAll(".rubric-item").length;
    const div = document.createElement("div");
    div.className =
      "rubric-item bg-slate-50 dark:bg-slate-800/40 p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 group transition animate-fade-in mb-4";

    div.innerHTML = `
          <div class="flex justify-between items-center mb-4">
              <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">New Criterion</span>
              <button type="button" class="remove-rubric w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:bg-red-50 hover:text-red-500 transition">
                  <i class="fas fa-times"></i>
              </button>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="md:col-span-3">
                  <input type="text" name="rubric[${index}][name]" 
                         class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold" 
                         placeholder="Criterion Name" required>
              </div>
              <div>
                  <input type="number" name="rubric[${index}][points]" 
                         class="rubric-points w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-black text-blue-600 text-center" 
                         value="10" min="0" required>
              </div>
          </div>`;

    rubricContainer.appendChild(div);
    updateTotalPoints();
  });

  rubricContainer.addEventListener("click", (e) => {
    if (e.target.closest(".remove-rubric")) {
      e.target.closest(".rubric-item").remove();
      updateTotalPoints();
    }
  });

  rubricContainer.addEventListener("input", (e) => {
    if (e.target.classList.contains("rubric-points")) updateTotalPoints();
  });
}

function updateTotalPoints() {
  const pointsInputs = document.querySelectorAll(".rubric-points");
  let total = 0;
  pointsInputs.forEach((input) => (total += parseInt(input.value) || 0));
  const totalDisplay = document.getElementById("total-points");
  if (totalDisplay) totalDisplay.textContent = total;
}

/**
 * 5. UI Helpers
 */
function showNotification(message, type = "info") {
  const bgColor =
    type === "success"
      ? "bg-emerald-500"
      : type === "danger"
      ? "bg-rose-500"
      : "bg-blue-600";
  const notification = document.createElement("div");
  notification.className = `fixed top-6 right-6 z-[9999] px-6 py-4 rounded-2xl shadow-2xl text-white font-bold flex items-center gap-3 transition-all duration-500 transform translate-x-full ${bgColor}`;
  notification.innerHTML = `<i class="fas fa-info-circle"></i> <span>${message}</span>`;
  document.body.appendChild(notification);
  setTimeout(() => notification.classList.remove("translate-x-full"), 100);
  setTimeout(() => {
    notification.classList.add("translate-x-[150%]");
    setTimeout(() => notification.remove(), 500);
  }, 4000);
}

function initAlerts() {
  document.querySelectorAll(".alert").forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
}

function initConfirmDialogs() {
  document.querySelectorAll("[data-confirm]").forEach((button) => {
    button.addEventListener("click", function (e) {
      if (!confirm(this.dataset.confirm || "Are you sure?")) e.preventDefault();
    });
  });
}

// Add CSS Animation for the rubric rows
const style = document.createElement("style");
style.innerHTML = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
`;
document.head.appendChild(style);

/**
 * 6. AJAX Form Submission
 */
function initAjaxSubmissions() {
  const submissionForm = document.querySelector("form[data-ajax]");

  if (!submissionForm) return;

  submissionForm.addEventListener("submit", async function (e) {
    e.preventDefault(); // Stop the page from reloading

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    const formData = new FormData(this);

    try {
      const response = await fetch(this.action, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest", // Tells PHP this is AJAX
        },
      });

      const result = await response.json();

      if (response.ok) {
        showNotification(result.message, "success");
        // Single-page feel: Don't redirect, just update the UI
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Submitted!';
        setTimeout(() => {
          window.location.href = `${API.baseUrl.replace(
            "/api",
            ""
          )}/student/submissions.php`;
        }, 1500);
      } else {
        throw new Error(result.message || "Submission failed");
      }
    } catch (error) {
      showNotification(error.message, "danger");
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
}

// Update your DOMContentLoaded to include it:
document.addEventListener("DOMContentLoaded", function () {
  // ... other inits
  initAjaxSubmissions();
});
