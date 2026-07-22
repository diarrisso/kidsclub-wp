---
name: Kids Club by zacp
description: Kinderzahnarztpraxis à Osnabrück — magenta signal, bleu de confiance et grandes surfaces douces.
colors:
  magenta: "#EA4589"
  magenta-deep: "#D52E72"
  navy: "#102E79"
  ink-soft: "#4A568C"
  blau-lead: "#012F7D"
  bg: "#FFFFFF"
  band: "#EFF2F0"
  band-pink: "#FBB9C4"
  line: "#26257F24"
  card-yellow: "#F3E3A6"
  card-blue: "#BBCCDA"
  card-green: "#C0CBC3"
  card-pink: "#F9E9E2"
  room-green: "#BDCCC2"
  room-yellow: "#F7E29D"
  room-orange: "#FCE8E1"
  room-blue: "#98ACBA"
  room-lila: "#CCC8CE"
typography:
  display:
    fontFamily: "Jost, system-ui, sans-serif"
    fontSize: "clamp(2.2rem, 4.5vw, 3.8rem)"
    fontWeight: 700
    lineHeight: 1.05
    letterSpacing: "0.01em"
  headline:
    fontFamily: "Jost, system-ui, sans-serif"
    fontSize: "clamp(1.9rem, 3.5vw, 3.125rem)"
    fontWeight: 800
    lineHeight: 1
    letterSpacing: "0.057em"
  title:
    fontFamily: "Jost, system-ui, sans-serif"
    fontSize: "clamp(1.35rem, 1.8vw, 1.7rem)"
    fontWeight: 800
    lineHeight: 1.02
    letterSpacing: "0.01em"
  lead:
    fontFamily: "Jost, system-ui, sans-serif"
    fontSize: "clamp(1.1rem, 1.6vw, 1.5rem)"
    fontWeight: 400
    lineHeight: 1.43
    letterSpacing: "0.01em"
  body:
    fontFamily: "Jost, system-ui, sans-serif"
    fontSize: "18px"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "Jost, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 600
    lineHeight: 1
    letterSpacing: "0.08em"
rounded:
  sm: "13px"
  md: "18px"
  lg: "28px"
  pill: "999px"
spacing:
  section: "clamp(60px, 8vw, 100px)"
  eyebrow-gap: "14px"
  title-gap: "clamp(14px, 2vw, 20px)"
  head-gap: "clamp(38px, 5vw, 58px)"
components:
  button-primary:
    backgroundColor: "{colors.bg}"
    textColor: "{colors.magenta-deep}"
    rounded: "{rounded.pill}"
    padding: "15px 28px"
  button-primary-hover:
    backgroundColor: "{colors.magenta-deep}"
    textColor: "{colors.bg}"
    rounded: "{rounded.pill}"
  button-navy:
    backgroundColor: "{colors.navy}"
    textColor: "{colors.bg}"
    rounded: "{rounded.pill}"
    padding: "15px 28px"
  button-lg:
    rounded: "{rounded.pill}"
    padding: "4px 45px"
  chip:
    backgroundColor: "{colors.bg}"
    textColor: "{colors.navy}"
    rounded: "{rounded.pill}"
    padding: "11px 20px"
  chip-active:
    backgroundColor: "{colors.magenta}"
    textColor: "{colors.bg}"
    rounded: "{rounded.pill}"
  card-service:
    backgroundColor: "{colors.card-blue}"
    textColor: "{colors.navy}"
    rounded: "{rounded.lg}"
    padding: "clamp(26px, 2.4vw, 36px) clamp(24px, 2.2vw, 34px)"
  card-team:
    backgroundColor: "{colors.bg}"
    textColor: "{colors.navy}"
    rounded: "{rounded.lg}"
    padding: "24px 22px 28px"
  input-text:
    backgroundColor: "{colors.bg}"
    textColor: "{colors.navy}"
    rounded: "12px"
    padding: "18px 22px"
---

# Design System: Kids Club by zacp

## 1. Overview

**Creative North Star: "La salle d'attente qu'on n'a pas envie de quitter"**

