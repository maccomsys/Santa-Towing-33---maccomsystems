# Santa Towing — WordPress & Elementor Free Conversion Deliverables

This directory contains **100%** of the WordPress + PHP + MySQL + **Elementor Free** conversion deliverables for the **Santa Towing Services** website. The original React/Vite source files (`src/`, `public/`, `scripts/`, `index.html`, `package.json`) remain completely untouched as the source of truth.

---

## Executive Confirmations

- **Every Page & Route Accounted For:** All **328** `<Route>` elements from `src/App.tsx` and **37** `src/pages/*.tsx` components are mapped 1:1 into **26** WordPress Pages, **261** localized Ghana Service Area pages (`st_service_area`), **4** dynamic CPT/Blog endpoints (`/sales/{id}`, `/rental/{id}`, `/parts/{id}`, `/blog/{id}`), `/search`, `/sitemap.xml`, **14** legacy `<Navigate replace>` `301` redirects + `1` sub-service alias redirect, **15** native WordPress Admin screens (`Santa Manager`), and `404` fallback handling. See `docs/COMPLETE-PAGE-INVENTORY.md` and `docs/URL-AND-SEO-MIGRATION-MAP.md`.
- **Zero Elementor Pro or Paid Dependencies:** All **15** Elementor templates use strictly **Elementor Free** (`version: "0.4"`, `106` total widgets = `39` Native Free + `67` custom `stwc-*` across `17` `\Elementor\Widget_Base` widgets; `0` Pro widgets, `0` raw `html` widgets, `0` `shortcode` widgets, `0` `<script>` tags). The **Santa Towing Business Manager** plugin uses native PHP/MySQL/WP Admin UI with **zero** Elementor dependency and **zero** paid libraries (`Pure-PHP PDF 1.4` engine built in).
- **Zero Node.js Needed on Server:** Runs cleanly on standard cPanel shared or VPS hosting (**PHP 8.1+**, **MySQL 5.7+ / MariaDB 10.3+**, **Apache/LiteSpeed**). CSS is pre-compiled statically (`theme/santa-towing/assets/css/santa-styles.css`), PDFs render in pure PHP (`class-stbm-pdf.php`), and interactivity runs in vanilla ES5/ES6 JavaScript. No React embeds, no iframes, no `npm`/`node` runtime required on the server.
- **Unfinished / Live-Verification-Pending Items (Honest Status):**
  1. **Live PHP/MySQL/WordPress Execution:** Because this build environment has `node` and `python3` only (no `php` CLI binary, MySQL server, or WordPress core), live `php -l`, theme/plugin activation, `dbDelta()` custom table creation, `wp_mail()` SMTP sending, CV upload to `wp-content/uploads/`, and streamed A4 PDF download could not execute inside this container and must be tested on your cPanel staging server.
  2. **Live Database Import (`Tools → Santa Full Importer`):** All migration JSONs, 42 media assets, 70 CPT/blog seeds, 26 pages, 261 service areas, and Business Manager defaults are packaged and statically audited, awaiting execution inside WordPress Admin on staging.
  3. **Live Visual Browser & Mobile Parity Checks (`docs/PAGE-MIGRATION-CHECKLIST.md` & `qa/run-qa.mjs`):** All checklist route rows remain intentionally marked `Pending Live WP Check` until verified side-by-side in a real browser across desktop (`1440px`), tablet (`768px`), and mobile (`375px`) viewports.
  4. **User's Browser `localStorage` / `IndexedDB` Data (If Any):** Any live edits saved in an administrator's browser on the old React app must be manually exported via `migration/legacy-browser-export-tool.html` and imported under **Tools → Santa Legacy Browser Importer** (or **Santa Manager → Backup & Export**).
  5. **Verbatim Source Placeholders Preserved:** Per *"no redesign, no simplification, no invented content"*, the 3 `[Editable Placeholder: ...]` blocks from React (`PricingPaymentPolicy.tsx`, `CarDamageLiabilityPolicy.tsx`, `CookiePolicy.tsx`) and `Faqs.tsx` line 343 (`"Contact Maccom Systems today"`) remain preserved verbatim and can be edited anytime via **Santa Manager → Pages Text**.

