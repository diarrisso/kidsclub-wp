# Product

## Register

brand

## Platform

web

## Users

Les parents — en pratique surtout les mères — qui cherchent un dentiste pour leur enfant
dans la région d'Osnabrück. Ils lisent la page avant l'enfant, souvent sur mobile, souvent
le soir, parfois après une mauvaise expérience ailleurs. L'enfant est le patient ; le parent
est le lecteur, celui qui compare et qui décide. Le travail qu'il vient accomplir est simple
et lourd à la fois : trouver un endroit où confier son enfant sans appréhension, et prendre
le rendez-vous.

## Product Purpose

Kids Club by zacp est la page vitrine d'un cabinet de dentisterie pédiatrique
(Kinderzahnarztpraxis) à Osnabrück. Elle présente le cabinet, ses cinq salles de soin, son
équipe, son déroulé de première visite et ses réponses à l'angoisse dentaire
(Lachgas / Vollnarkose). Le succès se mesure à une seule chose : **le rendez-vous pris en
ligne**, via le widget Masinga Booking.

## Positioning

Un cabinet dentaire conçu de A à Z pour les enfants — pas un cabinet d'adultes qui accepte
aussi les enfants.

## Conversion & proof

- CTA principal : prendre rendez-vous en ligne (widget Masinga Booking, déclencheur
  `data-booking-open`). CTA secondaire, pour qui n'est pas prêt à s'engager : appeler le
  cabinet, où une vraie personne répond aux questions avant l'engagement.
- La ligne qu'un parent retient après 10 secondes : **« Ici mon enfant n'aura pas peur. »**
- Belief ladder — l'ordre des convictions à installer avant le clic :
  1. Ce sont de vrais médecins compétents (équipe nommée, Leistungen).
  2. Ils ne soignent que des enfants — c'est leur spécialité, pas une option.
  3. Mon enfant n'y aura pas peur (les lieux, l'ambiance, les réponses à l'angoisse).
  4. Réserver est simple et immédiat.
- La belief ladder décrit l'ordre des **convictions**, pas l'ordre des sections. Décision
  client du 2026-07-21 : l'ordre actuel de la page — hero, willkommen, leistungen, galerie,
  zimmer, team, ablauf, faq, stimmen, termin, kontakt — est **conservé tel quel**. Ne pas
  le réordonner au nom de cette échelle.
- Preuves disponibles : témoignages de parents (section `stimmen`), photos réelles du
  cabinet et des cinq Zimmer (CPT `praxis_foto`, galerie filtrable par `bereich`), équipe
  présentée nommément avec visages et fonctions (CPT `team`, taxonomie `funktion`).
  Ni presse, ni logos partenaires à ce jour.

## Brand Personality

Joyeux, rassurant, professionnel — dans cet ordre de perception mais jamais au détriment du
dernier. La joie est le premier contact (magenta, formes dessinées à la main, motif arche et
cœur) ; le calme est ce qui reste ; la compétence médicale est ce qui fait signer. Le ton
s'adresse à un adulte inquiet, pas à un enfant : il ne mime pas la voix d'un enfant pour
plaire à son parent. Toute la langue visible est l'allemand.

## Anti-references

- **La clinique froide.** Blanc hôpital, gris acier, vocabulaire technique, distance
  médicale. C'est exactement ce qui angoisse un enfant — et ce que le parent fuit.
- **L'infantile à l'excès.** Comic Sans, arcs-en-ciel, personnages cartoon en pagaille. Le
  parent n'y lit plus aucune compétence médicale ; la joie devient un aveu d'amateurisme.
- **La landing SaaS à la mode.** Dégradés, grilles de cartes identiques icône + titre + texte,
  gros chiffres statistiques, eyebrow en majuscules tracées au-dessus de chaque section.

## Design Principles

- **La compétence porte la joie, jamais l'inverse.** Chaque élément ludique doit cohabiter
  avec un signal de sérieux médical dans le même champ de vision. Un bloc qui n'amuse que
  l'enfant coûte la confiance du parent.
- **Montrer plutôt qu'affirmer.** Les vraies photos du cabinet, les vrais visages de
  l'équipe, les vrais témoignages font le travail qu'aucune formule ne fait. Un aplat de
  couleur là où devrait se trouver une photo du lieu est un défaut, pas de la retenue.
- **Répondre à la peur avant qu'elle soit formulée.** L'angoisse dentaire est le frein
  principal ; la page doit la nommer et y répondre d'elle-même plutôt qu'attendre la
  question.
- **Le rendez-vous n'est jamais à plus d'un écran.** Quel que soit l'endroit où le parent
  s'arrête de lire, le chemin vers la réservation — ou vers le téléphone — est visible.
- **Lisible par une grand-mère, attirant pour un enfant de six ans.** Les deux à la fois,
  sans compromis dégradant l'un des deux.

## Decisions on record

Trois arbitrages du 2026-07-21, à ne pas rouvrir sans le client :

- **Les symboles décoratifs des overlays restent blancs**, conformément au PDF final, même
  s'ils contrastent peu sur les pastels. Ils sont `aria-hidden` et hors périmètre WCAG. Seuls
  les éléments *fonctionnels* — titre, bouton de fermeture — sont passés en `--navy`.
- **La section `angst` (Lachgas / Vollnarkose) n'est volontairement pas publiée.** Le layout,
  les champs et le CSS existent et sont fonctionnels : leur absence de la page n'est pas un bug.
- **Le téléphone de l'en-tête s'affiche en icône seule**, sans le numéro. Le numéro reste le
  nom accessible du lien (`aria-label`) et reste visible dans le pied de page.
- **Les eyebrows des dix sections sont conservés.** L'audit les signalait comme un motif
  répétitif ; le client a tranché en faveur du maintien. Seule leur lisibilité a été corrigée.
  Voir « La Règle de l'Eyebrow Assumé » dans DESIGN.md — et n'y toucher sous aucun prétexte
  sur `zimmer`, dont le titre est vide.

## Accessibility & Inclusion

Cible **WCAG 2.1 AA** : contraste de 4.5:1 sur le corps de texte, focus visible (déjà en
place via `:focus-visible`, contour magenta 3px), navigation clavier complète. Chaque
animation doit avoir une alternative sous `prefers-reduced-motion: reduce` — les animations
du hero (`winken`, `huepfen`, `laufband`) et les carrousels Swiper en particulier. Les
grands-parents accompagnent souvent : corps de texte généreux (18px de base), cibles
tactiles larges, aucun gris pâle sur fond teinté.
