---
target: page-landing.php (landing Kids Club)
total_score: 27
p0_count: 0
p1_count: 3
timestamp: 2026-07-21T06-58-08Z
slug: page-landing-php
---
⚠️ DEGRADED: single-context (le harness interdit l'invocation de sous-agents sans demande explicite de l'utilisateur)

## Design Health Score

| # | Heuristique | Score | Problème clé |
|---|-----------|-------|--------------|
| 1 | Visibilité de l'état du système | 3 | Bon : lien actif, `.scrolled`, états d'accordéon, pagination Swiper |
| 2 | Correspondance système / monde réel | 3 | Allemand naturel ; « Lachgas / Vollnarkose » reste du vocabulaire médical non explicité |
| 3 | Contrôle et liberté | 3 | Overlay : Escape + fermeture + piège de focus + inert. Rien à redire |
| 4 | Cohérence et standards | 3 | Style de bouton unifié, mais l'eyebrow est appliqué mécaniquement à 14 sections sur 15 |
| 5 | Prévention des erreurs | 2 | Validation CF7 par défaut uniquement ; pas de garde-fou propre au formulaire |
| 6 | Reconnaissance plutôt que rappel | 3 | Sections nommées, FAQ, Ablauf numéroté |
| 7 | Flexibilité et efficacité | 2 | **Aucun numéro de téléphone dans l'en-tête** — le CTA secondaire de la stratégie n'existe pas dans le parcours |
| 8 | Esthétique et minimalisme | 3 | Palette distinctive et tenue ; l'eyebrow systématique ajoute du bruit |
| 9 | Récupération d'erreur | 2 | Messages CF7 par défaut ; page offline soignée ; erreur d'embed visible aux seuls rédacteurs |
| 10 | Aide et documentation | 3 | FAQ + Ablauf + Angst font réellement le travail d'aide |
| **Total** | | **27/40** | **Acceptable — améliorations significatives nécessaires** |

## Anti-Patterns Verdict

**Évaluation LLM — le site ne « sent » pas l'IA.** La palette (magenta + navy + pastels de salle) est une donnée client, pas une invention de modèle ; la famille unique Jost tenue en 5 graisses est un choix, pas un défaut ; l'overlay plein écran à 1050 ms est un geste qu'aucun générateur ne produit spontanément. Deux tells subsistent :

1. **L'eyebrow majuscule tracé sur 14 des 15 layouts.** C'est le bannissement explicite du skill et l'anti-référence « landing SaaS » de PRODUCT.md, appliqué en grammaire de section.
2. **La grille Leistungen** = 4 cartes de même gabarit, icône + titre + texte + bouton. Sauvée par les 4 couleurs distinctes et la récompense de l'overlay, mais c'est la forme exacte de la « grille de cartes interchangeables ».

**Scan déterministe** (`detect.mjs`) : 7 findings, **tous dans `demo-blocks.html`** (fichier de démo local) — 2 couleurs hors palette, 3 tailles de police hors échelle, 1 « single font », 1 surabondance de tirets cadratins. **Le détecteur ne lit pas les `.php`** : zéro gabarit de production n'a été scanné. C'est une couverture partielle, pas un blanc-seing.

**Overlays visuels** : aucun. Pas d'inspection navigateur (interdite sans accord explicite).

## Overall Impression

C'est un thème artisanal soigné, pas un template. L'ingénierie d'accessibilité (piège de focus, `inert`, Escape, skip-link valide vers `<main id="top">`, 11 blocs `prefers-reduced-motion`) dépasse largement ce qu'on voit sur un site de cabinet. La plus grande opportunité n'est pas esthétique : **le parcours de conversion secondaire est absent du haut de page**. La stratégie dit « qui n'est pas prêt à réserver appelle » ; l'en-tête n'offre que le bouton de réservation. La règle CSS `.nav-phone` existe, aucun PHP ne la rend.

## What's Working

1. **La discipline des jetons.** `--section-pad`, `--gap-eyebrow`, `--gap-title`, `--gap-head` : une source de vérité unique pour le rythme vertical. C'est exactement ce qui manque à 95 % des thèmes et ce qui fait qu'un nouveau bloc tombe juste du premier coup.
2. **L'overlay Leistungen.** Couleur héritée de la carte, contenu décalé de 200 ms, fermeture qui pivote, `role="dialog"` + `aria-modal` + `aria-labelledby`, piège de focus, `inert` sur le reste de la page, Escape, et une échappatoire `prefers-reduced-motion` complète. Un composant de niveau produit.
3. **Le sérieux DSGVO.** Jost, Swiper et Alpine auto-hébergés, allowlist d'hôtes sur les iframes, `<script>` exclu du `wp_kses` de l'embed. Rien ne fuit vers un tiers.

## Priority Issues

### [P1] Le CTA secondaire n'existe pas dans le parcours
- **Pourquoi ça compte** : PRODUCT.md pose l'appel téléphonique comme repli pour le parent pas encore prêt. Sur la page, le seul `tel:` est dans le pied de page — au bout d'un défilement complet. Le parent hésitant n'a aucune porte de sortie visible ; il ferme l'onglet. La règle `.nav-phone` (header.php ligne ~251 du CSS) est du code mort qui prouve que l'intention existait.
- **Fix** : rendre le numéro dans `.nav-cta`, à côté du bouton de réservation, alimenté par le champ téléphone déjà présent dans les options (`inc/options.php:245`).
- **Commande** : `/impeccable layout`

