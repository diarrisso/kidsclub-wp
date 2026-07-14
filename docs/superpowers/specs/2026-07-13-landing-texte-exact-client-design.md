# Landing Kids Club — intégration du texte exact du client

**Date :** 2026-07-13
**Source :** `~/Downloads/Homepage Kids Club ZACP.docx` (livré par le client, 2026-07-08)
**Branche :** `feature/landing-texte-exact-client`

## Problème

La landing actuelle (page #5, « Kids Club — Startseite ») affiche des **réécritures condensées**
des textes du client. Le client veut **son texte exact** sur la landing page.

Trois écarts rendent l'intégration impossible en l'état :

1. **`leistungen.items.body` est un `textarea`** rendu par
   `wp_kses( $body, [ 'strong' => [] ] )` dans un `<p>` (`template-parts/layouts/leistungen.php:40`
   et `:69`). Seul `<strong>` survit — aucune liste, aucun sous-titre. Or le bloc
   « AngstpatientInnen » du client est composé de sous-sections et de **quatre listes à puces**.
2. **Aucun conteneur** n'existe pour les textes longs : « Der erste Zahnarztbesuch – ganz
   entspannt! » (5 paragraphes) n'est nulle part sur la page, et le bloc SEO
   « Kinderzahnarzt für Angstpatienten in Osnabrück » n'a pas de section.
3. **Trois traitements sont absents** de la page : Fissurenversiegelung, ICON (fondu dans une
   carte générique « Die Behandlung »), Extraktionen & Platzhalter. Pulpotomie et
   Wurzelkanalbehandlung sont fusionnés en un seul item alors que le client les traite séparément.

## Solution

### 1. Nouveau layout ACF `textblock` (générique, habillé aux composants du thème)

Un layout réutilisable, aux conventions du thème : `<section>` comme élément racine (exigé par
`template-parts/flexible.php`, qui y injecte le style de fond par regex), fond spray + couleur via
`kc_bg_spray_field()` / `kc_bg_color_field()`, en-tête `.section-head.center` + `.eyebrow` +
`.section-title`, animation `.reveal`.

**Champs** (labels en allemand — règle projet : toute l'UI éditeur est en allemand) :

| Champ | Type | Rôle |
|---|---|---|
| `tb_anchor` | text | Ancre optionnelle → `id` de la `<section>` (ex. `angst`), pour les liens internes et le menu |
| `tb_eyebrow` | text | Sur-titre |
| `tb_title` | text | Titre `<h2 class="section-title">` |
| `tb_style` | select | `fliesstext` (Fließtext) \| `karte` (Karte — carte pastel arrondie) |
| `tb_card_color` | select | Couleur de la carte (`yellow`/`blue`/`green`/`pink`), visible seulement si `tb_style = karte` |
| `tb_content` | **wysiwyg** (toolbar `full`) | Le corps : `<h3>`, `<ul>`, `<strong>`, `<a>` — rendu via `wp_kses_post()` |

**CSS nouveau : `.tb-prose`.** Il est indispensable, pas cosmétique : `kidsclub.css:12` applique
un reset global `ul{margin:0;padding:0;list-style:none}`. Sans styles dédiés, **les listes du
WYSIWYG s'afficheraient sans puce et sans retrait**. `.tb-prose` rhabille le contenu avec les
tokens existants (aucune couleur en dur) :

- `p` → `var(--ink-soft)`, `line-height:1.55`, largeur max ~860 px centrée (même largeur de
  lecture que `.section-eltern .accordion`)
- `h3` → `var(--font-display)`, weight 700, `var(--navy)`
- `ul > li` → **rétablit la puce** supprimée par le reset : puce `var(--magenta)`, retrait, interligne
- `strong` → `var(--navy)`
- `a` → `var(--magenta)`, souligné au survol (le `:focus-visible` est déjà géré globalement)
- variante `karte` → `border-radius: var(--r)`, `box-shadow: var(--shadow-sm)`, fond `var(--card-*)`
  — la facture exacte des cartes Leistungen et des `.accordion-item`

### 2. `leistungen.items.body` : `textarea` → `wysiwyg`

Rendu changé en `wp_kses_post( wpautop( $body ) )`, **sans** le `<p>` englobant (le WYSIWYG
fournit ses propres paragraphes).

`wpautop()` assure la **rétrocompatibilité** : les items déjà en base sont du texte brut sans
balise et resteraient collés sans lui ; il ne re-wrappe pas le contenu déjà en `<p>`.

`wp_kses_post()` autorise `<a href>` : la carte rose « AngstpatientInnen » peut donc porter son
lien vers `#angst` **sans nouveau champ ACF**.

Le découpage cartes/accordéon reste celui du template (`leistungen.php:19-20`) : **les 4 premiers
items = cartes colorées, le reste = accordéon** « Moderne Behandlung ».

### 3. Contenu

Poussé via `wp eval-file` + `update_field()` — **jamais** de `default_value` codé, **jamais** de
postmeta écrit à la main (règle projet). En **local d'abord** (`localhost:8090`), vérification par
relecture `get_field()`, prod seulement sur demande explicite.

## Ordre final des sections de la landing

```
hero
willkommen                      ← texte exact (5 §)
trenner
leistungen                      ← 11 items (4 cartes + 7 accordéon)
trenner
textblock  #angst               ← NOUVEAU · « Kinderzahnarzt für Angstpatienten in Osnabrück »
trenner
galerie
zimmer
trenner
team
trenner
ablauf                          ← 4 Tipps für die Eltern, texte exact
trenner
textblock  #erster-besuch       ← NOUVEAU · « Der erste Zahnarztbesuch – ganz entspannt! »
trenner
faq · stimmen · termin · kontakt   (inchangés)
```

## Mapping contenu — docx → champs ACF

| Bloc du docx | Destination | Type |
|---|---|---|
| « Willkommen im ZACP Kids Club » — **4 des 5 §** | `willkommen.text` | remplacement |
| « Unser Behandlungsspektrum… » — **le 5ᵉ § du Willkommen** | `leistungen.text` (Einleitung) | remplacement |
| ZACP Kids Putzschule | `leistungen.items[0]` — carte 🟡 `yellow` | remplacement |
| ZACP Prophylaxe (2 §) | `leistungen.items[1]` — carte 🔵 `blue` | remplacement |
| Fissurenversiegelung (2 §) | `leistungen.items[2]` — carte 🟢 `green` | **création** |
| AngstpatientInnen (1er §) + lien `#angst` | `leistungen.items[3]` — carte 🟣 `pink` | remplacement |
| ICON – ohne Bohren | `leistungen.items[4]` — accordéon | **création** (était fondu dans « Die Behandlung ») |
| Moderne Füllungstherapie (Komposite) | `leistungen.items[5]` — accordéon | remplacement |
| Pulpotomie (Milchzähne) | `leistungen.items[6]` — accordéon | **création** (était fusionné) |
| Wurzelkanalbehandlung (Milchzähne) | `leistungen.items[7]` — accordéon | **création** (était fusionné) |
| Kreidezähne (MIH) | `leistungen.items[8]` — accordéon | remplacement |
| Zahnextraktionen und Platzhalter | `leistungen.items[9]` — accordéon | **création** |
| Lachgassedierung und Vollnarkose | `leistungen.items[10]` — accordéon | remplacement |
| Bloc SEO « Angstpatienten Osnabrück » (sous-titres + 4 listes) | `textblock` ancre `angst` | **création** |
| « Der erste Zahnarztbesuch – ganz entspannt! » (5 §) | `textblock` ancre `erster-besuch` | **création** |
| Tipps für die Eltern (4) | `ablauf.items[0..3]` | vérification mot à mot |

Le champ `symbol` des 4 cartes conserve les symboles déjà choisis pour Putzschule, Prophylaxe et
AngstpatientInnen ; la nouvelle carte Fissurenversiegelung reprend le symbole de l'ancienne carte
« Die Behandlung » qu'elle remplace en position 3. Les items d'accordéon n'affichent pas de symbole.

## Fidélité au texte source

Politique retenue : **texte exact, sauf défauts objectifs**. Corrections appliquées, toutes
listées ici pour information du client :

1. **Doublon supprimé** — le bloc « Moderne Füllungstherapie mit Kunststoffen bei Kindern »
   figure **deux fois à l'identique** dans le docx (copier-coller). Publié une seule fois.
2. **`#` parasite retiré** — « individuelle Lösungen für Angstpatienten**#** » → sans le dièse.
3. **Grammaire** — « üben wie man alle Flächen des Zahnes effektiv **reinigen** » → « **reinigt** ».
4. **Titre normalisé** — « Fissuren Versieglung » (titre) → « **Fissurenversiegelung** », l'orthographe
   que le client utilise lui-même dans le corps du texte.

5. **Un paragraphe déplacé, pas dupliqué** — le § « Unser Behandlungsspektrum reicht von Vorsorge… »
   figure dans le bloc *Willkommen* du docx, mais décrit le spectre de traitement. Il devient
   l'**introduction des Leistungen** ; le Willkommen conserve les **quatre autres** paragraphes.
   Sans ce déplacement, le même paragraphe serait publié **deux fois sur la même page**.
6. **Deux libellés d'interface ajoutés** (absents du docx, nécessaires à la navigation) : le lien
   « Mehr erfahren » sur la carte AngstpatientInnen, et le sur-titre (eyebrow) des deux nouvelles
   sections. Aucun autre mot n'est ajouté au texte du client.

**Non corrigé, à signaler au client :** le document **alterne le tutoiement et le vouvoiement** —
le Willkommen dit « euch und eure Familie », les conseils aux parents disent « Sie / Ihr Kind ».
Le mix est **conservé tel quel** ; l'harmonisation est une décision éditoriale qui lui appartient.

## Hors périmètre

- **Praxis : Philosophie / Lage / Preise** — le docx ne fournit que les trois titres, **aucun
  texte**. Rien à intégrer. Le `textblock` est précisément le conteneur prêt à les recevoir.
- **Team** — le docx dit seulement « Fotos Ärzte mit Vita » / « Fotos Team ». Aucun texte, aucune
  photo fournie. Le CPT `team` existant reste inchangé.

## Vérification

- `composer test` (lint PHP + PHPStan niveau 5) doit passer.
- Relecture du contenu poussé via `get_field()` (local), item par item.
- Contrôle visuel dans Chrome — **uniquement après accord explicite de l'utilisateur**.
- Cache-bust : bump `$ver` dans `inc/enqueue.php` **et** `const CACHE` dans `sw.js` (le Service
  Worker a son propre cache-busting ; bumper `$ver` seul ne suffit pas).

## Risques

| Risque | Parade |
|---|---|
| Le passage `textarea` → `wysiwyg` mange le contenu existant | `wpautop()` + relecture des 8 items déjà en base après migration |
| La landing devient très longue (10 traitements en texte intégral) | Le curatif est replié dans l'accordéon « Moderne Behandlung » ; seules 4 cartes restent visibles |
| Une `<section>` absente du nouveau layout casse le fond spray, **silencieusement** | `textblock.php` a bien `<section>` comme élément racine (contrainte documentée dans `flexible.php`) |
| Perte du bouton de réservation Masinga | Le block `termin` n'est pas touché |
