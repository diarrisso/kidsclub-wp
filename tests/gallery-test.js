/**
 * Node-Test der reinen Galerie-Logik (DOM-frei).
 * Run: node tests/gallery-test.js
 */
const { createPraxisGallery } = require('../assets/js/gallery.js');

let pass = 0, fail = 0;
function check(label, cond) {
  if (cond) { pass++; console.log('PASS: ' + label); }
  else { fail++; console.log('FAIL: ' + label); }
}

const photos = [
  { id: 1, cat: 'empfang',    srcLarge: 'a', alt: 'A' },
  { id: 2, cat: 'empfang',    srcLarge: 'b', alt: 'B' },
  { id: 3, cat: 'behandlung', srcLarge: 'c', alt: 'C' },
  { id: 4, cat: '',           srcLarge: 'd', alt: 'D' },
];

const g = createPraxisGallery();
g.all = photos;

// Filter 'alle'
g.f = 'alle';
check('alle: total 4', g.total === 4);
check('alle: position 1 von 4', g.position === 1);

// Filter 'empfang'
g.setFilter('empfang');
check('empfang: total 2', g.total === 2);
check('empfang: current id 1', g.current.id === 1);

// next/prev mit Clamping innerhalb der gefilterten Liste
g.index = 0;
g.next();
check('next -> id 2', g.current.id === 2);
check('atEnd true', g.atEnd === true);
g.next();
check('next am Ende bleibt index 1', g.index === 1);
g.prev();
check('prev -> index 0', g.index === 0);
check('atStart true', g.atStart === true);
g.prev();
check('prev am Anfang bleibt index 0', g.index === 0);

// indexOfId respektiert den aktiven Filter
check('indexOfId(2) === 1 (in empfang)', g.indexOfId(2) === 1);
check('indexOfId(3) === -1 (gefiltert raus)', g.indexOfId(3) === -1);

// Filterwechsel
g.setFilter('alle');
check('alle erneut: total 4', g.total === 4);
check('indexOfId(3) === 2', g.indexOfId(3) === 2);

// Filterwechsel setzt den Index zurück (Finding I1)
g.setFilter('empfang');
g.index = 1;
g.setFilter('behandlung');
check('setFilter setzt index auf 0', g.index === 0);
check('setFilter: current ist erstes Foto der neuen Liste (id 3)', g.current && g.current.id === 3);

console.log(fail === 0 ? '\nALL PASS' : '\n' + fail + ' FAILED');
process.exit(fail === 0 ? 0 : 1);
