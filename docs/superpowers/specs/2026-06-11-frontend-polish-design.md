# Frontend Polish — Kids Club by ZACP
**Date:** 2026-06-11  
**Status:** Approved for implementation

---

## Scope

4 independent CSS/JS/PHP improvements validated via visual mockups:

1. Booking widget → centred modal overlay
2. Footer → compact accent-bar layout + QR code
3. Navbar CTA button → compact with calendar icon, no phone number
4. Contact form → compact field sizing

---

## 1. Booking Widget Modal

### Problem
The Masinga Booking widget renders inline inside `#termin`. The patient can scroll away, interact with the rest of the page, and the widget blends into the background with no visual focus.

### Solution
Wrap the widget in a modal overlay triggered by a button. The page content is locked behind the overlay.

### Architecture

**Trigger buttons** — two locations, same behaviour:
- Navbar CTA: `header.php` — `<button class="btn btn-primary btn-sm" id="bookingOpen">`
- Hero CTA: `template-parts/layouts/hero.php` — same `id="bookingOpen"` or `data-booking-open`

**Modal markup** — injected once in `footer.php` just before `</body>`:
```html
<div id="bookingModal" class="booking-modal" role="dialog" aria-modal="true" aria-label="Online Termin buchen" hidden>
  <div class="booking-modal__backdrop" id="bookingBackdrop"></div>
  <div class="booking-modal__card">
    <button class="booking-modal__close" id="bookingClose" aria-label="Schließen">✕</button>
    <div class="booking-modal__body">
      <!-- widget already in DOM, moved here via JS, or duplicated -->
      [masinga_booking]
    </div>
  </div>
</div>
```

**CSS** (`assets/css/kidsclub.css`):
- `.booking-modal` — `position: fixed; inset: 0; z-index: 300; display: flex; align-items: center; justify-content: center`
- `.booking-modal__backdrop` — `position: absolute; inset: 0; background: rgba(38,37,127,0.55); backdrop-filter: blur(3px)`
- `.booking-modal__card` — `position: relative; background: #fff; border-radius: 20px; width: min(560px, 94vw); max-height: 90vh; overflow-y: auto; padding: 28px`
- `.booking-modal__close` — top-right absolute, 36px circle, navy bg
- Hidden state: `[hidden]` attribute + `opacity: 0; pointer-events: none` transition

**JS** (`assets/js/kidsclub.js`):
```js
// open
document.querySelectorAll('[data-booking-open], #bookingOpen').forEach(btn =>
  btn.addEventListener('click', openBooking));

function openBooking() {
  modal.removeAttribute('hidden');
  document.body.style.overflow = 'hidden';
  document.getElementById('mainContent').inert = true;
  // focus trap: first focusable inside modal
}
function closeBooking() {
  modal.setAttribute('hidden', '');
  document.body.style.overflow = '';
  document.getElementById('mainContent').inert = false;
}
// close on backdrop click + Escape key
```

**Accessibility:**
- `inert` attribute on `#mainContent` (wraps everything except modal) — blocks focus, click, screen reader
- `Escape` key closes modal
- Focus returns to trigger button on close
- `aria-modal="true"` + `role="dialog"` on modal container

**Widget loading:** The shortcode `[masinga_booking]` is rendered server-side in the modal markup in `footer.php`. Script already loads via `wp_enqueue_script` (plugin handles it) — no lazy loading needed.

---

## 2. Footer — Compact Accent Bar

### Problem
Current footer: `padding: clamp(54px, 6vw, 80px)` — renders at ~530px height. Visually heavy.

### Solution
Replace with 4-column compact layout. Target height: ~200px.

### Structure (`footer.php`)

```
[5px magenta gradient bar]
[padding: 32px 0 20px]
  [4-col grid: 1.3fr 1fr 1.4fr 0.9fr]
    Col 1: Logo SVG + "Kids Club / by zacp" + short description (1 line) + social icons
    Col 2: "Navigation" heading + 6 nav links
    Col 3: "Kontakt" heading + address / phone / hours (each with icon chip)
    Col 4: "Online Termin" heading + QR code SVG + "Scanne & buche direkt vom Handy" + btn
  [border-top 1px]
  [bottom bar: copyright left | Impressum · Datenschutz right]
```

**QR Code:** Generated as inline SVG via PHP using `endroid/qr-code` (composer package). Encodes `https://kidsclub.masingatech.com`. Rendered inline — no external request, no `<img src="">`.

```php
// inc/qr.php — helper
function kc_booking_qr_svg(): string {
    $writer = new \Endroid\QrCode\Writer\SvgWriter();
    $qr = \Endroid\QrCode\QrCode::create('https://kidsclub.masingatech.com')
        ->setSize(120)->setMargin(4);
    return $writer->write($qr)->getString();
}
```

