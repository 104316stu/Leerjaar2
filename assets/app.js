// Filter projects by vak and search text. The choice is kept in the URL (?vak=…&q=…)
// so a filtered view can be shared or reloaded.
const form     = document.querySelector('[data-filters]');
const search   = document.getElementById('search');
const cats     = document.querySelectorAll('.cat');
const sections = document.querySelectorAll('.vak');
const empty    = document.querySelector('[data-empty]');
let activeCat  = '';

function filter() {
  const q = search.value.trim().toLowerCase();
  let shown = 0;

  sections.forEach(section => {
    let inSection = 0;
    section.querySelectorAll('.project').forEach(item => {
      const ok = (!activeCat || item.dataset.vak === activeCat) && (!q || item.dataset.search.includes(q));
      item.hidden = !ok;
      if (!ok) item.querySelector('details[open]')?.removeAttribute('open');
      if (ok) inSection++;
    });
    section.hidden = inSection === 0;
    shown += inSection;
  });

  empty.hidden = shown > 0;

  const params = new URLSearchParams();
  if (activeCat) params.set('vak', activeCat);
  if (q) params.set('q', q);
  const query = params.toString();
  history.replaceState(null, '', query ? '?' + query : location.pathname);
}

function setCat(value) {
  activeCat = value;
  cats.forEach(btn => {
    const on = btn.dataset.cat === value;
    btn.classList.toggle('is-active', on);
    btn.setAttribute('aria-pressed', String(on));
  });
}

search.addEventListener('input', filter);
form.addEventListener('submit', event => event.preventDefault());

cats.forEach(btn => btn.addEventListener('click', () => {
  setCat(btn.dataset.cat);
  filter();
}));

// "/" jumps to the search field, like on GitHub.
document.addEventListener('keydown', event => {
  if (event.key === '/' && document.activeElement !== search && !event.target.closest('input, textarea')) {
    event.preventDefault();
    search.focus();
  }
});

// Start from the URL.
const start = new URLSearchParams(location.search);
if (start.has('vak') && [...cats].some(btn => btn.dataset.cat === start.get('vak'))) setCat(start.get('vak'));
if (start.has('q')) search.value = start.get('q');
if (activeCat || search.value) filter();
