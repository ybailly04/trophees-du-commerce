export function initVoteButtons() {
  document.querySelectorAll("[data-vote-button]").forEach((button) => {
    button.addEventListener("click", async () => {
      button.disabled = true;
      button.classList.add("is-loading");
      let inner = button.querySelector('.button-text');

      const minSpinnerDelay = new Promise((resolve) => setTimeout(resolve, 3000));

      try {
        const [response] = await Promise.all([
          fetch(button.getAttribute("data-vote-url"), {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ _csrf: button.getAttribute("data-csrf") }),
          }),
          minSpinnerDelay,
        ]);

        const data = await response.json();

        if (!response.ok) {
          if (response.status === 409) {
            inner.textContent = `Votre pris en compte`;
            return;
          }
          throw new Error(data.error || "Erreur lors du vote.");
        }else{
          inner.textContent = `Votre pris en compte`;
        }

      } catch (error) {
        button.disabled = false;
      } finally {
        button.classList.remove("is-loading");
      }
    });
  });
}