# ZACP Homepage Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refondre la homepage du thème *Kids Club by zacp* pour qu'elle corresponde pixel-fidèlement à la maquette `ZACP/Assets/Homepage.pdf` (style épuré Jost, fond clair, sans grain).

**Architecture:** Rebuild markup + CSS section par section, en réutilisant le moteur ACF Flexible Content existant et en préservant l'intégration booking Masinga. Couleurs de marque réalignées sur le PDF, police unique Jost.

**Tech Stack:** WordPress (theme PHP), ACF Flexible Content, CSS custom (`assets/css/kidsclub.css`), Swiper.js, Alpine.js, fonts self-hosted (woff2). Lint : `phpcs` / `phpstan` (déjà configurés). Vérif visuelle : Chrome.

**Note méthode :** un reskin présentationnel ne se prête pas au TDD unitaire classique. La « vérification » de chaque tâche = **lint PHP** (phpcs/phpstan) + **comparaison visuelle Chrome** contre la tranche PDF correspondante. Les rares ajouts de logique (helper PHP, champ ACF) ont un check dédié.

**Référence :** spec `docs/superpowers/specs/2026-06-12-zacp-homepage-redesign-design.md`.
**Branche :** `feature/zacp-homepage-redesign` (déjà créée).
**Quand :** implémentation prévue **lundi** — ce document est le plan, pas l'exécution.

---

## Palette cible (échantillonnée du PDF)

```
--magenta : #EA4589   (titres, accents)
--navy    : #102E79   (corps, footer)
--bg      : #FFFFFF    (page)
--band    : #EFF2F0    (bandes sections)
carte jaune (Putzschule)        : #F3E3A6
carte bleue (Prophylaxe)        : #BBCCDA
carte verte (Die Behandlung)    : #C0CBC3
carte rose  (AngstpatientInnen) : #F9E9E2
```

## File Structure

| Fichier | Action | Responsabilité |
|---|---|---|
| `assets/fonts/jost/*.woff2` | Créer | Police Jost self-hosted |
| `assets/css/fonts.css` | Réécrire | `@font-face` Jost (remplace Fredoka/Caveat/Nunito) |
| `assets/css/kidsclub.css` | Modifier | Tokens + styles de toutes les sections |
| `inc/enqueue.php` | Modifier | Preload Jost au lieu de Fredoka |
| `assets/img/symbols/Symbol1-5.svg` | Créer | Illustrations cartes Leistungen |
| `assets/img/logo-quer.svg`, `logo-hoch.svg` | Créer | Logos header/footer |
| `assets/video/spray-quer.mp4`, `spray-hoch.mp4` | Créer | Fond vidéo hero |
| `inc/icons.php` | Modifier | Helper `kc_symbol()` pour les Symbol SVG |
| `inc/blocks.php` | Modifier | Champs ACF `card_color` + `symbol` sur Leistungen items |
| `header.php` | Modifier | Logo SVG + nav (booking préservé) |
| `template-parts/layouts/hero.php` | Modifier | Markup hero PDF |
| `template-parts/layouts/leistungen.php` | Modifier | 4 cartes pastel + symbols |
| `template-parts/layouts/praxis.php` / `zimmer.php` | Modifier | Carousel Praxis |
| `template-parts/layouts/team.php` | Modifier | Grille team |
| `template-parts/layouts/ablauf.php` | Modifier | Accordéon numéroté 1/2/3 |
| `footer.php` | Modifier | Footer navy |

---

## Task 1 : Intégrer la police Jost

**Files:**
- Create: `assets/fonts/jost/` (woff2 copiés depuis `ZACP/Assets/jost woff2/`)
- Modify: `assets/css/fonts.css` (réécriture complète)
- Modify: `inc/enqueue.php` (preload)

- [ ] **Step 1 : Copier les woff2 Jost**

