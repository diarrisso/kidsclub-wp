# Kids Club by zacp — WordPress-Integration (ACF Flexible Content)

Anleitung & Best Practices, um die Prototyp-Landingpage als **ACF-Flexible-Content-Blöcke**
in dein Theme zu überführen.

---

## 1. Grundprinzip (deine Idee = die richtige Praxis)

```
Eine Seitenvorlage  →  ein Flexible-Content-Feld "sections"
                       └─ pro Landing-Abschnitt EIN Layout (hero, leistungen, zimmer, …)
Render-Schleife      →  template-parts/flexible.php  (get_row_layout → Partial laden)
Markup pro Layout    →  template-parts/layouts/{name}.php
Inhalte              →  ACF-Felder (Text, Bild, Galerie, Repeater, Select)
Design               →  EIN globales CSS (Design-Tokens als CSS-Variablen) + EIN JS
```

**Warum so:** Redaktion ordnet/füllt Sektionen frei im Backend, ohne HTML. Das Design
liegt zentral in CSS-Variablen → identisch zu Print, an einer Stelle änderbar.

> Alternative: **native ACF-Blocks** (`block.json` + Gutenberg). Moderner, mit Live-Preview
> im Editor. Wenn du ohnehin Flexible Content nutzt, ist das hier der direktere Weg – die
> Layout-Partials sind 1:1 als Block-Render-Templates wiederverwendbar.

---

## 2. Dateien ins Theme kopieren

```
dein-theme/
├─ functions.php                 → require-Zeilen siehe unten
├─ page-landing.php              ← Seitenvorlage
├─ acf-json/                     ← leer anlegen, beschreibbar (ACF synct hier rein)
├─ inc/
│  ├─ enqueue.php                ← CSS/JS + Schriften
│  ├─ blocks.php                 ← Flexible-Content-Feld (Hero, Leistungen, Zimmer …)
│  ├─ options.php                ← Options-Seite: Header & Footer
│  ├─ icons.php                  ← zentrale SVG-Icons  kc_icon('heart')
│  └─ schema.php                 ← JSON-LD (SEO/AI)
├─ header.php                    ← Sticky-Nav + Mobile-Menü (aus Optionen)
├─ footer.php                    ← Footer-Spalten, Social, Rechtliches (aus Optionen)
├─ template-parts/
│  ├─ flexible.php               ← Render-Schleife
│  ├─ layouts/                   ← ein Partial pro Sektion
│  │  ├─ hero.php  leistungen.php  zimmer.php   (Beispiele – Rest nach Muster)
│  └─ partials/
│     └─ kids-svg.php            ← animierte Kinder (Inline-SVG)
└─ assets/
   ├─ css/kidsclub.css           ← = styles.css aus dem Prototyp
   ├─ js/kidsclub.js             ← = main.js aus dem Prototyp
   └─ img/ (hero-banner-bg.svg, logo-full.png, fonts/ …)
```

In **functions.php**:
```php
require get_theme_file_path( 'inc/enqueue.php' );
require get_theme_file_path( 'inc/icons.php' );
require get_theme_file_path( 'inc/options.php' );  // Header/Footer-Einstellungen
require get_theme_file_path( 'inc/blocks.php' );
require get_theme_file_path( 'inc/schema.php' );
```

Dann: Seite anlegen → Vorlage **„Kids Club Landing"** wählen → Sektionen hinzufügen.

---

## 3. Sektion → Layout (Mapping)

