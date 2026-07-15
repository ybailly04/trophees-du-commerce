import kjua from "kjua";

function initWhatsAppModal() {
  const modal = document.querySelector("[data-whatsapp-modal]");

  // QR-code generation
  const url = (modal.getAttribute("data-whatsapp-modal") || "").trim();
  if (!url) return;

  // Generate an SVG QR via kjua
  const svg = kjua({
    text: url,
    render: "svg",
    crisp: true,
    minVersion: 1,
    ecLevel: "M",
    size: 540,
    fill: "#000000",
    back: "#FFFFFF",
    rounded: 0,
  });

  // Let CSS control sizing
  svg.removeAttribute("width");
  svg.removeAttribute("height");
  svg.removeAttribute("style");

  // Insert into canvas (or multiple if needed)
  modal.querySelectorAll("[data-whatsapp-modal-qr-canvas]").forEach((placeholder, i) => {
    const node = i === 0 ? svg : svg.cloneNode(true);
    placeholder.appendChild(node);
  });

  // Add the link to all elements with [data-whatsapp-modal-link] attribute
  document.querySelectorAll("[data-whatsapp-modal-link]").forEach((linkEl) => {
    linkEl.setAttribute("href", url);
    linkEl.setAttribute("target", "_blank");
  });

  // Toggle open/close the modal
  document.querySelectorAll("[data-whatsapp-modal-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
      if (!modal) return;
      const isActive = modal.getAttribute("data-whatsapp-modal-status") === "active";
      modal.setAttribute("data-whatsapp-modal-status", isActive ? "not-active" : "active");
    });
  });

  // Close on ESC key
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" || event.keyCode === 27) {
      if (modal) {
        modal.setAttribute("data-whatsapp-modal-status", "not-active");
      }
    }
  });
}

// Initialize WhatsApp Modal (Generate QR Code)
document.addEventListener("DOMContentLoaded", function () {
  initWhatsAppModal();
});
