/* Kids Club by zacp — interactions */

// Geteilte Tab-Fokusfalle für modale Overlays (Booking-Modal + Galerie-Lightbox).
window.kcTrapTab = function (e, container, selector) {
  if (e.key !== 'Tab' || !container) { return; }
  var sel = selector || 'a[href],button:not([disabled]),input,textarea,select,[tabindex]:not([tabindex="-1"])';
  var nodes = Array.prototype.slice.call(container.querySelectorAll(sel));
  if (!nodes.length) { return; }
  var first = nodes[0];
  var last = nodes[nodes.length - 1];
  var active = document.activeElement;
  if (e.shiftKey) {
    if (active === first || nodes.indexOf(active) === -1) { e.preventDefault(); last.focus(); }
  } else if (active === last || nodes.indexOf(active) === -1) {
    e.preventDefault(); first.focus();
  }
};

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

  // Hero cinematic video reveal
  var heroSection = document.querySelector('.hero[data-media="video"]');
  if (heroSection) {
    var heroVideo = heroSection.querySelector('.hero-video');

    function revealHero() {
      heroSection.classList.add('hero--revealed');
    }

    // Mobile < 768px or reduced-motion: remove video element (stops network fetch), reveal immediately
    if (window.innerWidth < 768 ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      if (heroVideo) heroVideo.remove();
      revealHero();
    } else if (!heroVideo) {
      revealHero();
    } else {
      // 3s hard cap: text never hidden longer than this (covers autoplay-blocked + stall)
      setTimeout(revealHero, 3000);
      // Normal path: video ends → reveal (idempotent with timer)
      heroVideo.addEventListener('ended', revealHero, { once: true });
      heroVideo.addEventListener('error', revealHero, { once: true });
    }
  }

  // Hero Video Slider (data-media="video_slider")
  var heroSliderSection = document.querySelector('.hero[data-media="video_slider"]');
  if (heroSliderSection) {
    var swiperEl = heroSliderSection.querySelector('.hero-video-swiper');
    var sliderVideos = swiperEl ? Array.prototype.slice.call(swiperEl.querySelectorAll('video')) : [];
    var progressEl = swiperEl ? swiperEl.querySelector('.hero-video-swiper__progress') : null;

    // Mobile / reduced-motion: supprime les vidéos, garde le poster du premier slide
    if (window.innerWidth < 768 || window.matchMedia('(prefers-reduced-motion:reduce)').matches) {
      sliderVideos.forEach(function(v) { v.remove(); });
    } else if (sliderVideos.length >= 2 && typeof Swiper !== 'undefined') {

      // Construire les barres de progression
      if (progressEl) {
        sliderVideos.forEach(function() {
          var bar = document.createElement('span');
          bar.className = 'bar';
          progressEl.appendChild(bar);
        });
      }
      var bars = progressEl ? Array.prototype.slice.call(progressEl.querySelectorAll('.bar')) : [];

      var heroSlider = new Swiper('.hero-video-swiper', {
        loop: false,
        effect: 'fade',
        fadeEffect: { crossFade: true },
        allowTouchMove: false,
        speed: 700,
      });

      function playSlide(idx) {
        // Reset all bars
        bars.forEach(function(b) {
          b.classList.remove('active');
          b.style.removeProperty('--vbar-duration');
        });

        var video = sliderVideos[idx];
        if (!video) return;

        video.currentTime = 0;
        var playPromise = video.play();
        if (playPromise !== undefined) {
          playPromise.catch(function() {});
        }

        // Activate progress bar with video duration as animation timing
        if (bars[idx]) {
          var dur = video.duration || 6;
          bars[idx].style.setProperty('--vbar-duration', dur + 's');
          bars[idx].classList.add('active');
        }
      }

      function advanceSlider() {
        var next = heroSlider.activeIndex + 1;
        if (next >= sliderVideos.length) {
          heroSlider.slideTo(0, 700);
          setTimeout(function() { playSlide(0); }, 750);
        } else {
          heroSlider.slideNext(700);
          setTimeout(function() { playSlide(next); }, 750);
        }
      }

      sliderVideos.forEach(function(video) {
        video.addEventListener('ended', advanceSlider);
      });

      // Start first slide
      playSlide(0);
      heroSliderSection.classList.add('hero--revealed');
    }
  }

  // Spray-Hintergrund (Loop): Hero-Text bleibt dauerhaft über dem Video sichtbar
  // (kein Ausblenden mehr beim Abspielen).
})();

// Kundenstimmen Swiper
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('.stimmen-swiper')) {
        new Swiper('.stimmen-swiper', {
            slidesPerView: 1.1,
            spaceBetween: 20,
            loop: false,
            pagination: { el: '.stimmen-swiper__pagination', clickable: true },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 24 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
            },
            a11y: { enabled: true },
        });
    }
});

