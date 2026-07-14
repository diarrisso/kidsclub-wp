# Landing — texte exact du client : plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Publier le texte exact du client (docx du 2026-07-08) sur la landing Kids Club, en créant le layout ACF manquant et en débloquant le rendu des listes.

**Architecture:** Un nouveau layout ACF Flexible Content `textblock` (générique, WYSIWYG, habillé aux composants et tokens du thème) accueille les deux textes longs. Le champ `body` des Leistungen passe de `textarea` à `wysiwyg` pour permettre listes et sous-titres. Le contenu est ensuite poussé en base via `wp eval-file` + `update_field()`.

**Tech Stack:** WordPress + ACF Pro (champs déclarés en PHP dans `inc/blocks.php`), PHP 7.4+, CSS hand-authored (aucun build), Alpine.js, WP-CLI.

**Spec:** `docs/superpowers/specs/2026-07-13-landing-texte-exact-client-design.md`

## Global Constraints

- **Toute l'UI est en allemand** — labels ACF, `instructions`, `choices`, textes front. Le français est toléré **uniquement en commentaire de code**.
- **Aucun build CSS/JS.** `assets/css/kidsclub.min.css` est maintenu **à la main**. `inc/enqueue.php:17` charge le `.min.css` dès que `WP_DEBUG` est faux → **toute règle CSS doit être écrite dans les DEUX fichiers** (`kidsclub.css` et `kidsclub.min.css`), sinon elle marche en local et est invisible en production.
- **Cache-busting double** : bumper `$ver` dans `inc/enqueue.php:15` **et** `const CACHE` dans `assets/js/sw.js:6`. Bumper `$ver` seul ne suffit pas — le Service Worker sert du CSS stale indéfiniment.
- **Le contenu ne se code jamais en dur** : ni `default_value` ACF, ni postmeta écrit à la main. Toujours `get_field()` / `update_field()` via `wp eval-file`.
- **Tout layout doit avoir `<section>` comme élément racine** — `template-parts/flexible.php:44` y injecte le style de fond par regex. Sans `<section>`, le fond est perdu **silencieusement**.
- **Ne jamais casser le booking Masinga** : le layout `termin` et son bouton `data-booking-open` ne sont pas touchés.
- **Pas de déploiement** sans demande explicite. **Pas d'ouverture de Chrome** sans accord explicite.
- Environnement local : WordPress sur `http://localhost:8090`, racine `/Users/mdiarrisso/PhpstormProjects/kidsclub-wp`, thème symlinké depuis ce dépôt. Page landing = **ID 5**.
- Clé du field group ACF : **`group_kidsclub_landing`** (titre « Kids Club — Seiteninhalt »), `inc/blocks.php:28`.
- Commande de vérification : `composer test` (= `php -l` sur tout le PHP + PHPStan niveau 5, `--memory-limit=1G`).
- **`$SCRATCH`** désigne partout ci-dessous :
  `/private/tmp/claude-502/-Users-mdiarrisso-PhpstormProjects-wordpress/dbb36491-2312-4026-a24e-e3bb3b869d61/scratchpad`
  Les scripts `wp eval-file` y vivent — ils sont **temporaires et non versionnés**.
- ⚠️ **Le sandbox de Bash masque `docs/superpowers/`.** Les commandes `git` sur ce dossier doivent
  tourner **hors sandbox**, sinon `git add` échoue avec « pathspec did not match ». Le dossier est
  par ailleurs listé dans `.gitignore:25` alors que les specs/plans précédents **sont versionnés** →
  utiliser `git add -f`, conformément à l'historique.

---

## File Structure

| Fichier | Responsabilité | Action |
|---|---|---|
| `inc/blocks.php` | Déclaration des champs ACF | **Modifier** — ajouter `layout_textblock` ; passer `leistungen.items.body` en `wysiwyg` |
| `template-parts/layouts/textblock.php` | Markup du nouveau layout | **Créer** |
| `template-parts/layouts/leistungen.php` | Markup Leistungen | **Modifier** — lignes 40 et 69 (rendu du `body`) |
| `assets/css/kidsclub.css` | Styles source | **Modifier** — `.section-textblock`, `.tb-prose`, `.tb-card` |
| `assets/css/kidsclub.min.css` | Styles servis en prod | **Modifier** — mêmes règles, minifiées |
| `inc/enqueue.php` | Versioning des assets | **Modifier** — `$ver` 3.9.71 → 3.10.0 |
| `assets/js/sw.js` | Service Worker | **Modifier** — `CACHE` `kidsclub-v3.9.71` → `kidsclub-v3.10.0` |
| `scripts/content/2026-07-13-*.php` (scratchpad) | Scripts `wp eval-file` de contenu | **Créer** (temporaires, non versionnés) |

---

### Task 1 : Layout ACF `textblock` — déclaration des champs

**Files:**
- Modify: `inc/blocks.php` (ajouter le layout dans le tableau `layouts`, après `layout_willkommen`)

**Interfaces:**
- Consumes: `kc_bg_spray_field( $layout )`, `kc_bg_color_field( $layout )` (retournent chacun un **tableau de champs**, à étaler avec `...`), `kc_field( $name, $label, $type )` — définis en fin de `inc/blocks.php`.
- Produces: le layout `textblock` avec les sous-champs `bg_spray_preset`, `bg_spray_offset`, `bg_color_preset`, `background_color`, `tb_anchor`, `tb_eyebrow`, `tb_title`, `tb_style`, `tb_card_color`, `tb_content`. Les tâches 2, 5 et 6 en dépendent.

- [ ] **Step 1 : Écrire le test de présence du layout**

Créer `$SCRATCH/test-textblock-field.php` :

```php
<?php
// Vérifie que le layout "textblock" est bien enregistré avec tous ses sous-champs.
$group  = acf_get_field_group( 'group_kidsclub_landing' );
$fields = acf_get_fields( $group );
$flex   = null;
foreach ( $fields as $f ) {
	if ( 'flexible_content' === $f['type'] ) {
		$flex = $f;
	}
}
$names = wp_list_pluck( $flex['layouts'], 'name' );
echo 'LAYOUTS: ' . implode( ', ', $names ) . "\n";

if ( ! in_array( 'textblock', $names, true ) ) {
	echo "FAIL: layout 'textblock' absent\n";
	exit( 1 );
}
foreach ( $flex['layouts'] as $l ) {
	if ( 'textblock' !== $l['name'] ) {
		continue;
	}
	$subs = wp_list_pluck( $l['sub_fields'], 'name' );
	echo 'SUBFIELDS: ' . implode( ', ', $subs ) . "\n";
	$expected = [ 'bg_spray_preset', 'bg_color_preset', 'tb_anchor', 'tb_eyebrow', 'tb_title', 'tb_style', 'tb_card_color', 'tb_content' ];
	$missing  = array_diff( $expected, $subs );
	if ( $missing ) {
		echo 'FAIL: champs manquants -> ' . implode( ', ', $missing ) . "\n";
		exit( 1 );
	}
}
echo "PASS\n";
```

