# Section-Hintergründe Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Hintergrundbilder pro Section direkt auf die `<section>` legen (kein injizierter `<div>`), über einen zentralen PHP-Helper, mit pro Section einstellbarer Deckkraft/Größe/Position via ACF.

**Architecture:** Drei reine, testbare Helper-Funktionen in `inc/section-bg.php` erzeugen ein `style`-Attribut (Abschwächung über einen Schleier-Gradient statt `opacity`, damit der Inhalt deckend bleibt). `template-parts/flexible.php` injiziert dieses Attribut per bestehender Regex direkt ins `<section>`-Tag. Neue ACF-Felder pro Layout steuern Deckkraft/Größe/Position.

**Tech Stack:** PHP 7.4+ (CI: 8.4), WordPress, ACF Pro, hand-authored CSS (kein npm-Build).

## Global Constraints

- PHP-Runtime-Ziel **7.4+**; CI läuft auf **8.4**. (Array-Spread `...` nur auf numerisch indizierten Arrays — hier erfüllt.)
- ACF-Feldänderungen **nicht-destruktiv / abwärtskompatibel**: nie ein von Live-Inhalten genutztes Feld entfernen; Keys eindeutig je Layout (`field_kc_<name>_<layout>`).
- **Kein npm-Build** — CSS von Hand in **beiden** Dateien pflegen: `assets/css/kidsclub.css` *und* `assets/css/kidsclub.min.css`.
- **Cache-Busting:** bei jeder CSS/JS-Änderung `$ver` in `inc/enqueue.php` erhöhen (aktuell `3.2.9`).
- Ausgabe immer escapen: `esc_url()` für URLs, `esc_attr()` fürs Attribut.
- Verifikation des Themes: `composer test` (= `lint` + `stan` Level 5) muss grün bleiben. Lokaler Server: `http://localhost:8090`.
- Die 13 Layouts (für ACF-Verdrahtung): `hero`, `willkommen`, `leistungen`, `zimmer`, `ablauf`, `galerie`, `praxis`, `team`, `eltern`, `stimmen`, `faq`, `termin`, `kontakt`.

---

## File Structure

- **`inc/section-bg.php`** (neu) — drei Helper: `kc_section_bg_hex_to_rgb()`, `kc_section_bg_build_style()` (rein), `kc_section_bg_style()` (WP-Wrapper).
- **`tests/section-bg-test.php`** (neu) — eigenständiger Test der reinen Funktionen (kein WP-Bootstrap).
- **`functions.php`** (ändern) — `require` für `inc/section-bg.php`.
- **`inc/blocks.php`** (ändern) — neue Funktion `kc_bg_settings_fields()` + Einbindung in 13 Layouts.
- **`template-parts/flexible.php`** (ändern) — Injektion des Section-Styles statt `<div>`.
- **`assets/css/kidsclub.css`** + **`assets/css/kidsclub.min.css`** (ändern) — obsoletes `.kc-section-bg__img` entfernen.
- **`inc/enqueue.php`** (ändern) — `$ver` erhöhen.

---

### Task 1: Reine Helper-Funktionen + eigenständige Tests

**Files:**
- Create: `inc/section-bg.php`
- Test: `tests/section-bg-test.php`

**Interfaces:**
- Consumes: nichts.
- Produces:
  - `kc_section_bg_hex_to_rgb( string $hex ): string` → `"r,g,b"` (Fallback `"255,255,255"`).
  - `kc_section_bg_build_style( array $o ): string` → CSS-Deklarationen (ohne `style=`), oder `''`. Optionsschlüssel: `img` (string URL), `color` (string hex), `opacity` (int 0–100, Default 8), `size` (string, Default `115%`), `position` (string, Default `center top`).

- [ ] **Step 1: Den fehlschlagenden Test schreiben**

Create `tests/section-bg-test.php`:

```php
<?php
/**
 * Eigenständiger Test der reinen Helper aus inc/section-bg.php.
 * Kein WP-Bootstrap — die Escaping-Funktionen werden gestubbt.
 * Run: php tests/section-bg-test.php
 */
if ( ! function_exists( 'esc_url' ) )  { function esc_url( $u ) { return $u; } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return $s; } }

require __DIR__ . '/../inc/section-bg.php';

$failed = 0;
function check( $label, $actual, $expected ) {
	global $failed;
	if ( $actual === $expected ) {
		echo "PASS: $label\n";
	} else {
		$failed++;
		echo "FAIL: $label\n  expected: " . var_export( $expected, true ) . "\n  actual:   " . var_export( $actual, true ) . "\n";
	}
}

// hex -> rgb
check( 'hex 6-stellig',      kc_section_bg_hex_to_rgb( '#0E3A8E' ), '14,58,142' );
check( 'hex 3-stellig',      kc_section_bg_hex_to_rgb( '#fff' ),    '255,255,255' );
check( 'hex ungültig->weiß', kc_section_bg_hex_to_rgb( 'xyz' ),     '255,255,255' );

// build_style
check( 'leer -> ""', kc_section_bg_build_style( [] ), '' );

check( 'nur Farbe', kc_section_bg_build_style( [ 'color' => '#ffffff' ] ), 'background-color:#ffffff' );

check(
	'Bild Default (Deckkraft 8 -> alpha 0.92, weiß)',
	kc_section_bg_build_style( [ 'img' => 'http://x/a.png' ] ),
	'background-image:linear-gradient(rgba(255,255,255,0.92),rgba(255,255,255,0.92)),url(http://x/a.png);background-size:115%;background-position:center top;background-repeat:no-repeat'
);

check(
	'Bild + Farbe (Schleier nimmt Farbe)',
	kc_section_bg_build_style( [ 'img' => 'http://x/a.png', 'color' => '#000000', 'opacity' => 20, 'size' => 'cover', 'position' => 'center' ] ),
	'background-color:#000000;background-image:linear-gradient(rgba(0,0,0,0.8),rgba(0,0,0,0.8)),url(http://x/a.png);background-size:cover;background-position:center;background-repeat:no-repeat'
);

echo $failed === 0 ? "\nALL PASS\n" : "\n$failed FAILED\n";
exit( $failed === 0 ? 0 : 1 );
```

- [ ] **Step 2: Test laufen lassen, Fehlschlag bestätigen**

Run: `php tests/section-bg-test.php`
Expected: FAIL — `require` schlägt fehl, weil `inc/section-bg.php` noch nicht existiert (`Failed opening required ... section-bg.php`).

- [ ] **Step 3: Minimale Implementierung schreiben**

Create `inc/section-bg.php`:

```php
<?php
/**
 * Section-Hintergrund-Helper.
 *
 * Erzeugt ein inline `style`-Attribut, das DIREKT auf die <section> gehört.
 * Die Bild-Abschwächung läuft über einen Schleier-Gradient (statt `opacity`),
 * damit der Section-Inhalt voll deckend bleibt. Der Schleier nimmt die
 * Section-Hintergrundfarbe an (sonst Weiß), funktioniert also auf weißem
 * wie farbigem Untergrund.
 *
 * @package KidsClub
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'KC_SECTION_BG_TEST' ) ) {
	// Im normalen WP-Lauf hängt ABSPATH; im Standalone-Test wird die Datei
	// per require eingebunden — dann nicht abbrechen.
}

/**
 * Wandelt einen Hex-Farbwert in "r,g,b" um. Fällt bei Ungültigkeit auf Weiß zurück.
 */
function kc_section_bg_hex_to_rgb( string $hex ): string {
	$hex = ltrim( trim( $hex ), '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '255,255,255';
	}
	return hexdec( substr( $hex, 0, 2 ) ) . ',' . hexdec( substr( $hex, 2, 2 ) ) . ',' . hexdec( substr( $hex, 4, 2 ) );
}

/**
 * Baut die CSS-Deklarationen für den Section-Hintergrund (ohne `style=`).
 *
 * @param array $o { img?:string, color?:string, opacity?:int, size?:string, position?:string }
 * @return string CSS-Deklarationen mit `;` getrennt, oder '' wenn nichts zu setzen ist.
 */
function kc_section_bg_build_style( array $o ): string {
	$img      = isset( $o['img'] ) ? (string) $o['img'] : '';
	$color    = isset( $o['color'] ) ? (string) $o['color'] : '';
	$opacity  = isset( $o['opacity'] ) && '' !== $o['opacity'] ? (int) $o['opacity'] : 8;
	$size     = isset( $o['size'] ) && '' !== $o['size'] ? (string) $o['size'] : '115%';
	$position = isset( $o['position'] ) && '' !== $o['position'] ? (string) $o['position'] : 'center top';

	$has_img   = '' !== $img;
	$has_color = '' !== $color;

	if ( ! $has_img && ! $has_color ) {
		return '';
	}

	$opacity = max( 0, min( 100, $opacity ) );
	$alpha   = round( 1 - ( $opacity / 100 ), 3 );
	$rgb     = $has_color ? kc_section_bg_hex_to_rgb( $color ) : '255,255,255';

	$decls = [];
	if ( $has_color ) {
		$decls[] = 'background-color:' . $color;
	}
	if ( $has_img ) {
		$veil    = 'linear-gradient(rgba(' . $rgb . ',' . $alpha . '),rgba(' . $rgb . ',' . $alpha . '))';
		$decls[] = 'background-image:' . $veil . ',url(' . esc_url( $img ) . ')';
		$decls[] = 'background-size:' . $size;
		$decls[] = 'background-position:' . $position;
		$decls[] = 'background-repeat:no-repeat';
	}

	return implode( ';', $decls );
}

/**
 * Liest die Sub-Fields der aktuellen ACF-Row und liefert das fertige
 * `style`-Attribut (inkl. führendem Leerzeichen) oder '' zurück.
 */
function kc_section_bg_style(): string {
	$img = function_exists( 'get_sub_field' ) ? get_sub_field( 'background_image' ) : null;

	$style = kc_section_bg_build_style(
		[
			'img'      => is_array( $img ) && ! empty( $img['url'] ) ? $img['url'] : '',
			'color'    => function_exists( 'get_sub_field' ) ? (string) get_sub_field( 'background_color' ) : '',
			'opacity'  => function_exists( 'get_sub_field' ) ? get_sub_field( 'bg_opacity' ) : '',
			'size'     => function_exists( 'get_sub_field' ) ? (string) get_sub_field( 'bg_size' ) : '',
			'position' => function_exists( 'get_sub_field' ) ? (string) get_sub_field( 'bg_position' ) : '',
		]
	);

	return '' === $style ? '' : ' style="' . esc_attr( $style ) . '"';
}
```

- [ ] **Step 4: Test laufen lassen, Erfolg bestätigen**

Run: `php tests/section-bg-test.php`
Expected: alle `PASS`, letzte Zeile `ALL PASS`, Exit-Code 0.

- [ ] **Step 5: Lint + PHPStan**

Run: `composer lint && composer stan`
Expected: keine Syntaxfehler; PHPStan ohne Fehler (Helper liegt unter `inc/`, wird analysiert).

- [ ] **Step 6: Commit**

```bash
git add inc/section-bg.php tests/section-bg-test.php
git commit -m "feat(section-bg): pure background-style helpers + standalone tests"
```

---

### Task 2: Helper einbinden (functions.php)

**Files:**
- Modify: `functions.php:13` (nach `inc/blocks.php`)

**Interfaces:**
- Consumes: `kc_section_bg_style()` aus Task 1.
- Produces: Helper im WP-Lauf verfügbar (vor `flexible.php` geladen).

- [ ] **Step 1: require ergänzen**

In `functions.php` direkt nach der `inc/blocks.php`-Zeile einfügen:

```php
require get_theme_file_path( 'inc/blocks.php' );
require get_theme_file_path( 'inc/section-bg.php' );
```