---

## Quick-Start Installation Order (Beginner cPanel Guide: `docs/CPANEL-DEPLOYMENT-GUIDE.md`)

1. Set up a staging subdomain in cPanel (PHP 8.1+, `upload_max_filesize = 64M`, `post_max_size = 64M`, `memory_limit = 256M`) and install WordPress.
2. Upload & activate **`packages/santa-towing.zip`** (*Appearance → Themes → Upload Theme*).
3. Install & activate **Elementor (Free)** from WordPress.org (*Elementor → Settings → General*: check **Disable Default Colors** and **Disable Default Fonts**).
4. Upload & activate **`packages/santa-towing-website-core.zip`** (*Plugins → Add New → Upload Plugin*).
5. Upload & activate **`packages/santa-towing-business-manager.zip`**.
6. Upload & activate **`packages/santa-towing-importer.zip`**, run **Tools → Santa Full Importer** (Dry Run first, then Complete Site), verify **Settings → Reading**, **Appearance → Menus**, **Settings → Permalinks** (`/%postname%/`), and **AutoSSL**, then deactivate the temporary importer plugin.

---

## Complete Folder & File Inventory

### Root Folder
- `README.md` — This top-level directory index, installation quick-start, and parity/honesty confirmations.

### `packages/` (Ready-to-Upload Installable ZIP Archives)
- `packages/santa-towing.zip` — Custom WordPress theme (`santa-towing/`, 82 files, `8,974,007` bytes, includes pre-compiled Tailwind CSS & 42 verified images)
- `packages/santa-towing-website-core.zip` — Core plugin (`santa-towing-website-core/`, 34 files, `128,102` bytes: 7 CPTs, 17 Elementor Free widgets, rewrites, 301s, sitemap, forms)
- `packages/santa-towing-business-manager.zip` — Admin operations console plugin (`santa-towing-business-manager/`, 40 files, `6,076,989` bytes: 14 modules, 5 MySQL tables, CRM/Billing/PDF/Image Studio)
- `packages/santa-towing-importer.zip` — One-click WP Admin importer plugin (`santa-towing-importer/`, 29 files, `167,726` bytes: bundles migration PHP classes, JSON seeds, and all 15 Elementor templates)
- `packages/santa-towing-elementor-templates.zip` — Flat ZIP of all 15 Elementor Free `.json` templates (`38,017` bytes) for optional import under *Elementor → Templates → Import*

### `docs/` (Architecture Blueprints, Inventories, Guides & Reports)
- `docs/ADMIN-DASHBOARD-INVENTORY.md` — Inventory of all 15 `/admin/*` React screens & browser storage keys
- `docs/COMPLETE-PAGE-INVENTORY.md` — Complete audit of all 328 React `<Route>` definitions & 37 page components
- `docs/COMPONENT-AND-FUNCTIONALITY-INVENTORY.md` — Component, hook, form, slider, filter & modal inventory
- `docs/CONVERSION-PROGRESS.md` — Stage-by-stage (Stages 1–7) engineering progress log & file history
- `docs/CPANEL-DEPLOYMENT-GUIDE.md` — Beginner step-by-step cPanel guide (staging, WP install, theme/plugins, Elementor Free, import, menus/permalinks, SSL, QA, live cutover, backup/restore)
- `docs/ELEMENTOR-EDITING-GUIDE.md` — Non-developer guide to editing public pages & 17 `stwc-*` widgets in Elementor Free
- `docs/FINAL-QA-REPORT.md` — Consolidated Stage 7 QA sign-off, static check telemetry & honest list of live-pending items
- `docs/IMAGE-DOWNLOAD-REPORT.md` — Asset verification & SHA-256 report across all 42 images
- `docs/MIGRATION-DEPENDENCY-PLAN.md` — Dependency & execution sequencing plan across Stages 2–7
- `docs/MIGRATION-REPORT.md` — Detailed media, CPT, service-area, Business Manager defaults & legacy browser migration report
- `docs/PAGE-MIGRATION-CHECKLIST.md` — Per-route live staging QA tracking checklist
- `docs/PLUGIN-BUSINESS-MANAGER-GUIDE.md` — Technical & admin manual for `santa-towing-business-manager`
- `docs/PLUGIN-CORE-GUIDE.md` — Technical manual for `santa-towing-website-core` (CPTs, 17 widgets, rewrites, forms)
- `docs/THEME-INSTALL-GUIDE.md` — Installation & configuration guide for the `santa-towing` custom theme
- `docs/URL-AND-SEO-MIGRATION-MAP.md` — 1:1 URL parity, 14 `301` redirects, XML sitemap & Schema.org map
- `docs/WORDPRESS-MIGRATION-BLUEPRINT.md` — Overall React-to-WordPress conversion blueprint
- `docs/image-manifest.json` — Master image manifest (42 images w/ SHA-256, dimensions, alt text & source references)