// Zimmer: Desktop = statisches Grid (alle 5 sichtbar), Mobile = Swiper-Karussell.
// Swiper wird NUR unter 640px initialisiert; auf Desktop bleibt das Markup ein
// neutrales Grid (CSS). Auf Resize wird sauber initialisiert/zerstört.
document.addEventListener('DOMContentLoaded', function () {
    var sel = '.zimmer-swiper';
    if (!document.querySelector(sel)) {
        return;
    }
    var reduceMotion = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var mq = window.matchMedia('(max-width:639px)');
    var instance = null;

    function syncSwiper() {
        if (mq.matches && !instance) {
            instance = new Swiper(sel, {
                slidesPerView: 1,
                spaceBetween: 14,
                loop: true,
                autoplay: reduceMotion
                    ? false
                    : { delay: 2500, disableOnInteraction: false, pauseOnMouseEnter: true },
                breakpoints: {
                    480: { slidesPerView: 2, spaceBetween: 16 },
                },
                navigation: { prevEl: '.zimmer-swiper__prev', nextEl: '.zimmer-swiper__next' },
                pagination: { el: '.zimmer-swiper__pagination', clickable: true },
                a11y: { enabled: true },
            });
        } else if (!mq.matches && instance) {
            instance.destroy(true, true); // cleanStyles → Grid (CSS) übernimmt wieder
            instance = null;
        }
    }

    syncSwiper();
    mq.addEventListener('change', syncSwiper);
});

// Booking Modal
(function () {
  var modal   = document.getElementById('bookingModal');
  var lastFocus;

  if (!modal) return;

  function getFocusable() {
    return Array.prototype.slice.call(
      modal.querySelectorAll('a[href],button:not([disabled]),input,textarea,select,[tabindex]:not([tabindex="-1"])')
    );
  }

  function setPageInert(inert) {
    ['#header', 'main', '.mobile-menu', '.site-footer'].forEach(function (sel) {
      var el = document.querySelector(sel);
      if (el) el.inert = inert;
    });
  }

  function openModal() {
    lastFocus = document.activeElement;
    modal.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';
    setPageInert(true);
    var first = getFocusable()[0];
    if (first) first.focus();
  }

  function closeModal() {
    modal.setAttribute('hidden', '');
    document.body.style.overflow = '';
    setPageInert(false);
    if (lastFocus) lastFocus.focus();
  }

  // Open triggers (navbar btn, hero btn, footer QR btn)
  document.querySelectorAll('[data-booking-open]').forEach(function (btn) {
    btn.addEventListener('click', openModal);
  });

  // Close on ✕ button
  var closeBtn = document.getElementById('bookingClose');
  if (closeBtn) closeBtn.addEventListener('click', closeModal);

  // Close on backdrop click
  var backdrop = document.getElementById('bookingBackdrop');
  if (backdrop) backdrop.addEventListener('click', closeModal);

  // Close on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !modal.hasAttribute('hidden')) closeModal();
  });

  // Focus trap: keep Tab inside modal (shared helper)
  modal.addEventListener('keydown', function (e) {
    window.kcTrapTab(e, modal);
  });
}());