- [ ] **Step 2 : Lancer le test — il DOIT échouer**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp
wp eval-file "$SCRATCH/test-textblock-field.php"
```
Attendu : `FAIL: layout 'textblock' absent` et code de sortie 1.

- [ ] **Step 3 : Déclarer le layout dans `inc/blocks.php`**

Insérer ce bloc dans le tableau `layouts`, **juste après** `'layout_willkommen' => [ … ],` (qui se termine ligne 203) et **avant** le commentaire `/* ---------- LEISTUNGEN ---------- */` :

```php
								/* ---------- TEXTBLOCK (générique, réutilisable) ---------- */
								'layout_textblock'  => [
									'key'        => 'layout_textblock',
									'name'       => 'textblock',
									'label'      => 'Textblock (freier Text)',
									'display'    => 'block',
									'sub_fields' => [
										...kc_bg_spray_field( 'textblock' ),
										...kc_bg_color_field( 'textblock' ),
										[
											'key'          => 'field_kc_tb_anchor',
											'label'        => 'Anker-ID',
											'name'         => 'tb_anchor',
											'type'         => 'text',
											'instructions' => 'Optional. Ohne Raute, z. B. „angst“ — dann ist dieser Abschnitt über den Link #angst erreichbar.',
										],
										kc_field( 'tb_eyebrow', 'Eyebrow', 'text' ),
										kc_field( 'tb_title', 'Überschrift', 'text' ),
										[
											'key'           => 'field_kc_tb_style',
											'label'         => 'Darstellung',
											'name'          => 'tb_style',
											'type'          => 'select',
											'choices'       => [
												'fliesstext' => 'Fließtext (schlicht, zentriert)',
												'karte'      => 'Karte (Pastellfläche mit Schatten)',
											],
											'default_value' => 'fliesstext',
										],
										[
											'key'               => 'field_kc_tb_card_color',
											'label'             => 'Kartenfarbe',
											'name'              => 'tb_card_color',
											'type'              => 'select',
											'choices'           => [
												'yellow' => 'Gelb',
												'blue'   => 'Blau',
												'green'  => 'Grün',
												'pink'   => 'Rosa',
											],
											'default_value'     => 'pink',
											'conditional_logic' => [
												[
													[
														'field'    => 'field_kc_tb_style',
														'operator' => '==',
														'value'    => 'karte',
													],
												],
											],
										],
										[
											'key'          => 'field_kc_tb_content',
											'label'        => 'Inhalt',
											'name'         => 'tb_content',
											'type'         => 'wysiwyg',
											'media_upload' => 0,
											'tabs'         => 'visual',
											'toolbar'      => 'full',
											'instructions' => 'Zwischenüberschriften (H3), Aufzählungen und Fettungen sind erlaubt.',
										],
									],
								],
```

- [ ] **Step 4 : Relancer le test — il DOIT passer**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp
wp eval-file "$SCRATCH/test-textblock-field.php"
```
Attendu : `PASS`, et la ligne `SUBFIELDS:` liste les 10 sous-champs.

- [ ] **Step 5 : Lint + analyse statique**

```bash
cd /Users/mdiarrisso/PhpstormProjects/wordpress && composer test
```
Attendu : aucune erreur de syntaxe, PHPStan `[OK] No errors`.

- [ ] **Step 6 : Commit**

```bash
cd /Users/mdiarrisso/PhpstormProjects/wordpress
git add inc/blocks.php
git commit -m "feat(acf): add generic 'textblock' flexible layout"
```

---

### Task 2 : Template + CSS du `textblock`

**Files:**
- Create: `template-parts/layouts/textblock.php`
- Modify: `assets/css/kidsclub.css` (ajouter en fin de fichier, avant les media queries finales)
- Modify: `assets/css/kidsclub.min.css` (mêmes règles, minifiées)

**Interfaces:**
- Consumes: les sous-champs de la Task 1 (`tb_anchor`, `tb_eyebrow`, `tb_title`, `tb_style`, `tb_card_color`, `tb_content`).
- Produces: la classe CSS `.tb-prose` (styles du contenu WYSIWYG) et `.section-textblock`. Le rendu s'appuie sur les classes existantes `.section`, `.container`, `.section-head.center`, `.eyebrow`, `.section-title`, `.reveal`.

- [ ] **Step 1 : Écrire le test de rendu (il vérifie la puce des listes)**

Créer `$SCRATCH/test-textblock-render.php` — il ajoute une section `textblock` de démonstration à la page 5, rend la page, et vérifie que le `<ul>` sort bien **dans** `.tb-prose` :

```php
<?php
// Ajoute un textblock de démo en fin de page 5, rend la landing, vérifie le markup, puis nettoie.
$page_id = 5;
$rows    = get_field( 'sections', $page_id, false );
$backup  = $rows;

$rows[] = [
	'acf_fc_layout' => 'textblock',
	'tb_anchor'     => 'demo-test',
	'tb_eyebrow'    => 'Demo',
	'tb_title'      => 'Demo Überschrift',
	'tb_style'      => 'fliesstext',
	'tb_content'    => '<p>Absatz.</p><h3>Unterüberschrift</h3><ul><li>Erster Punkt</li><li>Zweiter Punkt</li></ul>',
];
update_field( 'sections', $rows, $page_id );

$html = file_get_contents( get_permalink( $page_id ) );

$ok = true;
foreach ( [
	'<section class="section section-textblock"'  => 'section racine + classe',
	'id="demo-test"'                              => 'ancre',
	'class="tb-prose'                             => 'conteneur prose',
	'<h3>Unterüberschrift</h3>'                   => 'sous-titre conservé',
	'<li>Erster Punkt</li>'                       => 'liste conservée',
] as $needle => $label ) {
	$found = false !== strpos( $html, $needle );
	echo ( $found ? 'OK   ' : 'FAIL ' ) . $label . "\n";
	$ok = $ok && $found;
}

// Nettoyage : on remet la page dans son état d'origine, quoi qu'il arrive.
update_field( 'sections', $backup, $page_id );

echo $ok ? "PASS\n" : "FAIL\n";
exit( $ok ? 0 : 1 );
```

