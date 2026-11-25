// Search through every vak, folder, project and file. The query is kept in the
// URL (?q=…) so a search can be shared or reloaded.
const form      = document.querySelector('[data-search-form]');
const search    = document.getElementById('search');
const view      = document.getElementById('view');
const results   = document.getElementById('results');
const noResults = document.getElementById('no-results');
const items     = results.querySelectorAll('li');

function runSearch() {
  // Every word has to match, so "oop 1.2" finds Opdracht 1.2 in OOP.
  const words = search.value.toLowerCase().split(/\s+/).filter(Boolean);
  const searching = words.length > 0;
  let shown = 0;

  items.forEach(item => {
    const ok = searching && words.every(word => item.dataset.search.includes(word));
    item.hidden = !ok;
    if (ok) shown++;
  });

  view.hidden = searching;
  results.hidden = !searching;
  noResults.hidden = shown > 0;

  const url = new URL(location.href);
  if (searching) url.searchParams.set('q', search.value.trim());
  else url.searchParams.delete('q');
  if (url.href !== location.href) history.replaceState(null, '', url);
}

form.hidden = false;
form.addEventListener('submit', event => event.preventDefault());
search.addEventListener('input', runSearch);

// "/" jumps to the search field, like on GitHub. Escape clears it.
document.addEventListener('keydown', event => {
  if (event.key === '/' && !event.target.closest('input, textarea')) {
    event.preventDefault();
    search.focus();
  } else if (event.key === 'Escape' && event.target === search) {
    search.value = '';
    runSearch();
  }
});

// Start from the URL.
search.value = new URLSearchParams(location.search).get('q') ?? '';
if (search.value) runSearch();