### [P1] Les titres d'overlay sont illisibles (1,18:1 à 1,67:1)
- **Pourquoi ça compte** : `.lsov__title` est en `#fff` sur les quatre pastels de carte. Mesures : 1,65:1 sur bleu, 1,28:1 sur jaune, 1,67:1 sur vert, **1,18:1 sur rose**. Le seuil AA pour du gros texte est 3:1. C'est le plus grand texte du site (jusqu'à 4,4rem) et il est quasi invisible pour un œil vieillissant ou en plein soleil — le contexte exact d'un parent sur mobile.
- **Fix** : passer le titre en `--navy` (7,4:1 à 10,5:1 sur les mêmes fonds, déjà la couleur des sous-titres de l'overlay). Décision à valider avec le designer : le PDF final impose peut-être le blanc.
- **Commande** : `/impeccable colorize`

### [P1] L'eyebrow est devenu de la grammaire, pas de la voix
- **Pourquoi ça compte** : 14 layouts sur 15 ouvrent sur le même sur-titre majuscule tracé, en `#9AA3B2` — qui échoue par ailleurs le contraste (2,54:1). Répété partout il ne hiérarchise plus rien : il annonce « page générée ». C'est nommément l'anti-référence « landing SaaS » de PRODUCT.md.
- **Fix** : garder l'eyebrow sur 2 ou 3 sections où il porte une vraie information, le retirer ailleurs, et remonter sa couleur vers `--ink-soft` (7:1) là où il reste.
- **Commande** : `/impeccable typeset`

### [P2] Le contenu est suspendu à JavaScript
- **Pourquoi ça compte** : `section.reveal > * { opacity: 0 }` cache tout le contenu de chaque section jusqu'à ce qu'un IntersectionObserver ajoute `.in`. Il y a bien un repli si l'API manque et une échappatoire `prefers-reduced-motion`, mais si le JS échoue (erreur d'un autre script, blocage réseau), la page s'affiche **entièrement blanche** avec un DOM complet. Le skill est explicite : une révélation doit enrichir un état visible par défaut, pas le conditionner.
- **Fix** : inverser — contenu visible par défaut, animation ajoutée par une classe `js-reveal` posée par le JS lui-même.
- **Commande** : `/impeccable animate`

### [P2] Les cibles tactiles du CTA principal sont sous les 44 px
- **Pourquoi ça compte** : `.btn-lg{padding:4px 45px}` donne ≈ 37 px de haut, `.btn-sm{padding:7px 16px}` ≈ 35 px. Ce sont respectivement le bouton « Buchen » de la section Termin et le bouton de réservation de l'en-tête — les deux points de conversion, tous deux trop petits pour un pouce sur mobile.
- **Fix** : `min-height:44px` sur `.btn`, sans toucher au padding horizontal (le dessin reste identique).
- **Commande** : `/impeccable adapt`

## Persona Red Flags

**Jordan (première visite)** : le premier écran est une vidéo de 7,7 Mo en lecture automatique ; sur une connexion mobile lente, Jordan voit un poster figé sans savoir si la page charge. L'eyebrow gris à 2,54:1 au-dessus de chaque titre est illisible et ne l'aide pas à se repérer. Aucun numéro de téléphone visible avant le pied de page.

**Casey (mobile, une main, distraite)** : les deux boutons de réservation font moins de 44 px de haut. Le CTA principal est en haut de l'écran, hors zone du pouce. Les overlays plein écran durent 1050 ms — une éternité quand on est interrompu. Si elle change d'application pendant l'animation, elle revient sur un état indéterminé.

**Sam (lecteur d'écran, clavier)** : très bien servi sur la mécanique — skip-link fonctionnel, focus visible partout (contour magenta 3px), piège de focus et `inert` sur les overlays, Escape. Mais les titres d'overlay à 1,18:1 le laissent dehors s'il a une basse vision plutôt qu'une cécité, et les boutons d'accordéon n'ont pas d'`aria-expanded` **avant** l'hydratation d'Alpine (l'attribut n'existe qu'en liaison `:aria-expanded`).

**Le parent inquiet d'Osnabrück (persona projet, tiré de PRODUCT.md)** : cherche « est-ce que ça va faire mal ». La section Angst existe et compare Lachgas / Vollnarkose — excellent. Mais il ne peut ni appeler depuis le haut de page, ni lire le titre de l'overlay qui contient précisément la réponse détaillée.

## Minor Observations

- `.hl` (surlignage magenta dans le texte courant) est à 3,68:1 — sous le seuil de 4,5:1 pour du texte de lecture.
- Le placeholder des champs CF7 (`#9aa3b2`) est à 2,54:1.
- `body{overflow-x:hidden}` masque un débordement plutôt que de le corriger, et fait de `body` un conteneur de défilement — risque connu de rupture de `position:sticky` sur l'en-tête. À vérifier en navigateur.
- Les guillemets décoratifs `»` des témoignages sont en `#fff` sur `--room-orange` (1,18:1) : purement décoratifs, donc hors WCAG, mais quasi invisibles — ils ne remplissent pas leur rôle.
- Quatre couleurs de survol sont écrites en dur dans `.team-card:nth-child(...)` (`#5A8B6E`, `#B89020`, `#C8704A`, `#3A6878`) hors du système de jetons.

## Questions to Consider

- Si le parent n'a pas le droit d'appeler depuis le haut de page, à quoi sert d'avoir écrit l'appel comme repli stratégique ?
- Que perdrait la page si l'eyebrow disparaissait de 12 sections sur 14 ? Et que gagnerait celui qui reste ?
- L'ordre réel des sections (stocké en base) suit-il la belief ladder validée — compétence, spécialisation, peur, facilité — ou l'ordre historique du prototype ?
- Une vidéo de 7,7 Mo en lecture automatique dit-elle « cabinet moderne » ou « site qui rame » au parent qui la découvre en 4G dans une salle d'attente ?