- [ ] **Step 2: Lint + Smoke-Test der Startseite**

Run: `composer lint && curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8090/`
Expected: keine Syntaxfehler; HTTP `200` (kein Fatal durch Doppeldefinition o. Ä.).

- [ ] **Step 3: Commit**

```bash
git add functions.php
git commit -m "feat(section-bg): wire section-bg helper into functions.php"
```

---

### Task 3: ACF-Felder Deckkraft/Größe/Position (inc/blocks.php)

**Files:**
- Modify: `inc/blocks.php` (neue Funktion nahe `kc_bg_color_field()` ~Zeile 608; 13 Einbindungsstellen)

**Interfaces:**
- Consumes: nichts (Felddefinitionen).
- Produces: `kc_bg_settings_fields( string $layout ): array` — Array aus 3 ACF-Feldern (`bg_opacity` range, `bg_size` select, `bg_position` select), Keys eindeutig je Layout, conditional auf das Bildfeld `field_kc_bg_<layout>`.

- [ ] **Step 1: Funktion hinzufügen**

In `inc/blocks.php` nach `kc_bg_color_field()` einfügen:

```php
/** Darstellungs-Einstellungen fürs Hintergrundbild pro Section. */
function kc_bg_settings_fields( $layout ) {
	$img_key   = 'field_kc_bg_' . $layout;
	$show_cond = [ [ [ 'field' => $img_key, 'operator' => '!=empty' ] ] ];

	return [
		[
			'key'               => 'field_kc_bgopa_' . $layout,
			'label'             => 'Deckkraft des Hintergrundbilds (%)',
			'name'              => 'bg_opacity',
			'type'              => 'range',
			'default_value'     => 8,
			'min'               => 0,
			'max'               => 100,
			'step'              => 1,
			'append'            => '%',
			'instructions'      => 'Höher = Bild stärker sichtbar.',
			'conditional_logic' => $show_cond,
		],
		[
			'key'               => 'field_kc_bgsize_' . $layout,
			'label'             => 'Bildgröße',
			'name'              => 'bg_size',
			'type'              => 'select',
			'choices'           => [
				'115%'    => 'Standard (115%)',
				'cover'   => 'Füllend (cover)',
				'contain' => 'Einpassend (contain)',
				'auto'    => 'Originalgröße (auto)',
			],
			'default_value'     => '115%',
			'conditional_logic' => $show_cond,
		],
		[
			'key'               => 'field_kc_bgpos_' . $layout,
			'label'             => 'Bildposition',
			'name'              => 'bg_position',
			'type'              => 'select',
			'choices'           => [
				'center top'    => 'Oben mittig',
				'center'        => 'Mittig',
				'center bottom' => 'Unten mittig',
				'left center'   => 'Links',
				'right center'  => 'Rechts',
			],
			'default_value'     => 'center top',
			'conditional_logic' => $show_cond,
		],
	];
}
```

- [ ] **Step 2: In allen 13 Layouts einbinden**

Jeweils direkt nach der Zeile `kc_bg_color_field( '<layout>' ),` die Spread-Zeile ergänzen. Beispiel `hero` (Zeilen 54–55):

```php
									kc_bg_field( 'hero' ),
									kc_bg_color_field( 'hero' ),
									...kc_bg_settings_fields( 'hero' ),
```

Für jedes Layout wiederholen: `hero`, `willkommen`, `leistungen`, `zimmer`, `ablauf`, `galerie`, `praxis`, `team`, `eltern`, `stimmen`, `faq`, `termin`, `kontakt`. (Auffinden: `grep -n "kc_bg_color_field(" inc/blocks.php` — es muss danach genauso viele `...kc_bg_settings_fields(`-Zeilen geben.)

- [ ] **Step 3: Konsistenz prüfen**

Run: `grep -c "kc_bg_color_field(" inc/blocks.php && grep -c "kc_bg_settings_fields( '" inc/blocks.php`
Expected: erste Zahl = Definitionen+Aufrufe der Farbe; die Zahl der `...kc_bg_settings_fields( '`-Aufrufe muss **13** sein (eine je Layout).

