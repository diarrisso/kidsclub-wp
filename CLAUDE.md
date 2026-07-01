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
  layout's sub-fields via `acf_add_local_field_group()` on `acf/init`. Each landing section =
  one flexible-content **layout** (`hero`, `leistungen`, `zimmer`, `praxis`, `team`, `ablauf`,
  `eltern`, `stimmen`, `faq`, `termin`, `kontakt`). `kc_field($name, $label, $type)` is the
  shorthand helper for simple fields (key convention: `field_kc_<name>`).
- **Markup:** one partial per layout in `template-parts/layouts/{name}.php`. The `$layout`
  name from `get_row_layout()` maps 1:1 to the filename.
- **Adding a section** = add a layout block in `inc/blocks.php` **and** create the matching
  `template-parts/layouts/{name}.php`. Both, or it renders nothing.
- ⚠️ Despite what `README.md` describes, there is **no `acf-json/` directory** — fields live in
  PHP in `inc/blocks.php`, not synced JSON. Edit fields there. Keep additions
  **non-destructive / backward-compatible** (never remove a field used by live content).

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
