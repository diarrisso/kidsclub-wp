---
target: page-landing.php (landing Kids Club)
total_score: 28
p0_count: 0
p1_count: 0
timestamp: 2026-07-21T16-21-22Z
slug: page-landing-php
---
⚠️ DEGRADED: single-context (le harness interdit l'invocation de sous-agents sans demande explicite)

Relance après les 10 commits de la branche feature/impeccable-init.

## Design Health Score

| # | Heuristique | Score | Évolution | Problème clé |
|---|-----------|-------|-----------|--------------|
| 1 | Visibilité de l'état | 3 | = | lien actif, .scrolled, accordéons, pagination |
| 2 | Système / monde réel | 3 | = | « Lachgas / Vollnarkose » non explicités |
| 3 | Contrôle et liberté | 3 | = | Escape + focus trap + inert : solide |
| 4 | Cohérence | 3 | = | style de bouton unifié ; eyebrow répété (choix client) |
| 5 | Prévention des erreurs | 2 | = | validation CF7 par défaut uniquement |
| 6 | Reconnaissance vs rappel | 3 | = | sections nommées, FAQ, Ablauf numéroté |
| 7 | Flexibilité | 3 | **+1** | le téléphone existe enfin dans l'en-tête |
| 8 | Esthétique / minimalisme | 3 | = | palette tenue |
| 9 | Récupération d'erreur | 2 | = | messages CF7 par défaut |
| 10 | Aide et documentation | 3 | = | FAQ + Ablauf + overlay Angst font le travail |
| **Total** | | **28/40** | **+1** | **Acceptable** |

## Anti-Patterns Verdict

Inchangé sur le fond. Les deux tells identifiés demeurent :

1. **Eyebrow sur 9 sections** — décision client explicite du 2026-07-21 de les
   conserver. Documenté dans DESIGN.md comme « La Règle de l'Eyebrow Assumé ».
2. **Grille de 4 cartes Leistungen** — sauvée par les 4 couleurs distinctes et
   la récompense de l'overlay.

Scan déterministe : `detect.mjs` retourne **0 finding** sur template-parts,
header.php, footer.php. Rappel de portée : le détecteur ne parse pas le PHP,
cette absence de finding ne vaut donc pas quitus.

## Ce qui a changé depuis le premier passage

- Titre d'overlay Angstpatient:innen : **1,18:1 → 10,53:1**. C'était le pire
  défaut du site, sur le texte que le parent le plus inquiet vient précisément lire.
- Téléphone dans l'en-tête (icône seule, 44×44) : le CTA secondaire de
  PRODUCT.md existe enfin dans le parcours.
- Contenu plus suspendu à JavaScript : `.kc-js` + secours 4 s.
- Vidéo hero 7,71 → 2,15 Mo ; 198 Ko de JS retirés des pages légales.
- Service worker : le cache périmé indéboulonnable est corrigé, une seule
  source de version.

## Priority Issues restants

### [P2] Prévention et récupération d'erreur laissées à CF7 par défaut
- **Pourquoi ça compte** : c'est le seul formulaire du site et le point de
  contact des parents qui n'appellent pas. Les messages par défaut de CF7 sont
  génériques et n'indiquent ni quel champ ni quoi faire. Un parent qui échoue
  là abandonne ; il n'y a pas de seconde tentative.
- **Fix** : messages CF7 personnalisés en allemand, nommant le champ et l'action.
- **Commande** : `/impeccable clarify`

### [P2] Deux couleurs de texte échouent encore WCAG AA
- `.hl` (surlignage magenta dans le corps de texte) : **3,68:1**, seuil 4,5:1.
- Placeholder des champs CF7 (`#9aa3b2`) : **2,54:1**.
- **Fix** : `--magenta-deep` pour `.hl` (4,71:1) ; `--ink-soft` pour le
  placeholder (7:1).
- **Commande** : `/impeccable colorize`

### [P3] Flèches du carrousel Einblicke à 32 px sur mobile
- `height:auto` sur `.swiper-nav-btn` laisse le ratio du SVG décider ; mesuré
  32 px à 360 et 390 px de large.
- **Commande** : `/impeccable adapt`

### [P3] `aria-expanded` n'existe qu'après hydratation d'Alpine
- Les accordéons FAQ/Leistungen/Ablauf portent `:aria-expanded` (liaison
  Alpine), donc l'attribut est absent du HTML servi. Un lecteur d'écran
  arrivant avant l'hydratation n'a pas l'état. `x-cloak` limite le flash
  visuel mais pas ce trou sémantique.
- **Fix** : `aria-expanded="false"` en dur dans le markup ; Alpine le remplace.

## Questions to Consider

- Le formulaire de contact est le seul chemin écrit vers le cabinet.
  Mérite-t-il encore les messages d'erreur d'usine ?
- Les 9 eyebrows sont conservés par décision : peut-on au moins leur faire dire
  autre chose que le titre qu'ils surplombent (7 sur 9 le répètent) ?