```bash
cd /Users/mdiarrisso/PhpstormProjects/wordpress
mkdir -p assets/fonts/jost
cp ZACP/Assets/"jost woff2"/Jost-Regular.woff2  assets/fonts/jost/
cp ZACP/Assets/"jost woff2"/Jost-Medium.woff2   assets/fonts/jost/
cp ZACP/Assets/"jost woff2"/Jost-SemiBold.woff2 assets/fonts/jost/
cp ZACP/Assets/"jost woff2"/Jost-Bold.woff2     assets/fonts/jost/
cp ZACP/Assets/"jost woff2"/Jost-ExtraBold.woff2 assets/fonts/jost/
ls assets/fonts/jost/
```
Expected: 5 fichiers .woff2 listés.

- [ ] **Step 2 : Réécrire `assets/css/fonts.css`**

```css
/* Kids Club — Jost self-hosted (DSGVO: keine IP an Google).
   font-display:swap = Text sofort sichtbar mit Fallback, kein FOIT. */
@font-face{font-family:'Jost';font-style:normal;font-weight:400;font-display:swap;src:url('../fonts/jost/Jost-Regular.woff2') format('woff2')}
@font-face{font-family:'Jost';font-style:normal;font-weight:500;font-display:swap;src:url('../fonts/jost/Jost-Medium.woff2') format('woff2')}
@font-face{font-family:'Jost';font-style:normal;font-weight:600;font-display:swap;src:url('../fonts/jost/Jost-SemiBold.woff2') format('woff2')}
@font-face{font-family:'Jost';font-style:normal;font-weight:700;font-display:swap;src:url('../fonts/jost/Jost-Bold.woff2') format('woff2')}
@font-face{font-family:'Jost';font-style:normal;font-weight:800;font-display:swap;src:url('../fonts/jost/Jost-ExtraBold.woff2') format('woff2')}
```

- [ ] **Step 3 : Mettre à jour le preload dans `inc/enqueue.php`**

Remplacer la ligne preload `fredoka-v17-latin-700.woff2` par :
```php
echo '<link rel="preload" href="' . esc_url( $dir . '/assets/fonts/jost/Jost-Bold.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
```
Et incrémenter `$ver` (ex. `'1.6.0'`) pour le cache-busting.

- [ ] **Step 4 : Lint**

Run: `composer phpcs 2>/dev/null || vendor/bin/phpcs inc/enqueue.php`
Expected: aucune erreur sur les fichiers modifiés.

- [ ] **Step 5 : Commit**

```bash
git add assets/fonts/jost assets/css/fonts.css inc/enqueue.php
git commit -m "feat(fonts): integrate Jost, replace Fredoka/Caveat/Nunito"
```

---

## Task 2 : Réaligner les design tokens (couleurs + typo, retrait grain)

**Files:**
- Modify: `assets/css/kidsclub.css` (bloc `:root` ~ lignes 18-70, `body`, `body::after`)

- [ ] **Step 1 : Mettre à jour le bloc `:root`**

Dans `:root{ … }` :
```css
--font-display:'Jost', system-ui, sans-serif;
--font-body:'Jost', system-ui, sans-serif;
/* supprimer --font-marker */

--magenta:#EA4589;
--magenta-deep:#D52E72;
--navy:#102E79;
--ink:#102E79;
--ink-soft:#4A568C;

--bg:#FFFFFF;
--band:#EFF2F0;
--surface:#FFFFFF;

/* cartes Leistungen */
--card-yellow:#F3E3A6;
--card-blue:#BBCCDA;
--card-green:#C0CBC3;
--card-pink:#F9E9E2;
```

- [ ] **Step 2 : Mettre `body` sur Jost + fond blanc**

```css
body{
  font-family:var(--font-body);
  color:var(--ink);
  background:var(--bg);
  line-height:1.6;
  font-size:18px;
  font-weight:400;
  -webkit-font-smoothing:antialiased;
  overflow-x:hidden;
}
```

- [ ] **Step 3 : Supprimer l'overlay grain**

Supprimer entièrement la règle `body::after{ … }` (overlay bruit). Supprimer aussi les sélecteurs `:root[data-heads="marker"] …`.