| Prototyp-Abschnitt        | Layout-Name   | Felder (ACF)                                            |
|---------------------------|---------------|---------------------------------------------------------|
| Hero-Banner               | `hero`        | Eyebrow, Titel, Highlight, Text, Bild, **Animation (Select)**, Kinder an/aus |
| Leistungsspektrum         | `leistungen`  | Eyebrow, Titel, Text, **Repeater** (Icon, Titel, Text)  |
| 5 Zimmer                  | `zimmer`      | Eyebrow, Titel, Text, **Repeater** (Name, Motto, Farbe) |
| Erster Besuch             | `ablauf`      | Repeater (Nr., Titel, Text)                             |
| Praxis-Galerie            | `praxis`      | **Galerie** + Chips-Repeater                            |
| Team / Behandler          | `team`        | Repeater (Foto, Name, Rolle, Text)                      |
| Für Eltern                | `eltern`      | Titel, Text, Repeater (Icon, Frage, Antwort)            |
| Kundenstimmen             | `stimmen`     | Repeater (Zitat, Name, Rolle)                           |
| FAQ                       | `faq`         | Repeater (Frage, Antwort) → zusätzlich FAQPage-Schema   |
| Termin (QR)               | `termin`      | Bild (QR), Buchungs-Embed (Textarea/oEmbed)             |
| Kontakt                   | `kontakt`     | Titel, Text, Formular-Shortcode (z. B. CF7)             |

`hero`, `leistungen`, `zimmer` sind fertig umgesetzt – die übrigen nach **demselben Muster**
in `inc/blocks.php` (Layout) + `template-parts/layouts/{name}.php` (Markup) ergänzen.

---

## 4. Bilder & Medien
- Im Prototyp sind die Bildflächen Platzhalter (`<image-slot>`). In WordPress werden daraus
  echte **ACF-Bild-/Galerie-Felder** → eure Foto-/Videoaufnahmen.
- Hero: eigenes Foto/Video als Hintergrund hochladen und **„Illustrierte Kinder = aus"** setzen.
- Immer `alt`-Texte pflegen (SEO + Barrierefreiheit). Bilder in WebP, responsive Größen nutzen.

## 5. Schriften & DSGVO ⚠️
- **Schriften selbst hosten** (Fredoka, Caveat, Nunito) – kein Google-Fonts-CDN auf einer
  Arztpraxis-Seite ohne Consent. Anleitung in `inc/enqueue.php`.
- Online-Buchung & evtl. Karten **erst nach Cookie-Consent** laden (z. B. Borlabs/Complianz).

## 6. SEO / AI
- Semantisches HTML ist bereits angelegt (genau **eine** H1, saubere H2/H3).
- `inc/schema.php` gibt **JSON-LD „Dentist"** aus → für Google & KI-Assistenten lesbar.
  Werte (Adresse, Tel, Geo, Öffnungszeiten) auf die echten Daten setzen.
- FAQ-Repeater zusätzlich als **FAQPage**-Schema ausgeben → Rich Results.
- Yoast/RankMath für Title/Description/OG, XML-Sitemap.

## 7. Animation
- Die Kinder-Animation steuert das `data-anim`-Attribut der `.hero-banner`-Sektion
  (`winken` · `huepfen` · `laufband` · `aus`) → im Hero als **ACF-Select** wählbar.
- CSS respektiert `prefers-reduced-motion` (keine Animation bei Nutzer-Wunsch).
- Das „Laufband" füllt `main.js` (`.marquee-track`) automatisch mit Marken-Motiven.

## 8. Performance
- CSS/JS mit Versionsnummer cachen (siehe `$ver` in `enqueue.php`).
- SVGs inline (keine extra Requests), Bilder lazy-loaden, Caching-Plugin.

## 9. Header & Footer (globale Inhalte)
- Unter **Theme-Einstellungen** (Tabs *Header* / *Footer*) zentral pflegbar:
  Logo, Navigation, CTA-Button · Footer-Spalten, Social-Media, Rechtliches, Copyright.
- `header.php` und `footer.php` lesen diese Werte via `get_field('name','option')`.
- Logo-Feld leer = automatisch das gezeichnete Bogen-Logo + Schriftzug.

---

### Schnellstart für den Entwickler
1. Dateien kopieren, `require`-Zeilen in `functions.php` einfügen.
2. ACF Pro aktiv? `acf-json/` anlegen (beschreibbar).
3. Seite mit Vorlage „Kids Club Landing" anlegen, Sektionen befüllen.
4. Restliche Layouts (Tabelle Abschnitt 3) nach Muster ergänzen.
5. Schriften self-hosten, Schema-Daten eintragen, Consent-Tool einrichten.