- [ ] **Step 4: Lint + PHPStan**

Run: `composer lint && composer stan`
Expected: keine Fehler.

- [ ] **Step 5: ACF-Felder im Admin sichtprüfen**

Im Browser `http://localhost:8090/wp-admin/` → Landing-Seite bearbeiten → eine Section mit gesetztem Hintergrundbild: die drei Felder (Deckkraft-Slider, Bildgröße, Bildposition) erscheinen; ohne Bild sind sie ausgeblendet (conditional logic).

- [ ] **Step 6: Commit**

```bash
git add inc/blocks.php
git commit -m "feat(section-bg): per-section opacity/size/position ACF fields"
```

---

### Task 4: Injektion auf die <section> statt <div> (flexible.php)

**Files:**
- Modify: `template-parts/flexible.php:24-60`

**Interfaces:**
- Consumes: `kc_section_bg_style()` aus Task 1.
- Produces: Section-Markup mit inline `style` am `<section>`-Tag; kein `.kc-section-bg__img`-Div mehr.

- [ ] **Step 1: Den Transform-Block ersetzen**

In `template-parts/flexible.php` den Abschnitt ab `$kc_bg = get_sub_field( 'background_image' );` bis zum `echo preg_replace( ... ) ?? $kc_html;` durch Folgendes ersetzen:

```php
		$layout = get_row_layout(); // z. B. "hero", "leistungen", "zimmer"

		// Inline-Style (Farbe + Schleier-Bild) direkt auf die <section>.
		$kc_bg_style        = kc_section_bg_style(); // '' oder ' style="..."'
		$kc_needs_transform = '' !== $kc_bg_style;

		if ( $kc_needs_transform ) {
			ob_start();
		}

		get_template_part( 'template-parts/layouts/' . $layout );

		if ( $kc_needs_transform ) {
			$kc_html = ob_get_clean();

			// Eine Regex-Passe: Style ins erste <section>-Tag injizieren.
			// Fallback auf $kc_html, falls preg_replace null liefert (pcre.backtrack_limit).
			echo preg_replace(
				'/<section([^>]*)>/',
				'<section$1' . $kc_bg_style . '>',
				$kc_html,
				1
			) ?? $kc_html;
		}
```

Hinweis: Die alte `$layout`-Zuweisung weiter oben entfällt, da sie hier neu gesetzt wird — sicherstellen, dass `$layout` nur **einmal** zugewiesen ist (die ursprüngliche Zeile `$layout = get_row_layout();` aus dem alten Block wird durch obige ersetzt, nicht dupliziert).

- [ ] **Step 2: Lint**

Run: `composer lint`
Expected: keine Syntaxfehler.

- [ ] **Step 3: Front-Render prüfen (Quelltext)**

Run:
```bash
curl -s http://localhost:8090/ | grep -o '<section[^>]*style="[^"]*background-image[^"]*"' | head -3
```
Expected: mindestens eine `<section ... style="...background-image:linear-gradient(...),url(...)...">`; **kein** `kc-section-bg__img` mehr:
```bash
curl -s http://localhost:8090/ | grep -c 'kc-section-bg__img'
```
Expected: `0`.

- [ ] **Step 4: Visuelle Prüfung im Browser**

`http://localhost:8090/` laden: Section mit Bild zeigt das Bild dezent (Default-Deckkraft), Text voll deckend; Section ohne Bild unverändert. Deckkraft im Admin hochsetzen → Bild deutlich sichtbarer, Text unverändert. Größe/Position ändern → wirkt. Farbige Section → Schleier nimmt die Farbe (kein Weißstich). Mobile-Breite prüfen (kein Überlauf).

- [ ] **Step 5: Commit**

```bash
git add template-parts/flexible.php
git commit -m "feat(section-bg): inject background style on <section>, drop overlay div"
```

---

### Task 5: CSS-Aufräumen + Cache-Busting

