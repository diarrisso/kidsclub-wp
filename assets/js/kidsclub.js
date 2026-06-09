/* Kids Club by zacp — interactions */
(function () {
  'use strict';

  // Year
  var y = document.getElementById('year');
  if (y) y.textContent = new Date().getFullYear();

  // Header shadow on scroll
  var header = document.getElementById('header');
  function onScroll() {
    if (!header) return;
    header.classList.toggle('scrolled', window.scrollY > 12);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mobile menu
  var burger = document.getElementById('burger');
  var menu = document.getElementById('mobileMenu');
  var close = document.getElementById('menuClose');
  function setMenu(open) {
    if (!menu) return;
    menu.classList.toggle('open', open);
    document.body.style.overflow = open ? 'hidden' : '';
  }
  if (burger) burger.addEventListener('click', function () { setMenu(true); });
  if (close) close.addEventListener('click', function () { setMenu(false); });
  if (menu) menu.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () { setMenu(false); });
  });

  // Smooth scroll with sticky-header offset
  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href');
      if (id.length < 2) return;
      var el = document.querySelector(id);
      if (!el) return;
      e.preventDefault();
      var top = el.getBoundingClientRect().top + window.scrollY - 88;
      window.scrollTo({ top: top, behavior: 'smooth' });
    });
  });

  // Reveal on scroll
  var reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          en.target.classList.add('in');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    reveals.forEach(function (el, i) {
      // small stagger inside grids
      el.style.transitionDelay = ((i % 4) * 70) + 'ms';
      io.observe(el);
    });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  // Hero marquee — infinite scrolling brand motifs
  var track = document.querySelector('.marquee-track');
  if (track) {
    var heart = '<svg viewBox="0 0 24 24" fill="#EC0A8C"><path d="M12 21C7 17 4 13.5 4 9.8 4 7 6 5 8.6 5c1.6 0 3 .8 3.4 2 .4-1.2 1.8-2 3.4-2C18 5 20 7 20 9.8c0 3.7-3 7.2-8 11.2Z"/></svg>';
    var tooth = '<svg viewBox="0 0 24 24" fill="#fff" stroke="#26257F" stroke-width="1.6"><path d="M12 3c-4 0-6 2.6-6 6.5 0 4 1.6 8 3.4 9 1.2.6 1.4-3.2 2.6-3.2s1.4 3.8 2.6 3.2c1.8-1 3.4-5 3.4-9C18 5.6 16 3 12 3Z"/></svg>';
    var star = '<svg viewBox="0 0 24 24" fill="#F7E29D" stroke="#E3C25A" stroke-width="1"><path d="m12 3 2.4 5.3L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.6-.7Z"/></svg>';
    var dot = '<svg viewBox="0 0 24 24" fill="#98ACBA"><circle cx="12" cy="12" r="7"/></svg>';
    var arch = '<svg viewBox="0 0 24 24" fill="none" stroke="#26257F" stroke-width="2.4" stroke-linecap="round"><path d="M5 21V11Q5 4 12 4Q19 4 19 11V21"/><path d="M12 19c-3-2.6-4.6-4-4.6-6 0-1.5 1.1-2.6 2.5-2.6.9 0 1.6.4 2.1 1.1.5-.7 1.2-1.1 2.1-1.1 1.4 0 2.5 1.1 2.5 2.6 0 2-1.6 3.4-4.6 6Z" fill="#EC0A8C" stroke="none"/></svg>';
    var unit = '<span class="m">' + heart + tooth + star + arch + dot + '</span>';
    track.innerHTML = unit.repeat(10);
  }
})();
