# Praxis-Galerie — Design-Spec

**Datum:** 2026-06-23
**Branch:** `feature/praxis-galerie`
**Status:** validiert, bereit für Implementierungsplan

## Ziel

Die Praxis-Foto-Galerie zu **einem** gepflegten Layout konsolidieren und um eine
vollwertige Lightbox erweitern — mit Fokus auf **Performance** (ausdrückliche
Vorgabe des Auftraggebers).

Auslöser: Es existieren **zwei** fast identische Layouts (`praxis.php`,
`galerie.php`), die beide aus demselben CPT `praxis_foto` lesen. Auf der Startseite
(lokal **und** Produktion) wird ausschließlich **`galerie`** verwendet; `praxis`
kommt in keiner Seitenrevision vor. `praxis.php` hat zudem keine Lightbox.

## Ausgangslage (Ist-Zustand)

- **CPT `praxis_foto`** (`inc/cpt/praxis.php`): intern (kein Public-Single),
  `supports` Titel + Beitragsbild (= das Foto) + `page-attributes` (Sortierung via
  `menu_order`). Taxonomie **`bereich`** (hierarchisch) steuert die Filter-Chips.
  **Bleibt unverändert.**
- **`galerie.php`** (Layout `galerie`): Section + Filter-Chips (Alpine, inline
  `x-data`) + Grid (`.praxis-gallery`) + **Lightbox** (`.gal-lightbox`, nur
  Öffnen/Schließen, keine Navigation). Felder: `gl_eyebrow`, `gl_title`, `gl_text`.
- **`praxis.php`** (Layout `praxis`): praktisch dieselbe Galerie **ohne** Lightbox,
  plus toter Fallback-Code (`prx_photos`/`prx_cats`/`gallery` — diese Felder sind in
  `inc/blocks.php` nicht mehr definiert). Felder: `prx_eyebrow`, `prx_title` + ein
  Message-Feld.
- **Alpine.js 3.14.0**, Standard-Build (kein CSP), selbst gehostet
  (`assets/vendor/alpine.min.js`), `defer` geladen, **auto-start**. Aktuell keine
  `Alpine.data()`-Komponenten — alles inline `x-data`.
- CSS vorhanden: `.chips`/`.chip`, `.praxis-gallery`(+`__item`, `--flat`,
  First-Child-2×2), `.gal-lightbox`(+`__close`). **Fehlend:** Prev/Next, Zähler,
  Bildunterschrift.

## Lösungsansatz

### 1. Konsolidierung: nur ein Layout

- **Entfernen** von `template-parts/layouts/praxis.php`.
- **Entfernen** des Layout-Blocks `layout_praxis` (Zeilen ~368–387) in
  `inc/blocks.php`. Da keine Live-Revision `praxis` nutzt, sind die `prx_*`-Felder
  inhaltlich verwaist → Entfernen ist in der Praxis nicht-destruktiv. Verwaiste
  Postmeta bleibt inert in der DB (keine Migration nötig).
- `galerie.php` ist fortan **das** Galerie-Layout.

### 2. Trennung Markup / Logik

**`galerie.php`** rendert:
- Section + Head (`gl_eyebrow`/`gl_title`/`gl_text`) + Chips + Grid (wie bisher).
- Einen `<script type="application/json">`-Block mit den Foto-Daten als
  **schlankes JSON-Array**, in Galerie-Reihenfolge:
  `[{ "id", "cat", "alt", "srcLarge", "srcFull", "w", "h" }, …]`.
  `srcLarge` = `large`-Größe (Lightbox-Anzeige), `srcFull` = `full` (nur falls
  später Zoom nötig — vorerst zeigt die Lightbox `srcLarge`), `w`/`h` aus den
  `large`-Metadaten für korrektes Seitenverhältnis.
- Die Lightbox-DOM-Hülle (Dialog, Bild, Prev/Next, Zähler, Caption, Close).

**`assets/js/gallery.js`** (neu) registriert die Logik:
```js
document.addEventListener('alpine:init', () => {
  Alpine.data('praxisGallery', () => ({ /* state + methods */ }));
});
```
- ⚠️ **Ladereihenfolge:** `gallery.js` MUSS **vor** `alpine.min.js` enqueued werden,
  sonst ist `alpine:init` bereits gefeuert und `Alpine.data` greift nicht. In
  `inc/enqueue.php` `gallery.js` ohne Abhängigkeit registrieren und **vor** dem
  `alpinejs`-`wp_enqueue_script` aufrufen (frühere Registrierung → frühere
  Ausführung bei gleichem `defer`). `$ver` erhöhen.
- Daten-Übergabe: `init()` liest das JSON aus dem `<script type="application/json">`
  im eigenen Wurzelelement (`this.$el.querySelector(...)`) und parst es einmal.

### 3. Lightbox-Verhalten (die Verbesserungen)

State: `f` (aktiver Bereich, Default `'alle'`), `open` (bool), `index` (Position in
der **gefilterten** Liste), `all` (alle Fotos), getter `list` (= nach `f`
gefilterte Fotos), getter `current` (= `list[index]`).