- [ ] **Step 2 : Lancer le test — il DOIT échouer**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp && wp eval-file "$SCRATCH/test-textblock-render.php"
```
Attendu : plusieurs `FAIL` (le template `textblock.php` n'existe pas → `get_template_part` ne rend rien) et code de sortie 1.

- [ ] **Step 3 : Créer `template-parts/layouts/textblock.php`**

```php
<?php
/**
 * Layout: Textblock (freier Text).
 * Felder: tb_anchor, tb_eyebrow, tb_title, tb_style, tb_card_color, tb_content
 *
 * Générique : sert aux textes longs du client (Angstpatienten, erster Zahnarztbesuch) et,
 * plus tard, à Philosophie / Lage / Preise. Le contenu vient d'un WYSIWYG, donc il peut
 * contenir des <h3>, des <ul> et des liens — d'où le rendu via wp_kses_post().
 *
 * <section> DOIT rester l'élément racine : template-parts/flexible.php y injecte le style
 * de fond (spray + couleur) par regex. Sans lui, le fond est perdu silencieusement.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tb_anchor  = get_sub_field( 'tb_anchor' );
$tb_eyebrow = get_sub_field( 'tb_eyebrow' );
$tb_title   = get_sub_field( 'tb_title' );
$tb_style   = get_sub_field( 'tb_style' ) ?: 'fliesstext';
$tb_color   = get_sub_field( 'tb_card_color' ) ?: 'pink';
$tb_content = get_sub_field( 'tb_content' );

$tb_prose_class = 'tb-prose reveal';
if ( 'karte' === $tb_style ) {
	$tb_prose_class .= ' tb-card tb-card--' . $tb_color;
}
?>

<section class="section section-textblock"<?php echo $tb_anchor ? ' id="' . esc_attr( $tb_anchor ) . '"' : ''; ?>>
	<div class="container">
		<?php if ( $tb_eyebrow || $tb_title ) : ?>
			<div class="section-head center reveal">
				<?php if ( $tb_eyebrow ) : ?>
					<span class="eyebrow"><?php echo esc_html( $tb_eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( $tb_title ) : ?>
					<h2 class="section-title"><?php echo esc_html( $tb_title ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $tb_content ) : ?>
			<div class="<?php echo esc_attr( $tb_prose_class ); ?>">
				<?php echo wp_kses_post( $tb_content ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
```

- [ ] **Step 4 : Ajouter le CSS dans `assets/css/kidsclub.css`**

À placer juste après le bloc `.accordion…` (autour de la ligne 610), en gardant le style du fichier (une règle par ligne, pas d'espaces superflus) :

```css
/* ---------- TEXTBLOCK (freier Text aus dem WYSIWYG) ---------- */
.section-textblock .tb-prose{max-width:860px;margin:0 auto}
.tb-prose p{margin:0 0 1.1em;color:var(--ink-soft);font-size:clamp(1.02rem,1.2vw,1.14rem);line-height:1.62}
.tb-prose p:last-child{margin-bottom:0}
.tb-prose h3{font-family:var(--font-display);font-weight:700;font-size:clamp(1.25rem,1.8vw,1.5rem);line-height:1.25;color:var(--navy);margin:1.9em 0 .6em}
.tb-prose h3:first-child{margin-top:0}
.tb-prose strong{color:var(--navy);font-weight:700}
.tb-prose a{color:var(--magenta);font-weight:600}
.tb-prose a:hover{text-decoration:underline}
/* Das globale Reset (ul{list-style:none}) entfernt die Aufzählungszeichen — hier gezielt zurückholen. */
.tb-prose ul{list-style:none;margin:0 0 1.3em;padding:0;display:grid;gap:.5em}
.tb-prose ul li{position:relative;padding-left:1.6em;color:var(--ink-soft);line-height:1.55}
.tb-prose ul li::before{content:"";position:absolute;left:0;top:.55em;width:.5em;height:.5em;border-radius:50%;background:var(--magenta)}
.tb-prose ol{margin:0 0 1.3em;padding-left:1.4em;list-style:decimal;color:var(--ink-soft)}
.tb-prose ol li{margin-bottom:.45em;line-height:1.55}
/* Variante „Karte“ — gleiche Anmutung wie die Leistungs-Karten / .accordion-item. */
.tb-prose.tb-card{padding:clamp(24px,3.4vw,40px);border-radius:var(--r);box-shadow:var(--shadow-sm)}
.tb-card--yellow{background:var(--card-yellow)}
.tb-card--blue{background:var(--card-blue)}
.tb-card--green{background:var(--card-green)}
.tb-card--pink{background:var(--card-pink)}
```

- [ ] **Step 5 : Reporter les MÊMES règles dans `assets/css/kidsclub.min.css`**

Le fichier minifié est maintenu à la main et c'est **lui qui est servi en production** (`inc/enqueue.php:17`, dès que `WP_DEBUG` est faux). Ajouter les règles ci-dessus **en fin de fichier**, sur une seule ligne, sans le commentaire :

```css
.section-textblock .tb-prose{max-width:860px;margin:0 auto}.tb-prose p{margin:0 0 1.1em;color:var(--ink-soft);font-size:clamp(1.02rem,1.2vw,1.14rem);line-height:1.62}.tb-prose p:last-child{margin-bottom:0}.tb-prose h3{font-family:var(--font-display);font-weight:700;font-size:clamp(1.25rem,1.8vw,1.5rem);line-height:1.25;color:var(--navy);margin:1.9em 0 .6em}.tb-prose h3:first-child{margin-top:0}.tb-prose strong{color:var(--navy);font-weight:700}.tb-prose a{color:var(--magenta);font-weight:600}.tb-prose a:hover{text-decoration:underline}.tb-prose ul{list-style:none;margin:0 0 1.3em;padding:0;display:grid;gap:.5em}.tb-prose ul li{position:relative;padding-left:1.6em;color:var(--ink-soft);line-height:1.55}.tb-prose ul li::before{content:"";position:absolute;left:0;top:.55em;width:.5em;height:.5em;border-radius:50%;background:var(--magenta)}.tb-prose ol{margin:0 0 1.3em;padding-left:1.4em;list-style:decimal;color:var(--ink-soft)}.tb-prose ol li{margin-bottom:.45em;line-height:1.55}.tb-prose.tb-card{padding:clamp(24px,3.4vw,40px);border-radius:var(--r);box-shadow:var(--shadow-sm)}.tb-card--yellow{background:var(--card-yellow)}.tb-card--blue{background:var(--card-blue)}.tb-card--green{background:var(--card-green)}.tb-card--pink{background:var(--card-pink)}
```

- [ ] **Step 6 : Relancer le test de rendu — il DOIT passer**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp && wp eval-file "$SCRATCH/test-textblock-render.php"
```
Attendu : cinq lignes `OK` puis `PASS`.

- [ ] **Step 7 : Vérifier que les deux CSS portent bien la règle**

```bash
cd /Users/mdiarrisso/PhpstormProjects/wordpress
grep -c "tb-prose ul li::before" assets/css/kidsclub.css assets/css/kidsclub.min.css
```
Attendu : `1` pour **chacun** des deux fichiers. Un `0` sur le `.min.css` = style absent en production.

- [ ] **Step 8 : Lint + commit**

```bash
composer test
git add template-parts/layouts/textblock.php assets/css/kidsclub.css assets/css/kidsclub.min.css
git commit -m "feat(textblock): render template + prose styles (restores list bullets killed by the global reset)"
```

---

### Task 3 : Leistungen — `body` en WYSIWYG (rétrocompatible)

**Files:**
- Modify: `inc/blocks.php:280-284` (le `kc_field( 'body', 'Beschreibung', 'textarea' )` du repeater `items`)
- Modify: `template-parts/layouts/leistungen.php:40` et `:69`

**Interfaces:**
- Consumes: rien des tâches précédentes.
- Produces: `leistungen.items[].body` accepte désormais du HTML (`<p>`, `<ul>`, `<strong>`, `<a>`). La Task 4 en dépend pour écrire le contenu.

- [ ] **Step 1 : Écrire le test de rétrocompatibilité**

Le risque réel : les 8 items **déjà en base** sont du texte brut sans balise. Sans `wpautop()`, ils s'afficheraient collés. Créer `$SCRATCH/test-leistungen-body.php` :

```php
<?php
// 1) L'ancien contenu (texte brut, sans balise) doit toujours sortir dans un <p>.
// 2) Le nouveau contenu (HTML) doit conserver ses listes.
$page_id = 5;
$rows    = get_field( 'sections', $page_id, false );
$backup  = $rows;

foreach ( $rows as $i => $row ) {
	if ( 'leistungen' !== $row['acf_fc_layout'] ) {
		continue;
	}
	// Item 0 : texte brut (simule l'ancien contenu). Item 4 (accordéon) : HTML avec liste.
	$rows[ $i ]['items'][0]['body'] = 'Alter Text ohne jedes Tag.';
	$rows[ $i ]['items'][4]['body'] = '<p>Neuer Text.</p><ul><li>Punkt A</li></ul>';
}
update_field( 'sections', $rows, $page_id );

$html = file_get_contents( get_permalink( $page_id ) );

$ok = true;
foreach ( [
	'<p>Alter Text ohne jedes Tag.</p>' => 'ancien texte brut auto-paragraphé (wpautop)',
	'<li>Punkt A</li>'                  => 'liste conservée dans l\'accordéon',
] as $needle => $label ) {
	$found = false !== strpos( $html, $needle );
	echo ( $found ? 'OK   ' : 'FAIL ' ) . $label . "\n";
	$ok = $ok && $found;
}

update_field( 'sections', $backup, $page_id );
echo $ok ? "PASS\n" : "FAIL\n";
exit( $ok ? 0 : 1 );
```

