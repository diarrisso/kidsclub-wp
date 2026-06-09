# Kids Club by zacp — WordPress + ACF Theme : Design Spec

**Date :** 2026-06-09
**Statut :** Approuvé

---

## Contexte

Thème WordPress pour un cabinet dentaire pédiatrique (Kinderzahnarzt) — **Kids Club by zacp**.
Le thème est partiellement existant dans `/Users/mdiarrisso/PhpstormProjects/wordpress/` :
3 sections sur 11 sont construites (`hero`, `leistungen`, `zimmer`), 8 sont à compléter.

---

## Objectif

1. Installer WordPress core + ACF Pro via WP-CLI (zéro interface graphique)
2. Compléter le thème avec les 8 layouts manquants
3. Utiliser l'agent **ACF Flexible Block Builder** pour créer chaque bloc ACF

---

## Architecture retenue : ACF Flexible Content (Approche A)

Continuation de l'architecture existante :
- Une `page-landing.php` → champ `sections` (Flexible Content)
- Boucle de rendu dans `template-parts/flexible.php`
- Un partial PHP par section dans `template-parts/layouts/{name}.php`
- Champs ACF enregistrés en PHP dans `inc/blocks.php` (+ sync `acf-json/`)

---

## Structure des dossiers

```
/Users/mdiarrisso/PhpstormProjects/kidsclub-wp/   ← WordPress core
├── wp-config.php
├── wp-content/
│   ├── themes/
│   │   └── kidsclub/                             ← thème complet
│   │       ├── style.css                         ← À CRÉER
│   │       ├── functions.php                     ← À CRÉER
│   │       ├── index.php                         ← À CRÉER
│   │       ├── page-landing.php                  ← existant
│   │       ├── header.php / footer.php           ← existants
│   │       ├── acf-json/                         ← À CRÉER (sync ACF)
│   │       ├── inc/
│   │       │   ├── blocks.php                    ← existant + 8 layouts à ajouter
│   │       │   ├── enqueue.php                   ← existant
│   │       │   ├── icons.php                     ← existant
│   │       │   ├── options.php                   ← existant
│   │       │   └── schema.php                    ← existant
│   │       ├── template-parts/
│   │       │   ├── flexible.php                  ← existant
│   │       │   ├── layouts/
│   │       │   │   ├── hero.php                  ← existant
│   │       │   │   ├── leistungen.php            ← existant
│   │       │   │   ├── zimmer.php                ← existant
│   │       │   │   ├── ablauf.php                ← À CRÉER
│   │       │   │   ├── praxis.php                ← À CRÉER
│   │       │   │   ├── team.php                  ← À CRÉER
│   │       │   │   ├── eltern.php                ← À CRÉER
│   │       │   │   ├── stimmen.php               ← À CRÉER
│   │       │   │   ├── faq.php                   ← À CRÉER
│   │       │   │   ├── termin.php                ← À CRÉER
│   │       │   │   └── kontakt.php               ← À CRÉER
│   │       │   └── partials/
│   │       │       └── kids-svg.php              ← existant
│   │       └── assets/
│   │           ├── css/kidsclub.css              ← existant
│   │           ├── js/kidsclub.js                ← existant
│   │           └── img/                          ← existant
│   └── plugins/
│       └── advanced-custom-fields-pro/           ← ACF Pro zip (manuel)
```

---

## Plan d'installation WordPress

### Prérequis
- WP-CLI installé (`wp --info`)
- MySQL en cours d'exécution
- ACF Pro `.zip` disponible localement