- **Filtertreue Navigation:** Chips setzen `f`; `list` wird daraus abgeleitet.
  `next()`/`prev()` laufen nur über `list` (mit Wrap-Around oder Clamping —
  **Clamping** gewählt: an den Enden keine Bewegung, Buttons werden `disabled`).
- **Öffnen:** Klick/Enter/Space auf ein Grid-Item setzt `index` auf die Position
  **innerhalb von `list`** und `open = true`.
- **Bedienung:** Prev/Next-Buttons; Tastatur `←`/`→` (nur wenn `open`), `Esc`
  schließt; **Touch-Swipe** (links/rechts) auf dem Bild (touchstart/-end,
  Schwellwert ~40px).
- **Zähler + Caption:** „Bild {index+1} von {list.length}" und `current.alt` als
  Bildunterschrift.
- **A11y:** `role="dialog"`, `aria-modal="true"`; **Fokusfalle** in der Lightbox
  (Tab zyklisch zwischen Prev/Next/Close); Fokus-Rückgabe an das auslösende
  Grid-Item beim Schließen; Zähler als `aria-live="polite"`; Prev/Next mit
  `aria-label`; an Listengrenzen `disabled` + `aria-disabled`.

### 4. Performance (ausdrückliche Priorität)

- **Grid-Thumbnails:** Größe `large`, `loading="lazy"`, `decoding="async"`,
  **explizite `width`/`height`** → kein Layout-Shift (CLS = 0).
- **Lightbox lädt on demand:** Das große Bild wird erst beim Öffnen gesetzt
  (`:src`), nie alle Vollbilder vorab. **Nachbarn vorladen:** beim Öffnen/Blättern
  `new Image().src` für `list[index±1].srcLarge` → flüssiges Blättern ohne
  Vorab-Last.
- **Schlankes JSON**, keine Drittanbieter-JS (Alpine bereits vorhanden), keine
  zusätzlichen Requests.
- `gallery.js` klein und nur dort relevant; misst sich an Core Web Vitals.

### 5. CSS

In `assets/css/kidsclub.css` **und** `assets/css/kidsclub.min.css` ergänzen
(kein npm-Build):
- `.gal-lightbox__nav` (Prev/Next, links/rechts mittig), `:disabled`-Stil,
  `:focus`-Outline (wie `.gal-lightbox__close`).
- `.gal-lightbox__counter`, `.gal-lightbox__caption` (unter dem Bild, hell auf
  dunklem Overlay).
- `@media (prefers-reduced-motion: reduce)`: Übergänge aus.
- `$ver` in `inc/enqueue.php` erhöhen (Cache-Busting, beide Assets).

## Komponentengrenzen

- **`galerie.php`** — *Was:* sammelt Fotos aus `praxis_foto` und rendert
  Markup + JSON. *Abhängigkeit:* CPT/Taxonomie, `gl_*`-Felder.
- **`assets/js/gallery.js`** — *Was:* gesamte Lightbox-/Filter-Interaktion als
  `Alpine.data('praxisGallery')`. *Nutzung:* `x-data="praxisGallery"` im Section-Tag.
  *Abhängigkeit:* Alpine (vor diesem Skript gestartet — Reihenfolge beachtet).
- **CSS** — Präsentation der neuen Lightbox-Bedienelemente.

## Tests / Verifikation

- `composer test` (lint + PHPStan Level 5) grün; `php -l` über alle Theme-Dateien.
- Chrome-Sichtprüfung auf `http://localhost:8090`:
  - Galerie rendert; Chips filtern; Lightbox öffnet per Klick/Enter/Space.
  - Prev/Next per Button, `←`/`→`, Swipe; **bei aktivem Bereich nur dessen Fotos**.
  - Zähler „Bild X von Y" korrekt; Caption sichtbar; an Grenzen Buttons `disabled`.
  - `Esc`/Hintergrund schließt; Fokus kehrt zum Auslöser zurück.
  - Keine Konsolenfehler; `Alpine.data('praxisGallery')` wird gefunden
    (Reihenfolge-Falle vermieden).
  - Performance: keine sichtbaren Layout-Shifts; Vollbilder erst on demand geladen
    (Network-Panel).
- ⚠️ Hinweis: Lokal existiert nur **1** `praxis_foto` — für aussagekräftige
  Lightbox-/Filter-Tests vorab einige Test-Fotos + Bereiche anlegen (Teil B), oder
  temporär per Seed.

## Nicht im Scope (Teil B — danach)

- **Inhalt pflegen:** Bereiche (Empfang, Wartezimmer, Behandlung, …) als Terme
  anlegen und Fotos im Admin hochladen — lokal und Produktion. Reine Redaktion,
  kein Code. Profitiert direkt von diesem Layout.
- Kein Zoom/Pinch in der Lightbox, kein Deep-Linking einzelner Fotos (YAGNI).