- [ ] **Step 2 : Lancer le test — il DOIT échouer**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp && wp eval-file "$SCRATCH/test-leistungen-body.php"
```
Attendu : `FAIL liste conservée…` — `wp_kses( $body, ['strong' => []] )` supprime le `<ul>`.

- [ ] **Step 3 : Passer le champ en `wysiwyg` dans `inc/blocks.php`**

Remplacer (lignes 280-284) :

```php
												kc_field(
													'body',
													'Beschreibung',
													'textarea',
												),
```

par :

```php
												[
													'key'          => 'field_kc_body',
													'label'        => 'Beschreibung',
													'name'         => 'body',
													'type'         => 'wysiwyg',
													'media_upload' => 0,
													'tabs'         => 'visual',
													'toolbar'      => 'basic',
													'instructions' => 'Aufzählungen und Links sind erlaubt. Bestehende Texte bleiben unverändert.',
												],
```

**La clé reste `field_kc_body`** — exactement celle que produisait `kc_field( 'body', … )`. Changer la clé perdrait tout le contenu déjà saisi.

- [ ] **Step 4 : Adapter le rendu dans `template-parts/layouts/leistungen.php`**

Ligne 40 (carte) — remplacer :

```php
					<p><?php echo wp_kses( $card['body'], [ 'strong' => [] ] ); ?></p>
```

par :

```php
					<div class="ls-card-body"><?php echo wp_kses_post( wpautop( $card['body'] ) ); ?></div>
```

Ligne 69 (accordéon) — remplacer :

```php
						<p><?php echo wp_kses( $item['body'], [ 'strong' => [] ] ); ?></p>
```

par :

```php
						<div class="ls-acc-body"><?php echo wp_kses_post( wpautop( $item['body'] ) ); ?></div>
```

`wpautop()` enveloppe le texte brut hérité dans des `<p>` **sans** re-envelopper le HTML déjà structuré. `wp_kses_post()` laisse passer `<ul>`, `<h3>`, `<strong>` et `<a href>` — c'est ce qui permet à la carte Angst de porter son lien `#angst` sans nouveau champ ACF.

- [ ] **Step 5 : Ajouter le CSS des deux nouveaux conteneurs (les deux fichiers CSS)**

Les anciens `<p>` étaient stylés par des sélecteurs existants ; les nouveaux `<div>` doivent hériter des mêmes règles. Dans `kidsclub.css` **et** `kidsclub.min.css`, à côté du bloc textblock :

```css
.ls-card-body p,.ls-acc-body p{margin:0 0 .9em}
.ls-card-body p:last-child,.ls-acc-body p:last-child{margin-bottom:0}
.ls-card-body ul,.ls-acc-body ul{list-style:none;margin:.6em 0;padding:0;display:grid;gap:.4em}
.ls-card-body ul li,.ls-acc-body ul li{position:relative;padding-left:1.4em}
.ls-card-body ul li::before,.ls-acc-body ul li::before{content:"";position:absolute;left:0;top:.55em;width:.45em;height:.45em;border-radius:50%;background:var(--magenta)}
.ls-card-body a,.ls-acc-body a{color:var(--magenta);font-weight:600}
.ls-card-body a:hover,.ls-acc-body a:hover{text-decoration:underline}
```

- [ ] **Step 6 : Relancer le test — il DOIT passer**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp && wp eval-file "$SCRATCH/test-leistungen-body.php"
```
Attendu : `OK` sur les deux lignes, puis `PASS`.

- [ ] **Step 7 : Vérifier que les 8 items existants n'ont pas été abîmés**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp
wp eval '$r=get_field("sections",5); foreach($r as $row){ if($row["acf_fc_layout"]!=="leistungen") continue; foreach($row["items"] as $i=>$it){ printf("[%d] %-28s %d Zeichen\n",$i,$it["heading"],mb_strlen($it["body"])); } }'
```
Attendu : les 8 items, chacun avec un `body` **non vide** (aucun `0 Zeichen`).

- [ ] **Step 8 : Lint + commit**

```bash
cd /Users/mdiarrisso/PhpstormProjects/wordpress && composer test
git add inc/blocks.php template-parts/layouts/leistungen.php assets/css/kidsclub.css assets/css/kidsclub.min.css
git commit -m "feat(leistungen): body textarea -> wysiwyg, render via wp_kses_post(wpautop()) for lists and links"
```

---

### Task 4 : Contenu — Willkommen, Leistungen (11 items), Ablauf

**Files:**
- Create: `$SCRATCH/content-01-leistungen.php` (script `wp eval-file`, temporaire)

**Interfaces:**
- Consumes: `leistungen.items[].body` en WYSIWYG (Task 3).
- Produces: le contenu exact en base. La Task 5 réutilise la même mécanique `get_field(..., false)` → mutation → `update_field()`.

- [ ] **Step 1 : Sauvegarder le champ AVANT toute écriture**

Filet de sécurité : un dump JSON du champ `sections` avant modification.

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp
wp eval 'echo wp_json_encode( get_field( "sections", 5, false ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );' \
  > "$SCRATCH/backup-sections-page5.json"
wc -c "$SCRATCH/backup-sections-page5.json"
```
Attendu : un fichier **non vide** (> 5000 octets). S'il est vide, **s'arrêter** — ne rien écrire.

- [ ] **Step 2 : Écrire le script de contenu**

`$SCRATCH/content-01-leistungen.php`. Le texte est celui du client, mot pour mot, avec les 4 corrections listées dans la spec.

```php
<?php
$page_id = 5;
$rows    = get_field( 'sections', $page_id, false );

// --- WILLKOMMEN : 4 des 5 paragraphes du client.
// Le 5e (« Unser Behandlungsspektrum… ») devient l'introduction des Leistungen : il y a sa place,
// et le publier aux deux endroits reviendrait à afficher deux fois le même paragraphe.
$willkommen = '<p><strong>Willkommen im ZACP Kids Club</strong></p>'
	. '<p>Im ZACP Kids Club dreht sich alles um gesunde Zähne – und um Kinder, die sich bei uns wohlfühlen. Unsere Praxis für Kinder- und Jugendzahnheilkunde liegt im Herzen von Osnabrück und ist ein Ort, an dem Zahnarztbesuche entspannt, altersgerecht und angstfrei ablaufen dürfen.</p>'
	. '<p>Wir begleiten Kinder und Jugendliche vom ersten Milchzahn bis ins junge Erwachsenenalter. Dabei nehmen wir uns Zeit, erklären alles verständlich und begegnen unseren kleinen Patientinnen und Patienten mit viel Geduld, Einfühlungsvermögen und einem offenen Ohr. Spielerische Elemente, eine kindgerechte Umgebung und ein liebevolles Team sorgen dafür, dass Vertrauen wachsen kann – die wichtigste Grundlage für eine gute Zahngesundheit.</p>'
	. '<p>Dabei arbeiten wir nach modernen, wissenschaftlich fundierten Standards. Insbesondere legen wir großen Wert auf Prävention. Unser Motto für eure Zukunft: Weg von der Reparatur-Zahnmedizin zur optimalen Vorsorge!</p>'
	. '<p>Der ZACP Kids Club ist mehr als eine Zahnarztpraxis: Er ist ein Ort, an dem Kinder gerne hingehen und Eltern sich gut aufgehoben fühlen. Wir freuen uns darauf, euch und eure Familie kennenzulernen!</p>';

