# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**Kids Club by zacp** — a proprietary WordPress theme for a pediatric dental practice
(Kinderzahnarztpraxis) in Osnabrück. The **repository root IS the theme directory** (there is
no `wp-content/themes/...` nesting). UI language is German; code comments mix German and French.

PHP 7.4+ runtime target; CI runs PHP 8.4. Requires **ACF Pro** (the whole content model is ACF).

## Commands

```bash
composer test     # = lint + stan (the canonical "run everything" command)
composer lint     # php -l syntax check across all non-vendor PHP
composer stan     # PHPStan level 5 with WordPress + ACF Pro stubs (--memory-limit=1G)
composer cs       # PHPCS — WordPress Coding Standards (phpcs.xml)
composer format   # phpcbf — auto-fix WPCS violations
```

- There is **no `package.json` / npm build** — CSS and JS are hand-authored in `assets/` and
  shipped as-is. The deploy script checks for `package.json` and skips the build when absent.
- CI (`.github/workflows/ci.yml`, on PR to `main`/`develop`): PHP syntax → `composer stan` →
  `composer cs`. PHPStan needs `--memory-limit=1G` (ACF stubs are heavy); PHPCS is configured
  with `ignore_warnings_on_exit` so warnings don't fail the build, only errors do.
- Run a single PHPStan/PHPCS path: `phpstan analyse --memory-limit=1G <file>` /
  `phpcs <file>`.

## Architecture — ACF Flexible Content

The entire front page is data-driven through one ACF Flexible Content field. To understand or
extend a section you must read across these layers:

```
page-landing.php                    Page template "Kids Club Landing"
  └─ get_template_part('template-parts/flexible')
        └─ loops have_rows('sections')          ← the flexible field
              └─ get_template_part('template-parts/layouts/{layout_name}')
```

- **Field definitions:** `inc/blocks.php` registers the `sections` flexible field and every
  layout's sub-fields via `acf_add_local_field_group()` on `acf/init` (field group key:
  `group_kidsclub_landing`). Each landing section = one flexible-content **layout** (`hero`,
  `willkommen`, `textblock`, `leistungen`, `zimmer`, `galerie`, `team`, `ablauf`, `eltern`,
  `stimmen`, `faq`, `termin`, `trenner`, `kontakt`). `kc_field($name, $label, $type)` is the
  shorthand helper for simple fields (key convention: `field_kc_<name>`).
- **`textblock`** is the generic long-copy layout: eyebrow + title + a full WYSIWYG (`h3`, `ul`,
  links), rendered through `wp_kses_post()`, with an optional `tb_anchor` giving the `<section>`
  its `id`, and a `karte` variant that reuses the Leistungen card look. Reach for it whenever the
  client sends prose that has no dedicated section — do not invent a new one-off layout.
  ⚠️ Its `.tb-prose` styles **restore the list bullets** that the global reset
  (`ul{list-style:none}`, `kidsclub.css:12`) strips. A WYSIWYG list rendered without them shows
  no bullets and no indent.
- **`willkommen`** has two `Darstellung` modes: `klassisch` (the original single WYSIWYG) and
  `editorial` (lead, two columns, full-bleed quote band, closing line). The old `text` field is
  kept as the klassisch fallback — switching back must never lose content.
- **`angst`** renders the anxiety section as **two comparison cards** (Lachgas / Vollnarkose)
  instead of running prose: the client's copy *compares* two options, so the form follows the
  content. Each card's body is a WYSIWYG — `<ul>` becomes a checkmark list, `<strong>` becomes a
  sub-heading. The cards `stretch` to equal height and their closing sentence is pushed to the
  bottom, so an uneven number of lists doesn't leave one card floating.
- **Full-bleed bands** (`.wk-motto`, `.ag-cta`) leave the `.container` on purpose
  (`width:100vw; margin-left:calc(50% - 50vw)`) and carry a **spray graphic** as background — the
  same 8 assets the `bg_spray_preset` field uses. `kc_spray_url()` whitelists them; a free value
  must never reach a file path.
  ⚠️ Any component on a coloured/image background **must declare its own `color`**: the theme sets
  `h1,h2,h3 { color: var(--magenta) }` globally, so an undeclared heading renders magenta-on-magenta
  — invisible.
- **Spacing:** use the theme tokens — `--gap-eyebrow` (eyebrow→title), `--gap-title`
  (title→description), `--gap-head` (head→content), `--section-pad`. Ad-hoc `clamp()` values are
  what makes a new block look "off" next to the others.
- **Markup:** one partial per layout in `template-parts/layouts/{name}.php`. The `$layout`
  name from `get_row_layout()` maps 1:1 to the filename.
- **Adding a section** = add a layout block in `inc/blocks.php` **and** create the matching
  `template-parts/layouts/{name}.php`. Both, or it renders nothing.
- ⚠️ Despite what `README.md` describes, there is **no `acf-json/` directory** — fields live in
  PHP in `inc/blocks.php`, not synced JSON. Edit fields there. Keep additions
  **non-destructive / backward-compatible** (never remove a field used by live content). In
  particular, **never change an existing field's `key`** — the content is stored against it, so
  renaming the key silently drops every value already entered.

### Writing content programmatically (`wp eval-file`)

Content is authored through `get_field()` / `update_field()`, never as an ACF `default_value` and
never by writing postmeta by hand. Two traps, both silent:

- **`get_field('sections', $id, false)` indexes rows by field KEY, not by name** — you get
  `field_kc_ls_text` and `field_kc_leistungen_items`, not `text` and `items`. Writing to `$row['items']`
  adds a dead key that ACF ignores: `update_field()` reports success and changes nothing. Always
  read the real structure before writing into it.