### Étapes
1. `wp core download --path=kidsclub-wp --locale=de_DE`
2. Créer la DB : `mysql -u root -e "CREATE DATABASE kidsclub_wp;"`
3. `wp config create --dbname=kidsclub_wp --dbuser=root --dbpass=... --path=kidsclub-wp`
4. `wp core install --url=http://localhost:8000 --title="Kids Club" --admin_user=admin --admin_email=diarrisso49@gmail.com --path=kidsclub-wp`
5. Copier le thème : `cp -r wordpress/ kidsclub-wp/wp-content/themes/kidsclub/`
6. Créer `style.css`, `functions.php`, `index.php`, `acf-json/`
7. Décompresser ACF Pro dans `kidsclub-wp/wp-content/plugins/`
8. `wp theme activate kidsclub --path=kidsclub-wp`
9. `wp plugin activate advanced-custom-fields-pro --path=kidsclub-wp`
10. `php -S localhost:8000 -t kidsclub-wp`

---

## Fichiers à créer (hors blocs)

### `style.css` — en-tête WordPress obligatoire
```css
/*
Theme Name: Kids Club by zacp
Description: Landing page theme pour cabinet dentaire pédiatrique
Version: 1.0.0
Author: zacp
Text Domain: kidsclub
*/
```

### `functions.php` — point d'entrée
```php
<?php
require get_theme_file_path('inc/enqueue.php');
require get_theme_file_path('inc/icons.php');
require get_theme_file_path('inc/options.php');
require get_theme_file_path('inc/blocks.php');
require get_theme_file_path('inc/schema.php');
```

### `index.php` — fallback WordPress
```php
<?php get_header(); get_footer(); ?>
```

---

## Les 8 layouts manquants (via agent ACF Flexible Block Builder)

| # | Layout | Champs ACF | Composant HTML |
|---|--------|-----------|----------------|
| 1 | `ablauf` | Eyebrow (text), Titre (text), Repeater items: Numéro (text), Titre (text), Texte (textarea) | Steps numérotés verticaux |
| 2 | `praxis` | Eyebrow (text), Titre (text), Galerie (gallery), Repeater chips: Label (text) | Galerie photo + chips filtre |
| 3 | `team` | Eyebrow (text), Titre (text), Repeater membres: Photo (image), Nom (text), Rôle (text), Texte (textarea) | Cards portrait grille |
| 4 | `eltern` | Eyebrow (text), Titre (text), Texte intro (textarea), Repeater items: Icon (text), Question (text), Réponse (textarea) | Accordéon Alpine.js |
| 5 | `stimmen` | Eyebrow (text), Titre (text), Repeater citations: Citation (textarea), Nom (text), Rôle (text) | Slider citations (Swiper.js) |
| 6 | `faq` | Eyebrow (text), Titre (text), Repeater: Question (text), Réponse (textarea) | Accordéon + JSON-LD FAQPage |
| 7 | `termin` | Eyebrow (text), Titre (text), Texte (textarea), Image QR (image), Embed code (textarea) | QR code + iframe booking |
| 8 | `kontakt` | Eyebrow (text), Titre (text), Texte (textarea), Shortcode CF7 (text) | do_shortcode() |

### Notes importantes
- `faq` : injecter le schema JSON-LD `FAQPage` inline (déjà prévu dans `inc/schema.php`)
- `praxis` : champ **Gallery** nécessite ACF Pro
- `stimmen` : réutiliser le pattern Swiper.js déjà dans `assets/js/kidsclub.js`
- `eltern` + `faq` : accordéon via **Alpine.js** (`x-data`, `x-show`, `@click`)

---

## Agents utilisés

| Agent | Rôle |
|-------|------|
| **ACF Flexible Block Builder** | Créer chacun des 8 layouts (champs `inc/blocks.php` + partial `template-parts/layouts/`) |
| **WordPress ACF Block System** | Vérifier la cohérence globale, options page, sync acf-json |
| **Frontend Expert Tailwind Alpine** | CSS des nouveaux layouts (accordéon, slider, cards) |

---

## Critères de succès

- [ ] `wp core version` retourne `6.x`
- [ ] `wp plugin list` montre ACF Pro actif
- [ ] `wp theme status kidsclub` = Active
- [ ] Les 11 sections s'affichent sur la page landing (`http://localhost:8000`)
- [ ] ACF sync : `acf-json/` contient le fichier de groupe de champs
- [ ] Pas d'erreur PHP dans les logs WordPress
