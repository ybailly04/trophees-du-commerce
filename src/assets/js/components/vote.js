import { detectIncognito } from "detectincognitojs";

let turnstileQueue = Promise.resolve();

function getTurnstileToken() {
  const result = turnstileQueue.then(() => requestTurnstileToken());
  turnstileQueue = result.catch(() => {});
  return result;
}

function requestTurnstileToken() {
  return new Promise((resolve, reject) => {
    if (typeof turnstile === "undefined" || window.__turnstileWidgetId == null) {
      reject(new Error("Vérification anti-robot indisponible."));
      return;
    }

    function cleanup() {
      document.removeEventListener("turnstile:token", onToken);
      document.removeEventListener("turnstile:error", onError);
    }

    function onToken(event) {
      cleanup();
      turnstile.reset(window.__turnstileWidgetId);
      resolve(event.detail);
    }

    function onError() {
      cleanup();
      turnstile.reset(window.__turnstileWidgetId);
      reject(new Error("Vérification anti-robot échouée."));
    }

    document.addEventListener("turnstile:token", onToken, { once: true });
    document.addEventListener("turnstile:error", onError, { once: true });

    turnstile.execute(window.__turnstileWidgetId);
  });
}

export function initVoteButtons() {
  document.querySelectorAll("[data-vote-button]").forEach((button) => {
    button.addEventListener("click", async () => {
      button.disabled = true;
      button.classList.add("is-loading");
      let inner = button.querySelector('.button-text');

      const minSpinnerDelay = new Promise((resolve) => setTimeout(resolve, 3000));

      try {
        //Detect incognito mode
        const { isPrivate } = await detectIncognito();
        if (isPrivate) {
          const html = document.querySelector('html');
          const popupText = document.querySelector('.popup-alert-text');

          html.classList.add('__popup-active');
          popupText.innerHTML = "Vous ne pouvez pas voter en navigation privée. <br/> Merci de changer de mode de navigation.";
          throw new Error("Navigation privée détectée");
        }

        const turnstileToken = await getTurnstileToken();

        const [response] = await Promise.all([
          fetch(button.getAttribute("data-vote-url"), {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
              _csrf: button.getAttribute("data-csrf"),
              "cf-turnstile-response": turnstileToken,
            }),
          }),
          minSpinnerDelay,
        ]);

        const data = await response.json();

        if (!response.ok) {
          if (response.status === 409) {
            inner.textContent = `Vote pris en compte`;
            return;
          }
          throw new Error(data.error || "Erreur lors du vote.");
        }else{
          inner.textContent = `Vote pris en compte`;
        }

      } catch (error) {
        button.disabled = false;
      } finally {
        button.classList.remove("is-loading");
      }
    });
  });
}