# Section-Hintergründe — Design-Spec

**Datum:** 2026-06-23
**Branch (geplant):** `feature/section-background`
**Status:** validiert, bereit für Implementierungsplan

## Ziel

Die optionalen Hintergrundbilder pro Section (ACF) so überarbeiten, dass:

1. Das Bild **direkt auf der `<section>`** sitzt — kein injizierter `<div>` mehr.
2. Ein **zentraler PHP-Helper** den Style erzeugt — keine Code-Duplikation.
3. Deckkraft, Größe und Position **pro Section** einstellbar sind (ACF-Felder).

Auslöser: Das aktuelle System ist für alle Sections uniform (`opacity:.06`,
`background-size:115% auto`, `background-position:center top`) und damit zu
unscheinbar, falsch dimensioniert und nicht pro Section justierbar.

## Ausgangslage (Ist-Zustand)

- `template-parts/flexible.php` liest pro Row `background_image` + `background_color`,
  puffert das Layout-Markup via `ob_start()` und injiziert per **einer** Regex-Passe
  einen `<div class="kc-section-bg__img">` als erstes Kind der `<section>` plus ein
  inline `background-color` auf der `<section>`.
- `assets/css/kidsclub.css` rendert diesen Div dezent: `position:absolute;inset:0;
  z-index:0; opacity:.06; background-size:115% auto; background-position:center top`.
- `inc/blocks.php` stellt die Felder über `kc_bg_field($layout)` und
  `kc_bg_color_field($layout)` bereit — **eindeutiger Key je Layout**, aufgerufen in
  allen 13 Layouts (`hero`, `willkommen`, `leistungen`, `zimmer`, `ablauf`, `galerie`,
  `praxis`, `team`, `eltern`, `stimmen`, `faq`, `termin`, `kontakt`).

**Warum der separate Div existiert:** Er erlaubt `opacity` nur auf dem Bild, ohne den
Section-Inhalt (Text) transparent zu machen. Verschiebt man `background-image` direkt
auf die `<section>`, ist `opacity` nicht mehr nutzbar (würde den Inhalt mit verblassen).

## Lösungsansatz

### Kerntechnik: Deckkraft ohne `opacity` — Schleier-Gradient

Statt `opacity` wird die Abschwächung über einen **Farb-Schleier in `linear-gradient`**
erzeugt, direkt als `background-image` der Section:

```
background-image:
  linear-gradient(rgba(r,g,b,A), rgba(r,g,b,A)),
  url(<bild>);
```

- `A = 1 − (bg_opacity / 100)` → ein Deckkraft-Wert von 8 % ergibt einen Schleier mit
  Alpha **0.92**; das Bild scheint zu 8 % durch.
- `(r,g,b)` = die `background_color` der Section (hex→rgb), sonst **Weiß** (`255,255,255`).
  Dadurch funktioniert die Abschwächung auf weißem **und** farbigem Section-Hintergrund.
- Der Inhalt bleibt voll deckend, weil ein `background` immer **hinter** dem Inhalt liegt
  — kein `z-index` nötig.

### 1. ACF-Felder pro Section — `inc/blocks.php`

Neue Funktion `kc_bg_settings_fields( $layout )` (gleiches Muster wie `kc_bg_field`),
gibt ein **Array von drei Feldern** mit pro Layout eindeutigen Keys zurück:

| Feld          | Typ    | Werte / Default                                                       |
|---------------|--------|----------------------------------------------------------------------|
| `bg_opacity`  | range  | 0–100, step 1, **Default 8** — „Deckkraft des Hintergrundbilds (%)“   |
| `bg_size`     | select | `cover` · `contain` · `auto` · `115%` (**Default**)                   |
| `bg_position` | select | `center top` (**Default**) · `center` · `center bottom` · `left center` · `right center` |

- Keys: `field_kc_bgopa_<layout>`, `field_kc_bgsize_<layout>`, `field_kc_bgpos_<layout>`.
- **Conditional Logic:** nur sichtbar, wenn `field_kc_bg_<layout>` (Bild) gesetzt ist.
- Eingebunden via Spread `...kc_bg_settings_fields( '<layout>' )` direkt nach
  `kc_bg_color_field( '<layout>' )` in allen 13 Layouts.
- **Nicht-destruktiv / abwärtskompatibel:** bestehende Inhalte ohne diese Felder greifen
  auf die Defaults im Helper zurück.

### 2. Zentraler Helper — neu: `inc/section-bg.php`

```php
function kc_section_bg_style(): string
```

- Liest die Sub-Fields der **aktuellen Row**: `background_image`, `background_color`,
  `bg_opacity`, `bg_size`, `bg_position`.
- Baut den Schleier-Gradient (Farbe aus `background_color`, sonst Weiß) und hängt
  `url(<bild>)` an; setzt `background-size`, `background-position`, `background-repeat:no-repeat`.
- Ohne Bild, aber mit Farbe → nur `background-color`.
- Ohne beides → leerer String.
- Rückgabe: fertiges ` style="…"`-Attribut, vollständig escaped (`esc_url`, `esc_attr`).
- Hilfsfunktion hex→rgb (kapselt 3-/6-stellige Hex, fällt bei Ungültigkeit auf Weiß zurück).
- `require` in `functions.php` in der bestehenden `inc/`-Reihenfolge.

### 3. `template-parts/flexible.php`

- `ob_start()`/Regex-Mechanik **bleibt** (bewährt, eine Passe).
- Statt `<div class="kc-section-bg__img">` + separatem `background-color` wird das
  **komplette `style`-Attribut aus `kc_section_bg_style()` in das `<section …>`-Tag**
  injiziert.
- Null-Fallback (`?? $kc_html`) bei `pcre.backtrack_limit` bleibt erhalten.

### 4. CSS — `assets/css/kidsclub.css`

- `.kc-section-bg__img` und `section:has(.kc-section-bg__img)` (sowie der Hero-Sonderfall
  `section.section-hero:has(...)`) **entfernen** — obsolet.
- `$ver` in `inc/enqueue.php` erhöhen (Cache-Busting).

## Komponentengrenzen

- **`kc_section_bg_style()`** — *Was:* erzeugt das Section-Style-Attribut aus den ACF-Feldern
  der aktuellen Row. *Nutzung:* einmal in `flexible.php`. *Abhängigkeit:* ACF-Row-Kontext
  (`get_sub_field`).
- **`kc_bg_settings_fields()`** — *Was:* liefert die drei Einstell-Felder für ein Layout.
  *Nutzung:* in `inc/blocks.php` je Layout. *Abhängigkeit:* keine.
- **`flexible.php`** — *Was:* orchestriert Render + Injektion. *Abhängigkeit:* Helper.

## Tests / Verifikation

- `composer test` (lint + PHPStan Level 5) muss grün bleiben.
- Chrome-Sichtprüfung auf `http://localhost:8090`:
  - Section mit Bild + Default-Deckkraft → Bild dezent sichtbar, Text voll deckend.
  - Deckkraft hochsetzen → Bild deutlich sichtbarer, Text unverändert.
  - `bg_size` / `bg_position` ändern → Bild skaliert/positioniert korrekt.
  - Farbige Section (`background_color`) → Schleier nimmt die Farbe an, kein Weißstich.
  - Section ohne Bild → unverändert (Regression).
- Mobile/Desktop-Check (kein Überlauf, korrekte Skalierung).

## Nicht im Scope (YAGNI)

- Keine neuen Bild-Uploads / Zuweisungen (separater Vorgang).
- Keine parallaxe / fixierte Hintergründe.
- Keine Migration bestehender Inhalte (Defaults decken Altbestand ab).