- **ACF already applies `wpautop()` to WYSIWYG fields.** Do not call it again in a template —
  besides being dead work, `wpautop(null)` on a row with no stored value is a PHP 8.1+ deprecation
  on every render. Render WYSIWYG with `wp_kses_post( (string) ( $value ?? '' ) )`.

`functions.php` wires everything by `require`-ing `inc/` modules in order:
`setup` · `seo-meta` · `enqueue` · `icons` · `options` · `cpt/team` · `cpt/praxis` · `blocks` ·
`schema` · `uploads-cache-bust` · `embed-allowlist` · `cleanup`.

### Global content (header / footer)

`header.php` and `footer.php` read from an ACF **options page** (`inc/options.php`, tabs
*Header* / *Footer*) via `get_field('name', 'option')` — logo, nav, CTA, footer columns,
social, legal links. Empty logo field → the drawn arch-heart SVG fallback is used.

### Custom post types (internal, no public single/archive)

- `inc/cpt/team.php` — CPT `team` + taxonomy `funktion` (Zahnärzte / Praxisteam). The `team`
  layout queries this CPT grouped by `funktion`, with a legacy repeater fallback.
- `inc/cpt/praxis.php` — CPT `praxis_foto` + taxonomy `bereich`. The Praxis-Galerie uses the
  taxonomy terms (that have photos) as Alpine.js filter chips.

## Key helpers & conventions

- `kc_icon($slug)` (`inc/icons.php`) — inline **24×24 line SVG** glyphs. Do **not** use it for
  the full-color illustration assets (those are large `<img>` files, a different concern).
- `kc_nav_url($link)` (`header.php`) — prepends `home_url()` to in-page anchors so nav links
  (`#leistungen`) work from sub-pages (Impressum/Datenschutz), not just the front page.
- `kc_embed_hosts_allowed($html)` (`inc/embed-allowlist.php`) — iframe host allowlist; `termin.php`
  only renders an embed (e.g. Doctolib fallback) if its host passes.
- **Self-hosted assets for DSGVO** — fonts (`assets/fonts/`, `assets/css/fonts.css`), Swiper
  and Alpine.js (`assets/vendor/`) are all local. **Never** load Google Fonts CDN or any
  third-party-hosted asset that would leak a visitor IP.
- **Cache-busting:** bump `$ver` in `inc/enqueue.php` on every CSS/JS change. The font
  **preload** link in the same file points at a specific woff2 — update it when the display
  font changes.

## Booking (Masinga) — do not break

The `termin` section renders the **Masinga Booking** widget (`[masinga_booking]` shortcode /
a `data-booking-open` trigger button), with a Doctolib iframe as optional fallback. The booking
widget is live in production. When rebuilding the header or `termin.php`, **preserve the booking
trigger button and its `data-booking-open` attribute / handler**.

## Project guardrails

- **German-only UI — every user-facing string is German.** This is a German practice; the
  whole admin and front-end is in German. All ACF labels, `instructions`, `choices` labels,
  option/select names, button text, and front-end copy **must be in German** (e.g. `Weiß`,
  `Puderrosa`, `Salbei`, `Benutzerdefiniert…`, not `Blanc`, `Rose poudré`, `Sauge`,
  `Personnalisée`). French is allowed **only in code comments**, never in anything an editor
  or visitor sees. When adding or editing a field/label, write it in German from the start.
- **Never load TailwindCSS (`main.css`) into WP admin** via `admin_enqueue_scripts` — the
  Preflight reset breaks native admin styling. A separate `admin.css` (no Preflight) if needed.
- **Swiper:** `.swiper` has `overflow:hidden` → put card-shadow `padding-bottom` on `.swiper`
  itself; `slidesPerView:'auto'` needs explicit slide widths; respect `prefers-reduced-motion`
  and slider a11y (aria-live, roledescription, slide counter).
- **Deployment:** `./deploy-scp.sh` tars the theme and ships it to Mittwald over SSH (sources
  `.deploy-config`, which is gitignored and holds no secrets). **Never deploy automatically** —
  only when the user explicitly says so, and only after code review. Prefer the `deploy-kidsclub`
  skill for the full flow.
  - **Run it WITHOUT a pipe** (`./deploy-scp.sh > /tmp/deploy.log 2>&1`, never `| grep | tail`):
    a pipe makes `$?` the pipe's last command (`exit 0`) and **masks a failed deploy**.
  - The tar **excludes** `ZACP/`, `.superpowers`, `*-backup.mp4`, `RAPPORT-*.md`, `.phpactor.json`,
    `docs/superpowers`. These are big local-only folders (e.g. `ZACP/` ≈ 270 MB of design assets);
    without the excludes the archive balloons to ~330 MB and the SCP transfer is killed → a
    silent, incomplete deploy. Keep these excludes in `deploy-scp.sh`.
  - **Always verify on the server after deploy** (not just "exit 0"):
    `ssh kidsclub "grep -c '<new-marker>' <path>/assets/css/kidsclub.min.css"` and the served
    `?ver=` via `curl`. See the global CLAUDE.md deploy rule.

## Pages & templates

`page-landing.php` (the flexible front page), `page-impressum.php`, `page-datenschutz.php`
(legal pages sharing `template-parts/pages/legal.php`). SEO/title handling lives in
`inc/setup.php` + `inc/seo-meta.php`; JSON-LD `Dentist` + `FAQPage` schema in `inc/schema.php`.
