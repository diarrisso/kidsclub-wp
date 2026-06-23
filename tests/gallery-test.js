/**
 * Node-Test der Galerie-Filterlogik (DOM-frei).
 * Run: node tests/gallery-test.js
 */
const { createPraxisGallery } = require('../assets/js/gallery.js');

let pass = 0, fail = 0;
function check(label, cond) {
  if (cond) { pass++; console.log('PASS: ' + label); }
  else { fail++; console.log('FAIL: ' + label); }
}

const g = createPraxisGallery();
check('Default-Filter ist "alle"', g.f === 'alle');

g.setFilter('empfang');
check('setFilter setzt f auf empfang', g.f === 'empfang');

g.setFilter('empfang');
check('setFilter mit gleichem Wert bleibt empfang', g.f === 'empfang');

g.setFilter('alle');
check('setFilter zurück auf alle', g.f === 'alle');

console.log(fail === 0 ? '\nALL PASS' : '\n' + fail + ' FAILED');
process.exit(fail === 0 ? 0 : 1);
