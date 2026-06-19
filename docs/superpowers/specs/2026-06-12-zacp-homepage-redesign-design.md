# Spec — Intégration homepage ZACP (refonte fidèle au PDF)

**Date :** 2026-06-12
**Branche :** `feature/zacp-homepage-redesign`
**Référence design :** `ZACP/Assets/Homepage.pdf` (1700×7308 px, homepage scrollable complète)
**Contenu source :** `ZACP/Homepage Kids Club ZACP.docx`

---

## 1. Objectif

Refondre **uniquement la homepage** du thème *Kids Club by zacp* pour qu'elle corresponde
fidèlement (pixel-fidélité) à la maquette `Homepage.pdf`. On abandonne l'esthétique
actuelle « graffiti / marker / grain » au profit du style épuré et plat du PDF.

**Hors périmètre :** sous-pages (Leistungen détaillées, Team, Praxis, Elterninfos…),
qui feront l'objet d'un chantier ultérieur.

## 2. Décisions de cadrage (validées)

| Sujet | Décision |
|---|---|
| Périmètre | Homepage seule |
| Esthétique | Fidélité totale au PDF (Jost, fond clair, plat, sans grain) |
| Hero | Layout statique du PDF **+ vidéo spray en fond** (moteur image/vidéo ACF déjà présent) |
| Contenu | Reskin **+ vrais textes** du `.docx` injectés comme contenu démo + ajustements ACF si besoin |
| Approche technique | **Rebuild markup + CSS par section** (fidélité maximale) |
| Booking Masinga | **Préservé / intouchable** (bouton + modal « Termin buchen ») |

## 3. Design tokens (cible PDF)

| Token | Valeur actuelle | Valeur cible (échantillonnée du PDF) |
|---|---|---|
| Police | Fredoka + Caveat + Nunito | **Jost** (un seul famille, tous poids) |
| `--magenta` | `#EC0A8C` | **`#EA4589`** |
| `--navy` / `--ink` | `#26257F` | **`#102E79`** |
| `--bg` (page) | `#FCE3E9` + grain overlay | **`#FFFFFF`** (grain retiré) |
| `--band` (sections) | — | **`#EFF2F0`** (gris clair) |
| Carte jaune (Putzschule) | — | **`#F3E3A6`** |
| Carte bleue (Prophylaxe) | — | **`#BBCCDA`** |
| Carte verte (Die Behandlung) | — | **`#C0CBC3`** |
| Carte rose (AngstpatientInnen) | — | **`#F9E9E2`** |

**Typographie :**
- Titres : Jost **Bold, MAJUSCULES**, couleur magenta.
- Corps : Jost Regular/Medium, couleur navy.
- `fonts.css` actuel (Fredoka/Caveat/Nunito) **remplacé** par un `fonts.css` Jost.
- Tokens `--font-marker` / `data-heads="marker"` et l'overlay grain (`body::after`) **supprimés**.

## 4. Assets à intégrer (copie `ZACP/Assets/` → `assets/` du thème)

| Source | Destination | Usage |
|---|---|---|
| `jost woff2/*.woff2` | `assets/fonts/jost/` | Police principale |
| `logo/logo quer.svg` | `assets/img/logo-quer.svg` | Logo header |
| `logo/logo hoch.svg` | `assets/img/logo-hoch.svg` | Logo footer / mobile |
| `symbols/Symbol1-5.svg` | `assets/img/symbols/` | Icônes illustrées des cartes Leistungen |
| `backgrounds/Spray1.svg`, `Spray2.svg` | `assets/img/` | Décor (optionnel) |
| `hero clips/spray-quer.mp4`, `spray-hoch.mp4` | `assets/video/` | Fond vidéo hero (desktop/mobile) |
| `Bildmaterial/*.jpg` | uploads / `assets/img/` | Photos cabinet (Praxis/Team démo) |

**Note Symbols :** les `Symbol*.svg` sont des **illustrations couleur 691×573**, pas des
glyphes 24×24. Ils ne passent donc PAS par `kc_icon()` (qui produit des SVG line 24×24).
Ils sont intégrés comme **fichiers référencés** (`<img src=…>` ou inline) dans les cartes.

## 5. Rebuild par section (markup + CSS)

1. **Header** (`header.php`)
   - Logo SVG `logo-quer.svg` (« Kids Club by zacp »).
   - Nav : Leistungen · Praxis · Team · Elterninfos · FAQ · Kontakt (lien actif souligné magenta).
   - Bouton pill magenta « 🗓 Termin buchen » → **conserve le déclencheur booking Masinga existant**.