// --- LEISTUNGEN : introduction (le 5e paragraphe du Willkommen, à sa vraie place).
$leistungen_intro = 'Unser Behandlungsspektrum reicht von Vorsorge und Prophylaxe über Füllungen und Zahnunfallbehandlung bis hin zu individuell abgestimmten Therapien für Jugendliche. Wenn unsere Kids besonders ängstlich sind, oder größere Eingriffe notwendig sind, bieten wir unsere Behandlungen auch in Lachgassedierung, Sedierung oder Vollnarkose an. Dabei werden wir von einem professionellen Anästhesisten Team verantwortungsvoll unterstützt.';

// --- LEISTUNGEN : 11 items. Les 4 premiers = cartes colorées, les 7 suivants = accordéon.
$items = [
	// ---- CARTE 1 (gelb) ----
	[
		'card_color' => 'yellow',
		'symbol'     => 'symbol1',
		'heading'    => 'ZACP Kids Putzschule',
		// Correction : « reinigen » -> « reinigt » (faute de grammaire du docx).
		'body'       => '<p>In unserer Putzschule lernen unsere Kids spielerisch die optimale Zahnpflege. Die Zähne werden angefärbt, sodass wir im Anschluss zusammen üben, wie man alle Flächen des Zahnes effektiv reinigt. Wir trainieren die häusliche Mundhygiene!</p>',
	],
	// ---- CARTE 2 (blau) ----
	[
		'card_color' => 'blue',
		'symbol'     => 'symbol2',
		'heading'    => 'ZACP Prophylaxe',
		'body'       => '<p>Die Prophylaxe bei Kindern umfasst die Zahnreinigung und die Kontrolluntersuchung. Karies entsteht, wenn über einen Zeitraum Plaque (Beläge) auf Zahnflächen bestehen bleibt. Durch die professionelle Reinigung in regelmäßigen Abständen (abhängig vom Kariesrisiko) können wir die Entstehung von kariösen Stellen vorbeugen. Insbesondere der dünne und mineralstoffarme Zahnschmelz bei Milchzähnen ist anfällig für Karies. Wir beugen so effektiv Karies vor, insbesondere an den Stellen, wo die häusliche Mundhygiene sich schwierig gestaltet.</p>'
			. '<p>Unsere Kinder werden so früh für die Mundhygiene sensibilisiert. Die Kombination aus regelmäßigen professionellen Reinigungen und zahnärztlichen Kontrollen ermöglicht es, mit naturgesunden und gepflegten Zähnen nachhaltig durchs Leben zu gehen.</p>',
	],
	// ---- CARTE 3 (grün) — NOUVEAU. Titre normalisé : « Fissuren Versieglung » -> « Fissurenversiegelung ».
	[
		'card_color' => 'green',
		'symbol'     => 'symbol3',
		'heading'    => 'Fissurenversiegelung',
		'body'       => '<p>Die Fissurenversiegelung ist eine bewährte, schmerzfreie Vorsorgemaßnahme, um die Backenzähne (Molaren und Prämolaren) von Kindern effektiv vor Karies zu schützen. In den feinen Rillen der Kauflächen können sich leicht Bakterien und Speisereste festsetzen. Durch das Auftragen eines speziellen, zahnfarbenen Schutzlacks werden diese Fissuren dauerhaft versiegelt und so vor Karies geschützt.</p>'
			. '<p>Die Behandlung ist schnell, ohne Bohren und völlig schmerzfrei. Sie eignet sich besonders für neu durchgebrochene bleibende Backenzähne und trägt wesentlich zur langfristigen Zahngesundheit Ihres Kindes bei.</p>',
	],
	// ---- CARTE 4 (rosa) — teaser + lien vers la section détaillée #angst.
	[
		'card_color' => 'pink',
		'symbol'     => 'symbol4',
		'heading'    => 'AngstpatientInnen',
		'body'       => '<p>Der Zahnarztbesuch ist für viele Kinder mit Angst verbunden. Als Kinderzahnarzt in Osnabrück sowie für das Osnabrücker Land und das Umland haben wir uns auf die einfühlsame Behandlung von ängstlichen Kindern und Angstpatienten spezialisiert.</p>'
			. '<p><a href="#angst">Mehr erfahren</a></p>',
	],
	// ---- ACCORDÉON « Moderne Behandlung » ----
	[
		'card_color' => 'green',
		'symbol'     => 'symbol5',
		'heading'    => 'Kariesbehandlung ohne Bohren mit ICON',
		'body'       => '<p>Die ICON-Methode ist eine moderne, schonende Behandlung von früher Karies bei Kindern – ganz ohne Bohren. Dabei wird ein spezielles, flüssiges Kunststoffmaterial in den porösen Zahnschmelz eingebracht. Dieses versiegelt die beginnende Karies von innen, stoppt ihr Fortschreiten und erhält die natürliche Zahnsubstanz.</p>'
			. '<p>Die Behandlung ist schmerzfrei, angstfrei und meist ohne Betäubung möglich, weshalb sie besonders gut für Kinder geeignet ist. ICON wird vor allem bei frühzeitiger Karies im Milch- und bleibenden Zahn eingesetzt und kann helfen, größere zahnärztliche Eingriffe zu vermeiden.</p>',
	],
	// Le docx répète ce bloc DEUX FOIS à l'identique — publié une seule fois.
	[
		'card_color' => 'green',
		'symbol'     => 'symbol6',
		'heading'    => 'Moderne Füllungstherapie mit Kunststoffen',
		'body'       => '<p>Wenn bei Kindern eine Zahnfüllung notwendig ist, setzen wir auf moderne, hochwertige Kunststofffüllungen (Komposite). Diese ermöglichen eine schonende und zahnerhaltende Behandlung, da nur die erkrankte Zahnsubstanz entfernt wird. Die Füllungen sind zahnfarben, stabil und passen sich unauffällig dem natürlichen Zahn an.</p>'
			. '<p>Dank moderner Technik erfolgt die Behandlung kindgerecht, möglichst schmerzfrei und in entspannter Atmosphäre. Kunststofffüllungen sind gut verträglich, langlebig und sowohl für Milchzähne als auch für bleibende Zähne bestens geeignet. So sorgen wir für gesunde, schöne Kinderzähne – heute und in der Zukunft.</p>',
	],
	[
		'card_color' => 'green',
		'symbol'     => 'symbol7',
		'heading'    => 'Pulpotomie (Milchzähne)',
		'body'       => '<p>Ist die Karies bei einem Milchzahn bereits tief fortgeschritten, kann eine Pulpotomie helfen, den Zahn dennoch zu erhalten. Dabei wird nur der entzündete Teil des Zahnnervs in der Zahnkrone entfernt, während der gesunde Anteil in der Wurzel bestehen bleibt.</p>'
			. '<p>Die Behandlung erfolgt schonend, kindgerecht und schmerzfrei unter lokaler Betäubung. Ziel der Pulpotomie ist es, den Milchzahn bis zum natürlichen Zahnwechsel zu erhalten und so Kauen, Sprechen und die gesunde Entwicklung der bleibenden Zähne zu unterstützen.</p>',
	],
	[
		'card_color' => 'green',
		'symbol'     => 'symbol8',
		'heading'    => 'Wurzelkanalbehandlung (Milchzähne)',
		'body'       => '<p>Ist ein Milchzahn durch tiefe Karies oder eine Entzündung des Zahnnervs stark geschädigt, kann eine Wurzelkanalbehandlung notwendig sein, um den Zahn zu erhalten. Dabei werden die entzündeten oder abgestorbenen Anteile des Zahninneren sorgfältig entfernt, die feinen Wurzelkanäle gereinigt und anschließend mit einem speziellen, gut verträglichen Material gefüllt.</p>'
			. '<p>Die Behandlung erfolgt schonend, kindgerecht und schmerzfrei unter Betäubung. Der Erhalt des Milchzahns ist wichtig, da er als Platzhalter für den bleibenden Zahn dient und eine gesunde Entwicklung von Kiefer, Sprache und Kauen unterstützt.</p>',
	],
	[
		'card_color' => 'green',
		'symbol'     => 'symbol9',
		'heading'    => 'Behandlung von Kreidezähnen (MIH)',
		'body'       => '<p>Kreidezähne, medizinisch MIH (Molaren-Inzisiven-Hypomineralisation) genannt, sind bei Kindern keine Seltenheit. Die betroffenen Zähne sind oft besonders empfindlich, bröselig und anfällig für Karies. Essen, Trinken oder Zähneputzen kann dadurch unangenehm oder schmerzhaft sein.</p>'
			. '<p>In unserer Kinderzahnarztpraxis bieten wir individuell abgestimmte, schonende Behandlungskonzepte für MIH an. Je nach Ausprägung reichen diese von schützenden Versiegelungen und Fluoridierungen über moderne Kunststofffüllungen bis hin zu Kinderzahnkronen, wenn der Zahn stark geschädigt ist und zusätzlichen Schutz benötigt.</p>'
			. '<p>In bestimmten Situationen kann es außerdem sinnvoll sein, den Vitamin-D3-Spiegel des Kindes zu überprüfen, da dieser eine Rolle für die Zahnentwicklung spielen kann. Bei Bedarf arbeiten wir dabei interdisziplinär, z. B. mit Kinderärzten oder Heilpraktikern zusammen.</p>'
			. '<p>Unser Ziel ist es, Beschwerden zu lindern, die Zähne zu stabilisieren und die Zahngesundheit Ihres Kindes langfristig zu erhalten – stets kindgerecht, einfühlsam und individuell.</p>',
	],
	[
		'card_color' => 'green',
		'symbol'     => 'symbol1',
		'heading'    => 'Zahnextraktionen und Platzhalter',
		'body'       => '<p>Manchmal ist ein Milchzahn so stark geschädigt oder entzündet, dass er trotz aller Bemühungen nicht erhalten werden kann. In diesen Fällen führen wir eine Zahnextraktion besonders schonend, kindgerecht und schmerzfrei unter Betäubung durch. Dabei achten wir auf eine ruhige Atmosphäre, damit sich Ihr Kind sicher fühlt.</p>'
			. '<p>Muss ein Milchzahn vorzeitig entfernt werden, kann ein Platzhalter sinnvoll sein. Dieser sorgt dafür, dass der Platz für den bleibenden Zahn erhalten bleibt und sich die Nachbarzähne nicht verschieben. So unterstützen Platzhalter eine gesunde Entwicklung von Kiefer, Zahnstellung und Biss.</p>',
	],
	[
		'card_color' => 'pink',
		'symbol'     => 'symbol4',
		'heading'    => 'Lachgassedierung und Vollnarkose',
		'body'       => '<p><strong>Sanfte Hilfe für Angstpatienten und bei größeren Eingriffen.</strong></p>'
			. '<p>Ein Zahnarztbesuch kann für Kinder mit Angst, schlechten Vorerfahrungen oder großem Behandlungsbedarf sehr belastend sein. Um auch diesen Kindern eine stressfreie, sichere und kindgerechte Behandlung zu ermöglichen, bieten wir in unserer Kinderzahnarztpraxis Lachgassedierung sowie – in ausgewählten Fällen – eine Behandlung in Vollnarkose an.</p>',
	],
];

