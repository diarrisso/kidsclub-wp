# Praxis-Galerie Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Auf ein einziges Galerie-Layout konsolidieren und eine vollwertige, performante Lightbox (Prev/Next + Filtertreue + Zähler/Caption + A11y) ergänzen.

**Architecture:** `galerie.php` rendert Markup + ein schlankes Foto-JSON; die gesamte Interaktion lebt in `assets/js/gallery.js` als `Alpine.data('praxisGallery')` mit DOM-freier, in Node testbarer Kernlogik. Das Skript wird VOR Alpine geladen (als dessen Abhängigkeit), damit `alpine:init` greift.

**Tech Stack:** PHP 7.4+ (CI 8.4), WordPress, ACF Pro, Alpine.js 3.14 (Standard-Build), hand-authored CSS/JS (kein npm-Build), Node 22 für JS-Tests.

## Global Constraints

- PHP 7.4+ Ziel; CI 8.4. `composer test` (lint + PHPStan Level 5) muss grün bleiben.
- **Kein npm-Build** — JS/CSS von Hand. CSS in **beiden** Dateien pflegen: `assets/css/kidsclub.css` und `assets/css/kidsclub.min.css`.
- **Cache-Busting:** `$ver` in `inc/enqueue.php` bei jeder JS/CSS-Änderung erhöhen (aktueller Stand `3.3.2`).
- **Alpine Standard-Build** (kein CSP). `Alpine.data('praxisGallery')` muss registriert sein, BEVOR Alpine startet → `gallery.js` ist Abhängigkeit von `alpinejs` und ebenfalls `defer`.
- **Ausgabe escapen:** `esc_url`, `esc_attr`, `esc_js`, `esc_html`. JSON via `wp_json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP)` (sicher in `<script>`).
- **CPT/Taxonomie unverändert:** `praxis_foto` (Beitragsbild = Foto, `menu_order`), Taxonomie `bereich`.
- **Nicht-destruktiv:** Live nutzt nur Layout `galerie`; `praxis` ist ungenutzt und wird entfernt.
- Server lokal: `http://localhost:8090`. JS-Test: `node tests/gallery-test.js`. JS-Syntax: `node --check assets/js/gallery.js`.

---

## File Structure

- **`template-parts/layouts/praxis.php`** — wird gelöscht.
- **`inc/blocks.php`** — Layout-Block `layout_praxis` (~Z. 368–387) wird entfernt.
- **`assets/js/gallery.js`** (neu) — `createPraxisGallery()` Kernlogik + Alpine-Registrierung + Node-Export.
- **`tests/gallery-test.js`** (neu) — Node-Test der Kernlogik.
- **`inc/enqueue.php`** — `gallery.js` vor Alpine einreihen; `$ver` erhöhen.
- **`template-parts/layouts/galerie.php`** — Markup + JSON + Lightbox-DOM, `x-data="praxisGallery"`.
- **`assets/css/kidsclub.css`** + **`assets/css/kidsclub.min.css`** — Lightbox-Bedienelemente.

---

### Task 1: Layout `praxis` entfernen (Konsolidierung)

**Files:**
- Delete: `template-parts/layouts/praxis.php`
- Modify: `inc/blocks.php` (Block `layout_praxis`, ~Z. 368–387)

**Interfaces:**
- Consumes: nichts.
- Produces: nur noch Layout `galerie` existiert.

- [ ] **Step 1: Template löschen**

```bash
git rm template-parts/layouts/praxis.php
```

- [ ] **Step 2: Layout-Block in `inc/blocks.php` entfernen**

Den gesamten `'layout_praxis' => [ … ],`-Block entfernen. Er beginnt mit:

```php
								'layout_praxis'     => [
									'key'        => 'layout_praxis',
									'name'       => 'praxis',
									'label'      => 'Praxis-Galerie',
```

und endet mit dem schließenden `],` vor dem nächsten Kommentar `/* ---------- TEAM ---------- */`. Den `message`-Subfield (`field_kc_prx_hinweis`) und die `prx_*`-Felder mitentfernen. Den Kommentar `/* ---------- TEAM ---------- */` und den `layout_team`-Block unverändert lassen.

- [ ] **Step 3: Verifizieren, dass keine Referenz mehr existiert**