2. **Hero** (`template-parts/layouts/hero.php`)
   - Titre « GESUNDE ZÄHNE VON ANFANG AN » (magenta, Jost bold, 2 lignes).
   - Fond : **vidéo spray** (`spray-quer` desktop / `spray-hoch` mobile) via le champ
     `hero_media_type=video` + `hero_video` déjà supporté ; image poster en fallback.
   - Overlay coloré façon collage du PDF.

3. **Willkommen** (intro)
   - Paragraphe centré ; « Herzlich Willkommen! » en gras magenta.
   - Texte réel du `.docx` (section « Willkommen im ZACP Kids Club »).

4. **Leistungen** (`template-parts/layouts/leistungen.php`)
   - Titre « LEISTUNGEN » + intro.
   - **Grille 4 cartes pastel** : Putzschule (jaune), Prophylaxe (bleu),
     Die Behandlung (vert), AngstpatientInnen (rose).
   - Chaque carte : couleur de fond dédiée + **Symbol SVG** illustré + titre + texte (du `.docx`).

5. **Die Praxis** (`template-parts/layouts/praxis.php` + `zimmer.php`)
   - Titre « DIE PRAXIS » + intro « fünf Behandlungszimmer ».
   - **Carousel** des 5 Zimmer (Swiper, flèches nav blanches) — réutilise les composants Swiper.

6. **Kids Club Team** (`template-parts/layouts/team.php`)
   - Titre « KIDS CLUB TEAM » magenta + grille cartes photo (nom + vita).

7. **Der erste Besuch** (`template-parts/layouts/ablauf.php`)
   - Titre « DER ERSTE BESUCH » + intro.
   - **Accordéon numéroté 1 / 2 / 3** : bandes jaune / vert / rose, gros chiffre fantôme à
     gauche, chevron à droite, question en navy avec partie en gras.

8. **Footer** (`footer.php`)
   - Bloc navy `#102E79` : description cabinet, colonne liens (Leistungen/Praxis/Team/FAQ),
     colonne contact (T / E / Newsletter), logo arche-cœur.
   - Réseaux sociaux (FB / IG) ; copyright ; liens légaux (Impressum · Datenschutzerklärung · AGB).

## 6. Ajustements ACF (`inc/blocks.php`)

- **Leistungen → items** : ajouter (si absents)
  - un champ **couleur de carte** (select : `jaune` / `bleu` / `vert` / `rose`),
  - un champ **symbole** (select : `symbol1` … `symbol5`) en remplacement/complément du champ
    `icon` actuel (slug `kc_icon`).
- Autres layouts (hero, team, praxis, ablauf) : réutilisation des champs existants ;
  ajustement uniquement si un champ manque pour reproduire le PDF.
- Tout ajout de champ doit rester rétro-compatible (pas de suppression de champ utilisé en prod).

## 7. Contenu réel injecté (depuis `.docx`)

Saisi comme contenu par défaut/démo de la homepage : Willkommen, les 4 cartes Leistungen
(Putzschule, Prophylaxe, Die Behandlung, AngstpatientInnen), intro Praxis, intro + items
« Der erste Besuch ».

## 8. Préservé / non touché

- Booking Masinga (bouton + modal « Termin buchen »).
- Moteur ACF Flexible Content (`template-parts/flexible.php`).
- Pages légales (`page-impressum.php`, `page-datenschutz.php`).
- SEO / schema (`inc/seo-meta.php`, `inc/schema.php`).

## 9. Vérification (avant tout déploiement)

1. Build / rebuild CSS.
2. Test visuel **Chrome** desktop + mobile, comparé au PDF **section par section**.
3. GIF du scroll complet de la homepage.
4. **Code-review obligatoire** (agent + CodeRabbit) sur le diff de branche.
5. Aucun déploiement tant que la review n'est pas validée et que l'utilisateur n'a pas dit « deploy ».

## 10. Risques & points d'attention

- **Booking Masinga** : ne pas casser le data-attribute / handler du bouton pendant le rebuild du header.
- **Swiper** : respecter les règles projet (padding-bottom sur `.swiper`, slidesPerView auto + width,
  shadow sur conteneur externe, `prefers-reduced-motion`, accessibilité aria).
- **Symbols 691×573** : optimiser (dimensions d'affichage contraintes, `loading="lazy"` hors hero).
- **Vidéo hero** : `preload="none"`, poster image, `muted/playsinline/autoplay`, respect reduced-motion.
- **Compat ACF** : ajouts de champs non destructifs.
