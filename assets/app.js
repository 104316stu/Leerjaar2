const search = document.getElementById('search');
const cats   = document.querySelectorAll('.cat');
const rows   = document.querySelectorAll('tr.row');
const empty  = document.getElementById('empty');
let activeCat = '';

function filter() {
  const q = search.value.trim().toLowerCase();
  let shown = 0;
  rows.forEach(row => {
    const ok = (!activeCat || row.dataset.cat === activeCat) && (!q || row.dataset.search.includes(q));
    row.hidden = !ok;
    // A hidden project also hides its expanded README row.
    const readme = row.nextElementSibling;
    if (readme && readme.classList.contains('readme-row') && !ok) {
      readme.hidden = true;
      const btn = row.querySelector('.readme-toggle');
      if (btn) { btn.setAttribute('aria-expanded', 'false'); btn.textContent = '+ README'; }
    }
    if (ok) shown++;
  });
  empty.hidden = shown > 0;
}

search.addEventListener('input', filter);

cats.forEach(btn => btn.addEventListener('click', () => {
  cats.forEach(x => x.classList.remove('is-active'));
  btn.classList.add('is-active');
  activeCat = btn.dataset.cat;
  filter();
}));

document.querySelectorAll('.readme-toggle').forEach(btn => btn.addEventListener('click', () => {
  const readme = btn.closest('tr').nextElementSibling;
  const open = readme.hidden;
  readme.hidden = !open;
  btn.setAttribute('aria-expanded', String(open));
  btn.textContent = open ? '− README' : '+ README';
}));