**CSS changes:**
- `.site-footer` padding: `32px 0 20px` (was `clamp(54px,6vw,80px) 0 30px`)
- `.footer-top` grid: `1.3fr 1fr 1.4fr 0.9fr` + accent bar via `::before` pseudo-element
- `.footer-accent` — `height: 5px; background: linear-gradient(90deg, var(--magenta), #FF6BB3)`
- `.footer-qr` — `text-align: center`; QR SVG wrapped in white rounded box
- `.footer-qr-btn` — magenta pill button below QR

**Responsive (≤760px):** 2-col grid (brand+nav / kontakt+QR stacked)

---

## 3. Navbar CTA Button

### Problem
`.btn` uses `padding: 15px 28px; font-size: 1.02rem` — designed for hero CTAs, too large in the 68px navbar.

### Solution
Add `.btn-sm` modifier class. Apply to navbar CTA only. No change to hero/section buttons.

**CSS addition:**
```css
.btn-sm { padding: 7px 16px; font-size: .82rem; }
.btn-sm svg { width: 14px; height: 14px; }
```

**`header.php` change:**
- Remove `<span class="nav-phone">` entirely
- Navbar CTA button: add `btn-sm` class + calendar SVG icon inside button

**Calendar icon (inline SVG, 14×14):**
```html
<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
  <rect x="3" y="4" width="18" height="18" rx="2"/>
  <line x1="16" y1="2" x2="16" y2="6"/>
  <line x1="8" y1="2" x2="8" y2="6"/>
  <line x1="3" y1="10" x2="21" y2="10"/>
</svg>
```

---

## 4. Contact Form — Compact Fields

### Problem
`.field input / .field textarea`: `padding: 13px 15px; font-size: 1rem`. Textarea `min-height: 130px`. Makes the form card dominate the Kontakt section.

### Solution
Reduce all field dimensions in both `.field` and `.wpcf7` CSS layers (both must be updated — `.field textarea` overrides `.wpcf7 textarea` due to specificity).

**CSS changes (`assets/css/kidsclub.css`):**

| Property | Before | After |
|---|---|---|
| `.field input, .field textarea` padding | `13px 15px` | `8px 11px` |
| `.field input, .field textarea` font-size | `1rem` | `.88rem` |
| `.field textarea` min-height | `130px` | `72px` |
| `.field label` font-size | `.92rem` | `.78rem` |
| `.field label` style | bold | uppercase + letter-spacing .04em |
| `.field` margin-bottom | `12px` | `8px` |
| `.form-row` gap | `16px` | `10px` |
| `.kontakt-form-wrap` padding | `clamp(24px,3vw,36px)` | `20px 24px` |
| `.wpcf7 input` padding | `11px 14px` | `8px 11px` |
| `.wpcf7 textarea` min-height | `80px` | `72px` |
| `.wpcf7 input[type=submit]` padding | `11px 22px` | `8px 18px` |

No change to validation error styles, response output, or checkbox styles.

---

## Files to Change

| File | Changes |
|---|---|
| `assets/css/kidsclub.css` | `.btn-sm`, modal CSS, footer CSS, form compact CSS |
| `assets/js/kidsclub.js` | Modal open/close/focus-trap logic |
| `header.php` | Remove phone, add `btn-sm` + calendar icon to CTA, add `data-booking-open` |
| `footer.php` | New compact layout + QR code col + accent bar |
| `template-parts/layouts/hero.php` | Add `data-booking-open` to hero CTA |
| `template-parts/layouts/termin.php` | Remove inline widget (moved to modal in footer.php) |
| `inc/qr.php` | New file — `kc_booking_qr_svg()` helper |
| `composer.json` | Add `endroid/qr-code` |
| `functions.php` | `require_once` for `inc/qr.php` |

---

## Out of Scope

- Hero section redesign
- Stimmen / Team / Praxis block changes
- Mobile menu redesign
- Any new sections

---

## Testing Checklist

- [ ] Modal opens on navbar CTA click
- [ ] Modal opens on hero CTA click  
- [ ] Page scroll locked when modal open
- [ ] Click backdrop closes modal
- [ ] Escape key closes modal
- [ ] Tab key stays within modal (focus trap)
- [ ] Modal works on mobile (≤760px)
- [ ] Footer renders correctly at 1280px, 1024px, 760px, 375px
- [ ] QR code SVG renders (not broken/empty)
- [ ] Form fields compact on desktop and mobile
- [ ] CF7 validation errors still visible
- [ ] Navbar button compact, no phone number visible
- [ ] PHPCS passes (no new errors)