// --- ABLAUF : les 4 conseils aux parents, texte exact du client.
$ablauf_items = [
	[
		'abl_nr'      => '1',
		'abl_heading' => 'Positivität!',
		'abl_body'    => 'Sprecht mit euren Kindern positiv erwartend über den bevorstehenden Besuch. Lasst eure eigenen womöglich negativen Gefühle außen vor. Zur Vorbereitung können Kinderbücher helfen. Worte wie Schmerzen, bohren oder Angst sollten nicht Teil der vorbereitenden Gespräche sein.',
	],
	[
		'abl_nr'      => '2',
		'abl_heading' => 'Elternthemen?',
		'abl_body'    => 'Für die Atmosphäre im Behandlungszimmer ist es wichtig, dass Sie Ihre Fragen vor oder nach der Behandlung stellen. Ihr Kind darf eine unkomplizierte und entdeckerische Erfahrung beim ersten und allen weiteren Termin machen.',
	],
	[
		'abl_nr'      => '3',
		'abl_heading' => 'Kids first!',
		'abl_body'    => 'Ihr Kind steht im Mittelpunkt. Lassen Sie Ihr Kind auf unsere Fragen antworten. Wir verstehen, wenn ihr Kind nicht beim ersten Mal kooperiert. Bleiben Sie gelassen!',
	],
	[
		'abl_nr'      => '4',
		'abl_heading' => 'Loben ohne belohnen?!',
		'abl_body'    => 'Loben Sie Ihre Kinder! Belohnungen implizieren oft eine große Hürde. Etwas Kompliziertes oder Schlimmes.',
	],
];

// --- Application : on ne touche QUE les trois layouts visés, le reste des sections
// (hero, galerie, zimmer, team, termin…) est réécrit tel quel, IDs d'images inclus.
foreach ( $rows as $i => $row ) {
	switch ( $row['acf_fc_layout'] ) {
		case 'willkommen':
			$rows[ $i ]['text'] = $willkommen;
			break;
		case 'leistungen':
			$rows[ $i ]['eyebrow']         = 'Leistungen';
			$rows[ $i ]['title']           = 'Was wir tun';
			$rows[ $i ]['text']            = $leistungen_intro;
			$rows[ $i ]['accordion_title'] = 'Moderne Behandlung';
			$rows[ $i ]['items']           = $items;
			break;
		case 'ablauf':
			$rows[ $i ]['abl_eyebrow'] = 'Der erste Termin';
			$rows[ $i ]['abl_title']   = 'Tipps für die Eltern vor dem ersten Besuch';
			$rows[ $i ]['abl_text']    = '';
			$rows[ $i ]['items']       = $ablauf_items;
			break;
	}
}

update_field( 'sections', $rows, $page_id );
echo "Contenu écrit.\n";
```

- [ ] **Step 3 : Exécuter le script**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp
wp eval-file "$SCRATCH/content-01-leistungen.php"
```
Attendu : `Contenu écrit.`

- [ ] **Step 4 : Relire le contenu écrit (règle projet : vérifier par relecture)**

```bash
wp eval '$r=get_field("sections",5);
foreach($r as $row){
  if($row["acf_fc_layout"]==="leistungen"){
    printf("LEISTUNGEN : %d items\n", count($row["items"]));
    foreach($row["items"] as $i=>$it) printf("  [%2d] %-42s %s  %d Zeichen\n",$i,$it["heading"],$it["card_color"],mb_strlen($it["body"]));
  }
  if($row["acf_fc_layout"]==="willkommen") printf("WILLKOMMEN : %d Zeichen\n", mb_strlen($row["text"]));
  if($row["acf_fc_layout"]==="ablauf") printf("ABLAUF : %d Tipps\n", count($row["items"]));
}'
```
Attendu : `LEISTUNGEN : 11 items` (les 4 premiers `yellow/blue/green/pink`), `WILLKOMMEN` non vide, `ABLAUF : 4 Tipps`. Aucun `0 Zeichen`.