### `qa/` (Functional QA Report & Automated Browser Comparison Script)
- `qa/FUNCTIONALITY-TEST-REPORT.md` — 15-subsystem functional parity verification report, bug fixes & Node/Python checks
- `qa/run-qa.mjs` — Playwright/browser QA & visual diff runner script for comparing React preview vs. live WordPress staging

### `assets/` (`assets/images/` — 42 Verified Source Images Preserved Exact-Name & SHA-256 Clean)
- `assets/images/avatar-dispatch-director-256.jpg`
- `assets/images/avatar-fleet-manager-256.jpg`
- `assets/images/avatar-operations-lead-256.jpg`
- `assets/images/avatar-safety-specialist-256.jpg`
- `assets/images/avatar-support-officer-256.jpg`
- `assets/images/construction_haulage_1790967998627.jpg`
- `assets/images/haulage-bulk-freight-800.jpg`
- `assets/images/haulage-commercial-fleet-800.jpg`
- `assets/images/haulage-container-port-600.jpg`
- `assets/images/haulage-container-port-800.jpg`
- `assets/images/haulage-container-port-1920.jpg`
- `assets/images/haulage-heavy-logistics-600.jpg`
- `assets/images/haulage-heavy-logistics-800.jpg`
- `assets/images/haulage-highway-freight-800.jpg`
- `assets/images/haulage-highway-freight-1200.jpg`
- `assets/images/haulage-highway-freight-1920.jpg`
- `assets/images/haulage-industrial-machinery-800.jpg`
- `assets/images/haulage-semi-truck-600.jpg`
- `assets/images/haulage-semi-truck-800.jpg`
- `assets/images/haulage-semi-truck-1920.jpg`
- `assets/images/haulage-shipping-containers-600.jpg`
- `assets/images/haulage-shipping-containers-800.jpg`
- `assets/images/haulage-warehouse-cargo-800.jpg`
- `assets/images/image-1-towing-vehicle.jpg`
- `assets/images/image-2-suv-kia-sportage-2024.png`
- `assets/images/image-3-sedan-toyota-corolla.jpg`
- `assets/images/image-4-van-hyundai-h1-2022.jpg`
- `assets/images/image-5-economy-nissan-almera-2021.jpg`
- `assets/images/image-6-luxury-mercedes-benz.jpg`
- `assets/images/image-7-pickup-toyota-hilux-2023.jpg`
- `assets/images/image-8-heavy-duty-recovery-vehicle.jpg`
- `assets/images/image-9-car-battery-maintenance.jpg`
- `assets/images/image-10-engine-oil-fluids-maintenance.jpg`
- `assets/images/image-11-brake-parts-disk-maintenance.jpg`
- `assets/images/image-12-tyre-maintenance.jpg`
- `assets/images/image-13-filters-maintenance.jpg`
- `assets/images/image-14-suspension-parts-maintenance.jpg`
- `assets/images/image-15-car-lighting-maintenance.jpg`
- `assets/images/image-16-accessories.jpg`
- `assets/images/santa-app-mobile-screen.png`
- `assets/images/santa-logo.png`
- `assets/images/welcome-towing-image.jpg`

