# Santa Towing — Elementor Free Editing & Template Guide

**Target Page Builder:** Elementor FREE (`>= 3.x`, zero Elementor Pro dependencies, zero raw `html` widgets, zero `shortcode` widgets, zero inline `<script>` tags inside templates).  
**Global Design Tokens:** Linked via **Appearance → Customize / Santa Website Core → Theme Colors & Elementor Kit Sync** (`#015557` Deep Teal, `#192e2e` Dark Slate, `#d80000` Emergency Red, `#131514` Charcoal, `Plus Jakarta Sans` + `Inter`).

---

## 1. Architecture Boundaries: Elementor Free vs. PHP Theme vs. Admin Plugin

| Scope | Technology | Editability |
|---|---|---|
| **Public Marketing & Landing Pages (13 pages + 261 Service Area Routes)** | **Elementor Free** (`0.4` JSON exports in `elementor-templates/`) | Open any page → **Edit with Elementor** to modify headings, body text, cards, images, icons, buttons, spacing, background colors, and responsive layout directly. |
| **Global Header, Footer, Single CPTs, Archives, Search & Sitemap** | **`santa-towing` PHP Theme** + **`santa-towing-website-core`** | Managed automatically by theme templates (`header.php`, `footer.php`, `archive-st_*.php`, `single-st_*.php`, `archive.php`, `single.php`, `search.php`, `template-sitemap.php`) without requiring Elementor Pro Theme Builder. |
| **Legal & Policy Pages (11–12 pages)** | **PHP Theme (`template-policy.php`)** by default; **Elementor Free** option exported for `privacy-policy` | By default on import, all 12 policy pages use `template-policy.php` for uniform legal sidebar typography (`pilot-pages.json` / `phase-b-pages.json`). `pilot-privacy-policy.json` is also included for visual Elementor editing if desired. |
| **Operations, Billing, CRM, Image Studio & Site Content Admin** | **`santa-towing-business-manager` Plugin** | **Strictly outside Elementor.** Standalone WP Admin app (`#stbm-root`) under **WP Admin → Business Manager**, zero Elementor coupling. |

---

## 2. Public Pages Editable in Elementor Free (Template Map)

All **15** JSON templates in `Santa WordPress Website Conversions/elementor-templates/` use strictly **Native Elementor Free widgets** (`section`, `column`, `heading`, `text-editor`, `image`, `button`, `divider`, `icon-box`) and our **17 Custom Elementor Free `stwc-*` Widgets**.

