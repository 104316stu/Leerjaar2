// prototype zone 2 - wand van stemmen
// klik op een naam: spot blijft aan en het verhaal komt onder de wand

let namen = document.querySelectorAll('.naam');
let verhaal = document.getElementById('verhaal');

namen.forEach(function (n) {
  n.addEventListener('click', function () {
    namen.forEach(function (m) { m.classList.remove('aan'); });
    n.classList.add('aan');
    verhaal.textContent = n.dataset.verhaal;
  });
});
