# Kids Club by zacp — WordPress ACF Theme Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Installer WordPress core + ACF Pro via WP-CLI et compléter le thème Kids Club avec les 8 layouts ACF Flexible Content manquants.

**Architecture:** WordPress core dans `kidsclub-wp/`, thème dans `wp-content/themes/kidsclub/` (copié depuis `wordpress/`). Architecture ACF Flexible Content existante étendue — chaque section = champs ACF dans `inc/blocks.php` + partial PHP dans `template-parts/layouts/{name}.php`. Les 8 nouveaux layouts sont créés via l'agent **ACF Flexible Block Builder**.

**Tech Stack:** WordPress 6.x, WP-CLI, ACF Pro 6.x, PHP 8.x, Alpine.js 3.x (CDN), Swiper.js (existant kidsclub.js), MySQL 8.x

---

## Fichiers à créer / modifier

| Action | Fichier |
|--------|---------|
| CRÉER | `kidsclub-wp/wp-content/themes/kidsclub/style.css` |
| CRÉER | `kidsclub-wp/wp-content/themes/kidsclub/functions.php` |
| CRÉER | `kidsclub-wp/wp-content/themes/kidsclub/index.php` |
| CRÉER | `kidsclub-wp/wp-content/themes/kidsclub/acf-json/` (dossier) |
| MODIFIER | `kidsclub-wp/wp-content/themes/kidsclub/inc/enqueue.php` (+ Alpine.js CDN) |
| MODIFIER | `kidsclub-wp/wp-content/themes/kidsclub/inc/blocks.php` (+ 8 layouts) |
| CRÉER | `template-parts/layouts/ablauf.php` |
| CRÉER | `template-parts/layouts/praxis.php` |
| CRÉER | `template-parts/layouts/team.php` |
| CRÉER | `template-parts/layouts/eltern.php` |
| CRÉER | `template-parts/layouts/stimmen.php` |
| CRÉER | `template-parts/layouts/faq.php` |
| CRÉER | `template-parts/layouts/termin.php` |
| CRÉER | `template-parts/layouts/kontakt.php` |

---

## Task 1 : Installer WordPress core via WP-CLI

**Files:**
- Crée : `kidsclub-wp/` (dossier racine WordPress)
- Crée : `kidsclub-wp/wp-config.php`

- [ ] **Step 1.1 : Vérifier WP-CLI et MySQL**

```bash
wp --info
mysql --version
```

Attendu : version WP-CLI + version MySQL affichées sans erreur.

- [ ] **Step 1.2 : Télécharger WordPress core en allemand**

```bash
cd /Users/mdiarrisso/PhpstormProjects
wp core download --path=kidsclub-wp --locale=de_DE
```

Attendu : `Success: WordPress downloaded.`

- [ ] **Step 1.3 : Créer la base de données MySQL**

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS kidsclub_wp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Attendu : aucune erreur.

- [ ] **Step 1.4 : Créer wp-config.php**

```bash
wp config create \
  --dbname=kidsclub_wp \
  --dbuser=root \
  --dbpass=kidsclub2026 \
  --dbhost=127.0.0.1 \
  --path=kidsclub-wp
```

Attendu : `Success: Generated 'wp-config.php' file.`

- [ ] **Step 1.5 : Installer WordPress**

```bash
wp core install \
  --url=http://localhost:8000 \
  --title="Kids Club by zacp" \
  --admin_user=admin \
  --admin_password=Admin2026! \
  --admin_email=diarrisso49@gmail.com \
  --path=kidsclub-wp
```

Attendu : `Success: WordPress installed successfully.`

- [ ] **Step 1.6 : Désactiver les plugins Hello Dolly / Akismet inutiles**

```bash
wp plugin delete hello --path=kidsclub-wp
wp plugin delete akismet --path=kidsclub-wp
```

- [ ] **Step 1.7 : Lancer le serveur PHP**

```bash
php -S localhost:8000 -t /Users/mdiarrisso/PhpstormProjects/kidsclub-wp &
```

Attendu : `http://localhost:8000` affiche le site WordPress par défaut.

- [ ] **Step 1.8 : Commit**

```bash
cd /Users/mdiarrisso/PhpstormProjects/wordpress
git add -A
git commit -m "feat: WordPress core installed via WP-CLI"
```

---

## Task 2 : Copier le thème et créer les fichiers manquants