/* === Dékorative Animationen (3 Varianten) === */
(function () {
  if (window.matchMedia('(prefers-reduced-motion:reduce)').matches) return;

  var body = document.body;

  /* ── Variant 1 : Schwebende Brand-Symbole (anim-floating) ── */
  if (body.classList.contains('anim-floating')) {
    var svgs = [
      '<svg viewBox="0 0 24 24" fill="#EC0A8C"><path d="M12 21C7 17 4 13.5 4 9.8 4 7 6 5 8.6 5c1.6 0 3 .8 3.4 2 .4-1.2 1.8-2 3.4-2C18 5 20 7 20 9.8c0 3.7-3 7.2-8 11.2Z"/></svg>',
      '<svg viewBox="0 0 24 24" fill="none" stroke="#26257F" stroke-width="1.6"><path d="M12 3c-4 0-6 2.6-6 6.5 0 4 1.6 8 3.4 9 1.2.6 1.4-3.2 2.6-3.2s1.4 3.8 2.6 3.2c1.8-1 3.4-5 3.4-9C18 5.6 16 3 12 3Z"/></svg>',
      '<svg viewBox="0 0 24 24" fill="#F7E29D" stroke="#D4A832" stroke-width="0.8"><path fill="#C8962A" stroke="none" d="M12 9C11.4 10.5 11.4 12.5 12 15C12.6 12.5 12.6 10.5 12 9Z"/><path fill="none" stroke="#C8962A" stroke-width="0.7" stroke-linecap="round" d="M12 9C11 7 9 6 8.5 5M12 9C13 7 15 6 15.5 5"/><path d="M12 10C9 8 4.5 8.5 4 11C3.5 13.5 8 14 12 12Z"/><path d="M12 10C15 8 19.5 8.5 20 11C20.5 13.5 16 14 12 12Z"/><path d="M12 12.5C8 11 4 13 5 16C6 18.5 10 17.5 12 14.5Z"/><path d="M12 12.5C16 11 20 13 19 16C18 18.5 14 17.5 12 14.5Z"/></svg>',
    ];
    var positions = [
      [8,12],[18,45],[5,70],[88,8],[92,55],[75,30],[30,88],[60,15],[45,65],[80,78]
    ];
    var range = document.createRange();
    positions.forEach(function(pos, i) {
      var el = document.createElement('div');
      el.className = 'kc-floater';
      el.appendChild(range.createContextualFragment(svgs[i % svgs.length]));
      var size = 36 + (i % 3) * 14;
      var dur  = 6 + (i % 5) * 1.8;
      var delay = (i % 4) * 0.8;
      var opacity = 0.55 + (i % 4) * 0.08;
      el.style.cssText = [
        'width:' + size + 'px',
        'height:' + size + 'px',
        'left:' + pos[0] + '%',
        'top:' + pos[1] + '%',
        'opacity:' + opacity,
        'animation:kc-float ' + dur + 's ' + delay + 's ease-in-out infinite',
      ].join(';');
      body.appendChild(el);
    });
  }

  /* ── Variant 2 : Cursor-Funken (anim-sparkle) ── */
  if (body.classList.contains('anim-sparkle')) {
    var sColors = ['#EC0A8C','#26257F','#F7E29D','#98ACBA','#BDCCC2','#FCE8E1'];
    var lastSparkle = 0;
    function spawnSparkle(x, y) {
      var now = Date.now();
      if (now - lastSparkle < 55) return;
      lastSparkle = now;
      var el = document.createElement('div');
      el.className = 'kc-sparkle';
      var size = 6 + Math.floor(now % 7);
      el.style.cssText = [
        'left:' + (x + (size % 3) * 4 - 6) + 'px',
        'top:' + (y + (size % 5) * 3 - 7) + 'px',
        'width:' + size + 'px',
        'height:' + size + 'px',
        'background:' + sColors[size % sColors.length],
      ].join(';');
      body.appendChild(el);
      setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 680);
    }
    document.addEventListener('mousemove', function (e) {
      spawnSparkle(e.clientX, e.clientY);
    }, { passive: true });
    document.addEventListener('touchmove', function (e) {
      if (e.touches[0]) spawnSparkle(e.touches[0].clientX, e.touches[0].clientY);
    }, { passive: true });
  }

  /* ── Variant 3 : Confetti beim Scrollen (anim-confetti) ── */
  if (body.classList.contains('anim-confetti')) {
    var cColors = ['#EC0A8C','#26257F','#F7E29D','#98ACBA','#BDCCC2','#FCE8E1'];
    var fired = [];
    function burst() {
      for (var j = 0; j < 28; j++) {
        (function (idx) {
          setTimeout(function () {
            var p = document.createElement('div');
            p.className = 'kc-confetti-piece';
            var size = 5 + (idx % 6);
            var fallDur = 1.6 + (idx % 8) * 0.25;
            var rot = (idx % 2 === 0 ? 1 : -1) * (180 + idx * 19);
            p.style.cssText = [
              'left:' + ((idx * 3.7) % 100) + 'vw',
              'width:' + size + 'px',
              'height:' + size + 'px',
              'background:' + cColors[idx % cColors.length],
              'border-radius:' + (idx % 3 === 0 ? '50%' : '2px'),
              '--fall-dur:' + fallDur + 's',
              '--rot:' + rot + 'deg',
            ].join(';');
            body.appendChild(p);
            setTimeout(function () {
              if (p.parentNode) p.parentNode.removeChild(p);
            }, 4500);
          }, idx * 65);
        })(j);
      }
    }
    if ('IntersectionObserver' in window) {
      var cObs = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting && fired.indexOf(en.target) === -1) {
            fired.push(en.target);
            burst();
          }
        });
      }, { threshold: 0.3 });
      document.querySelectorAll('[class*="section-"]').forEach(function (s) {
        cObs.observe(s);
      });
    }
  }
}());

/* === BACK-TO-TOP ============================================================ */
(function () {
  var btn = document.getElementById('backToTop');
  if (!btn) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

  function check() {
    var y = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || 0;
    var v = y > 400;
    btn.classList.toggle('is-visible', v);
    btn.setAttribute('aria-hidden', String(!v));
    btn.tabIndex = v ? 0 : -1;
  }

  window.addEventListener('scroll', check, { passive: true });
  document.addEventListener('scroll', check, { passive: true });
  check();

  btn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: reduceMotion ? 'instant' : 'smooth' });
  });
}());

/* === QR-AUTO-OPEN: open booking modal when arrived via ?termin=1 (QR code) === */
(function () {
  function openFromQuery() {
    if (new URLSearchParams(location.search).get('termin') === '1') {
      var btn = document.querySelector('[data-booking-open]');
      if (btn) btn.click();
    }
  }
  if (document.readyState !== 'loading') openFromQuery();
  else document.addEventListener('DOMContentLoaded', openFromQuery);
}());
