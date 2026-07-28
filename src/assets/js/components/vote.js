export function initVoteButtons() {
  document.querySelectorAll("[data-vote-button]").forEach((button) => {
    button.addEventListener("click", async () => {
      button.disabled = true;

      try {
        const response = await fetch(button.getAttribute("data-vote-url"), {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ _csrf: button.getAttribute("data-csrf") }),
        });

        const data = await response.json();

        if (!response.ok) {
          if (response.status === 409) {
            button.textContent = `Déjà voté (${button.querySelector("[data-vote-count]")?.textContent ?? ""})`;
            return;
          }
          throw new Error(data.error || "Erreur lors du vote.");
        }

        const countEl = button.querySelector("[data-vote-count]");
        if (countEl) countEl.textContent = data.count;
      } catch (error) {
        button.disabled = false;
        alert(error.message);
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  initVoteButtons();
});
