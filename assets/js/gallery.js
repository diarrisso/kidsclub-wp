/**
 * Praxis-Galerie — Alpine-Komponente: Bereich-Filter + Lightbox mit Navigation.
 *
 * Die Kernlogik (Filtern, Blättern mit Clamping) ist DOM-frei und in Node testbar.
 * DOM-gebundene Teile (init/Fokus/Preload/Swipe) sind gekapselt und nur im Browser aktiv.
 */
(function () {
  function createPraxisGallery() {
    return {
      all: [],
      f: 'alle',
      open: false,
      index: 0,
      _trigger: null,
      _touchX: null,

      // ---- reine Logik (Node-testbar) ----
      get list() {
        return this.f === 'alle'
          ? this.all
          : this.all.filter((p) => p.cat === this.f);
      },
      get current() {
        return this.list[this.index] || null;
      },
      get total() {
        return this.list.length;
      },
      get position() {
        return this.total ? this.index + 1 : 0;
      },
      get atStart() {
        return this.index <= 0;
      },
      get atEnd() {
        return this.index >= this.total - 1;
      },
      setFilter(cat) {
        this.f = cat;
      },
      indexOfId(id) {
        return this.list.findIndex((p) => p.id === id);
      },
      next() {
        if (!this.atEnd) {
          this.index += 1;
          this._preloadNeighbors();
        }
      },
      prev() {
        if (!this.atStart) {
          this.index -= 1;
          this._preloadNeighbors();
        }
      },

      // ---- DOM-gebunden (im Browser) ----
      init() {
        const tag = this.$el.querySelector('script[type="application/json"]');
        if (tag) {
          try {
            this.all = JSON.parse(tag.textContent) || [];
          } catch (e) {
            this.all = [];
          }
        }
      },
      openById(id, ev) {
        const i = this.indexOfId(id);
        if (i < 0) {
          return;
        }
        this.index = i;
        this.open = true;
        this._trigger = ev && ev.currentTarget ? ev.currentTarget : null;
        this._preloadNeighbors();
        this.$nextTick(() => {
          if (this.$refs && this.$refs.lbClose) {
            this.$refs.lbClose.focus();
          }
        });
      },
      close() {
        this.open = false;
        if (this._trigger && this._trigger.focus) {
          this._trigger.focus();
        }
      },
      onTouchStart(ev) {
        this._touchX = ev.changedTouches ? ev.changedTouches[0].clientX : null;
      },
      onTouchEnd(ev) {
        if (this._touchX === null || !ev.changedTouches) {
          return;
        }
        const dx = ev.changedTouches[0].clientX - this._touchX;
        if (dx <= -40) {
          this.next();
        } else if (dx >= 40) {
          this.prev();
        }
        this._touchX = null;
      },
      _preloadNeighbors() {
        if (typeof Image === 'undefined') {
          return;
        }
        [this.index - 1, this.index + 1].forEach((i) => {
          const p = this.list[i];
          if (p && p.srcLarge) {
            const im = new Image();
            im.src = p.srcLarge;
          }
        });
      },
    };
  }

  // Browser: VOR Alpine geladen → bei alpine:init registrieren.
  if (typeof document !== 'undefined') {
    document.addEventListener('alpine:init', function () {
      window.Alpine.data('praxisGallery', createPraxisGallery);
    });
  }
  // Node-Test: Factory exportieren.
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { createPraxisGallery };
  }
})();