**Files:**
- Modify: `assets/css/kidsclub.css:888-896` (Kommentar + 3 Regeln)
- Modify: `assets/css/kidsclub.min.css` (gleiche Regeln, minifiziert)
- Modify: `inc/enqueue.php:15`

**Interfaces:**
- Consumes: nichts.
- Produces: keine toten `.kc-section-bg__img`-Regeln mehr; neue Asset-Version.

- [ ] **Step 1: Obsolete Regeln aus `kidsclub.css` entfernen**

Diesen Block (Kommentar + drei Selektoren) löschen:

```css
/* Optionales Sektions-Hintergrundbild (ACF, pro Sektion).
   .kc-section-bg__img est injecté comme premier enfant de <section> via ob_start()
   dans flexible.php — l'image est ainsi au-dessus du background-color de la section
   mais en dessous du contenu (z-index:0 vs z-index:1 du contenu). */
.kc-section-bg__img{position:absolute;inset:0;z-index:0;pointer-events:none;
  background-size:115% auto;background-position:center top;background-repeat:no-repeat;
  opacity:.06}
section:has(.kc-section-bg__img){position:relative;overflow:hidden}
section.section-hero:has(.kc-section-bg__img){overflow:visible}
```

- [ ] **Step 2: Gleiche Regeln aus `kidsclub.min.css` entfernen**

Run zum Auffinden: `grep -o '[^}]*kc-section-bg__img[^}]*}' assets/css/kidsclub.min.css`
Alle Vorkommen von `.kc-section-bg__img{...}`, `section:has(.kc-section-bg__img){...}` und `section.section-hero:has(.kc-section-bg__img){...}` aus der minifizierten Datei entfernen.

- [ ] **Step 3: Entfernung verifizieren**

Run: `grep -c 'kc-section-bg__img' assets/css/kidsclub.css assets/css/kidsclub.min.css`
Expected: beide `0`.

- [ ] **Step 4: `$ver` erhöhen**

In `inc/enqueue.php` Zeile 15:

```php
		$ver    = '3.3.0'; // bei jedem CSS/JS-Update erhöhen (Cache-Busting)
```

- [ ] **Step 5: Regressionsprüfung**

Run: `composer test`
Expected: `lint` + `stan` grün.
Browser-Hardreload (Cmd+Shift+R) auf `http://localhost:8090/`: Layout unverändert, Hintergrundbilder wie in Task 4, keine verschobenen Sections.

- [ ] **Step 6: Commit**

```bash
git add assets/css/kidsclub.css assets/css/kidsclub.min.css inc/enqueue.php
git commit -m "chore(section-bg): drop obsolete overlay CSS, bump asset version 3.3.0"
```

---

## Self-Review

**Spec coverage:**
- Bild direkt auf `<section>` → Task 4. ✔
- Zentraler Helper, keine Duplikation → Task 1/2, ein Aufruf in `flexible.php`. ✔
- Deckkraft/Größe/Position pro Section (ACF) → Task 3. ✔
- Schleier-Gradient statt `opacity`, Farbe aus `background_color` sonst Weiß → Task 1 `kc_section_bg_build_style()`. ✔
- `<div>` und obsoletes CSS entfernen → Task 4 + Task 5. ✔
- Cache-Busting → Task 5. ✔
- Nicht-destruktive ACF-Erweiterung, Defaults für Altbestand → Task 1 (Helper-Defaults) + Task 3 (`default_value`). ✔

**Type consistency:** `kc_section_bg_style()`, `kc_section_bg_build_style()`, `kc_section_bg_hex_to_rgb()`, `kc_bg_settings_fields()` in allen Tasks identisch benannt; Optionsschlüssel `img/color/opacity/size/position` konsistent zwischen Test (Task 1) und Wrapper (Task 1) und Aufruf (implizit). ✔

**Placeholder scan:** keine TBD/TODO; jeder Code-Schritt zeigt vollständigen Code und exakte Befehle mit erwarteter Ausgabe. ✔