**Files:**
- Crée : `kidsclub-wp/wp-content/themes/kidsclub/style.css`
- Crée : `kidsclub-wp/wp-content/themes/kidsclub/functions.php`
- Crée : `kidsclub-wp/wp-content/themes/kidsclub/index.php`
- Crée : `kidsclub-wp/wp-content/themes/kidsclub/acf-json/`

- [ ] **Step 2.1 : Copier le thème existant**

```bash
cp -r /Users/mdiarrisso/PhpstormProjects/wordpress/ \
      /Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/themes/kidsclub/
```

- [ ] **Step 2.2 : Créer style.css (en-tête WordPress obligatoire)**

Créer `/Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/themes/kidsclub/style.css` :

```css
/*
Theme Name:  Kids Club by zacp
Description: Landing page theme pour cabinet dentaire pédiatrique — Kids Club by zacp
Version:     1.0.0
Author:      zacp
Text Domain: kidsclub
*/
```

- [ ] **Step 2.3 : Créer functions.php**

Créer `/Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/themes/kidsclub/functions.php` :

```php
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require get_theme_file_path( 'inc/enqueue.php' );
require get_theme_file_path( 'inc/icons.php' );
require get_theme_file_path( 'inc/options.php' );
require get_theme_file_path( 'inc/blocks.php' );
require get_theme_file_path( 'inc/schema.php' );
```

- [ ] **Step 2.4 : Créer index.php (fallback WordPress)**

Créer `/Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/themes/kidsclub/index.php` :

```php
<?php get_header(); the_content(); get_footer(); ?>
```