### `elementor-templates/` (15 Elementor Free `version: "0.4"` Page & Route JSON Templates)
- `elementor-templates/pilot-home.json` — Homepage (`/`)
- `elementor-templates/pilot-services.json` — Our Services (`/services/`)
- `elementor-templates/pilot-fleet.json` — Our Fleet (`/fleet/`)
- `elementor-templates/pilot-privacy-policy.json` — Privacy Policy reference export (`/privacy-policy/`)
- `elementor-templates/pilot-service-area-accra.json` — Pilot Accra route (`/service-areas/accra/`)
- `elementor-templates/page-towing.json` — Towing Services (`/towing/`)
- `elementor-templates/page-haulage.json` — Heavy Haulage (`/haulage/`)
- `elementor-templates/page-repairs.json` — Auto Repairs & Maintenance (`/repairs/`)
- `elementor-templates/page-body-works.json` — Body Works & Spray Painting (`/body-works/`)
- `elementor-templates/page-contact.json` — Contact Us (`/contact/`)
- `elementor-templates/page-careers.json` — Careers (`/careers/`)
- `elementor-templates/page-faqs.json` — Frequently Asked Questions (`/faqs/`)
- `elementor-templates/page-gallery.json` — Media Gallery (`/gallery/`)
- `elementor-templates/page-service-areas.json` — Service Areas Directory (`/service-areas/`)
- `elementor-templates/service-area-single-template.json` — Shared single Service Area layout for all 261 Ghana areas

### `migration/` (Importers, Migration Datasets & Legacy Browser Export Tools)
- `migration/class-st-media-sync.php` — Idempotent 42-asset WordPress Media Library synchronizer & Elementor token hydrator
- `migration/class-stbm-legacy-importer.php` — Standalone WP Admin (`Tools → Santa Legacy Browser Importer`) & WP-CLI importer
- `migration/santa-full-importer.php` — Master full-site & chunked 261-service-area WP Admin + WP-CLI importer (`Tools → Santa Full Importer`)
- `migration/santa-pilot-importer.php` — Stage 4 Phase A 5-route pilot importer (`Tools → Santa Pilot Importer`)
- `migration/business-manager-defaults.json` — Extracted defaults (30 image slots, 32 adverts, 16 regions/232 areas, billing & studio seeds)
- `migration/cpt-seeds.json` — 70 CPT & blog post seeds + 4 navigation menu definitions
- `migration/media-manifest.json` — Media sync lookup manifest (filenames, SHA-256, MIME, dimensions, alt text)
- `migration/migration-audit-report.json` — Machine-readable static migration audit telemetry
- `migration/phase-b-pages.json` — 22 Phase B pages (marketing, directory, utilities & 11 verbatim policies)
- `migration/pilot-pages.json` — 5 Pilot route definitions & SEO metadata
- `migration/service-areas-261.json` — All 261 localized Service Area definitions (`35` local districts + `226` intercity corridors)
- `migration/legacy-browser-export-snippet.js` — Read-only browser console script exporting `localStorage` + `IndexedDB` (`santa_image_studio_db`)
- `migration/legacy-browser-export-tool.html` — Standalone clickable HTML helper & bookmarklet for Legacy React Browser export

### `theme/` (`theme/santa-towing/` — Custom WordPress Theme)
- **Root Templates & Theme Core:**
  - `theme/santa-towing/404.php`
  - `theme/santa-towing/archive-st_car.php`
  - `theme/santa-towing/archive-st_part.php`
  - `theme/santa-towing/archive-st_rental.php`
  - `theme/santa-towing/archive.php`
  - `theme/santa-towing/footer.php`
  - `theme/santa-towing/front-page.php`
  - `theme/santa-towing/functions.php`
  - `theme/santa-towing/header.php`
  - `theme/santa-towing/home.php`
  - `theme/santa-towing/index.php`
  - `theme/santa-towing/page.php`
  - `theme/santa-towing/readme.txt`
  - `theme/santa-towing/screenshot.png`
  - `theme/santa-towing/search.php`
  - `theme/santa-towing/single-st_car.php`
  - `theme/santa-towing/single-st_part.php`
  - `theme/santa-towing/single-st_rental.php`
  - `theme/santa-towing/single-st_service_area.php`
  - `theme/santa-towing/single.php`
  - `theme/santa-towing/style.css`
