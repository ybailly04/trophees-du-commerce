document.querySelectorAll('[data-search]').forEach((wrapper) => {
  const toggle = wrapper.querySelector('[data-search-toggle]');
  const panel = wrapper.querySelector('[data-search-panel]');
  const form = wrapper.querySelector('[data-search-form]');
  const input = wrapper.querySelector('[data-search-input]');
  const resultsList = wrapper.querySelector('[data-search-results]');

  if (!toggle || !panel || !form || !input || !resultsList) return;

  let debounceTimer = null;
  let controller = null;

  const openPanel = () => {
    panel.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    input.focus();
  };

  const closePanel = () => {
    panel.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
  };

  const clearResults = () => {
    resultsList.innerHTML = '';
    resultsList.hidden = true;
  };

  const renderResults = (results) => {
    resultsList.innerHTML = '';

    if (!results.length) {
      const empty = document.createElement('li');
      empty.className = 'header-search-empty';
      empty.textContent = 'Aucun candidat trouvé.';
      resultsList.appendChild(empty);
      resultsList.hidden = false;
      return;
    }

    results.forEach((candidate) => {
      const item = document.createElement('li');
      item.className = 'header-search-result';

      const link = document.createElement('a');
      link.href = candidate.url;

      if (candidate.logo) {
        const img = document.createElement('img');
        img.src = candidate.logo;
        img.alt = '';
        link.appendChild(img);
      }

      const title = document.createElement('span');
      title.textContent = candidate.title;
      link.appendChild(title);

      item.appendChild(link);
      resultsList.appendChild(item);
    });

    resultsList.hidden = false;
  };

  const search = async (query) => {
    if (controller) controller.abort();
    controller = new AbortController();

    try {
      const response = await fetch(`/candidates/search?q=${encodeURIComponent(query)}`, {
        signal: controller.signal,
      });

      if (!response.ok) throw new Error('search failed');

      const data = await response.json();
      renderResults(data.results || []);
    } catch (error) {
      if (error.name !== 'AbortError') clearResults();
    }
  };

  toggle.addEventListener('click', () => {
    if (panel.hidden) {
      openPanel();
    } else {
      closePanel();
    }
  });

  input.addEventListener('input', () => {
    const query = input.value.trim();

    clearTimeout(debounceTimer);

    if (query.length < 2) {
      clearResults();
      return;
    }

    debounceTimer = setTimeout(() => search(query), 250);
  });

  form.addEventListener('submit', (e) => e.preventDefault());

  document.addEventListener('click', (e) => {
    if (!panel.hidden && !wrapper.contains(e.target)) closePanel();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !panel.hidden) {
      closePanel();
      input.value = '';
      clearResults();
      toggle.focus();
    }
  });
});