- [ ] **Step 5 : Vérifier le rendu HTML (le lien de la carte Angst doit survivre au filtrage)**

```bash
curl -s http://localhost:8090/ | grep -c 'href="#angst"'
curl -s http://localhost:8090/ | grep -c 'Fissurenversiegelung'
```
Attendu : `1` pour chacun. Un `0` sur `#angst` signifierait que `wp_kses_post()` n'est pas en place (Task 3 incomplète).

- [ ] **Step 6 : Commit** (le contenu vit en base, pas dans git — on commit seulement si un fichier du thème a bougé ; sinon passer à la Task 5.)

---

### Task 5 : Contenu — les deux nouvelles sections `textblock`

**Files:**
- Create: `$SCRATCH/content-02-textblocks.php`

**Interfaces:**
- Consumes: le layout `textblock` (Task 1) et son template (Task 2).
- Produces: deux sections `textblock` (ancres `angst` et `erster-besuch`) insérées dans `sections`, chacune précédée d'un `trenner`.

- [ ] **Step 1 : Écrire le script**

```php
<?php
$page_id = 5;
$rows    = get_field( 'sections', $page_id, false );

// --- Section « Angstpatienten » : le bloc SEO du client, avec ses sous-titres et ses 4 listes.
// Correction : le « # » parasite après « individuelle Lösungen für Angstpatienten » est retiré.
$angst_content = '<p>Unser Ziel ist es, Kindern aus Osnabrück und der Region eine stressfreie, sanfte und altersgerechte Zahnbehandlung zu ermöglichen.</p>'
	. '<h3>Zahnarztangst bei Kindern – wir nehmen Sorgen ernst</h3>'
	. '<p>Zahnarztangst ist bei Kindern weit verbreitet. Häufige Gründe sind:</p>'
	. '<ul><li>frühere negative Zahnarzterfahrungen</li><li>Angst vor Schmerzen oder Spritzen</li><li>ungewohnte Geräusche und Gerüche</li><li>Schüchternheit oder hohe Sensibilität</li></ul>'
	. '<p>In unserer Kinderzahnarztpraxis in Osnabrück nehmen wir uns Zeit, erklären jeden Schritt kindgerecht und gehen individuell auf jedes Kind ein.</p>'
	. '<h3>Lachgas beim Kinderzahnarzt in Osnabrück</h3>'
	. '<p>Die Lachgasbehandlung für Kinder ist eine bewährte Methode zur Reduktion von Angst und Stress. Vorteile der Lachgassedierung:</p>'
	. '<ul><li>beruhigend und angstlösend</li><li>Ihr Kind bleibt wach und ansprechbar</li><li>keine Spritze notwendig</li><li>schnelle Erholung nach der Behandlung</li></ul>'
	. '<p>Lachgas eignet sich besonders für ängstliche Kinder aus Osnabrück und dem Osnabrücker Land, die grundsätzlich kooperationsfähig sind.</p>'
	. '<h3>Vollnarkose beim Kinderzahnarzt – Behandlung ohne Angst</h3>'
	. '<p>Bei sehr starker Zahnarztangst oder umfangreichen Behandlungen kann eine Zahnbehandlung unter Vollnarkose sinnvoll sein.</p>'
	. '<p><strong>Wann ist eine Vollnarkose empfehlenswert?</strong></p>'
	. '<ul><li>ausgeprägte Zahnarztangst</li><li>sehr junge Kinder</li><li>mehrere notwendige Zahnbehandlungen</li><li>Kinder mit besonderen Bedürfnissen</li></ul>'
	. '<p><strong>Ihre Vorteile:</strong></p>'
	. '<ul><li>Ihr Kind schläft während der gesamten Behandlung</li><li>keine Angst und keine Schmerzen</li><li>mehrere Eingriffe in einer Sitzung möglich</li><li>Betreuung durch ein erfahrenes Anästhesie-Team</li></ul>'
	. '<p>Die Sicherheit Ihres Kindes steht dabei stets an erster Stelle. Vor jeder Behandlung unter Vollnarkose erfolgt eine ausführliche Beratung.</p>'
	. '<h3>Ihr Kinderzahnarzt in Osnabrück und dem Osnabrücker Land</h3>'
	. '<ul><li>viel Einfühlungsvermögen und Geduld</li><li>kindgerechte Atmosphäre</li><li>moderne, schonende Behandlungsmethoden</li><li>individuelle Lösungen für Angstpatienten</li><li>enge Zusammenarbeit mit Eltern</li></ul>'
	. '<p>Wir begleiten Kinder aus Osnabrück und dem Osnabrücker Land auf dem Weg zu gesunden Zähnen – ohne Angst und ohne Druck.</p>'
	. '<h3>Kinderzahnarzt Osnabrück – Angstfreie Behandlung für Ihr Kind 🦷</h3>'
	. '<p>Ob Lachgasbehandlung oder Vollnarkose beim Kinderzahnarzt – gemeinsam finden wir die passende Lösung für Ihr Kind. Vereinbaren Sie gerne einen Beratungstermin in unserer Kinderzahnarztpraxis in Osnabrück.</p>';

$angst = [
	'acf_fc_layout' => 'textblock',
	'tb_anchor'     => 'angst',
	'tb_eyebrow'    => 'Angstpatienten',
	'tb_title'      => 'Kinderzahnarzt für Angstpatienten in Osnabrück',
	'tb_style'      => 'fliesstext',
	'tb_card_color' => 'pink',
	'tb_content'    => $angst_content,
];

// --- Section « Der erste Zahnarztbesuch » : les 5 paragraphes du client, en carte rosée.
$besuch = [
	'acf_fc_layout' => 'textblock',
	'tb_anchor'     => 'erster-besuch',
	'tb_eyebrow'    => 'Der erste Termin',
	'tb_title'      => 'Der erste Zahnarztbesuch – ganz entspannt!',
	'tb_style'      => 'karte',
	'tb_card_color' => 'pink',
	'tb_content'    => '<p>Der erste Besuch beim Zahnarzt ist etwas ganz Besonderes – und bei uns vor allem eines: stressfrei und positiv. Unser Ziel ist es, dass sich Ihr Kind von Anfang an wohlfühlt und den Zahnarztbesuch mit guten Gefühlen verbindet.</p>'
		. '<p>Beim ersten Termin geht es nicht ums Bohren oder Behandeln, sondern ums Kennenlernen. Ihr Kind darf unsere Praxis entdecken, auf dem Behandlungsstuhl „Probe sitzen“ und unsere Instrumente neugierig anschauen. Alles geschieht spielerisch und ohne Zeitdruck.</p>'
		. '<p>Wir erklären jeden Schritt kindgerecht und in Ruhe – oft mit kleinen Geschichten oder Bildern. So bauen wir Vertrauen auf und nehmen mögliche Ängste ernst. Natürlich dürfen Mama oder Papa die ganze Zeit dabei sein.</p>'
		. '<p>Unser Wunsch: Ein Lächeln beim Hinausgehen – und die Vorfreude auf den nächsten Besuch.</p>'
		. '<p>Im anschließenden Gespräch erklären wir Ihnen unser Vorsorgekonzept und falls notwendig weitere Behandlungsbedürftigkeiten.</p>',
];

$trenner = [ 'acf_fc_layout' => 'trenner' ];

// Insertion : #angst juste après le layout leistungen, #erster-besuch juste après ablauf.
// On reconstruit la liste au lieu d'utiliser des index en dur — ceux-ci se décalent
// dès la première insertion.
$out = [];
foreach ( $rows as $row ) {
	$out[] = $row;
	if ( 'leistungen' === $row['acf_fc_layout'] ) {
		$out[] = $trenner;
		$out[] = $angst;
	}
	if ( 'ablauf' === $row['acf_fc_layout'] ) {
		$out[] = $trenner;
		$out[] = $besuch;
	}
}

update_field( 'sections', $out, $page_id );

// Idempotence : relancer ce script ne doit pas créer de doublons.
$check = get_field( 'sections', $page_id, false );
$n     = 0;
foreach ( $check as $row ) {
	if ( 'textblock' === $row['acf_fc_layout'] ) {
		++$n;
	}
}
printf( "textblocks en base : %d (attendu : 2)\n", $n );
```