Le système traduit un lieu physique avant de traduire une marque. Les quatre couleurs de
cartes (jaune, bleu, vert, rose) et les cinq couleurs de Zimmer ne sont pas une palette
décorative : ce sont les salles du cabinet, transposées à l'écran. Un parent qui parcourt la
page doit reconnaître l'endroit quand il y entre pour de vrai. Rien ne pique, rien n'est
carré, rien ne brille : les rayons sont larges (28px sur les cartes, pilule totale sur tout
ce qui se clique) et les surfaces sont des aplats mats.

Sur cette douceur, deux couleurs cardinales font le travail sérieux. Le **Vertrauensblau**
`#102E79` porte tout le texte, toute la lecture, toute la crédibilité médicale. Le
**Kids-Club Magenta** `#EA4589` ne décrit rien : il signale. Titres, action, état actif,
anneau de focus. La joie est dans les surfaces ; la compétence est dans le texte. Cette
répartition est la traduction visuelle directe du principe stratégique « la compétence porte
la joie, jamais l'inverse ».

Le système rejette explicitement trois voisinages : la clinique froide (blanc hôpital, gris
acier), l'infantile à l'excès (arcs-en-ciel, cartoon en pagaille), et la landing SaaS à la
mode (dégradés, grilles de cartes interchangeables, gros chiffres). La couleur ici est
franche mais jamais criarde, dessinée mais jamais puérile.

**Key Characteristics:**
- Une seule famille typographique (Jost), le contraste vient des graisses 400 → 800 et des majuscules
- Deux couleurs cardinales + une famille de surfaces douces empruntées aux vraies salles
- Aucun angle vif : pilule (999px) pour l'interactif, 28px pour les conteneurs
- Plat au repos ; l'ombre n'apparaît qu'en réponse à un geste
- Un seul moment spectaculaire assumé : l'overlay Leistungen plein écran
- Tout le texte visible est en allemand, sans exception

## 2. Colors

Une base blanche quasi totale, deux couleurs cardinales qui ne se partagent jamais le même
rôle, et une famille de surfaces pastel empruntée aux salles du cabinet.

### Primary
- **Kids-Club Magenta** (`#EA4589`) : la couleur du signal. Titres de section, état actif,
  soulignement de navigation, anneau de focus, puces de liste dans les overlays. Jamais un
  fond de grande surface, jamais du texte courant.
- **Magenta Deep** (`#D52E72`) : la variante d'action. Bordure et texte du bouton principal
  au repos, fond de ce même bouton au survol. Plus dense que le magenta de signal, elle tient
  le contraste sur blanc là où le magenta clair échouerait.

### Secondary
- **Vertrauensblau** (`#102E79`) : le bleu de confiance. Porte l'intégralité du texte de
  lecture, les titres de cartes, le bloc contact, le bouton d'envoi du formulaire. C'est la
  couleur de l'encre, pas un accent.
- **Blau Lead** (`#012F7D`) : réservé aux descriptions de section (`.section-lead`,
  `.section-head .lead`). Un cran plus saturé que l'encre pour distinguer l'introduction du
  corps sans changer de taille.

### Tertiary
- **Surfaces de salle** — Sauge (`#BDCCC2`), Jaune doux (`#F7E29D`), Pêche (`#FCE8E1`),
  Bleu ardoise (`#98ACBA`), Lilas (`#CCC8CE`) : les cinq Zimmer, chacune identifiant une
  salle réelle. Fond des cartes Zimmer, liseré supérieur des cartes équipe (rotation par
  `nth-child(5n+…)`), fond des témoignages.
- **Surfaces de prestation** — Jaune (`#F3E3A6`), Bleu (`#BBCCDA`), Vert (`#C0CBC3`),
  Rose (`#F9E9E2`) : les quatre variantes de carte Leistungen. La carte et son overlay
  plein écran partagent obligatoirement la même couleur.

### Neutral
- **Blanc** (`#FFFFFF`) : le fond par défaut du site et la surface des cartes équipe, chips,
  champs de formulaire. Le blanc domine ; la couleur est l'exception.
- **Bande claire** (`#EFF2F0`) et **Bande rose** (`#FBB9C4`) : les seuls fonds de section
  non blancs, utilisés en pleine largeur pour rythmer le défilement.