| Route | Page Title | Elementor JSON Export | Page Template | Primary Editable Widgets Used |
|---|---|---|---|---|
| `/` | Home | `pilot-home.json` | `template-elementor-canvas.php` | `stwc-hero-slider`, `stwc-feature-cards` (`quick-actions-overlap`), Native 2-Col `#about` (`image.st-el-img-4x3` + `icon-box.st-floating-hotline-card` + `heading` + `text-editor` + `.st-el-btn-row` `button`s), `stwc-services-grid`, `stwc-haulage-showcase`, `stwc-cars-catalog`, `stwc-rental-fleet`, `stwc-parts-showcase`, `stwc-why-choose-us`, `stwc-blog-grid`, `stwc-faq-accordion` |
| `/services/` | Our Services | `pilot-services.json` | `template-elementor-fullwidth.php` | Native Hero (`heading`, `text-editor`, 2 `button`s), Native Intro (`heading`, `divider.st-section-underline`, `text-editor`), `stwc-services-grid`, `stwc-feature-cards` (`icon-center-white` Stand Out & `process-steps` 5 steps), `stwc-spotlight-split` (`primary-banner-split`), `stwc-faq-accordion`, Native 2-Button CTA Section |
| `/fleet/` | Our Fleet | `pilot-fleet.json` | `template-elementor-fullwidth.php` | `stwc-page-hero` (`building` badge icon), `stwc-feature-cards` (`icon-top-light`, `icon-dark-glass`, `photo-overlay`, `process-steps`), `stwc-spotlight-split` (`checklist-feature` + `fleet-health` overlay), `stwc-enquiry-forms` (`fleet`), `stwc-faq-accordion` (`Fleet`), Native Bottom CTA |
| `/towing/` | Emergency Towing & Recovery | `page-towing.json` | `template-elementor-fullwidth.php` | `stwc-page-hero` (`show_side_card: yes` Urban Arrival + WhatsApp Pin), `stwc-feature-cards` (`service-cards` 6 capabilities), Native Headings (`#towing-request-section`), `stwc-towing-request-form`, `stwc-feature-cards` (`process-steps` + 261 Corridors bottom callout bar), `stwc-faq-accordion` (`Towing`) |
| `/haulage/` | Heavy Haulage & Logistics | `page-haulage.json` | `template-elementor-fullwidth.php` | `stwc-page-hero` (4 highlight `stats`), `stwc-feature-cards` (`photo-cards` 8 haulage services), `stwc-haulage-showcase`, `stwc-spotlight-split` (`checklist-feature` + `dispatch-callout`), `stwc-enquiry-forms` (`haulage`), `stwc-faq-accordion` (`custom` 5 haulage FAQs), Native 2-Column CTA Banner |
| `/repairs/` | Auto Repairs & Diagnostics | `page-repairs.json` | `template-elementor-fullwidth.php` | `stwc-page-hero`, `stwc-feature-cards` (`service-cards` 6 workshop services), `stwc-spotlight-split` (`primary-card` OBD-II Diagnostics), `stwc-feature-cards` (`pricing-packages` 3 preventative packages), `stwc-enquiry-forms` (`contact`), `stwc-faq-accordion` (`Repairs`) |
| `/body-works/` | Auto Body Works & Spraying | `page-body-works.json` | `template-elementor-fullwidth.php` | `stwc-page-hero`, `stwc-feature-cards` (`service-cards` 6 collision/paint cards), `stwc-spotlight-split` (`before-after-tabs` interactive 3-tab showcase), `stwc-enquiry-forms` (`contact`), `stwc-faq-accordion` (`custom` 4 body shop FAQs) |
| `/contact/` | Contact Us & Dispatch | `page-contact.json` | `template-elementor-fullwidth.php` | `stwc-page-hero` (centered), `stwc-feature-cards` (`service-cards` 4 clickable contact cards), `stwc-enquiry-forms` (`contact`), Native 2-Column Row (`st-hours-card-col` Operating Hours + `stwc-faq-accordion` compact `custom` FAQs) |
| `/careers/` | Careers & Open Vacancies | `page-careers.json` | `template-elementor-fullwidth.php` | `stwc-page-hero`, `stwc-feature-cards` (`service-cards` 4 Why Work With Us cards), `stwc-careers-board` (dynamic `st_career` jobs + CV upload form), `stwc-feature-cards` (`process-steps` `minimal-num` 4 hiring stages) |
| `/faqs/` | Frequently Asked Questions | `page-faqs.json` | `template-elementor-fullwidth.php` | `stwc-page-hero` (`show_faq_search: yes` live JS filter), `stwc-faq-accordion` (all categories w/ tabs), `stwc-feature-cards` (3 centered support cards w/ buttons), Native Boxed `#015557` CTA Card |
| `/gallery/` | Fleet & Workshop Gallery | `page-gallery.json` | `template-elementor-fullwidth.php` | `stwc-page-hero` (centered), `stwc-gallery-showcase` (CPT + category filters + lightbox), `stwc-spotlight-split` (`before-after-slider` drag slider), `stwc-feature-cards` (`photo-feed` 4 linked square tiles) |
| `/service-areas/` | Ghana Service Areas Directory | `page-service-areas.json` | `template-elementor-fullwidth.php` | `stwc-page-hero`, `stwc-feature-cards` (`link-pills` 20 featured districts & highways), `stwc-service-area-route` (`directory` 16 regions / 232 towns live search), `stwc-towing-request-form` |
| `/service-areas/accra/` | Pilot District Example | `pilot-service-area-accra.json` | `template-elementor-fullwidth.php` | `stwc-service-area-route` (`single_route` Accra hero + dispatch copy + prev/next links) + `stwc-towing-request-form` |
| `/service-areas/{slug}/` | Dynamic Template for All 261 Routes | `service-area-single-template.json` | `template-elementor-fullwidth.php` | `stwc-service-area-route` (`single_route` reading current post CPT meta/copy) + `stwc-towing-request-form` |
| `/privacy-policy/` | Privacy Policy (Optional Visual Export) | `pilot-privacy-policy.json` | `template-elementor-fullwidth.php` | `stwc-page-hero` (`shield-check` above-title icon, centered), Native `text-editor` prose, 3-col Native `icon-box` Dispatch Contact strip |