⚠️ **Ce script n'est PAS idempotent** : le relancer ajouterait deux sections de plus. Il ne s'exécute **qu'une fois**. En cas de relance accidentelle, restaurer depuis `backup-sections-page5.json` (Task 4, Step 1).

- [ ] **Step 2 : Exécuter**

```bash
cd /Users/mdiarrisso/PhpstormProjects/kidsclub-wp
wp eval-file "$SCRATCH/content-02-textblocks.php"
```
Attendu : `textblocks en base : 2 (attendu : 2)`

- [ ] **Step 3 : Vérifier l'ordre final des sections**

```bash
wp eval '$r=get_field("sections",5); foreach($r as $i=>$row){ printf("[%2d] %s%s\n",$i,$row["acf_fc_layout"], !empty($row["tb_anchor"]) ? "  #".$row["tb_anchor"] : ""); }'
```
Attendu : `textblock #angst` **après** `leistungen`, `textblock #erster-besuch` **après** `ablauf`, chacun précédé d'un `trenner`.

- [ ] **Step 4 : Vérifier le rendu (l'ancre, les listes, la cible du lien)**

```bash
curl -s http://localhost:8090/ > /tmp/kc.html
grep -c 'id="angst"' /tmp/kc.html            # attendu : 1  (la cible du lien de la carte existe)
grep -c 'tb-prose' /tmp/kc.html              # attendu : 2  (les deux sections)
grep -c '<li>keine Spritze notwendig</li>' /tmp/kc.html   # attendu : 1  (liste préservée)
grep -c 'tb-card--pink' /tmp/kc.html         # attendu : 1  (variante carte)
```

- [ ] **Step 5 : Vérifier que rien n'a été perdu ailleurs**

Le script réécrit **tout** le tableau `sections` : il faut prouver que les sections à images et le booking sont intacts.

```bash
grep -c 'data-booking-open' /tmp/kc.html     # attendu : >= 1  (bouton Masinga préservé)
wp eval '$r=get_field("sections",5,false); foreach($r as $row){ if($row["acf_fc_layout"]==="galerie") printf("galerie : %d photos\n", is_array($row["gl_photos"]) ? count($row["gl_photos"]) : 0); if($row["acf_fc_layout"]==="hero") printf("hero : media OK\n"); }'
```
Attendu : la galerie a toujours ses photos (**pas 0**), le hero est présent, le bouton booking est rendu.

---

### Task 6 : Cache-busting, contrôle qualité, revue

**Files:**
- Modify: `inc/enqueue.php:15`
- Modify: `assets/js/sw.js:6`

- [ ] **Step 1 : Bumper les DEUX versions**

`inc/enqueue.php` ligne 15 :
```php
		$ver    = '3.10.0'; // bei jedem CSS/JS-Update erhöhen (Cache-Busting)
```

`assets/js/sw.js` ligne 6 :
```js
const CACHE = 'kidsclub-v3.10.0';
```

Bumper `$ver` seul ne suffit pas : le Service Worker sert du CSS stale indéfiniment tant que `CACHE` ne change pas.

- [ ] **Step 2 : Vérifier que les deux sont alignés**

```bash
cd /Users/mdiarrisso/PhpstormProjects/wordpress
grep -n "3\.10\.0" inc/enqueue.php assets/js/sw.js
```
Attendu : une ligne pour chacun des deux fichiers.

- [ ] **Step 3 : Suite complète**

```bash
composer test
```
Attendu : `No syntax errors detected` partout, PHPStan `[OK] No errors`.

- [ ] **Step 4 : Style de code**

```bash
composer cs
```
Les **warnings** ne bloquent pas (`ignore_warnings_on_exit`), les **erreurs** si. En cas d'erreur : `composer format`, puis relancer.

- [ ] **Step 5 : Commit**

```bash
git add inc/enqueue.php assets/js/sw.js
git commit -m "chore(assets): bump cache version 3.9.71 -> 3.10.0 (css + service worker)"
```

- [ ] **Step 6 : Code review — OBLIGATOIRE avant tout déploiement**

Lancer l'agent `pr-review-toolkit:code-reviewer` sur le diff complet de la branche :

```bash
git diff main...HEAD
```

Points d'attention à lui signaler explicitement :
- **XSS** : `wp_kses_post()` sur du contenu WYSIWYG saisi par un éditeur de confiance — vérifier qu'aucun `echo` non échappé n'a été introduit dans `textblock.php` et `leistungen.php`.
- **Rétrocompatibilité ACF** : la clé du champ `body` est restée `field_kc_body` (la changer perdrait le contenu existant).
- **Le `.min.css`** contient bien les mêmes règles que le `.css` (sinon : invisible en production).
- **`<section>`** est bien l'élément racine de `textblock.php` (sinon : fond perdu silencieusement).

Corriger **impérativement** les points CRITICAL et IMPORTANT avant la suite.

- [ ] **Step 7 : Vérification visuelle — DEMANDER D'ABORD**

Poser la question à l'utilisateur : *« Je dois ouvrir Chrome pour vérifier le rendu des deux nouvelles sections — tu confirmes ? »* et **attendre un « oui » explicite**. Profil Chrome obligatoire : **« Claude Code Test » (`Profile 6`)**.

À contrôler : les puces des listes du bloc Angst, le lien « Mehr erfahren » qui saute bien à `#angst`, la carte rosée du premier rendez-vous, l'accordéon « Moderne Behandlung » (7 entrées), et le bouton de réservation Masinga toujours fonctionnel.

- [ ] **Step 8 : Signaler au client les écarts relevés**

Rédiger la note listant : le doublon Füllungen (publié une fois), le `#` parasite retiré, `reinigen` → `reinigt`, « Fissuren Versieglung » → « Fissurenversiegelung », le déplacement du § *Behandlungsspektrum*, les deux libellés d'interface ajoutés — et **la question ouverte du tutoiement/vouvoiement mélangé**, qui est une décision éditoriale à lui.

- [ ] **Step 9 : Rappel — Praxis (Philosophie / Lage / Preise)**

Le docx ne fournit **que les titres**. Redemander les textes au client. Le layout `textblock` est prêt à les recevoir.

---

## Ce que ce plan ne fait PAS

- **Aucun déploiement.** La branche reste locale jusqu'à une demande explicite. ⚠️ Un push sur `main` déclenche un **déploiement automatique en production** (`.github/workflows/deploy.yml`).
- **Aucune ouverture de Chrome** sans un « oui » explicite au moment T.
- **Aucun contenu Praxis / Team** — le docx n'en fournit pas.