- **Encre douce** (`#4A568C`) : texte secondaire, légendes, corps des cartes équipe.
- **Filet** (`#26257F24`) : séparateurs et bordures. Un bleu très transparent, jamais un gris.

### Named Rules

**La Règle des Deux Rôles.** Le magenta signale, le bleu raconte. Un texte de lecture n'est
jamais magenta ; un bouton d'action n'est jamais bleu clair. Si les deux couleurs commencent
à faire le même travail dans une section, la section est à refaire.

**La Règle de la Salle.** Une couleur de surface (`card-*`, `room-*`) désigne toujours la
même chose d'un bout à l'autre du site. Une carte Leistungen bleue ouvre un overlay bleu. On
ne pioche pas dans ces couleurs pour faire joli ailleurs.

**La Règle du Blanc Majoritaire.** Le blanc reste le fond dominant. Les bandes colorées
pleine largeur sont des respirations rythmiques, jamais l'état par défaut de la page.

> Le CSS contient aussi une palette de réserve de 13 couleurs fournie par le client
> (préfixe `--pal-*`) et cinq valeurs Figma (`--fig-*`) dont une seule est en service.
> Ce sont des réserves documentaires : ne pas les introduire dans un écran sans décision
> explicite du client.

## 3. Typography

**Display Font:** Jost (avec `system-ui, sans-serif` en secours)
**Body Font:** Jost — la même famille, cinq graisses auto-hébergées (400/500/600/700/800)
**Label/Mono Font:** aucune. Le système n'utilise pas de monospace.

**Character:** Une géométrique unique, tenue du plus léger au plus gras. Jost a la rondeur
d'un caractère de jeu et la rigueur d'un caractère de signalétique — exactement la tension
recherchée entre l'enfant et le médecin. Le contraste ne vient jamais d'un second caractère :
il vient de la graisse, de la casse et de l'échelle.

### Hierarchy
- **Display** (700, `clamp(2.2rem, 4.5vw, 3.8rem)`, 1.05, majuscules) : le titre du hero,
  seul H1 de la page.
- **Headline** (800, `clamp(1.9rem, 3.5vw, 3.125rem)`, 1, tracking `.057em`, majuscules) :
  les titres de section. Le tracking positif est délibéré — il empêche les majuscules grasses
  de se souder.
- **Title** (800, `clamp(1.35rem, 1.8vw, 1.7rem)`, 1.02, majuscules) : titres de cartes
  Leistungen et de Zimmer, en Vertrauensblau et non en magenta.
- **Lead** (400, `clamp(1.1rem, 1.6vw, 1.5rem)`, 1.43) : la description sous chaque titre de
  section, en Blau Lead. Une seule taille de lead pour toutes les sections du site.
- **Body** (400, 18px, 1.6) : le texte courant. Les proses d'overlay sont plafonnées à 60ch,
  les corps de carte à 34ch.
- **Label** (600, 1rem, tracking `.08em`, majuscules) : l'eyebrow au-dessus d'un titre de
  section, en gris `#9AA3B2` — magenta dans le hero uniquement.

### Named Rules

**La Règle de la Graisse.** Une hiérarchie se creuse par la graisse et la casse, jamais par
un second caractère. Introduire une deuxième famille dans ce système est un changement
d'identité, pas un ajustement.

**La Règle du Titre Bleu.** Les H1/H2/H3 sont magenta par défaut (`kidsclub.css`). Tout titre
posé sur un fond coloré ou une image doit donc redéclarer sa couleur — sans quoi il rend
magenta sur magenta, invisible.

**La Règle de l'Eyebrow Assumé.** L'eyebrow majuscule tracé coiffe **chaque** section, et
c'est une décision explicite du client (2026-07-21), pas un oubli : ne pas le retirer. Le
constat d'audit reste vrai — répété sur dix sections, dont sept où il ne fait que redire le
titre, il tient de la grammaire plus que de la voix, et c'est le seul point par lequel la page
frôle l'anti-référence « landing SaaS » de PRODUCT.md. Ce qui a été corrigé, c'est sa
lisibilité (2,54:1 → 7:1). ⚠️ L'eyebrow de la section `zimmer` est **structurel** : le titre de
cette section est vide, l'eyebrow porte seul son intitulé. Le vider décapiterait la section.