Run:
```bash
grep -rn "layout_praxis\|'name'       => 'praxis'\|prx_eyebrow\|prx_title\|layouts/praxis" inc/ template-parts/
```
Expected: keine Treffer (Exit 1 / leere Ausgabe). Das Wort „praxis" darf weiter vorkommen (CPT `praxis_foto`, Layout `galerie` liest dieses) — nur die Layout-`praxis`-Referenzen müssen weg.

- [ ] **Step 4: Lint + PHPStan**

Run: `php -l inc/blocks.php && composer stan`
Expected: keine Syntaxfehler; PHPStan `[OK] No errors`.

- [ ] **Step 5: Front-Check (Galerie rendert weiter)**

Run: `curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8090/` und `curl -s http://localhost:8090/ | grep -c 'section-galerie'`
Expected: `200`; `section-galerie` ≥ `1` (Galerie unverändert vorhanden).

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "refactor(galerie): remove unused praxis layout, consolidate on galerie"
```

---

### Task 2: `gallery.js` — Kernlogik + Node-Test (TDD)

**Files:**
- Create: `assets/js/gallery.js`
- Test: `tests/gallery-test.js`

**Interfaces:**
- Consumes: nichts.
- Produces: `createPraxisGallery()` → Objekt mit Feldern `all`, `f`, `open`, `index` und API: getters `list`/`current`/`total`/`position`/`atStart`/`atEnd`; methods `setFilter(cat)`, `indexOfId(id)`, `next()`, `prev()`, `openById(id, ev)`, `close()`, `onTouchStart(ev)`, `onTouchEnd(ev)`, `init()`. Browser registriert es als `Alpine.data('praxisGallery')`.

- [ ] **Step 1: Den fehlschlagenden Test schreiben**

Create `tests/gallery-test.js`:

```js
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

console.log(fail === 0 ? '\nALL PASS' : '\n' + fail + ' FAILED');
process.exit(fail === 0 ? 0 : 1);
```

- [ ] **Step 2: Test laufen lassen, Fehlschlag bestätigen**

Run: `node tests/gallery-test.js`
Expected: FAIL — `Cannot find module '../assets/js/gallery.js'` (Datei existiert noch nicht).

- [ ] **Step 3: `assets/js/gallery.js` implementieren**

Create `assets/js/gallery.js`:

```js
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
```

- [ ] **Step 4: Test + Syntax prüfen**

Run: `node tests/gallery-test.js && node --check assets/js/gallery.js`
Expected: alle `PASS`, `ALL PASS`, Exit 0; `node --check` ohne Ausgabe (Syntax ok).

- [ ] **Step 5: Commit**

```bash
git add assets/js/gallery.js tests/gallery-test.js
git commit -m "feat(galerie): gallery.js Alpine component (filter + lightbox nav) + node test"
```

---

### Task 3: `gallery.js` vor Alpine einreihen + Cache-Bust

**Files:**
- Modify: `inc/enqueue.php` (Block ~Z. 32–37)

**Interfaces:**
- Consumes: `assets/js/gallery.js`.
- Produces: `gallery.js` wird vor `alpine.min.js` ausgegeben; `praxisGallery` ist registrierbar.

- [ ] **Step 1: Enqueue anpassen**

In `inc/enqueue.php` den Alpine-Block ersetzen. Vorher:

```php
		// 5. Alpine.js — selbst gehostet, requis pour les accordéons (eltern, faq)
		wp_enqueue_script( 'alpinejs', $dir . '/assets/vendor/alpine.min.js', [], '3.14.0', true );
		wp_script_add_data( 'alpinejs', 'defer', true );
```

Nachher:

```php
		// 5. Galerie-Komponente — MUSS vor Alpine laufen (registriert Alpine.data
		//    bei 'alpine:init'). Als Abhängigkeit von Alpine => garantierte Reihenfolge.
		wp_enqueue_script( 'kc-gallery', $dir . '/assets/js/gallery.js', [], $ver, true );
		wp_script_add_data( 'kc-gallery', 'defer', true );

		// 6. Alpine.js — selbst gehostet, requis pour les accordéons (eltern, faq)
		wp_enqueue_script( 'alpinejs', $dir . '/assets/vendor/alpine.min.js', [ 'kc-gallery' ], '3.14.0', true );
		wp_script_add_data( 'alpinejs', 'defer', true );
