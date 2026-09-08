const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 Mo
const MAX_GALLERY_FILES = 5;

const SINGLE_FILE_FIELDS = [
  { name: "logo", label: "Logo" },
  { name: "image", label: "Image principale" },
];

function fileTooLarge(file) {
  return !!file && file.size > MAX_FILE_SIZE;
}

function clearFieldErrors(form) {
  form.querySelectorAll(".form-field.has-error").forEach((field) => {
    field.classList.remove("has-error");
    field.querySelector(".form-field-error")?.remove();
  });
}

function setFieldError(field, message) {
  if (!field) return;
  field.classList.add("has-error");

  let errorEl = field.querySelector(".form-field-error");
  if (!errorEl) {
    errorEl = document.createElement("div");
    errorEl.className = "form-field-error";
    field.appendChild(errorEl);
  }
  errorEl.textContent = message;
}

function renderFormErrors(form, messages) {
  let list = form.parentElement.querySelector(":scope > .form-errors");

  if (!messages.length) {
    list?.remove();
    return;
  }

  if (!list) {
    list = document.createElement("ul");
    list.className = "form-errors";
    form.parentElement.insertBefore(list, form);
  }

  list.innerHTML = "";
  messages.forEach((message) => {
    const li = document.createElement("li");
    li.textContent = message;
    list.appendChild(li);
  });
}

function validateCandidatureForm(form) {
  const errors = [];
  clearFieldErrors(form);

  if (!form.checkValidity()) {
    form.querySelectorAll(":invalid").forEach((el) => {
      const field = el.closest(".form-field");
      setFieldError(field, el.validationMessage);
      errors.push(el.validationMessage);
    });
  }

  SINGLE_FILE_FIELDS.forEach(({ name, label }) => {
    const input = form.querySelector(`input[type="file"][name="${name}"]`);
    const file = input?.files?.[0];

    if (file && fileTooLarge(file)) {
      const message = `Le fichier "${label}" dépasse la taille maximale autorisée (5 Mo).`;
      setFieldError(input.closest(".form-field"), message);
      errors.push(message);
    }
  });

  const galleryInput = form.querySelector('input[type="file"][name="gallery[]"]');
  const galleryFiles = galleryInput?.files ? Array.from(galleryInput.files) : [];

  if (galleryFiles.length) {
    const galleryField = galleryInput.closest(".form-field");

    if (galleryFiles.length > MAX_GALLERY_FILES) {
      const message = `Vous ne pouvez pas envoyer plus de ${MAX_GALLERY_FILES} photos dans la galerie.`;
      setFieldError(galleryField, message);
      errors.push(message);
    }

    if (galleryFiles.some(fileTooLarge)) {
      const message = "Une des images de la galerie dépasse la taille maximale autorisée (5 Mo).";
      setFieldError(galleryField, message);
      errors.push(message);
    }
  }

  return [...new Set(errors)];
}

export function initCandidatureForm() {
  const form = document.querySelector(".form-form");
  if (!form || form.dataset.candidatureFormInit) return;
  form.dataset.candidatureFormInit = "true";

  form.addEventListener("submit", (event) => {
    const errors = validateCandidatureForm(form);

    if (errors.length > 0) {
      event.preventDefault();
      renderFormErrors(form, errors);

      const firstErrorField = form.querySelector(".has-error");
      (firstErrorField || form).scrollIntoView({ behavior: "smooth", block: "center" });
      return;
    }

    renderFormErrors(form, []);

    const submitButton = form.querySelector(".form-submit");
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.classList.add("is-loading");
    }
  });

  form.querySelectorAll("input, textarea, select").forEach((el) => {
    el.addEventListener("input", () => {
      const field = el.closest(".form-field");
      if (field?.classList.contains("has-error")) {
        field.classList.remove("has-error");
        field.querySelector(".form-field-error")?.remove();
      }
    });
  });
}