- [ ] **Step 2.5 : Créer le dossier acf-json/**

```bash
mkdir -p /Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/themes/kidsclub/acf-json
chmod 775 /Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/themes/kidsclub/acf-json
```

- [ ] **Step 2.6 : Ajouter Alpine.js dans inc/enqueue.php**

Dans `/kidsclub-wp/wp-content/themes/kidsclub/inc/enqueue.php`, ajouter après `wp_enqueue_script('kidsclub', ...)` :

```php
	// Swiper.js — requis pour le slider Kundenstimmen
	wp_enqueue_style(
		'swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
		[], '11.0.0'
	);
	wp_enqueue_script(
		'swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
		[], '11.0.0', true
	);

	// Alpine.js — requis pour les accordéons (eltern, faq)
	wp_enqueue_script(
		'alpinejs',
		'https://cdn.jsdelivr.net/npm/alpinejs@3.14.0/dist/cdn.min.js',
		[], '3.14.0', true
	);
```

- [ ] **Step 2.7 : Activer le thème via WP-CLI**

```bash
wp theme activate kidsclub --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
```

Attendu : `Success: Switched to 'Kids Club by zacp' theme.`

- [ ] **Step 2.8 : Vérifier**

```bash
wp theme status kidsclub --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
```

Attendu : ligne `Active kidsclub`.

- [ ] **Step 2.9 : Commit**

```bash
git add -A && git commit -m "feat: theme structure setup — style.css, functions.php, acf-json/"
```

---

## Task 3 : Installer et activer ACF Pro

**Files:**
- Crée : `kidsclub-wp/wp-content/plugins/advanced-custom-fields-pro/`

**Prérequis :** Avoir le fichier `.zip` ACF Pro disponible localement.

- [ ] **Step 3.1 : Décompresser ACF Pro dans les plugins**

```bash
# Remplace /chemin/vers/advanced-custom-fields-pro.zip par le chemin réel du zip
unzip /chemin/vers/advanced-custom-fields-pro.zip \
      -d /Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/plugins/
```

- [ ] **Step 3.2 : Activer ACF Pro via WP-CLI**

```bash
wp plugin activate advanced-custom-fields-pro \
   --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
```

Attendu : `Plugin 'advanced-custom-fields-pro' activated.`

- [ ] **Step 3.3 : Vérifier que ACF est actif**

```bash
wp plugin list --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
```

Attendu : ligne `advanced-custom-fields-pro ... Active`.

- [ ] **Step 3.4 : Vérifier la page options dans WP-Admin**

Ouvrir `http://localhost:8000/wp-admin` → connexion `admin / Admin2026!` → vérifier que "Theme-Einstellungen" apparaît dans le menu latéral gauche (injecté par `inc/options.php`).

- [ ] **Step 3.5 : Commit**

```bash
git commit -m "feat: ACF Pro installed and activated"
```

---

## Task 4 : Layout `ablauf` — Erster Besuch (Steps)

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php` (ajouter layout ablauf)
- Crée : `template-parts/layouts/ablauf.php`

- [ ] **Step 4.1 : Ajouter le layout dans inc/blocks.php**

Dans le tableau `'layouts'` de `inc/blocks.php`, après `layout_zimmer`, ajouter :

```php
/* ---------- ABLAUF (ERSTER BESUCH) ---------- */
'layout_ablauf' => [
    'key'        => 'layout_ablauf',
    'name'       => 'ablauf',
    'label'      => 'Erster Besuch (Ablauf)',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'abl_eyebrow', 'Eyebrow', 'text' ),
        kc_field( 'abl_title',   'Überschrift', 'text' ),
        kc_field( 'abl_text',    'Einleitung', 'textarea' ),
        [
            'key'          => 'field_kc_abl_items',
            'label'        => 'Schritte',
            'name'         => 'items',
            'type'         => 'repeater',
            'layout'       => 'block',
            'button_label' => 'Schritt hinzufügen',
            'sub_fields'   => [
                kc_field( 'abl_nr',      'Nummer',       'text' ),
                kc_field( 'abl_heading', 'Titel',        'text' ),
                kc_field( 'abl_body',    'Beschreibung', 'textarea' ),
            ],
        ],
    ],
],
```

- [ ] **Step 4.2 : Créer template-parts/layouts/ablauf.php**

```php
<?php
/**
 * Layout: Ablauf — Erster Besuch (nummerierte Schritte)
 * Felder: abl_eyebrow, abl_title, abl_text, items (Repeater: abl_nr, abl_heading, abl_body)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'abl_eyebrow' );
$title   = get_sub_field( 'abl_title' );
$text    = get_sub_field( 'abl_text' );
$items   = get_sub_field( 'items' );
?>
<section class="section-ablauf reveal" id="ablauf">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $text ) : ?>
            <p class="section-lead"><?php echo esc_html( $text ); ?></p>
        <?php endif; ?>
        <?php if ( $items ) : ?>
        <ol class="ablauf-steps">
            <?php foreach ( $items as $step ) : ?>
            <li class="ablauf-step">
                <span class="step-nr" aria-hidden="true"><?php echo esc_html( $step['abl_nr'] ); ?></span>
                <div class="step-body">
                    <h3><?php echo esc_html( $step['abl_heading'] ); ?></h3>
                    <?php if ( $step['abl_body'] ) : ?>
                        <p><?php echo esc_html( $step['abl_body'] ); ?></p>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ol>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 4.3 : Vérifier dans WP-Admin**

Aller dans `http://localhost:8000/wp-admin` → Pages → Landing → "Sektion hinzufügen" → le layout "Erster Besuch (Ablauf)" apparaît dans la liste.

- [ ] **Step 4.4 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/ablauf.php
git commit -m "feat: add ablauf layout (Erster Besuch steps)"
```

---

## Task 5 : Layout `praxis` — Galerie

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php` (ajouter layout praxis)
- Crée : `template-parts/layouts/praxis.php`

- [ ] **Step 5.1 : Ajouter le layout dans inc/blocks.php**

Après `layout_ablauf` :

```php
/* ---------- PRAXIS GALERIE ---------- */
'layout_praxis' => [
    'key'        => 'layout_praxis',
    'name'       => 'praxis',
    'label'      => 'Praxis-Galerie',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'prx_eyebrow', 'Eyebrow', 'text' ),
        kc_field( 'prx_title',   'Überschrift', 'text' ),
        [
            'key'          => 'field_kc_prx_gallery',
            'label'        => 'Bilder',
            'name'         => 'gallery',
            'type'         => 'gallery',
            'instructions' => 'Fotos der Praxis (WebP empfohlen)',
            'min'          => 1,
        ],
        [
            'key'          => 'field_kc_prx_chips',
            'label'        => 'Filter-Chips',
            'name'         => 'chips',
            'type'         => 'repeater',
            'layout'       => 'table',
            'button_label' => 'Chip hinzufügen',
            'sub_fields'   => [
                kc_field( 'prx_chip_label', 'Label', 'text' ),
            ],
        ],
    ],
],
```

- [ ] **Step 5.2 : Créer template-parts/layouts/praxis.php**

```php
<?php
/**
 * Layout: Praxis-Galerie
 * Felder: prx_eyebrow, prx_title, gallery (ACF Gallery), chips (Repeater)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'prx_eyebrow' );
$title   = get_sub_field( 'prx_title' );
$gallery = get_sub_field( 'gallery' );
$chips   = get_sub_field( 'chips' );
?>
<section class="section-praxis reveal" id="praxis">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $chips ) : ?>
        <div class="chips" role="list">
            <?php foreach ( $chips as $chip ) : ?>
                <span class="chip" role="listitem"><?php echo esc_html( $chip['prx_chip_label'] ); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ( $gallery ) : ?>
        <div class="praxis-gallery">
            <?php foreach ( $gallery as $img ) : ?>
            <figure class="praxis-gallery__item">
                <img
                    src="<?php echo esc_url( $img['sizes']['large'] ?? $img['url'] ); ?>"
                    alt="<?php echo esc_attr( $img['alt'] ?: 'Praxis Kids Club' ); ?>"
                    loading="lazy"
                    width="<?php echo esc_attr( $img['sizes']['large-width'] ?? $img['width'] ); ?>"
                    height="<?php echo esc_attr( $img['sizes']['large-height'] ?? $img['height'] ); ?>"
                >
            </figure>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 5.3 : Vérifier dans WP-Admin**

Layout "Praxis-Galerie" visible dans la liste des sections → champ Galerie présent (nécessite ACF Pro).

- [ ] **Step 5.4 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/praxis.php
git commit -m "feat: add praxis gallery layout (ACF Pro gallery field)"
```

---

## Task 6 : Layout `team` — Behandler / Team

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php`
- Crée : `template-parts/layouts/team.php`

- [ ] **Step 6.1 : Ajouter le layout dans inc/blocks.php**

```php
/* ---------- TEAM ---------- */
'layout_team' => [
    'key'        => 'layout_team',
    'name'       => 'team',
    'label'      => 'Team / Behandler',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'tm_eyebrow', 'Eyebrow', 'text' ),
        kc_field( 'tm_title',   'Überschrift', 'text' ),
        [
            'key'          => 'field_kc_tm_members',
            'label'        => 'Teammitglieder',
            'name'         => 'members',
            'type'         => 'repeater',
            'layout'       => 'block',
            'button_label' => 'Teammitglied hinzufügen',
            'sub_fields'   => [
                [ 'key'=>'field_kc_tm_photo', 'label'=>'Foto',  'name'=>'photo', 'type'=>'image' ],
                kc_field( 'tm_name', 'Name', 'text' ),
                kc_field( 'tm_role', 'Rolle', 'text' ),
                kc_field( 'tm_bio',  'Kurztext', 'textarea' ),
            ],
        ],
    ],
],
```

- [ ] **Step 6.2 : Créer template-parts/layouts/team.php**

```php
<?php
/**
 * Layout: Team / Behandler
 * Felder: tm_eyebrow, tm_title, members (Repeater: photo, tm_name, tm_role, tm_bio)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'tm_eyebrow' );
$title   = get_sub_field( 'tm_title' );
$members = get_sub_field( 'members' );
?>
<section class="section-team reveal" id="team">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $members ) : ?>
        <div class="team-grid">
            <?php foreach ( $members as $member ) :
                $photo = $member['photo'];
            ?>
            <article class="team-card">
                <?php if ( $photo ) : ?>
                <div class="team-card__img">
                    <img
                        src="<?php echo esc_url( $photo['sizes']['medium'] ?? $photo['url'] ); ?>"
                        alt="<?php echo esc_attr( $photo['alt'] ?: $member['tm_name'] ); ?>"
                        loading="lazy"
                    >
                </div>
                <?php endif; ?>
                <div class="team-card__body">
                    <h3><?php echo esc_html( $member['tm_name'] ); ?></h3>
                    <p class="team-card__role"><?php echo esc_html( $member['tm_role'] ); ?></p>
                    <?php if ( $member['tm_bio'] ) : ?>
                        <p><?php echo esc_html( $member['tm_bio'] ); ?></p>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 6.3 : Vérifier dans WP-Admin** → layout "Team / Behandler" présent.

- [ ] **Step 6.4 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/team.php
git commit -m "feat: add team layout (portrait cards with repeater)"
```

---

## Task 7 : Layout `eltern` — Für Eltern (Accordéon)

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php`
- Crée : `template-parts/layouts/eltern.php`

- [ ] **Step 7.1 : Ajouter le layout dans inc/blocks.php**

```php
/* ---------- FÜR ELTERN (Akkordeon) ---------- */
'layout_eltern' => [
    'key'        => 'layout_eltern',
    'name'       => 'eltern',
    'label'      => 'Für Eltern',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'el_eyebrow', 'Eyebrow', 'text' ),
        kc_field( 'el_title',   'Überschrift', 'text' ),
        kc_field( 'el_text',    'Einleitung', 'textarea' ),
        [
            'key'          => 'field_kc_el_items',
            'label'        => 'FAQ-Punkte',
            'name'         => 'items',
            'type'         => 'repeater',
            'layout'       => 'block',
            'button_label' => 'Punkt hinzufügen',
            'sub_fields'   => [
                kc_field( 'el_icon',     'Icon-Slug', 'text' ),
                kc_field( 'el_question', 'Frage', 'text' ),
                kc_field( 'el_answer',   'Antwort', 'textarea' ),
            ],
        ],
    ],
],
```

- [ ] **Step 7.2 : Créer template-parts/layouts/eltern.php**

```php
<?php
/**
 * Layout: Für Eltern — Akkordeon mit Alpine.js
 * Felder: el_eyebrow, el_title, el_text, items (Repeater: el_icon, el_question, el_answer)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'el_eyebrow' );
$title   = get_sub_field( 'el_title' );
$text    = get_sub_field( 'el_text' );
$items   = get_sub_field( 'items' );
?>
<section class="section-eltern reveal" id="eltern">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $text ) : ?>
            <p class="section-lead"><?php echo esc_html( $text ); ?></p>
        <?php endif; ?>
        <?php if ( $items ) : ?>
        <div class="accordion" x-data="{ open: null }">
            <?php foreach ( $items as $i => $item ) : ?>
            <div class="accordion-item">
                <button
                    class="accordion-trigger"
                    @click="open === <?php echo $i; ?> ? open = null : open = <?php echo $i; ?>"
                    :aria-expanded="open === <?php echo $i; ?>"
                    type="button"
                >
                    <?php if ( $item['el_icon'] ) : ?>
                        <?php echo kc_icon( esc_attr( $item['el_icon'] ) ); ?>
                    <?php endif; ?>
                    <span><?php echo esc_html( $item['el_question'] ); ?></span>
                    <svg class="accordion-chevron" :class="{ 'is-open': open === <?php echo $i; ?> }"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </button>
                <div class="accordion-panel" x-show="open === <?php echo $i; ?>" x-transition>
                    <p><?php echo esc_html( $item['el_answer'] ); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 7.3 : Vérifier** → layout "Für Eltern" présent, accordéon fonctionnel avec Alpine.js.

- [ ] **Step 7.4 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/eltern.php
git commit -m "feat: add eltern accordion layout (Alpine.js x-show)"
```

---

## Task 8 : Layout `stimmen` — Kundenstimmen (Swiper)

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php`
- Crée : `template-parts/layouts/stimmen.php`

- [ ] **Step 8.1 : Ajouter le layout dans inc/blocks.php**

```php
/* ---------- KUNDENSTIMMEN (Swiper) ---------- */
'layout_stimmen' => [
    'key'        => 'layout_stimmen',
    'name'       => 'stimmen',
    'label'      => 'Kundenstimmen',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'st_eyebrow', 'Eyebrow', 'text' ),
        kc_field( 'st_title',   'Überschrift', 'text' ),
        [
            'key'          => 'field_kc_st_items',
            'label'        => 'Bewertungen',
            'name'         => 'items',
            'type'         => 'repeater',
            'layout'       => 'block',
            'button_label' => 'Bewertung hinzufügen',
            'sub_fields'   => [
                kc_field( 'st_quote', 'Zitat',  'textarea' ),
                kc_field( 'st_name',  'Name',   'text' ),
                kc_field( 'st_role',  'Rolle',  'text' ),
            ],
        ],
    ],
],
```

- [ ] **Step 8.2 : Créer template-parts/layouts/stimmen.php**

```php
<?php
/**
 * Layout: Kundenstimmen — Swiper-Slider
 * Felder: st_eyebrow, st_title, items (Repeater: st_quote, st_name, st_role)
 * Nutzt Swiper.js aus kidsclub.js — CSS-Klasse .stimmen-swiper muss initialisiert werden.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'st_eyebrow' );
$title   = get_sub_field( 'st_title' );
$items   = get_sub_field( 'items' );
?>
<section class="section-stimmen reveal" id="stimmen">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $items ) : ?>
        <div class="swiper stimmen-swiper" aria-roledescription="Karussell">
            <div class="swiper-wrapper" aria-live="polite">
                <?php foreach ( $items as $i => $item ) : ?>
                <div class="swiper-slide stimmen-card" role="group" aria-label="Bewertung <?php echo ( $i + 1 ); ?> von <?php echo count( $items ); ?>">
                    <blockquote class="stimmen-quote">
                        <p>&ldquo;<?php echo esc_html( $item['st_quote'] ); ?>&rdquo;</p>
                        <footer class="stimmen-author">
                            <strong><?php echo esc_html( $item['st_name'] ); ?></strong>
                            <?php if ( $item['st_role'] ) : ?>
                                <span><?php echo esc_html( $item['st_role'] ); ?></span>
                            <?php endif; ?>
                        </footer>
                    </blockquote>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination stimmen-swiper__pagination" aria-hidden="true"></div>
        </div>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 8.3 : Initialiser Swiper dans kidsclub.js**

Dans `assets/js/kidsclub.js`, ajouter l'initialisation du slider stimmen (si pas encore présent) :

```js
// Kundenstimmen Swiper
if (document.querySelector('.stimmen-swiper')) {
    new Swiper('.stimmen-swiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        pagination: { el: '.stimmen-swiper__pagination', clickable: true },
        breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } },
    });
}
```

- [ ] **Step 8.4 : Vérifier** → slider testimonials fonctionnel sur la page landing.

- [ ] **Step 8.5 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/stimmen.php assets/js/kidsclub.js
git commit -m "feat: add stimmen Swiper slider layout (testimonials)"
```

---

## Task 9 : Layout `faq` — FAQ + JSON-LD FAQPage

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php`
- Crée : `template-parts/layouts/faq.php`

- [ ] **Step 9.1 : Ajouter le layout dans inc/blocks.php**

```php
/* ---------- FAQ + JSON-LD ---------- */
'layout_faq' => [
    'key'        => 'layout_faq',
    'name'       => 'faq',
    'label'      => 'FAQ',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'fq_eyebrow', 'Eyebrow', 'text' ),
        kc_field( 'fq_title',   'Überschrift', 'text' ),
        [
            'key'          => 'field_kc_fq_items',
            'label'        => 'Fragen & Antworten',
            'name'         => 'items',
            'type'         => 'repeater',
            'layout'       => 'block',
            'button_label' => 'Frage hinzufügen',
            'sub_fields'   => [
                kc_field( 'fq_question', 'Frage',   'text' ),
                kc_field( 'fq_answer',   'Antwort', 'textarea' ),
            ],
        ],
    ],
],
```

- [ ] **Step 9.2 : Créer template-parts/layouts/faq.php**

```php
<?php
/**
 * Layout: FAQ — Akkordeon + JSON-LD FAQPage schema
 * Felder: fq_eyebrow, fq_title, items (Repeater: fq_question, fq_answer)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'fq_eyebrow' );
$title   = get_sub_field( 'fq_title' );
$items   = get_sub_field( 'items' );
?>
<section class="section-faq reveal" id="faq">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $items ) : ?>
        <div class="accordion faq-accordion" x-data="{ open: null }">
            <?php foreach ( $items as $i => $item ) : ?>
            <div class="accordion-item">
                <button
                    class="accordion-trigger"
                    @click="open === <?php echo $i; ?> ? open = null : open = <?php echo $i; ?>"
                    :aria-expanded="open === <?php echo $i; ?>"
                    type="button"
                >
                    <span><?php echo esc_html( $item['fq_question'] ); ?></span>
                    <svg class="accordion-chevron" :class="{ 'is-open': open === <?php echo $i; ?> }"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </button>
                <div class="accordion-panel" x-show="open === <?php echo $i; ?>" x-transition>
                    <p><?php echo esc_html( $item['fq_answer'] ); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- JSON-LD FAQPage schema (SEO / Rich Results) -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                <?php foreach ( $items as $i => $item ) : ?>
                {
                    "@type": "Question",
                    "name": <?php echo wp_json_encode( $item['fq_question'] ); ?>,
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": <?php echo wp_json_encode( $item['fq_answer'] ); ?>
                    }
                }<?php echo ( $i < count( $items ) - 1 ) ? ',' : ''; ?>

                <?php endforeach; ?>
            ]
        }
        </script>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 9.3 : Vérifier** → accordéon FAQ + `<script type="application/ld+json">` présent dans le code source.

- [ ] **Step 9.4 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/faq.php
git commit -m "feat: add faq accordion layout with FAQPage JSON-LD schema"
```

---

## Task 10 : Layout `termin` — Terminbuchung (QR + Embed)

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php`
- Crée : `template-parts/layouts/termin.php`

- [ ] **Step 10.1 : Ajouter le layout dans inc/blocks.php**

```php
/* ---------- TERMIN (QR + Buchungs-Embed) ---------- */
'layout_termin' => [
    'key'        => 'layout_termin',
    'name'       => 'termin',
    'label'      => 'Termin buchen',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'tr_eyebrow', 'Eyebrow', 'text' ),
        kc_field( 'tr_title',   'Überschrift', 'text' ),
        kc_field( 'tr_text',    'Text', 'textarea' ),
        [ 'key'=>'field_kc_tr_qr',    'label'=>'QR-Code Bild',    'name'=>'qr_image',    'type'=>'image' ],
        [ 'key'=>'field_kc_tr_embed', 'label'=>'Buchungs-Embed-Code', 'name'=>'embed_code',  'type'=>'textarea',
          'instructions'=>'iframe oder Script-Tag des Buchungs-Tools (z. B. Doctolib).' ],
    ],
],
```

- [ ] **Step 10.2 : Créer template-parts/layouts/termin.php**

```php
<?php
/**
 * Layout: Termin buchen — QR-Code + Buchungs-Embed
 * Felder: tr_eyebrow, tr_title, tr_text, qr_image (image), embed_code (textarea)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow    = get_sub_field( 'tr_eyebrow' );
$title      = get_sub_field( 'tr_title' );
$text       = get_sub_field( 'tr_text' );
$qr         = get_sub_field( 'qr_image' );
$embed_code = get_sub_field( 'embed_code' );
?>
<section class="section-termin reveal" id="termin">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $text ) : ?>
            <p class="section-lead"><?php echo esc_html( $text ); ?></p>
        <?php endif; ?>
        <div class="termin-layout">
            <?php if ( $qr ) : ?>
            <div class="termin-qr">
                <img
                    src="<?php echo esc_url( $qr['url'] ); ?>"
                    alt="QR-Code für Online-Terminbuchung"
                    width="200" height="200"
                    loading="lazy"
                >
                <p class="termin-qr__label">QR-Code scannen</p>
            </div>
            <?php endif; ?>
            <?php if ( $embed_code ) : ?>
            <div class="termin-embed">
                <?php echo wp_kses_post( $embed_code ); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
```

- [ ] **Step 10.3 : Vérifier** → layout "Termin buchen" présent, champs QR + embed visibles.

- [ ] **Step 10.4 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/termin.php
git commit -m "feat: add termin booking layout (QR code + embed)"
```

---

## Task 11 : Layout `kontakt` — Kontaktformular (CF7)

> **Agent :** Utiliser l'**ACF Flexible Block Builder** pour ce bloc.

**Files:**
- Modifie : `inc/blocks.php`
- Crée : `template-parts/layouts/kontakt.php`

- [ ] **Step 11.1 : Ajouter le layout dans inc/blocks.php**

```php
/* ---------- KONTAKT (CF7 Shortcode) ---------- */
'layout_kontakt' => [
    'key'        => 'layout_kontakt',
    'name'       => 'kontakt',
    'label'      => 'Kontakt',
    'display'    => 'block',
    'sub_fields' => [
        kc_field( 'kt_eyebrow',   'Eyebrow', 'text' ),
        kc_field( 'kt_title',     'Überschrift', 'text' ),
        kc_field( 'kt_text',      'Text', 'textarea' ),
        [
            'key'          => 'field_kc_kt_shortcode',
            'label'        => 'Formular-Shortcode',
            'name'         => 'form_shortcode',
            'type'         => 'text',
            'instructions' => 'z. B. [contact-form-7 id="123" title="Kontakt"]',
        ],
    ],
],
```

- [ ] **Step 11.2 : Créer template-parts/layouts/kontakt.php**

```php
<?php
/**
 * Layout: Kontakt — CF7-Formular via Shortcode
 * Felder: kt_eyebrow, kt_title, kt_text, form_shortcode (text)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow        = get_sub_field( 'kt_eyebrow' );
$title          = get_sub_field( 'kt_title' );
$text           = get_sub_field( 'kt_text' );
$form_shortcode = get_sub_field( 'form_shortcode' );
?>
<section class="section-kontakt reveal" id="kontakt">
    <div class="container">
        <?php if ( $eyebrow ) : ?>
            <span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $text ) : ?>
            <p class="section-lead"><?php echo esc_html( $text ); ?></p>
        <?php endif; ?>
        <?php if ( $form_shortcode ) : ?>
        <div class="kontakt-form">
            <?php echo do_shortcode( sanitize_text_field( $form_shortcode ) ); ?>
        </div>
        <?php endif; ?>
    </div>
</section>
```

- [ ] **Step 11.3 : Vérifier** → layout "Kontakt" présent.

- [ ] **Step 11.4 : Commit**

```bash
git add inc/blocks.php template-parts/layouts/kontakt.php
git commit -m "feat: add kontakt CF7 shortcode layout"
```

---

## Task 12 : Créer la page landing + vérification finale

**Files:**
- Aucun fichier créé — configuration WordPress via WP-CLI

- [ ] **Step 12.1 : Créer la page landing dans WordPress**

```bash
wp post create \
  --post_type=page \
  --post_title="Kids Club — Startseite" \
  --post_status=publish \
  --page_template=page-landing.php \
  --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
```

- [ ] **Step 12.2 : Définir comme page d'accueil**

```bash
# Récupérer l'ID de la page créée (remplacer XX par l'ID retourné)
wp option update show_on_front page --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
wp option update page_on_front XX  --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
```

- [ ] **Step 12.3 : Vérifier les 11 sections dans WP-Admin**

Aller dans `http://localhost:8000/wp-admin` → Pages → Kids Club Startseite → "Sektion hinzufügen" → confirmer que les 11 layouts apparaissent :

```
✅ Hero (Banner)
✅ Leistungsspektrum
✅ 5 Zimmer
✅ Erster Besuch (Ablauf)
✅ Praxis-Galerie
✅ Team / Behandler
✅ Für Eltern
✅ Kundenstimmen
✅ FAQ
✅ Termin buchen
✅ Kontakt
```

- [ ] **Step 12.4 : Vérifier les erreurs PHP**

```bash
wp --info --path=/Users/mdiarrisso/PhpstormProjects/kidsclub-wp
tail -50 /Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/debug.log 2>/dev/null || echo "No debug log"
```

- [ ] **Step 12.5 : Activer le debug WordPress pour le développement**

Dans `wp-config.php`, vérifier que ces lignes existent (WP-CLI les ajoute normalement) :

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

- [ ] **Step 12.6 : Vérifier sync acf-json/**

```bash
ls -la /Users/mdiarrisso/PhpstormProjects/kidsclub-wp/wp-content/themes/kidsclub/acf-json/
```

Attendu : un fichier `.json` créé automatiquement par ACF Pro lors de la première sauvegarde du groupe de champs.

- [ ] **Step 12.7 : Commit final**

```bash
git add -A
git commit -m "feat: landing page created, all 11 ACF sections verified"
```

---

## Checklist de succès finale

```
✅ wp core version  →  6.x
✅ wp plugin list   →  advanced-custom-fields-pro  Active
✅ wp theme status  →  kidsclub  Active
✅ http://localhost:8000  →  site WordPress Kids Club visible
✅ 11 layouts disponibles dans l'éditeur ACF
✅ acf-json/ contient le fichier JSON (sync activé)
✅ Alpine.js chargé (accordéons fonctionnels)
✅ Swiper stimmen initialisé
✅ JSON-LD FAQPage dans le source de la page FAQ
✅ Aucune erreur dans wp-content/debug.log
```