```

- [ ] **Step 2: `$ver` erhöhen**

In `inc/enqueue.php` Zeile 15: `$ver = '3.3.2';` → `$ver = '3.4.0';`

- [ ] **Step 3: Lint**

Run: `php -l inc/enqueue.php`
Expected: keine Syntaxfehler.

- [ ] **Step 4: Ladereihenfolge im HTML prüfen**

Run:
```bash
curl -s http://localhost:8090/ | grep -nE 'gallery\.js|alpine\.min\.js'
```
Expected: die Zeile mit `gallery.js` erscheint VOR der Zeile mit `alpine.min.js` (kleinere Zeilennummer). Beide mit `defer`.

- [ ] **Step 5: Konsole prüfen (keine Fehler)**

Browser `http://localhost:8090/` öffnen, DevTools-Konsole: keine Fehler. (Galerie nutzt noch das alte inline `x-data` bis Task 4 — das ist ok, `praxisGallery` ist nur registriert, nicht genutzt.)

- [ ] **Step 6: Commit**

```bash
git add inc/enqueue.php
git commit -m "feat(galerie): enqueue gallery.js before Alpine, bump assets to 3.4.0"
```

---

### Task 4: `galerie.php` — Markup + JSON + Lightbox mit Navigation

**Files:**
- Modify (Rewrite): `template-parts/layouts/galerie.php`

**Interfaces:**
- Consumes: `Alpine.data('praxisGallery')` (Task 2), Felder `gl_eyebrow`/`gl_title`/`gl_text`.
- Produces: Galerie-Section mit JSON-Daten + Lightbox-DOM.

- [ ] **Step 1: `galerie.php` neu schreiben**

Ersetze den gesamten Inhalt von `template-parts/layouts/galerie.php` durch:

```php
<?php
/**
 * Layout: Galerie mit Bereich-Filtern und Lightbox (Alpine-Komponente praxisGallery).
 *
 * Source : CPT `praxis_foto` (Beitragsbild = Foto, Sortierung menu_order).
 * Filter : Taxonomie `bereich` → Chips. Lightbox: Prev/Next, Tastatur, Swipe,
 * Zähler + Caption, filtertreue Navigation. Logik in assets/js/gallery.js.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'gl_eyebrow' );
$title   = get_sub_field( 'gl_title' );
$text    = get_sub_field( 'gl_text' );

/* Fotos aus dem CPT sammeln. */
$photos     = [];
$cats_order = [];

$foto_posts = get_posts(
	[
		'post_type'      => 'praxis_foto',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]
);

foreach ( $foto_posts as $foto ) {
	$img_id = get_post_thumbnail_id( $foto );
	if ( ! $img_id ) {
		continue;
	}
	$terms = get_the_terms( $foto, 'bereich' );
	$slug  = '';
	if ( $terms && ! is_wp_error( $terms ) ) {
		$slug = $terms[0]->slug;
		if ( ! isset( $cats_order[ $slug ] ) ) {
			$cats_order[ $slug ] = $terms[0]->name;
		}
	}
	$large = wp_get_attachment_image_src( $img_id, 'large' );
	$full  = wp_get_attachment_image_src( $img_id, 'full' );
	if ( ! $large ) {
		continue;
	}
	$photos[] = [
		'id'       => (int) $img_id,
		'cat'      => $slug,
		'alt'      => get_the_title( $foto ),
		'srcLarge' => $large[0],
		'srcFull'  => $full ? $full[0] : $large[0],
		'w'        => (int) $large[1],
		'h'        => (int) $large[2],
	];
}

/* JSON sicher in <script> einbetten (Tags/Ampersands hexen). */
$photos_json = wp_json_encode( $photos, JSON_HEX_TAG | JSON_HEX_AMP );
?>
<section
	class="section-galerie reveal"
	id="galerie"
	x-data="praxisGallery"
>
	<script type="application/json"><?php echo $photos_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode mit JSON_HEX_TAG|JSON_HEX_AMP ?></script>

	<div class="container">

		<div class="section-head center reveal">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $text ) : ?>
				<p class="lead"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cats_order ) : ?>
		<div class="chips" role="group" aria-label="<?php esc_attr_e( 'Galerie filtern', 'kidsclub' ); ?>">
			<button type="button" class="chip"
				:class="f === 'alle' && 'chip--active'"
				:aria-pressed="f === 'alle'"
				@click="setFilter('alle')"><?php esc_html_e( 'Alle', 'kidsclub' ); ?></button>
			<?php foreach ( $cats_order as $slug => $label ) : ?>
			<button type="button" class="chip"
				:class="f === '<?php echo esc_js( $slug ); ?>' && 'chip--active'"
				:aria-pressed="f === '<?php echo esc_js( $slug ); ?>'"
				@click="setFilter('<?php echo esc_js( $slug ); ?>')"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( $photos ) : ?>
		<div class="praxis-gallery" :class="f !== 'alle' && 'praxis-gallery--flat'">
			<?php foreach ( $photos as $p ) : ?>
			<figure
				class="praxis-gallery__item"
				role="button"
				tabindex="0"
				aria-label="<?php echo esc_attr( $p['alt'] ); ?>"
				style="cursor:pointer"
				x-show="f === 'alle'<?php echo $p['cat'] ? " || f === '" . esc_js( $p['cat'] ) . "'" : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_js appliqué ?>"
				@click="openById(<?php echo (int) $p['id']; ?>, $event)"
				@keydown.enter="openById(<?php echo (int) $p['id']; ?>, $event)"
				@keydown.space.prevent="openById(<?php echo (int) $p['id']; ?>, $event)"
			>
				<?php
				echo wp_get_attachment_image(
					$p['id'],
					'large',
					false,
					[
						'loading'  => 'lazy',
						'decoding' => 'async',
						'alt'      => $p['alt'],
					]
				);
				?>
			</figure>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

	</div>

	<!-- Lightbox -->
	<div
		class="gal-lightbox"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Bild-Vorschau', 'kidsclub' ); ?>"
		x-show="open"
		x-cloak
		@keydown.escape.window="close()"
		@keydown.arrow-left.window="open && prev()"
		@keydown.arrow-right.window="open && next()"
		@click.self="close()"
	>
		<button type="button" class="gal-lightbox__close" x-ref="lbClose"
			aria-label="<?php esc_attr_e( 'Schließen', 'kidsclub' ); ?>" @click="close()">&times;</button>

		<button type="button" class="gal-lightbox__nav gal-lightbox__nav--prev"
			@click="prev()" :disabled="atStart" :aria-disabled="atStart"
			aria-label="<?php esc_attr_e( 'Vorheriges Bild', 'kidsclub' ); ?>">&lsaquo;</button>

		<figure class="gal-lightbox__figure">
			<img
				:src="current && current.srcLarge"
				:alt="current && current.alt"
				:width="current && current.w"
				:height="current && current.h"
				@touchstart="onTouchStart($event)"
				@touchend="onTouchEnd($event)">
			<figcaption class="gal-lightbox__caption">
				<span class="gal-lightbox__counter" aria-live="polite"
					x-text="'<?php esc_html_e( 'Bild', 'kidsclub' ); ?> ' + position + ' <?php esc_html_e( 'von', 'kidsclub' ); ?> ' + total"></span>
				<span class="gal-lightbox__alt" x-text="current && current.alt"></span>
			</figcaption>
		</figure>

		<button type="button" class="gal-lightbox__nav gal-lightbox__nav--next"
			@click="next()" :disabled="atEnd" :aria-disabled="atEnd"
			aria-label="<?php esc_attr_e( 'Nächstes Bild', 'kidsclub' ); ?>">&rsaquo;</button>
	</div>

</section>
```