- **`theme/santa-towing/page-templates/`:**
  - `theme/santa-towing/page-templates/template-elementor-blank.php`
  - `theme/santa-towing/page-templates/template-elementor-canvas.php`
  - `theme/santa-towing/page-templates/template-elementor-fullwidth.php`
  - `theme/santa-towing/page-templates/template-policy.php`
  - `theme/santa-towing/page-templates/template-sitemap.php`
- **`theme/santa-towing/template-parts/`:**
  - `theme/santa-towing/template-parts/breadcrumbs.php`
  - `theme/santa-towing/template-parts/card-blog.php`
  - `theme/santa-towing/template-parts/card-car.php`
  - `theme/santa-towing/template-parts/card-part.php`
  - `theme/santa-towing/template-parts/card-rental.php`
  - `theme/santa-towing/template-parts/floating-buttons.php`
  - `theme/santa-towing/template-parts/footer-content.php`
  - `theme/santa-towing/template-parts/navbar.php`
  - `theme/santa-towing/template-parts/scroll-to-footer-arrow.php`
  - `theme/santa-towing/template-parts/service-area-template.php`
  - `theme/santa-towing/template-parts/top-bar.php`
- **`theme/santa-towing/assets/`:**
  - `theme/santa-towing/assets/css/santa-styles.css` — Pre-compiled Tailwind CSS design system (`#015557`, `#192e2e`, `#d80000`, `#131514`)
  - `theme/santa-towing/assets/js/santa-main.js` — Navbar, hero slider, archive filters, accordions, sitemap search, newsletter AJAX
  - `theme/santa-towing/assets/js/santa-motion.js` — IntersectionObserver scroll reveal motion
  - `theme/santa-towing/assets/images/` — All 42 verified image assets (exact same 42 filenames listed under `assets/images/` above)

### `plugins/` (3 Custom WordPress Plugins)

#### 1. `plugins/santa-towing-website-core/`
- `plugins/santa-towing-website-core/readme.txt`
- `plugins/santa-towing-website-core/santa-towing-website-core.php`
- `plugins/santa-towing-website-core/uninstall.php`
- **`assets/`:**
  - `plugins/santa-towing-website-core/assets/css/santa-core-admin.css`
  - `plugins/santa-towing-website-core/assets/css/santa-core.css`
  - `plugins/santa-towing-website-core/assets/js/santa-core-admin.js`
  - `plugins/santa-towing-website-core/assets/js/santa-core.js`
- **`includes/`:**
  - `plugins/santa-towing-website-core/includes/class-cpts.php`
  - `plugins/santa-towing-website-core/includes/class-elementor.php`
  - `plugins/santa-towing-website-core/includes/class-forms.php`
  - `plugins/santa-towing-website-core/includes/class-rewrites.php`
  - `plugins/santa-towing-website-core/includes/class-settings.php`
  - `plugins/santa-towing-website-core/includes/data-defaults.php`
  - `plugins/santa-towing-website-core/includes/helpers.php`