---

## 3. Custom Elementor Free Widgets Reference (17 Widgets)

All custom widgets register under the **Santa Towing** category in Elementor Free's left widget panel (`plugins/santa-towing-website-core/includes/class-elementor.php`).

1. **`stwc-hero-slider`** — Multi-slide Home Hero carousel with slides repeater, background images, overlay gradient, headline/subtitle, primary/secondary CTA buttons, and mobile slide spacing.
2. **`stwc-page-hero`** — Inner page hero banner with repeater breadcrumbs, optional above-title icon or pill badge, H1 (`<span>` color highlights allowed safely via `wp_kses`), subtitle, left/center alignment, multi-CTA repeater, optional right dispatch photo/metric card (`show_side_card`), bottom highlight stats strip (`stats`), and optional live FAQ search input (`show_faq_search`).
3. **`stwc-feature-cards`** — Versatile 11-variant card/grid widget (`quick-actions-overlap`, `icon-center-white`, `icon-top-light`, `icon-dark-glass`, `process-steps`, `service-cards`, `photo-cards`, `photo-overlay`, `pricing-packages`, `photo-feed`, `link-pills`) plus optional bottom callout bar (`show_callout`) with `callout_links` repeater and responsive Desktop/Tablet/Mobile `columns`.
4. **`stwc-spotlight-split`** — Four layout modes: `primary-banner-split` (2-col Emergency CTA + photo), `checklist-feature` (`light`/`primary`/`primary-card` tones with `points` repeater + `fleet-health` or `dispatch-callout` image overlays), `before-after-tabs` (tabbed collision/paint transformation switcher powered cleanly by `santa-core.js`), and `before-after-slider` (interactive drag comparison range bar).
5. **`stwc-services-grid`** — 3-column or custom-column photo + icon services grid with direct links to each service page.
6. **`stwc-haulage-showcase`** — Heavy haulage capability cards, trailer lineup, and Tema/Takoradi Port corridor highlights.
7. **`stwc-cars-catalog`** — Dynamic `st_car` Vehicle Sales grid with category filter pills (`All`, `SUV`, `Sedan`, `Pickup`, `Van`, `Hatchback`).
8. **`stwc-rental-fleet`** — Dynamic `st_rental` Car Rental fleet cards showing daily/weekly rates and WhatsApp booking links.
9. **`stwc-parts-showcase`** — Dynamic `st_part` Genuine Auto Parts catalog with category tabs and inquiry trigger.
10. **`stwc-why-choose-us`** — Brand trust pillars & value propositions grid.
11. **`stwc-blog-grid`** — Latest WordPress `post` articles grid (`posts_per_page`, category & reading-time metadata).
12. **`stwc-faq-accordion`** — Dual-mode FAQ accordion supporting either dynamic `st_faq` CPT queries (`faq_source: cpt`, filtered by topic, optional topic filter tabs) or per-page editable `custom_faqs` repeater (`faq_source: custom`), plus `compact_mode` for side-by-side layouts (used on `/contact/`).
13. **`stwc-gallery-showcase`** — Filterable masonry/grid gallery from `st_gallery` CPTs or custom gallery media with interactive full-screen Prev/Next lightbox.
14. **`stwc-service-area-route`** — Dual-mode coverage widget: `directory` (searchable 16 Ghana Regions / 232 Localities map) or `single_route` (individual District/Corridor hero, localized dispatch copy, Call/WhatsApp pills, Previous/Next corridor navigation).
15. **`stwc-towing-request-form`** — Emergency Tow Request form with live 16 Regions → 232 Areas cascading selector, AJAX dispatch lead saving (`wp_santa_inquiries`), and instant WhatsApp GPS handoff.
16. **`stwc-enquiry-forms`** — Multi-mode AJAX + WhatsApp form (`haulage`, `fleet`, `contact`, `career`) paired with an editable side showcase photo.
17. **`stwc-careers-board`** — Renders `#open-positions`, active `st_career` vacancies, and interactive `Apply Now` drawer with PDF/DOC/DOCX CV upload validation (replaces the legacy `[stwc_careers]` shortcode cleanly).