## 4. Elevation

Le système est **plat au repos**. Le bouton principal porte explicitement `box-shadow:none` —
c'est une décision, pas un oubli. Les conteneurs (cartes équipe, chips, formulaire) ne
portent qu'une ombre-souffle destinée à les détacher du blanc, jamais à simuler une hauteur.
La vraie élévation est réservée à la réponse à un geste : au survol, l'élément se soulève de
2 à 7px et son ombre s'amplifie d'un cran. Toutes les ombres sont teintées de bleu
(`rgba(38,37,127,…)`), jamais de noir — une ombre noire trahirait immédiatement la douceur du
système.

### Shadow Vocabulary
- **Souffle** (`box-shadow: 0 2px 12px rgba(38,37,127,.08)`) : état de repos des chips,
  cartes équipe, formulaire de contact, header au défilement.
- **Réponse** (`box-shadow: 0 18px 40px -18px rgba(38,37,127,.26)`) : état de survol des
  cartes et des boutons secondaires.
- **Portée** (`box-shadow: 0 42px 84px -34px rgba(38,37,127,.40)`) : survol du bouton navy,
  le seul cas où l'élévation devient spectaculaire.
- **Halo magenta** (`box-shadow: 0 18px 40px -16px color-mix(in oklab, #EA4589 56%, transparent)`) :
  réservé à l'état actif d'une chip de filtre.

### Named Rules

**La Règle du Geste.** Une ombre est une réponse, pas une décoration. Si un élément gagne une
ombre sans qu'un utilisateur l'ait survolé, focalisé ou ouvert, l'ombre est de trop.

**La Règle de l'Ombre Bleue.** Aucune ombre noire. Toute nouvelle ombre se compose sur
`rgba(38,37,127,…)`.

## 5. Components

### Buttons
- **Shape:** pilule intégrale (`999px`), quelle que soit la taille.
- **Primary:** fond blanc, texte et bordure 2px en Magenta Deep (`#D52E72`), padding
  `15px 28px`, aucune ombre. C'est le style unifié de tout le projet.
- **Hover / Focus:** le remplissage s'inverse — fond Magenta Deep, texte blanc, translation
  de −2px. Focus visible : contour magenta 3px, offset 3px (règle globale `:focus-visible`).
- **Navy:** fond Vertrauensblau, texte blanc, ombre Réponse au repos et Portée au survol.
  Réservé au second niveau d'action (envoi de formulaire).
- **Ghost / Light:** fond blanc à ombre-souffle, ou blanc translucide avec `backdrop-filter`
  pour les boutons posés sur une image de hero.
- **Tailles:** `btn-lg` (padding `4px 45px`) pour le CTA de réservation ; `btn-sm`
  (padding `7px 16px`) pour les actions secondaires.

### Chips
- **Style:** pilule blanche, texte Vertrauensblau en 700, padding `11px 20px`, ombre-souffle.
- **State:** au survol, fond magenta et texte blanc avec translation de −3px. L'état
  sélectionné (`--active`) fige ce fond magenta, ajoute le halo magenta et neutralise la
  translation. Utilisées comme filtres de la galerie Praxis (termes de la taxonomie `bereich`).

### Cards / Containers
- **Corner Style:** 28px sur les cartes et conteneurs (`--r-lg`), 18px par défaut, 13px pour
  les petits éléments.
- **Background:** carte Leistungen = une des quatre surfaces de prestation ; carte équipe =
  blanc avec un liseré supérieur emprunté aux couleurs de salle ; témoignage = Pêche.
- **Shadow Strategy:** voir Elevation — souffle au repos, réponse au survol.
- **Border:** aucune, hors le liseré supérieur coloré des cartes équipe.
- **Internal Padding:** `clamp(26px, 2.4vw, 36px)` verticalement, `clamp(24px, 2.2vw, 34px)`
  horizontalement sur les cartes de prestation.
- **Grid:** `repeat(auto-fit, minmax(280px, 1fr))` — la grille s'adapte sans point de rupture.

### Inputs / Fields
- **Style:** fond blanc, aucune bordure, rayon 12px, padding `18px 22px`, texte
  Vertrauensblau. Le champ se distingue par sa surface, pas par un trait.
