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

  // Hero marquee — Symbol illustrations (Symbol1–9)
  var track = document.querySelector('.marquee-track');
  if (track) {
    var base = (typeof kcData !== 'undefined' && kcData.themeUri) ? kcData.themeUri : '';
    var fragment = document.createDocumentFragment();
    for (var rep = 0; rep < 4; rep++) {
      var span = document.createElement('span');
      span.className = 'm';
      for (var n = 1; n <= 9; n++) {
        var img = document.createElement('img');
        img.src     = base + '/assets/img/symbols/Symbol' + n + '.svg';
        img.alt     = '';
        img.width   = 58;
        img.height  = 48;
        img.loading = 'lazy';
        span.appendChild(img);
      }
      fragment.appendChild(span);
    }
    track.appendChild(fragment);
  }

  // Hero cinematic video reveal
  var heroSection = document.querySelector('.hero[data-media="video"]');
  if (heroSection) {
    var heroVideoDesktop = heroSection.querySelector('.hero-video--desktop');
    var heroVideoMobile  = heroSection.querySelector('.hero-video--mobile');
    var isMobile = window.innerWidth < 768 ||
                   window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Pick the active video and discard the other to stop network fetch.
    var heroVideo;
    if (isMobile && heroVideoMobile) {
      if (heroVideoDesktop) heroVideoDesktop.remove();
      heroVideo = heroVideoMobile;
    } else {
      if (heroVideoMobile) heroVideoMobile.remove();
      heroVideo = heroVideoDesktop || heroSection.querySelector('.hero-video');
    }

    function revealHero() {
      heroSection.classList.add('hero--revealed');
    }

    if (!heroVideo) {
      revealHero();
    } else if (isMobile && !heroVideoMobile) {
      // Mobile but no mobile video: skip video, reveal immediately.
      heroVideo.remove();
      revealHero();
    } else {
      heroVideo.preload = 'metadata';
      heroVideo.play().catch(function() {});
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

// Kundenstimmen Swiper — Autoplay optional via ACF-Feld "st_autoplay"
// (data-autoplay am .stimmen-swiper-wrap). Bei Autoplay: kein Pfeil/Punkt-DOM
// (siehe stimmen.php), daher hier auch keine navigation/pagination-Konfiguration.
document.addEventListener('DOMContentLoaded', function () {
    if (!document.querySelector('.stimmen-swiper')) {
        return;
    }
    var stimmenWrap = document.querySelector('.stimmen-swiper-wrap');
    var isAutoplay = !!stimmenWrap && stimmenWrap.dataset.autoplay === '1';
    var reduceMotion = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

    var stimmenConfig = {
        slidesPerView: 1.05,
        spaceBetween: 20,
        loop: isAutoplay,
        breakpoints: {
            768: { slidesPerView: 2, spaceBetween: 28 },
        },
        a11y: { enabled: true },
    };

    if (isAutoplay) {
        stimmenConfig.autoplay = reduceMotion
            ? false
            : { delay: 3500, disableOnInteraction: false, pauseOnMouseEnter: true };
    } else {
        stimmenConfig.navigation = { prevEl: '.stimmen-swiper__prev', nextEl: '.stimmen-swiper__next' };
        stimmenConfig.pagination = { el: '.stimmen-swiper__pagination', clickable: true };
    }

    new Swiper('.stimmen-swiper', stimmenConfig);
});

// Einblicke: Foto-Karussell (Swiper). 1 Foto mobil → 3 auf Desktop.
document.addEventListener('DOMContentLoaded', function () {
    if (!document.querySelector('.einblicke-swiper')) {
        return;
    }
    var reduceMotion = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    new Swiper('.einblicke-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        autoplay: reduceMotion
            ? false
            : { delay: 3500, disableOnInteraction: false, pauseOnMouseEnter: true },
        navigation: { prevEl: '.einblicke-swiper__prev', nextEl: '.einblicke-swiper__next' },
        pagination: { el: '.einblicke-swiper__pagination', clickable: true },
        breakpoints: {
            640: { slidesPerView: 2, spaceBetween: 20 },
            1024: { slidesPerView: 3, spaceBetween: 24 },
        },
        a11y: { enabled: true },
    });
});

// Räume: farbige Kreise. Mouseover/Fokus zeigt den Text (CSS). Zusätzlich
// Tap/Klick-Toggle für Touch-Geräte (kein Hover) + Tastatur (Enter/Space).
document.addEventListener('DOMContentLoaded', function () {
    var circles = document.querySelectorAll('.section-zimmer .room.has-desc');
    if (!circles.length) {
        return;
    }
    circles.forEach(function (el) {
        el.addEventListener('click', function () {
            var open = el.classList.toggle('is-open');
            el.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                el.click();
            }
        });
    });
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
    var base = (typeof kcData !== 'undefined' && kcData.themeUri) ? kcData.themeUri : '';
    // Eigene Symbole aus den Theme-Optionen, sonst Theme-Standard.
    var custom = (typeof kcData !== 'undefined' && kcData.floatingSymbols && kcData.floatingSymbols.length) ? kcData.floatingSymbols : null;
    var positions = [
      [8,12],[18,45],[5,70],[88,8],[92,55],[75,30],[30,88],[60,15],[45,65],[80,78]
    ];
    positions.forEach(function(pos, i) {
      var el = document.createElement('div');
      el.className = 'kc-floater';
      var img = document.createElement('img');
      img.src     = custom ? custom[i % custom.length] : base + '/assets/img/symbols/Symbol' + ((i % 5) + 1) + 'a.svg';
      img.alt     = '';
      img.loading = 'lazy';
      el.appendChild(img);
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

/* === „mehr“-Overlays (Leistungen): Slide von rechts, Escape, Scroll-Lock, Fokusfalle, Inert === */
(function () {
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var lastTrigger = null, openEl = null;
  var SEL = 'a[href],button:not([disabled]),input,textarea,select,[tabindex]:not([tabindex="-1"])';

  // Overlays ans Ende von <body> hängen (Portal): so deaktiviert setInert(main) den Dialog NICHT.
  var overlays = document.querySelectorAll('.lsov');
  if (!overlays.length) { return; }
  [].forEach.call(overlays, function (o) { document.body.appendChild(o); });

  function setInert(on) {
    ['#header', 'main', '.mobile-menu', '.site-footer'].forEach(function (sel) {
      var el = document.querySelector(sel);
      if (el) { el.inert = on; }
    });
  }
  function firstFocusable(el) { return el.querySelector(SEL); }

  function openOverlay(id, trigger) {
    var el = document.getElementById(id);
    if (!el) { return; }
    lastTrigger = trigger || null; openEl = el;
    el.hidden = false;
    document.body.classList.add('lsov-lock');
    setInert(true);
    // Reflow erzwingen -> die Transition (translateX 100% -> 0) startet zuverlässig, auch wenn
    // rAF im Hintergrund-Tab pausiert. Fokus ERST nach is-open (visibility:hidden ist nicht fokussierbar).
    void el.offsetWidth;
    el.classList.add('is-open');
    var f = firstFocusable(el); if (f) { f.focus(); }
    el.scrollTop = 0;
  }

  function finishClose(el) {
    el.hidden = true;
    document.body.classList.remove('lsov-lock');
  }
  function closeOverlay() {
    if (!openEl) { return; }
    var el = openEl; openEl = null;
    el.classList.remove('is-open');
    setInert(false);                          // Hintergrund SOFORT reaktivieren ...
    if (lastTrigger) { lastTrigger.focus(); } // ... damit der Fokus zurück auf „mehr“ kann (inert = nicht fokussierbar)
    if (reduce) {
      finishClose(el); // reduced-motion: keine transform-Transition -> sofort freigeben
    } else {
      var done = function (e) {
        if (e && (e.target !== el || e.propertyName !== 'transform')) { return; }
        if (el.classList.contains('is-open')) { return; } // während des Schließens neu geöffnet -> nicht ausblenden
        el.removeEventListener('transitionend', done);
        finishClose(el);
      };
      el.addEventListener('transitionend', done);
      var d = parseInt(getComputedStyle(el).getPropertyValue('--lsov-duration'), 10) || 1050;
      setTimeout(function () {
        if (!el.classList.contains('is-open')) { el.removeEventListener('transitionend', done); finishClose(el); }
      }, d + 250);
    }
  }

  document.addEventListener('click', function (e) {
    var o = e.target.closest('[data-lsov-open]');
    if (o) { e.preventDefault(); openOverlay(o.getAttribute('data-lsov-open'), o); return; }
    if (e.target.closest('[data-lsov-close]')) { closeOverlay(); }
  });
  document.addEventListener('keydown', function (e) {
    if (!openEl) { return; }
    if (e.key === 'Escape') { closeOverlay(); return; }
    if (typeof window.kcTrapTab === 'function') { window.kcTrapTab(e, openEl, SEL); }
  });
}());