---

## 4. Safe Editing Guidelines (Preventing Broken Layout & Responsiveness)

When editors customize text, photos, or buttons inside Elementor Free, these built-in theme/plugin safeguards preserve mobile & desktop alignment:

1. **Text & Long Copy Overflow Protection:**
   - Headings (`.elementor-widget-heading .elementor-heading-title`), body text (`.elementor-widget-text-editor`), and all custom widget wrappers apply `min-width: 0; overflow-wrap: break-word; word-wrap: break-word;` across CSS Grid & Flex containers (`santa-core.css` & `style.css`).
   - Horizontal scrolling on mobile viewports (`max-width: 100%; overflow-x: clip;` on `.elementor-page`) is blocked without clipping sticky headers.
   - *Tip:* In `stwc-page-hero` titles, only keep `<span style="color:#d80000;">...</span>` if highlighting specific words; do not paste raw `<div>` blocks inside headings.
2. **Image Aspect-Ratio & Cropping Lock:**
   - Uploading taller or wider photos into native `image` widgets (`pilot-home.json` `.st-el-img-4x3`) or custom widgets (`stwc-page-hero` side card, `stwc-feature-cards` `photo-cards` / `photo-feed`, `stwc-spotlight-split`) automatically preserves proportions via CSS `aspect-ratio` (`4/3`, `16/10`, `16/9`, `1/1`) and `object-fit: cover`.
   - **Never remove** the `_css_classes: st-el-img-4x3` class on the Home `#about` image unless intentionally changing its height aspect ratio.
3. **Side-by-Side Button Rows:**
   - Elementor Free does not have Flexbox Containers enabled by default in all setups. Paired button columns on `pilot-home.json`, `pilot-services.json`, `page-haulage.json`, and `page-faqs.json` carry `_css_classes: st-el-btn-row` (and `st-el-btn-center` when centered) so adjacent `button` widgets wrap cleanly side by side on desktop and stack nicely on mobile.
4. **Operating Hours Card (`page-contact.json`):**
   - Column `ct40002` uses `_css_classes: st-hours-card-col` and its `text-editor` uses `<ul class="st-hours-list">...</ul>`. Edit the times inside Visual or Text view without deleting the `<ul>`/`<li>` tags to keep the two-column aligned dividers intact.
5. **Before/After Interactive Modules (`page-body-works.json` & `page-gallery.json`):**
   - Edit tab labels, captions, and Before/After images directly in the **Santa Spotlight & Before/After Showcase** panel controls — zero JavaScript or custom HTML editing required (`santa-core.js` automatically re-binds inside Elementor live preview via `elementor/frontend/init`).

---

## 5. Importing Templates Manually (If Needed Outside Full Importer)

While `migration/santa-full-importer.php` automatically imports all pages, media attachments, menus, CPT seeds, and hydrates `__ST_MEDIA_URL__*__` / `__ST_HOME_URL__` tokens across all 15 JSONs, you can also import any `.json` file manually:

1. Replace `__ST_HOME_URL__` with your site URL and upload images first if importing raw JSON manually (the Full Importer does this automatically).
2. In WP Admin go to **Templates → Saved Templates → Import Templates** (Elementor Free core feature).
3. Open any target Page → **Edit with Elementor** → Folder icon → **My Templates** → **Insert**.
4. Ensure Page Attributes / Elementor Settings Page Layout matches `template-elementor-canvas.php` (Home) or `template-elementor-fullwidth.php` (Inner pages).