- **Placeholder:** `#9AA3B2` à opacité pleine.
- **Case à cocher de consentement:** carré blanc 28px, rayon 6px, coche SVG bleue `#0E3A8E`
  injectée en `background-image` à l'état coché.
- **Submit:** pilule Vertrauensblau pleine, qui s'éclaircit en `#1A4FA8` et se soulève de 3px
  au survol.

### Navigation
- **Style:** header collant blanc, hauteur 86px, qui gagne un filet et une ombre-souffle au
  défilement (`.scrolled`).
- **Liens:** Jost 700, 1.1rem, Vertrauensblau. Le soulignement magenta 3px se déploie de la
  gauche vers la droite au survol (`right: 100% → 0`, 250ms) ; la page courante le garde
  déployé et passe en magenta.
- **Mobile:** overlay plein écran, avec bouton de fermeture et logo issus des options du thème.

### Overlay Leistungen (composant signature)
Le seul geste spectaculaire du système. Un panneau plein écran glisse depuis la droite en
1050 ms (`cubic-bezier(.22,.68,.24,1)`), dans la couleur exacte de la carte qui l'a ouvert.
Son contenu suit avec 200 ms de retard et une translation de 48px : ce décalage est ce qui
donne la sensation de matière. Titre blanc en majuscules jusqu'à `4.4rem` avec césure
allemande activée, bouton de fermeture qui pivote de 90° au survol, sections en deux colonnes
séparées par un filet bleu transparent, listes à tiret magenta. Sous
`prefers-reduced-motion: reduce`, tout est instantané et rien ne bouge.

## 6. Do's and Don'ts

### Do:
- **Do** utiliser les jetons de rythme existants — `--section-pad`, `--gap-eyebrow`,
  `--gap-title`, `--gap-head`. Un `clamp()` inventé sur place est la raison n°1 pour laquelle
  un nouveau bloc « sonne faux » à côté des autres.
- **Do** redéclarer explicitement la `color` de tout titre posé sur un fond coloré ou une
  image : la règle globale les rend magenta, donc invisibles sur magenta.
- **Do** garder la couleur de l'overlay identique à celle de la carte qui l'ouvre.
- **Do** écrire chaque chaîne visible en allemand — étiquettes ACF, instructions, boutons,
  contenus. Le français reste autorisé dans les commentaires de code, nulle part ailleurs.
- **Do** fournir une alternative sous `@media (prefers-reduced-motion: reduce)` pour toute
  animation ajoutée, carrousels Swiper compris.
- **Do** monter le texte vers l'encre plutôt que de l'alléger : le corps est en 18px et doit
  rester lisible par un grand-parent.

### Don't:
- **Don't** virer vers **la clinique froide** — blanc hôpital, gris acier, filets gris
  neutres. Les séparateurs sont bleus transparents, pas gris.
- **Don't** virer vers **l'infantile à l'excès** — Comic Sans, arcs-en-ciel, cartoon en
  pagaille. Le parent doit continuer à lire une compétence médicale.
- **Don't** virer vers **la landing SaaS à la mode** — dégradés, grilles de cartes
  interchangeables icône + titre + texte, gros chiffres statistiques, et surtout pas
  d'eyebrow majuscule tracé au-dessus de *chaque* section.
- **Don't** utiliser `background-clip: text` sur un dégradé, ni de bordure latérale colorée
  de plus d'1px comme accent de carte ou d'encadré.
- **Don't** ajouter d'ombre noire, ni d'ombre au repos sur un bouton principal.
- **Don't** introduire une seconde famille typographique : le contraste se fait à la graisse.
- **Don't** puiser dans les palettes de réserve `--pal-*` / `--fig-*` sans décision explicite
  du client — elles sont documentaires, pas disponibles.
- **Don't** éditer `kidsclub.min.css` sans contrôle de parité mécanique avec `kidsclub.css` :
  le fichier minifié est celui servi en production, une divergence y devient un bug
  invisible en local.

**Test d'audit en une phrase :** si un aplat de couleur occupe la place où devrait se trouver
une vraie photo du cabinet, de l'équipe ou d'une salle, le rendu est raté — quel que soit son
équilibre chromatique.