- [ ] **Step 4 : Titres Jost bold majuscules magenta**

```css
h1,h2,h3,.display,.section-title{
  font-family:var(--font-display);
  font-weight:700;
  text-transform:uppercase;
  color:var(--magenta);
  letter-spacing:.01em;
  line-height:1.05;
}
.display{font-size:clamp(2.6rem,6vw,4.6rem)}
.section-title{font-size:clamp(2rem,4vw,3.2rem)}
```

- [ ] **Step 5 : Vérif visuelle**

Lancer un serveur local WP (ou env existant), ouvrir la homepage dans Chrome, hard refresh. Comparer la typo/fond global à `Homepage.pdf` (police Jost visible, fond blanc, titres magenta majuscules, plus de grain).

- [ ] **Step 6 : Commit**

```bash
git add assets/css/kidsclub.css
git commit -m "feat(tokens): PDF palette (magenta #EA4589, navy #102E79, white bg), drop grain"
```

---

## Task 3 : Intégrer les Symbol SVG (illustrations cartes)

**Files:**
- Create: `assets/img/symbols/Symbol1-5.svg`
- Modify: `inc/icons.php` (ajout helper `kc_symbol()`)

- [ ] **Step 1 : Copier les SVG**

```bash
mkdir -p assets/img/symbols
cp ZACP/Assets/symbols/Symbol*.svg assets/img/symbols/
ls assets/img/symbols/
```
Expected: Symbol1.svg … Symbol5.svg.

- [ ] **Step 2 : Ajouter le helper `kc_symbol()` dans `inc/icons.php`**

Les Symbols sont des illustrations couleur 691×573 → on les référence en `<img>`, pas en glyphe inline.
```php
/**
 * Symbol-Illustration (Leistungs-Karten). Aufruf: kc_symbol('symbol1')
 * Gibt ein <img> auf die SVG-Datei zurück (Voll-Farbe, 691×573).
 */
function kc_symbol( $slug, $alt = '' ) {
	$allowed = [ 'symbol1', 'symbol2', 'symbol3', 'symbol4', 'symbol5' ];
	if ( ! in_array( $slug, $allowed, true ) ) {
		return '';
	}
	$num = substr( $slug, -1 );
	$url = get_theme_file_uri( "assets/img/symbols/Symbol{$num}.svg" );
	return '<img class="svc-symbol" src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" width="96" height="80">';
}
```

- [ ] **Step 3 : Check syntaxe PHP**

Run: `php -l inc/icons.php`
Expected: `No syntax errors detected`.

- [ ] **Step 4 : Commit**

```bash
git add assets/img/symbols inc/icons.php
git commit -m "feat(symbols): add Symbol1-5 illustrations + kc_symbol() helper"
```

---

## Task 4 : ACF — champs `card_color` + `symbol` sur Leistungen items

**Files:**
- Modify: `inc/blocks.php` (sub_fields du repeater `items`, lignes ~157-165)

- [ ] **Step 1 : Remplacer les `sub_fields` du repeater Leistungen**

Remplacer le bloc `'sub_fields' => [ kc_field('icon', …), kc_field('heading', …), kc_field('body', …) ]` par :
```php
'sub_fields'   => [
	[
		'key'     => 'field_kc_ls_card_color',
		'label'   => 'Kartenfarbe',
		'name'    => 'card_color',
		'type'    => 'select',
		'choices' => [
			'yellow' => 'Gelb (Putzschule)',
			'blue'   => 'Blau (Prophylaxe)',
			'green'  => 'Grün (Behandlung)',
			'pink'   => 'Rosa (Angst)',
		],
		'default_value' => 'yellow',
	],
	[
		'key'     => 'field_kc_ls_symbol',
		'label'   => 'Symbol',
		'name'    => 'symbol',
		'type'    => 'select',
		'choices' => [
			'symbol1' => 'Symbol 1',
			'symbol2' => 'Symbol 2',
			'symbol3' => 'Symbol 3',
			'symbol4' => 'Symbol 4',
			'symbol5' => 'Symbol 5',
		],
		'default_value' => 'symbol1',
	],
	kc_field( 'heading', 'Titel', 'text' ),
	kc_field( 'body', 'Beschreibung', 'textarea' ),
],
```
*(Le champ `icon` legacy est retiré du formulaire ; il reste rétro-compatible côté données — non lu si absent.)*