- [ ] **Step 2: Lint**

Run: `php -l template-parts/layouts/galerie.php`
Expected: keine Syntaxfehler.

- [ ] **Step 3: Markup-Check (JSON + x-data vorhanden, kein altes Lightbox-Inline)**

Run:
```bash
curl -s http://localhost:8090/ | grep -oE 'x-data="praxisGallery"|gal-lightbox__nav|application/json' | sort | uniq -c
```
Expected: `x-data="praxisGallery"` (1×), `gal-lightbox__nav` (≥2×), `application/json` (1×).

- [ ] **Step 4: Commit**

```bash
git add template-parts/layouts/galerie.php
git commit -m "feat(galerie): lightbox with prev/next, filter-aware nav, counter & caption"
```

---

### Task 5: CSS für Lightbox-Bedienelemente

**Files:**
- Modify: `assets/css/kidsclub.css` (nach den `.gal-lightbox`-Regeln, ~Z. 465)
- Modify: `assets/css/kidsclub.min.css` (gleiche Regeln, minifiziert)
- Modify: `inc/enqueue.php` (`$ver`)

**Interfaces:**
- Consumes: Markup-Klassen aus Task 4.
- Produces: gestylte Prev/Next, Zähler, Caption.

- [ ] **Step 1: Regeln in `assets/css/kidsclub.css` ergänzen**

Direkt nach der Regel `.gal-lightbox__close:focus{...}` (~Z. 465) einfügen:

```css
.gal-lightbox__figure{margin:0;display:flex;flex-direction:column;align-items:center;gap:14px;max-width:94vw}
.gal-lightbox__nav{position:absolute;top:50%;transform:translateY(-50%);width:52px;height:52px;border:none;border-radius:50%;background:rgba(255,255,255,.14);color:#fff;font-size:2rem;line-height:1;cursor:pointer;display:grid;place-items:center;transition:background .25s var(--ease)}
.gal-lightbox__nav:hover{background:var(--magenta)}
.gal-lightbox__nav:focus{outline:3px solid var(--magenta);outline-offset:3px}
.gal-lightbox__nav--prev{left:18px}
.gal-lightbox__nav--next{right:18px}
.gal-lightbox__nav:disabled{opacity:.3;cursor:default;background:rgba(255,255,255,.14)}
.gal-lightbox__caption{display:flex;flex-direction:column;align-items:center;gap:3px;color:#fff;text-align:center}
.gal-lightbox__counter{font-weight:700;font-size:.95rem;opacity:.85}
.gal-lightbox__alt{font-size:1rem}
@media (prefers-reduced-motion:reduce){.gal-lightbox__nav{transition-duration:0ms}}
```

- [ ] **Step 2: Dieselben Regeln in `assets/css/kidsclub.min.css` anhängen**

Die obigen Regeln minifiziert (eine Zeile, keine überflüssigen Leerzeichen) an die `.gal-lightbox`-Gruppe der min-Datei anhängen. Beispiel-Splice (an die bestehende `.gal-lightbox__close:focus{...}`-Regel direkt anschließen):

```css
.gal-lightbox__figure{margin:0;display:flex;flex-direction:column;align-items:center;gap:14px;max-width:94vw}.gal-lightbox__nav{position:absolute;top:50%;transform:translateY(-50%);width:52px;height:52px;border:none;border-radius:50%;background:rgba(255,255,255,.14);color:#fff;font-size:2rem;line-height:1;cursor:pointer;display:grid;place-items:center;transition:background .25s var(--ease)}.gal-lightbox__nav:hover{background:var(--magenta)}.gal-lightbox__nav:focus{outline:3px solid var(--magenta);outline-offset:3px}.gal-lightbox__nav--prev{left:18px}.gal-lightbox__nav--next{right:18px}.gal-lightbox__nav:disabled{opacity:.3;cursor:default;background:rgba(255,255,255,.14)}.gal-lightbox__caption{display:flex;flex-direction:column;align-items:center;gap:3px;color:#fff;text-align:center}.gal-lightbox__counter{font-weight:700;font-size:.95rem;opacity:.85}.gal-lightbox__alt{font-size:1rem}@media (prefers-reduced-motion:reduce){.gal-lightbox__nav{transition-duration:0ms}}
```

- [ ] **Step 3: `$ver` erhöhen + Verifizieren**

In `inc/enqueue.php`: `$ver = '3.4.0';` → `$ver = '3.4.1';`

Run:
```bash
grep -c 'gal-lightbox__nav' assets/css/kidsclub.css assets/css/kidsclub.min.css
tail -c 30 assets/css/kidsclub.min.css
```
Expected: beide CSS ≥ `1`; das Ende der min-Datei sieht wie valides CSS aus (endet auf `}`).

- [ ] **Step 4: Regressions-Check**

Run: `composer stan && php -l inc/enqueue.php`
Expected: `[OK] No errors`; keine Syntaxfehler.

- [ ] **Step 5: Commit**

```bash
git add assets/css/kidsclub.css assets/css/kidsclub.min.css inc/enqueue.php
git commit -m "feat(galerie): lightbox nav/counter/caption styles, bump assets to 3.4.1"
```

---

### Task 6: Test-Seed + End-to-End-Verifikation (Chrome)

**Files:**
- keine Code-Änderung (Test-Daten lokal, nicht committet).

**Interfaces:**
- Consumes: das fertige Layout (Tasks 1–5).
- Produces: bestätigtes Verhalten.

- [ ] **Step 1: Test-Bereiche + Fotos seeden (WP-CLI, lokal)**

Run (vom WP-Root `~/PhpstormProjects/kidsclub-wp`):