- **`includes/widgets/` (17 Custom Elementor Free Widgets):**
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-base.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-blog-grid.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-careers-board.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-cars-catalog.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-enquiry-forms.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-faq-accordion.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-feature-cards.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-gallery-showcase.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-haulage-showcase.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-hero-slider.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-page-hero.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-parts-showcase.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-rental-fleet.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-service-area-route.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-services-grid.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-spotlight-split.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-towing-request-form.php`
  - `plugins/santa-towing-website-core/includes/widgets/class-widget-why-choose-us.php`
- **`templates/`:**
  - `plugins/santa-towing-website-core/templates/virtual-area-service.php`
  - `plugins/santa-towing-website-core/templates/virtual-service-area.php`

#### 2. `plugins/santa-towing-business-manager/`
- `plugins/santa-towing-business-manager/readme.txt`
- `plugins/santa-towing-business-manager/santa-towing-business-manager.php`
- `plugins/santa-towing-business-manager/uninstall.php`
- **`includes/`:**
  - `plugins/santa-towing-business-manager/includes/class-stbm-admin-ui.php`
  - `plugins/santa-towing-business-manager/includes/class-stbm-ajax.php`
  - `plugins/santa-towing-business-manager/includes/class-stbm-db.php`
  - `plugins/santa-towing-business-manager/includes/class-stbm-defaults.php`
  - `plugins/santa-towing-business-manager/includes/class-stbm-exports.php`
  - `plugins/santa-towing-business-manager/includes/class-stbm-legacy-importer.php`
  - `plugins/santa-towing-business-manager/includes/class-stbm-pdf.php`
  - `plugins/santa-towing-business-manager/includes/class-stbm-roles.php`
- **`assets/`:**
  - `plugins/santa-towing-business-manager/assets/css/stbm-admin.css`
  - `plugins/santa-towing-business-manager/assets/js/stbm-app.js`
  - `plugins/santa-towing-business-manager/assets/js/stbm-billing.js`
  - `plugins/santa-towing-business-manager/assets/js/stbm-image-studio.js`
  - **`plugins/santa-towing-business-manager/assets/images/`** (25 bundled standalone admin fallback assets):
    - `avatar-dispatch-director-256.jpg`, `avatar-fleet-manager-256.jpg`, `avatar-operations-lead-256.jpg`, `avatar-safety-specialist-256.jpg`, `avatar-support-officer-256.jpg`
    - `construction_haulage_1790967998627.jpg`, `welcome-towing-image.jpg`, `santa-logo.png`, `santa-app-mobile-screen.png`
    - `image-1-towing-vehicle.jpg`, `image-2-suv-kia-sportage-2024.png`, `image-3-sedan-toyota-corolla.jpg`, `image-4-van-hyundai-h1-2022.jpg`, `image-5-economy-nissan-almera-2021.jpg`, `image-6-luxury-mercedes-benz.jpg`, `image-7-pickup-toyota-hilux-2023.jpg`, `image-8-heavy-duty-recovery-vehicle.jpg`, `image-9-car-battery-maintenance.jpg`, `image-10-engine-oil-fluids-maintenance.jpg`, `image-11-brake-parts-disk-maintenance.jpg`, `image-12-tyre-maintenance.jpg`, `image-13-filters-maintenance.jpg`, `image-14-suspension-parts-maintenance.jpg`, `image-15-car-lighting-maintenance.jpg`, `image-16-accessories.jpg`

#### 3. `plugins/santa-towing-importer/`
- `plugins/santa-towing-importer/readme.txt`
- `plugins/santa-towing-importer/santa-towing-importer.php`
- **`elementor-templates/`** (Synced copies of all 15 Elementor Free `.json` templates):
  - `page-body-works.json`, `page-careers.json`, `page-contact.json`, `page-faqs.json`, `page-gallery.json`, `page-haulage.json`, `page-repairs.json`, `page-service-areas.json`, `page-towing.json`, `pilot-fleet.json`, `pilot-home.json`, `pilot-privacy-policy.json`, `pilot-service-area-accra.json`, `pilot-services.json`, `service-area-single-template.json`
- **`migration/`** (Synced copies of importer PHP engines, JSON datasets, and export tools):
  - `business-manager-defaults.json`, `class-st-media-sync.php`, `class-stbm-legacy-importer.php`, `cpt-seeds.json`, `legacy-browser-export-snippet.js`, `legacy-browser-export-tool.html`, `media-manifest.json`, `phase-b-pages.json`, `pilot-pages.json`, `santa-full-importer.php`, `santa-pilot-importer.php`, `service-areas-261.json`
