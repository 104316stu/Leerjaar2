// experiment 2 - klik op een schilderij, dan komt hij recht naar voren met uitleg

let groot = document.getElementById('groot');
let grootImg = groot.querySelector('img');
let grootTekst = groot.querySelector('p');

document.querySelectorAll('.schilderij').forEach(function (s) {
  s.addEventListener('click', function () {
    grootImg.src = s.src;
    grootTekst.textContent = s.dataset.tekst;
    groot.classList.add('open');
  });
});

// klik ergens om terug te gaan
groot.addEventListener('click', function () {
  groot.classList.remove('open');
});
