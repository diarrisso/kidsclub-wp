/**
 * Praxis-Galerie — Alpine-Komponente: nur Bereich-Filter (keine Lightbox).
 *
 * Beim Filterwechsel wird die Scroll-Position der Galerie-Oberkante verankert,
 * damit das Umschalten die Seite NICHT springen lässt.
 */
(function () {
  function createPraxisGallery() {
    return {
      f: 'alle',
      setFilter(cat) {
        if (cat === this.f) {
          return;
        }
        var top = this.$el ? this.$el.getBoundingClientRect().top : 0;
        this.f = cat;
        if (this.$nextTick) {
          this.$nextTick(
            function () {
              var delta = this.$el.getBoundingClientRect().top - top;
              if (delta) {
                window.scrollBy(0, delta);
              }
            }.bind(this)
          );
        }
      },
    };
  }

  if (typeof document !== 'undefined') {
    document.addEventListener('alpine:init', function () {
      window.Alpine.data('praxisGallery', createPraxisGallery);
    });
  }
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { createPraxisGallery };
  }
})();
