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
            button.textContent = `Déjà voté`;
            return;
          }
          throw new Error(data.error || "Erreur lors du vote.");
        }else{
          button.textContent = `Déjà voté`;
        }

      } catch (error) {
        button.disabled = false;
      }
    });
  });
}