```bash
cd ~/PhpstormProjects/kidsclub-wp
wp term create bereich "Empfang" --slug=empfang
wp term create bereich "Wartezimmer" --slug=wartezimmer
wp term create bereich "Behandlung" --slug=behandlung
# 6 Test-Fotos aus vorhandenen Attachments (IDs der Mediathek ermitteln)
IDS=$(wp post list --post_type=attachment --post_mime_type=image --field=ID --posts_per_page=6 --format=ids)
i=0
for A in $IDS; do
  i=$((i+1))
  B=empfang; [ $i -gt 2 ] && B=wartezimmer; [ $i -gt 4 ] && B=behandlung
  PID=$(wp post create --post_type=praxis_foto --post_status=publish --post_title="Test Foto $i" --menu_order=$i --porcelain)
  wp post meta update $PID _thumbnail_id $A
  wp post term set $PID bereich $B
done
wp post list --post_type=praxis_foto --fields=ID,post_title,menu_order
```
Expected: 3 Bereiche angelegt; 6 `praxis_foto` mit Beitragsbild und je einem Bereich.

- [ ] **Step 2: Galerie laden + JSON prüfen**

Browser `http://localhost:8090/` (Hard-Reload Cmd+Shift+R), zur Galerie scrollen. DevTools-Konsole: keine Fehler. Im DOM: `<script type="application/json">` enthält 6 Einträge mit `srcLarge`/`w`/`h`.

- [ ] **Step 3: Funktionale Prüfung**

- Chips: „Alle" zeigt 6; ein Bereich filtert korrekt; aktiver Chip `chip--active`.
- Klick auf Foto → Lightbox öffnet, Fokus auf Schließen-Button.
- Prev/Next per Button und `←`/`→`; **bei aktivem Bereich nur dessen Fotos** (z. B. „Empfang" → Zähler „Bild X von 2").
- An den Grenzen sind Prev bzw. Next `disabled` (ausgegraut).
- Zähler „Bild X von Y" aktualisiert; Caption = Foto-Titel.
- `Esc`/Klick auf Hintergrund schließt; Fokus zurück auf das ausgelöste Foto.
- Touch-Swipe (DevTools Device-Toolbar): links = next, rechts = prev.

- [ ] **Step 4: Performance-Stichprobe**

- Network-Panel: beim Laden werden NICHT alle Vollbilder geladen; Lightbox-Bild lädt erst beim Öffnen; beim Blättern ist das nächste Bild dank Preload bereits da.
- Keine sichtbaren Layout-Shifts (Bilder haben `width`/`height`).

- [ ] **Step 5: Optional aufräumen**

Test-Daten dürfen lokal bleiben (helfen bei Teil B). Falls Entfernen gewünscht:
```bash
wp post list --post_type=praxis_foto --field=ID | xargs -r -n1 wp post delete --force
```

(Keine Code-Commit in dieser Task — reine Verifikation.)

---

## Self-Review

**Spec coverage:**
- Konsolidierung auf ein Layout / `praxis` entfernen → Task 1. ✔
- Trennung Markup/Logik (`galerie.php` + `gallery.js`) → Task 2 + Task 4. ✔
- Ladereihenfolge vor Alpine → Task 3. ✔
- Prev/Next (Button, Tastatur, Swipe) → Task 2 (Logik) + Task 4 (DOM) + Task 5 (Style). ✔
- Filtertreue Navigation → Task 2 (`list`/`indexOfId` über gefilterte Liste). ✔
- Zähler + Caption → Task 4 (`position`/`total`/`current.alt`). ✔
- A11y (dialog/modal, Fokusfalle via lbClose-Fokus + Rückgabe, aria-live, disabled) → Task 4. ✔
- Performance (lazy/async, explizite w/h, on-demand + Preload, schlankes JSON) → Task 2 (`_preloadNeighbors`) + Task 4. ✔
- CSS beide Dateien + reduced-motion + Cache-Bust → Task 5. ✔
- Tests (Node-Logik + Chrome E2E) → Task 2 + Task 6. ✔

**Type/Name consistency:** `praxisGallery`, `createPraxisGallery`, `setFilter`, `openById`, `indexOfId`, `position`, `total`, `atStart`, `atEnd`, `current`, `onTouchStart`, `onTouchEnd`, `lbClose` identisch in gallery.js (Task 2) und galerie.php (Task 4). JSON-Felder `id/cat/alt/srcLarge/srcFull/w/h` identisch zwischen PHP-Erzeugung (Task 4) und JS-Nutzung (Task 2/4). ✔

**Placeholder scan:** kein TBD/TODO; jeder Code-Schritt enthält vollständigen Code; Befehle mit erwarteter Ausgabe. ✔