- [ ] **Step 2 : Check syntaxe**

Run: `php -l inc/blocks.php`
Expected: `No syntax errors detected`.

- [ ] **Step 3 : Vérif admin**

Ouvrir la page Landing en WP admin → la section *Leistungsspektrum* doit montrer les selects « Kartenfarbe » et « Symbol » par item.

- [ ] **Step 4 : Commit**

```bash
git add inc/blocks.php
git commit -m "feat(acf): leistungen card_color + symbol selects"
```

---

## Task 5 : Header — logo SVG + nav (booking préservé)

**Files:**
- Create: `assets/img/logo-quer.svg` (copie de `ZACP/Assets/logo/logo quer.svg`)
- Modify: `header.php`
- Modify: `assets/css/kidsclub.css` (styles `.site-header`, `.nav-links`)

- [ ] **Step 1 : Copier le logo**

```bash
cp ZACP/Assets/logo/"logo quer.svg" assets/img/logo-quer.svg
```

- [ ] **Step 2 : Brancher le logo SVG par défaut dans `header.php`**

Dans la construction `$brand`, remplacer le fallback arch par le logo SVG fichier (le champ `header_logo` option reste prioritaire s'il est défini) :
```php
$default_logo = '<img src="' . esc_url( get_theme_file_uri( 'assets/img/logo-quer.svg' ) ) . '" alt="Kids Club by zacp" width="150" height="56">';
$brand = $logo
	? '<img src="' . esc_url( $logo['url'] ) . '" alt="' . esc_attr( $logo['alt'] ?: 'Kids Club by zacp' ) . '" style="height:48px;width:auto">'
	: $default_logo;
```

- [ ] **Step 3 : Ne PAS toucher au bouton booking**

Vérifier que `<button … data-booking-open …>` reste intact (déclencheur Masinga). Seul son style change via CSS (`.btn-primary`).

- [ ] **Step 4 : CSS header (fond blanc, nav navy, lien actif magenta)**

```css
.site-header{background:#fff;box-shadow:0 1px 0 rgba(16,46,121,.08)}
.nav-links a{color:var(--navy);font-weight:500;text-transform:none}
.nav-links a:hover,.nav-links a[aria-current="page"]{color:var(--magenta)}
.btn-primary{background:var(--magenta);color:#fff;border-radius:var(--pill)}
```

- [ ] **Step 5 : Vérif visuelle (slice hd_0)** — comparer header à la tranche haute du PDF.

- [ ] **Step 6 : Commit**

```bash
git add assets/img/logo-quer.svg header.php assets/css/kidsclub.css
git commit -m "feat(header): PDF logo + nav style, preserve booking trigger"
```

---

## Task 6 : Hero — markup PDF + vidéo spray

**Files:**
- Create: `assets/video/spray-quer.mp4`, `assets/video/spray-hoch.mp4`
- Modify: `template-parts/layouts/hero.php`
- Modify: `assets/css/kidsclub.css` (`.hero-banner`)

- [ ] **Step 1 : Copier les clips**

```bash
mkdir -p assets/video
cp ZACP/Assets/"hero clips"/spray-quer.mp4 assets/video/
cp ZACP/Assets/"hero clips"/spray-hoch.mp4 assets/video/
```

- [ ] **Step 2 : CSS hero (titre magenta 2 lignes, overlay clair)**

```css
.hero-banner{position:relative;min-height:62vh;display:grid;place-items:center;overflow:hidden;background:#fff}
.hero-banner .hero-video,.hero-banner .hero-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.hero-banner::after{content:'';position:absolute;inset:0;background:linear-gradient(0deg,rgba(255,255,255,.25),rgba(255,255,255,.25))}
.hero-banner-inner{position:relative;z-index:2;text-align:center}
.hero-banner .display{color:var(--magenta);text-transform:uppercase}
```

- [ ] **Step 3 : Vérifier le rendu vidéo** — la section `hero.php` supporte déjà `hero_media_type=video` + `hero_video` + poster. Aucun changement de logique requis ; ajuster uniquement le markup/classes si nécessaire pour coller au PDF.

- [ ] **Step 4 : Vérif visuelle (slice hd_0/hd_1)** — titre « GESUNDE ZÄHNE VON ANFANG AN » magenta sur fond clair/vidéo.

- [ ] **Step 5 : Commit**

```bash
git add assets/video template-parts/layouts/hero.php assets/css/kidsclub.css
git commit -m "feat(hero): PDF layout + spray video background"
```

---

## Task 7 : Section Willkommen (intro)

**Files:**
- Modify: `template-parts/layouts/hero.php` ou intro existante (selon où vit le texte Willkommen)
- Modify: `assets/css/kidsclub.css`

- [ ] **Step 1 : CSS intro centrée**

```css
.intro-welcome{max-width:820px;margin:64px auto;text-align:center;color:var(--navy);font-size:1.15rem}
.intro-welcome strong{color:var(--magenta)}
```

- [ ] **Step 2 : Contenu** — texte réel du `.docx` (« Herzlich Willkommen! Im ZACP Kids Club dreht sich alles um gesunde Zähne … angstfrei ablaufen. »), « Herzlich Willkommen! » en `<strong>`.

- [ ] **Step 3 : Vérif visuelle (slice hd_1)**.

- [ ] **Step 4 : Commit**

```bash
git add -A && git commit -m "feat(intro): Willkommen section per PDF"
```

---

## Task 8 : Leistungen — 4 cartes pastel + symbols

**Files:**
- Modify: `template-parts/layouts/leistungen.php`
- Modify: `assets/css/kidsclub.css`

- [ ] **Step 1 : Markup carte avec couleur + symbol**

Dans la boucle `while ( have_rows('items') )`, remplacer le rendu `.svc` par :
```php
<article class="svc svc--<?php echo esc_attr( get_sub_field( 'card_color' ) ?: 'yellow' ); ?>">
	<?php echo kc_symbol( get_sub_field( 'symbol' ) ?: 'symbol1', get_sub_field( 'heading' ) ); ?>
	<h3><?php echo esc_html( get_sub_field( 'heading' ) ); ?></h3>
	<p><?php echo esc_html( get_sub_field( 'body' ) ); ?></p>
</article>
```

- [ ] **Step 2 : CSS grille 2×2 + variantes couleur**

```css
.services-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:28px;max-width:var(--maxw);margin-inline:auto}
@media(max-width:720px){.services-grid{grid-template-columns:1fr}}
.svc{border-radius:var(--r-lg);padding:36px 32px;position:relative}
.svc h3{color:var(--magenta);text-transform:uppercase;margin:.4em 0}
.svc p{color:var(--navy)}
.svc-symbol{position:absolute;top:28px;right:28px;width:64px;height:auto}
.svc--yellow{background:var(--card-yellow)}
.svc--blue{background:var(--card-blue)}
.svc--green{background:var(--card-green)}
.svc--pink{background:var(--card-pink)}
.svc--pink h3{color:var(--magenta)}
```

- [ ] **Step 3 : Contenu** — 4 cartes du `.docx` : Putzschule (yellow/symbol1), Prophylaxe (blue/symbol2), Die Behandlung (green/symbol3), AngstpatientInnen (pink/symbol4).

- [ ] **Step 4 : Vérif visuelle (slice hd_2)** — 4 cartes pastel, titres magenta, symbol en haut à droite.

- [ ] **Step 5 : Commit**

```bash
git add template-parts/layouts/leistungen.php assets/css/kidsclub.css
git commit -m "feat(leistungen): 4 pastel cards + symbol illustrations"
```

---

## Task 9 : Die Praxis — carousel 5 Zimmer

**Files:**
- Modify: `template-parts/layouts/praxis.php` et/ou `zimmer.php`
- Modify: `assets/css/kidsclub.css`

- [ ] **Step 1 : Vérifier le markup Swiper existant** — réutiliser les composants Swiper du projet (nav + pagination réutilisables, cf. règle projet). Flèches nav blanches comme le PDF.

- [ ] **Step 2 : CSS bande Praxis (fond `--band`) + cartes Zimmer**

```css
#praxis{background:var(--band);padding:80px 0}
#praxis .section-title{text-align:center}
.zimmer-card{border-radius:var(--r-lg);overflow:hidden;background:#cfd3d6}
```
Respecter les règles Swiper projet : `padding-bottom` sur `.swiper` pour le shadow, `slidesPerView:'auto'` + width explicite, `prefers-reduced-motion`, aria.

- [ ] **Step 3 : Vérif visuelle (slice hd_3)**.

- [ ] **Step 4 : Commit**

```bash
git add -A && git commit -m "feat(praxis): Zimmer carousel per PDF"
```

---

## Task 10 : Kids Club Team — grille photos

**Files:**
- Modify: `template-parts/layouts/team.php`
- Modify: `assets/css/kidsclub.css`

- [ ] **Step 1 : CSS grille 3 colonnes + carte photo**

```css
.team-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;max-width:var(--maxw);margin-inline:auto}
@media(max-width:720px){.team-grid{grid-template-columns:1fr}}
.team-card{background:#fff;border:1px solid var(--line);border-radius:var(--r)}
.team-card figcaption{color:var(--navy);font-weight:600;padding:12px 16px;text-align:center}
.team-section .section-title{text-align:center;color:var(--magenta)}
```

- [ ] **Step 2 : Vérif visuelle (slice hd_4)** — titre magenta + grille cartes (nom Dr. … sous chaque photo).

- [ ] **Step 3 : Commit**

```bash
git add -A && git commit -m "feat(team): Kids Club Team grid per PDF"
```

---

## Task 11 : Der erste Besuch — accordéon numéroté 1/2/3

**Files:**
- Modify: `template-parts/layouts/ablauf.php`
- Modify: `assets/css/kidsclub.css`

- [ ] **Step 1 : Markup accordéon (Alpine.js, déjà chargé)**

Chaque item = bande colorée avec gros chiffre fantôme + question + chevron. Couleurs cyclées jaune/vert/rose. Utiliser `x-data`/`x-show` Alpine cohérent avec les autres accordéons (faq/eltern) du thème.

- [ ] **Step 2 : CSS bandes colorées + chiffre fantôme**

```css
.ablauf-item{display:flex;align-items:center;gap:24px;border-radius:var(--r-lg);padding:22px 28px;margin-bottom:18px}
.ablauf-item:nth-child(3n+1){background:var(--card-yellow)}
.ablauf-item:nth-child(3n+2){background:var(--card-green)}
.ablauf-item:nth-child(3n+3){background:var(--card-pink)}
.ablauf-num{font-family:var(--font-display);font-weight:800;font-size:3rem;color:rgba(255,255,255,.7);line-height:1}
.ablauf-q{color:var(--navy);font-weight:500}
.ablauf-q strong{font-weight:700}
.ablauf-chevron{margin-left:auto;color:var(--navy)}
```

- [ ] **Step 3 : Vérif visuelle (slice hd_8)** — bandes jaune/vert/rose, chiffre 1/2/3 fantôme, question navy.

- [ ] **Step 4 : Commit**

```bash
git add -A && git commit -m "feat(ablauf): numbered colored accordion per PDF"
```

---

## Task 12 : Footer navy

**Files:**
- Create: `assets/img/logo-hoch.svg` (copie `ZACP/Assets/logo/logo hoch.svg`)
- Modify: `footer.php`
- Modify: `assets/css/kidsclub.css`

- [ ] **Step 1 : Copier le logo vertical**

```bash
cp ZACP/Assets/logo/"logo hoch.svg" assets/img/logo-hoch.svg
```

- [ ] **Step 2 : CSS footer**

```css
.site-footer{background:var(--navy);color:#fff;padding:64px 0 32px}
.site-footer a{color:#fff}
.site-footer a:hover{color:var(--card-pink)}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:40px;max-width:var(--maxw);margin-inline:auto}
@media(max-width:820px){.footer-grid{grid-template-columns:1fr 1fr}}
.footer-legal{display:flex;justify-content:space-between;border-top:1px solid rgba(255,255,255,.2);margin-top:40px;padding-top:20px;font-size:.9rem}
```

- [ ] **Step 3 : Markup footer** — colonnes : description cabinet · liens (Leistungen/Praxis/Team/FAQ) · contact (T 0541 456 78998 / E info@zacp.de / Newsletter) · logo arche-cœur ; social FB/IG ; légal Impressum · Datenschutzerklärung · AGB.

- [ ] **Step 4 : Vérif visuelle (slice hd_8 bas)**.

- [ ] **Step 5 : Commit**

```bash
git add assets/img/logo-hoch.svg footer.php assets/css/kidsclub.css
git commit -m "feat(footer): navy footer per PDF"
```

---

## Task 13 : Contenu réel homepage (saisie WP admin)

**Files:** aucun fichier code — saisie dans WP admin (ou script wp-cli optionnel).

- [ ] **Step 1 : Saisir le contenu démo** depuis `ZACP/Homepage Kids Club ZACP.docx` : Willkommen, 4 cartes Leistungen (avec couleur + symbol), intro Praxis, items Der erste Besuch. Affecter `card_color` et `symbol` corrects par carte.

- [ ] **Step 2 : Vérif** — la homepage rendue correspond au PDF de bout en bout.

*(Pas de commit — données en base, pas en code. Documenter dans la PR que le contenu démo a été saisi.)*

---

## Task 14 : Vérification finale + revue

- [ ] **Step 1 : Lint complet**

Run:
```bash
vendor/bin/phpcs && vendor/bin/phpstan analyse
```
Expected: 0 erreur.

- [ ] **Step 2 : Vérif visuelle complète Chrome** — desktop + mobile, scroll homepage comparé aux 9 tranches `hd_*.png` / au PDF. Vérifier : booking « Termin buchen » ouvre toujours le modal Masinga.

- [ ] **Step 3 : GIF du scroll** — capturer un GIF du scroll complet de la homepage (documentation PR).

- [ ] **Step 4 : Code-review obligatoire** — lancer l'agent code-reviewer sur le diff complet de branche (sécurité, perf, accessibilité, règles Swiper). Corriger CRITICAL + IMPORTANT.

- [ ] **Step 5 : PR + CodeRabbit** — créer la PR vers `main`, autofix CodeRabbit, puis attendre validation. **Aucun déploiement** tant que la review n'est pas validée et que l'utilisateur n'a pas dit « deploy ».

---

## Rappels transverses

- **Ne pas casser** le bouton `data-booking-open` (booking Masinga).
- **Swiper** : padding-bottom sur `.swiper`, slidesPerView auto + width, shadow sur conteneur externe, `prefers-reduced-motion`, aria (`aria-live`, `aria-roledescription`, compteur « Folie X von Y »).
- **Vidéo hero** : `preload="none"`, poster, `muted/playsinline/autoplay`, respect reduced-motion.
- **ACF** : ajouts de champs non destructifs (champ `icon` legacy conservé en données).
- **Cache-busting** : incrémenter `$ver` dans `inc/enqueue.php` à chaque update CSS/JS.
- **Commits** fréquents, en anglais, un par tâche.
