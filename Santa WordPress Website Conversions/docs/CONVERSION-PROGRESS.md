# Santa Towing WordPress Conversion — Progress Tracker

**Target Architecture:** WordPress 6.x + PHP 8.1+ + MySQL / MariaDB + Custom Theme (`santa-towing`) + Custom Modular Plugins + **Elementor FREE Only** (cPanel compatible; zero Node.js, zero React embeds, zero iframes, zero paid plugins/themes).  
**Source of Truth:** Original React/Vite project (`src/`, `public/`, `scripts/`, `index.html`, `package.json`) — **100% Untouched**.

---

## Stage Status Overview

| Stage | Scope | Status | Verification Summary |
|---|---|---|---|
| **Stage 1** | Full Codebase Audit & Architecture Blueprinting | **COMPLETED** | All 328 `App.tsx` routes, 37 top-level `src/pages/*.tsx` components, 261 `service-areas/*.tsx` files, all sections/layout/editable/SEO components, contexts/hooks, 15 `/admin/*` screens, billing/jsPDF/Image Studio engines, 7 `localStorage` + 4 `sessionStorage` + 1 `IndexedDB` stores audited & documented |
| **Stage 2** | Custom Theme Foundation (`santa-towing`) | **COMPLETED** | 33 PHP templates/partials, static pre-compiled Tailwind v4 CSS (`137,658` bytes minified), vanilla Motion & Interaction JS, 42 bundled images, `screenshot.png` (1200×900), `THEME-INSTALL-GUIDE.md`, and verified `packages/santa-towing.zip` (`8.55 MB`, 81 files). Selector alignment fixes applied and re-verified. |
| **Stage 3** | Core Plugin (`santa-towing-website-core`) | **COMPLETED** | 7 CPTs, 9 Taxonomies, Tabbed Site Settings w/ Elementor Kit color sync, 13 Elementor Free Custom Widgets (all 13 verified w/ Content, Image/Media, Style & Responsive controls), URL Rewrites + 14 301s + Virtual Area Templates, Secure AJAX/CV/WhatsApp Forms (`wp_santa_inquiries`), and verified `packages/santa-towing-website-core.zip` (`100.50 KB`, 30 files) |
| **Stage 4 (Phase A — Pilot)** | 5-Route Pilot Migration & Elementor Free Exports | **APPROVED & COMPLETED (Pre-Live WP Check)** | User approved 5 Pilot routes (`Home`, `Privacy Policy`, `Services`, `Fleet`, `Accra`). Exported in `pilot-pages.json` + 5 Elementor Free v0.4 JSONs + `santa-pilot-importer.php` (`0` marked Done pre-live WP check). |
| **Stage 4 (Phase B — Full Site)** | Full-Site & 261 Service Area Generator | **READY FOR LIVE WP IMPORT (Unverified Live)** | All **22** remaining pages (`phase-b-pages.json` + 9 `page-*.json` Elementor Free templates + 11 verbatim legal policies), **70** CPT/blog items + **42** images + **4** menus (`cpt-seeds.json`), and **261** service-area pages (`service-areas-261.json` + `service-area-single-template.json`) wired into `santa-full-importer.php`. Added `theme/santa-towing/home.php` for `/blog/` (`page_for_posts`) & rebuilt `packages/santa-towing.zip`. (`0` routes marked Done pending live WP execution.) |
| **Stage 5** | Business Manager Plugin (`santa-towing-business-manager`) — Billing, CRM, Content & Image Studio | **COMPLETED (Static Verified — Pending Live WP Check)** | Standalone WordPress admin console (`10` PHP files, `3` JS engines, `1` scoped CSS, `25` assets, `0` Elementor dependency): `5` custom MySQL tables via `dbDelta()`, `st_manager`/`st_staff` RBAC + `26` nonced AJAX + `3` export endpoints, Pure-PHP A4 PDF 1.4 generator, HTML5 Canvas Image Studio w/ WP Media upload, Unified Legacy Browser JSON Importer, all `14` modules, and verified `packages/santa-towing-business-manager.zip` (`6.07 MB`, `40` files). |
| **Stage 5B** | Data, Media & Legacy Browser Migration Tooling | **COMPLETED (Static Verified — Pending Live WP & Browser Import)** | Verified `42/42` images SHA-256 match + preserved filenames & `_wp_attachment_image_alt` (`media-manifest.json`, `class-st-media-sync.php`), `258/258` `site-content.json` keys, `30` `SITE_IMAGES` slots, `32` adverts, `16` regions/`232` areas, `70` CPT seeds, `26` pages, `261` service areas, Business Manager defaults (`business-manager-defaults.json`), read-only browser `localStorage`+`IndexedDB` export snippet & HTML bookmarklet (`legacy-browser-export-snippet.js`), Business Manager + standalone importer (`class-stbm-legacy-importer.php`), `migration-audit-report.json`, and `docs/MIGRATION-REPORT.md`. *(Browser `localStorage`/`IndexedDB` & live MySQL writes strictly marked Pending until admin runs them on live host.)* |
| **Stage 6** | Functional Parity Audit, Hardening & Automated Verification | **COMPLETED (Static & Node Verified — Pending Live WP Check)** | Audited & upgraded all `15` functional areas across theme & both plugins (`17` parity bugs resolved), passed `node --check` + Node mock-DOM functional tests on all JS modules, passed Python static PHP audit on all `69` PHP files, rebuilt & verified all `3` ZIPs (`testzip() = None`), documented results in `qa/FUNCTIONALITY-TEST-REPORT.md`. *(Visual live-WP/cPanel browser QA still pending live host.)* |
| **Stage 6B** | Elementor Free Public Page Editable Architecture | **COMPLETED (Static & Node Verified — Pending Live WP Check)** | Replaced **100%** of raw `html`, `shortcode`, and inline `<script>` widgets across all **15/15** `elementor-templates/*.json` exports (`106` total widgets: `39` Native Free + `67` custom `stwc-*` across `17` `stwc-*` widgets; `0` Elementor Pro widgets). Added `stwc-page-hero`, `stwc-feature-cards`, `stwc-spotlight-split`, `stwc-careers-board`, overflow/image resilience CSS, JS `elementor/frontend/init` hooks, rebuilt `santa-towing.zip` + `santa-towing-website-core.zip`, kept `santa-towing-business-manager` strictly outside Elementor, and wrote `docs/ELEMENTOR-EDITING-GUIDE.md`. |
| **Stage 7** | cPanel Packaging & Deployment Runbook | **COMPLETED (Static Deliverables Packaged — Pending Live cPanel Execution)** | Built `santa-towing-importer.zip` (`29` files, `167,726` bytes) & `santa-towing-elementor-templates.zip` (`15` files, `38,017` bytes) alongside verified `santa-towing.zip`, `santa-towing-website-core.zip`, and `santa-towing-business-manager.zip`; synced guides (`PLUGIN-CORE-GUIDE.md`, `PLUGIN-BUSINESS-MANAGER-GUIDE.md`, `THEME-INSTALL-GUIDE.md`); wrote `docs/CPANEL-DEPLOYMENT-GUIDE.md`, `docs/FINAL-QA-REPORT.md`, and top-level `README.md` inventorying every file and folder. |

---

## Stage 1 — Verified Key Findings & Honest Limitations

1. **Exact Route & File Reconciliation:**
   - Verified `src/App.tsx` contains **328** `<Route>` elements: `34` static/parameterized top-level public routes + `2` dynamic service-area routes (`/service-areas/:areaSlug`, `/service-areas/:areaSlug/:serviceSlug`) + `1` wildcard 404 (`NotFound`) + `14` `<Navigate replace>` alias redirects + `261` static `/service-areas/*` routes (`35` Local Districts + `226` Intercity Corridors) + `16` `/admin/*` route tags (`15` navigable admin screens).
   - Verified `src/pages/` contains **37** top-level `.tsx` files (`34 + 2 + 1 = 37`).
2. **Unrecoverable Browser-Only Runtime Data (From ZIP Alone):**
   - React persists edits, billing records, customers, settings, and Image Studio assets solely to **7 `localStorage` keys**, **4 `sessionStorage` keys**, and **1 `IndexedDB` database (`santa_image_studio_db`)**.
   - Only factory seed defaults (`site-content.json`, `inventoryDefaults.ts`, `blogData.ts`, & sample invoice/receipt seeds) exist in source files. Any live browser edits or real client invoices/receipts must be manually exported via both backup tools (`Admin > Profile > Backup` + `Admin > Backup & Export`) prior to migration.
3. **Confirmed Static Mock UI / Source Defects Documented Honestly:**
   - **Mock Form Submissions:** Haulage quote (`/haulage`), Car inquiry (`/sales/:id`), Rental booking (`/rental/:id`), Part quote (`/parts/:id`), Fleet/Contact forms, Newsletter (`/blog`), and Careers application drawer (fake `"Browse files"` button) simulate submission locally without emailing or persisting; Contact offers a real WhatsApp deep link. WordPress replaces mock submits with real `admin-ajax.php` handlers (`wp_mail()` + `wp_santa_inquiries` table + real CV upload).
   - **Static Archive Controls:** `/sales` quick search (`e.preventDefault()`), sidebar checkboxes, financing/trade-in buttons, and Load More are static UI in React; `/rental` category `View Cars` buttons are unwired; `/parts` cart/quantity/YMM/review are UI placeholders; `/gallery` 4-tile Instagram strip & video cards lack dynamic players.
   - **Navigation & Content Bugs in Source:** `PartDetails.tsx` prev/next uses hardcoded `[1..6]` instead of live parts; `BlogPost.tsx` prev/next reads static `BLOG_POSTS` instead of `content.blog.posts`; `Faqs.tsx` line 343 says `"Contact Maccom Systems today"`; 3 policy pages (`PricingPaymentPolicy`, `CarDamageLiabilityPolicy`, `CookiePolicy`) contain literal `[Editable Placeholder: ...]` strings.
   - **SEO & Sitemap Deficiencies in Source:** `SEO.tsx` does not dynamically inject OpenGraph/Twitter tags; `public/sitemap.xml` has not been generated in `public/`; `scripts/generate-sitemap.ts` fails to extract cars/parts after the `inventoryDefaults.ts` refactor and leaks `<Navigate replace>` alias URLs.
4. **Elementor Free Boundary Confirmed:**
   - **Elementor Free** cannot author global Header/Footer, Single CPT templates, Archive filters, or dynamic Service Area templates without Elementor Pro. Those live natively in our `santa-towing` PHP theme, while marketing pages use Elementor Free JSON templates powered by Custom Elementor Free Widgets (`\Elementor\Widget_Base`).

---

## Stage 2 — Verification & Honest Environment Notes

1. **Tailwind v4 Static Compilation Verified:**
   - Compiled `src/index.css` design tokens + utility candidates scanned across `src/**/*.{ts,tsx,js,jsx}` and `Santa WordPress Website Conversions/theme/santa-towing/**/*.{php,js,html}` (`7,238` candidates) and minified via `lightningcss` into `assets/css/santa-styles.css` (`137,658` bytes). Confirmed all required tokens (`--color-primary`, `#015557`, `#192e2e`, `#d80000`, `bg-secondary`, `xl:flex`, `lg:translate-y-9`, `printable-document-root`, `@media print`) are present.
2. **Selector Alignment & Style Audit Fixes Applied:**
   - Fixed JS/HTML selector mismatches in `assets/js/santa-main.js`: header scroll target (`#st-main-header` + `data-is-home`), mobile drawer toggle (`#st-mobile-drawer` `hidden` class + `.st-icon-open`/`.st-icon-close`), accordion chevron selector (`.st-accordion-icon, .st-chevron`), and newsletter AJAX target (`#st-footer-newsletter-form` / `#st-footer-newsletter-success`).
   - Aligned `st-accordion-panel` in `front-page.php` and `single-st_car.php`, `st-animate-bob` in `template-parts/scroll-to-footer-arrow.php`, and added `.prose` + `.navigation.pagination` CSS rules in `style.css`.
3. **Code Hygiene & Package Integrity Checks:**
   - `node --check` passed on both `assets/js/santa-main.js` and `assets/js/santa-motion.js`.
   - Structural Python parser audit across all **33** `.php` files verified balanced PHP tags/delimiters, `ABSPATH` security guards, valid `get_template_part()` paths, and complete `st_icon()` icon mappings (`56` icons defined, `0` undefined).
   - Packaged `Santa WordPress Website Conversions/packages/santa-towing.zip` (`8.55 MB`, `81` archived files under `santa-towing/`) and passed `zipfile.ZipFile.testzip()`.
4. **Honest Build Container Limitation Disclosed:**
   - The Linux container in this environment does **not** have a `php` CLI binary or live WordPress/MySQL runtime installed (`which php` absent). Therefore, `php -l` linting and live browser/WordPress activation rendering could not be executed locally here and must be verified on a PHP 8.1+ WordPress/cPanel server (automated PHP bracket/syntax parsing, JS AST checks, CSS compilation, and ZIP integrity checks all passed).

---

## Stage 3 — Core Plugin (`santa-towing-website-core`) Verification & Notes

1. **Architecture & Feature Completeness:**
   - Implemented **7 CPTs** (`st_car`, `st_rental`, `st_part`, `st_service_area`, `st_gallery`, `st_faq`, `st_career`), **9 Taxonomies**, all theme-compatible post meta keys + admin Meta Boxes with Media Library gallery pickers, and custom admin list columns.
   - Implemented **Tabbed Admin Settings** (`Brand & Logo`, `Theme Colors & Elementor Kit Sync`, `Contact & WhatsApp`, `Social, Footer & Floating UI`, `SEO & Service Area Copy`), **Inquiries & Dispatch Leads** table viewer + CSV export, and **Routes & System Diagnostics** (`Flush Rewrite Rules`).
   - Implemented **URL Rewrites & SEO Engine**: numeric ID resolution (`/sales/{id}`, `/rental/{id}`, `/parts/{id}`, `/blog/{id}`), all 14 React `<Navigate replace>` 301 redirects + `vehicle-recovery` → `car-recovery` 301, virtual HTTP 200 templates covering all 32 Districts + 232 Ghana Areas + 4 specialized towing sub-services, `/sitemap.xml`, `robots.txt` sitemap append, and `AutoRepair` Schema.org JSON-LD.
   - Implemented **13 Elementor Free Custom Widgets** extending `\Elementor\Widget_Base` with Content, Media/Image, Style, and Responsive (`add_responsive_control`) controls, using strictly Elementor Free APIs plus companion fallback shortcodes.
   - Implemented **Secure Form Handlers** (`dbDelta` `wp_santa_inquiries` table, nonce, honeypot, CV PDF/DOC/DOCX ≤ 5MB validation, `wp_mail()` alerts, and exact React WhatsApp `wa.me` deep-link formatting).
2. **Automated Verification Executed:**
   - Verified all **13/13** Elementor Free custom widgets explicitly register Content (`TAB_CONTENT`), Image/Media (`Controls_Manager::MEDIA`/`GALLERY`), Style (`TAB_STYLE`), and Responsive (`add_responsive_control`) controls.
   - Validated all **25** `.php` files for `<?php` headers, `ABSPATH` / `WP_UNINSTALL_PLUGIN` security guards, zero `ElementorPro` references, and balanced parentheses/braces/brackets outside strings/heredocs/comments (`0` errors).
   - Validated both `.js` files (`assets/js/santa-core.js`, `assets/js/santa-core-admin.js`) via `node --check` (`0` errors).
   - Packaged `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip` (`100.50 KB`, `30` archived entries under `santa-towing-website-core/`) and verified `zipfile.ZipFile.testzip()` returned `None`.
3. **Honest Tool Limitation Reminder:**
   - Because `php` CLI and a live MySQL/WordPress server are not present in this build container, runtime execution (`php -l`, WP activation, and visual Elementor Editor rendering) must be verified on a live PHP 8.1+ WordPress/cPanel host.

---

## Stage 4 (Phase A — Pilot) — Deliverables, Parity Alignment & Gate Status

1. **5 Pilot Routes Selected & Prepared:**
   - **Home (`/`):** `pilot-home.json` (`template-elementor-canvas.php`, static `page_on_front`) covering all 11 homepage sections (`stwc-hero-slider`, overlapping 5 Quick Actions with Lucide SVG badges, `#about` with 24/7 floating dispatch card & inline rounded pills, `stwc-services-grid`, `stwc-haulage-showcase`, `stwc-cars-catalog`, `stwc-rental-fleet`, `stwc-parts-showcase`, `stwc-why-choose-us`, `stwc-blog-grid`, `stwc-faq-accordion`) with 18 bundled image tokens and `show_call_btn` disabled to prevent duplicate hero Call CTAs.
   - **Privacy Policy (`/privacy-policy/`):** Exact 10-section legal prose + breadcrumb (`Home / Privacy Policy`) configured cleanly for `template-policy.php` sidebar layout alongside a full standalone Elementor Free export (`pilot-privacy-policy.json` with `ShieldCheck` hero badge & 3-icon Dispatch Contact strip).
   - **Our Services (`/services/`):** `pilot-services.json` (`template-elementor-fullwidth.php`) with intro header, breadcrumbs above the 9-card `stwc-services-grid`, 6 Stand Out features with Lucide SVG icons, 5 Process steps, dark Emergency Towing spotlight with gradient overlay + 10 service assets, `stwc-faq-accordion`, and CTA banner.
   - **Our Fleet (`/fleet/`):** `pilot-fleet.json` (`template-elementor-fullwidth.php`) with B2B Hero, 6 fleet service cards + 6 benefits + 6 industry photo cards (all with Lucide SVG icons), 5 onboarding steps, 98% Client Portal preview, interactive `stwc-enquiry-forms` (`mode=fleet`), `stwc-faq-accordion` (`category=Fleet`), and CTA banner.
   - **Accra Service Area (`/service-areas/accra/`):** `st_service_area` CPT (`accra`, `st_region=Greater Accra`) + `pilot-service-area-accra.json` rendering `stwc-service-area-route` (`single_route` mode) with exact React localized Accra copy, breadcrumbs, `Call Now` + `WhatsApp Us` pills, and Previous (`Aburi to Accra`) / Next (`Accra to Abuakwa`) links.
2. **Core Plugin Parity Fixes & Rebuilt Package:**
   - Updated `plugins/santa-towing-website-core/includes/class-rewrites.php` to align all **14** React `<Navigate replace>` routes (including `/vehicle-damage-liability-policy/` → `/car-damage-liability-policy/`, `/terms-conditions/`, `/about` → `/#about`, and detail pattern regex `^/(?:vehicles|cars|vehicle-details|car-details)/[^/]+$`), honor `_st_seo_title` and `_st_keywords_json` across all singular pages/CPTs, and preserve full verbatim `_st_seo_description` meta values.
   - Updated `plugins/santa-towing-website-core/includes/widgets/class-widget-service-area-route.php` in `single_route` mode to output full React breadcrumbs, H1 title, `Call Now` / `WhatsApp Us` actions, and Previous / Next Area links.
   - Rebuilt and integrity-verified `packages/santa-towing-website-core.zip` (`103,631` bytes, `30` files, `testzip() = None`).
3. **Static Validation & Checklist Policy:**
   - Verified all 5 `elementor-templates/pilot-*.json` templates are Elementor Free `0.4` JSON (`0` Elementor Pro widgets/hooks, `0` unknown custom widget slugs, `0` missing media filenames), validated `migration/pilot-pages.json` + both PHP migration files (`<?php`, `ABSPATH` guards, balanced delimiters), and created `docs/PAGE-MIGRATION-CHECKLIST.md` tracking all `37` top-level/dynamic pages + `15` redirects + `261` service areas.
   - **Zero routes are marked Done** in `PAGE-MIGRATION-CHECKLIST.md` because live PHP/WordPress/MySQL execution is unavailable in this build container. Pilot received explicit user approval (`"approve"`) to proceed into Stage 4 Phase B.

---

## Stage 4 (Phase B — Full Site & 261 Service Area Generator) — Deliverables & Honest Verification

1. **Remaining Pages & Policy Manifest (`migration/phase-b-pages.json`):**
   - **11 Marketing, Directory & Utility Pages:** `towing`, `haulage`, `repairs`, `body-works`, `contact`, `careers`, `faqs`, `gallery`, `service-areas` (paired with the `9` Elementor Free `elementor-templates/page-*.json` exports), plus `sitemap` (`page-templates/template-sitemap.php`) and `blog` (`set_as_posts_page: true`). Exact SEO titles, descriptions, canonicals, and keywords extracted verbatim from `src/pages/*.tsx` and `src/data/site-content.json`.
   - **11 Remaining Legal & Policy Pages (`template-policy.php`):** `terms-conditions`, `towing-service-terms`, `cancellation-refund-policy`, `pricing-payment-policy`, `disclaimer`, `cookie-policy`, `service-area-policy`, `emergency-service-policy`, `complaints-policy`, `car-damage-liability-policy`, `accessibility-statement` — full prose extracted verbatim from each `.tsx` component with JSX cleanly normalized (`className` → `class`, `<Link to>` → `<a href="__ST_HOME_URL__/...">`, balanced HTML tags).
2. **Blog Index Delegation (`theme/santa-towing/home.php` & Theme ZIP Rebuild):**
   - Added `theme/santa-towing/home.php` delegating cleanly to `archive.php` so assigning WordPress `page_for_posts` (`/blog/`) renders the full React-parity Blog Hero, categories strip, and post grid instead of falling back to `index.php`.
   - Rebuilt and integrity-verified `packages/santa-towing.zip` (`8,967,405` bytes, `82` archived files, `testzip() = None`).
3. **Full-Site & 261 Service Area Idempotent Importer (`migration/santa-full-importer.php`):**
   - Unified WP-CLI (`wp eval-file`) + WP Admin Tools (`Tools -> Santa Full Importer`) importer supporting nonced Dry Run previews, scoped steps, and cPanel-timeout-safe `offset` / `limit` batching with `wp_defer_term_counting(true)`.
   - Imports & links idempotently (`_st_legacy_id` + `get_page_by_path`): all **42** media assets (`ST_Migration_Media_Sync`), `santa_towing_site_content` + `custom_logo` + Elementor Kit colors, all **4** nav menus (`primary` w/ children, `footer_quick`, `footer_services`, `footer_legal`), **70** CPT/blog records (`12 st_car` w/ specs & galleries, `6 st_rental` w/ rates & features, `6 st_part` w/ SKUs & specs JSON, `6 post` w/ exact dates, categories & tags, `28 st_faq`, `8 st_gallery`, `4 st_career`), all **26** WP pages (`4` non-area Pilot pages + `22` Phase B pages with `page_on_front=home` and `page_for_posts=blog`), and all **261** `st_service_area` routes (`35` Local Districts + `226` Intercity Corridors from `service-areas-261.json`) with unique SEO titles, localized copy, keywords, prev/next navigation, `st_region` taxonomy, and Elementor Free `service-area-single-template.json`.
4. **Automated Static Verification & Honest Limitations:**
   - Verified all **15/15** `elementor-templates/*.json` templates (`page-*.json` 9, `pilot-*.json` 5, `service-area-single-template.json` 1) contain zero Elementor Pro dependencies, only registered widget slugs, and `100%` valid bundled image filenames.
   - Verified `migration/phase-b-pages.json` (`22` pages), `migration/cpt-seeds.json` (`70` CPT/blog records, `42` media assets, `4` menus), and `migration/service-areas-261.json` (`261` unique slugs, `261` unique SEO titles, `261` unique descriptions).
   - Passed static PHP header, `ABSPATH` security guard, and balanced-delimiter validation across all **62** `.php` files in `theme/`, `plugins/`, and `migration/`.
   - **Honest Container Limitation:** Because `php` CLI and a live WordPress/MySQL runtime are absent in this container, `wp eval-file` DB execution and live browser QA could not be run locally; **0 routes are marked `Done`** in `docs/PAGE-MIGRATION-CHECKLIST.md` until executed and visually verified on a live WordPress/cPanel server.

---

## Stage 5 — Business Manager Plugin (`santa-towing-business-manager`) Verification & Notes

1. **Architecture & Complete 14-Module Parity (Zero Elementor Dependency):**
   - Implemented standalone WordPress admin suite replicating the React `/admin/*` console look & workflows: dark `#131514` sidebar with `#015557` emerald active pill, sticky top bar, KPI cards, searchable/filterable data tables, modals, and live Desktop/Mobile preview pane (`#stbm-root` scoped CSS).
   - Replaced all browser `localStorage` / `IndexedDB` stores with MySQL (`dbDelta()` tables `wp_stbm_customers`, `wp_stbm_invoices`, `wp_stbm_receipts`, `wp_stbm_activity_logs`, `wp_stbm_studio_images`), WP options (`santa_towing_site_content`, `stbm_doc_settings`), and CPT inventory CRUD (`st_car`, `st_rental`, `st_part`, `st_gallery`, `st_faq`, `st_career`, `st_service_area` registered if core plugin is inactive).
   - Replaced client-side login with native WordPress authentication + custom roles (`st_manager`, `st_staff`) and `7` granular capabilities (`st_staff` explicitly denied `stbm_manage_brand` for Colors & Logo).
   - Added pure-PHP A4 PDF 1.4 generator (`STBM_PDF`), HTML5 `<canvas>` Image Studio w/ WP Media Library persistence, protected CSV/JSON/PDF export endpoints, and JSON Merge/Replace restore.
2. **Automated Verification Executed:**
   - Passed `node --check` on all 3 JS engines (`stbm-app.js`, `stbm-billing.js`, `stbm-image-studio.js`).
   - Passed Python static PHP audit across all `9` PHP files (`<?php` headers, `ABSPATH`/`WP_UNINSTALL_PLUGIN` guards, balanced delimiters, zero Elementor/React/iframe/localStorage runtime references, `25/25` AJAX endpoints calling `STBM_Roles::verify_ajax()`, `3/3` export endpoints calling `STBM_Roles::verify_admin_post()`, and `$wpdb->prepare()` query safety).
   - Built and verified `packages/santa-towing-business-manager.zip` (`6,062,874` bytes, `39` archived entries under `santa-towing-business-manager/`, `zipfile.ZipFile.testzip() = None`).
3. **Honest Container Limitation:**
   - Because `php` CLI and a live WordPress/MySQL runtime are absent in this build container, live WP activation, `dbDelta()` execution, WP Media uploads, and visual admin QA could not be executed locally and must be verified on a live PHP 8.1+ WordPress/cPanel server.

---

## Files Created or Updated Inside `Santa WordPress Website Conversions/`

### Stage 1 (`docs/`)
1. `Santa WordPress Website Conversions/docs/WORDPRESS-MIGRATION-BLUEPRINT.md` — Created
2. `Santa WordPress Website Conversions/docs/COMPLETE-PAGE-INVENTORY.md` — Created
3. `Santa WordPress Website Conversions/docs/COMPONENT-AND-FUNCTIONALITY-INVENTORY.md` — Created
4. `Santa WordPress Website Conversions/docs/ADMIN-DASHBOARD-INVENTORY.md` — Created
5. `Santa WordPress Website Conversions/docs/URL-AND-SEO-MIGRATION-MAP.md` — Created
6. `Santa WordPress Website Conversions/docs/MIGRATION-DEPENDENCY-PLAN.md` — Created
7. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Created & Updated in Stages 2 & 3

### Stage 2 (`theme/santa-towing/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/theme/santa-towing/style.css` — Theme metadata, Elementor helpers, accordions, reveal transitions, `.st-animate-bob`, admin-bar offset, `.prose`, `.pagination`
2. `Santa WordPress Website Conversions/theme/santa-towing/functions.php` — Setup, CSS/JS enqueue, `:root` tokens, Elementor font filter, `st_get_content()`, `st_get_logo_url()`, `st_route_url()`, `st_is_elementor_page()`, `st_icon()` (56 inline Lucide SVGs), newsletter AJAX handler
3. `Santa WordPress Website Conversions/theme/santa-towing/header.php` — HTML shell, skip-link, TopBar + NavBar wrapper
4. `Santa WordPress Website Conversions/theme/santa-towing/footer.php` — Footer + emergency floating actions wrapper
5. `Santa WordPress Website Conversions/theme/santa-towing/front-page.php` — Elementor detection + 11-section native React parity Home fallback
6. `Santa WordPress Website Conversions/theme/santa-towing/index.php` — Fallback loop template
7. `Santa WordPress Website Conversions/theme/santa-towing/page.php` — Standard WP Page template
8. `Santa WordPress Website Conversions/theme/santa-towing/single.php` — Blog post single with reading progress bar, copy-link, share & prev/next
9. `Santa WordPress Website Conversions/theme/santa-towing/single-st_car.php` — Vehicle sale detail with interactive gallery, tabs & buyer FAQs
10. `Santa WordPress Website Conversions/theme/santa-towing/single-st_rental.php` — Vehicle rental detail template
11. `Santa WordPress Website Conversions/theme/santa-towing/single-st_part.php` — Auto part detail template
12. `Santa WordPress Website Conversions/theme/santa-towing/single-st_service_area.php` — Dynamic service area single wrapper
13. `Santa WordPress Website Conversions/theme/santa-towing/archive.php` — Blog archive & category bar
14. `Santa WordPress Website Conversions/theme/santa-towing/archive-st_car.php` — Cars for Sale archive with live category filter tabs
15. `Santa WordPress Website Conversions/theme/santa-towing/archive-st_rental.php` — Car Rentals archive template
16. `Santa WordPress Website Conversions/theme/santa-towing/archive-st_part.php` — Auto Parts archive template
17. `Santa WordPress Website Conversions/theme/santa-towing/search.php` — Global multi-post-type search results
18. `Santa WordPress Website Conversions/theme/santa-towing/404.php` — Custom 404 recovery page
19. `Santa WordPress Website Conversions/theme/santa-towing/readme.txt` — Theme readme & changelog
20. `Santa WordPress Website Conversions/theme/santa-towing/screenshot.png` — 1200×900 WordPress Theme directory preview thumbnail
21. `Santa WordPress Website Conversions/theme/santa-towing/page-templates/template-elementor-canvas.php` — Elementor Full-Bleed Canvas (with theme Header/Footer)
22. `Santa WordPress Website Conversions/theme/santa-towing/page-templates/template-elementor-fullwidth.php` — Elementor Full-Width Inner Page (with top clearance)
23. `Santa WordPress Website Conversions/theme/santa-towing/page-templates/template-elementor-blank.php` — Elementor Blank Canvas (no Header/Footer)
24. `Santa WordPress Website Conversions/theme/santa-towing/page-templates/template-policy.php` — Legal & Policy page layout
25. `Santa WordPress Website Conversions/theme/santa-towing/page-templates/template-sitemap.php` — Live-filterable HTML Website Sitemap
26. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/top-bar.php` — 24/7 hotline & social TopBar
27. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/navbar.php` — Desktop (`xl:flex`), Tablet (`lg:flex xl:hidden`), & Mobile Accordion Drawer (`lg:hidden`)
28. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/footer-content.php` — 5-column Footer, 12 legal links & AJAX newsletter form
29. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/floating-buttons.php` — Fixed Call/Emergency Tow + WhatsApp CTAs
30. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/breadcrumbs.php` — Accessible breadcrumb navigation
31. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/scroll-to-footer-arrow.php` — Animated Hero scroll-to-footer button
32. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/service-area-template.php` — Shared Local District & Intercity Corridor partial
33. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/card-car.php` — Reusable Car Sales card
34. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/card-rental.php` — Reusable Car Rental card
35. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/card-part.php` — Reusable Auto Part card
36. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/card-blog.php` — Reusable Blog Post card
37. `Santa WordPress Website Conversions/theme/santa-towing/assets/css/santa-styles.css` — Pre-compiled minified Tailwind v4 stylesheet (`137,658` bytes)
38. `Santa WordPress Website Conversions/theme/santa-towing/assets/js/santa-main.js` — Vanilla JS interactive controller
39. `Santa WordPress Website Conversions/theme/santa-towing/assets/js/santa-motion.js` — IntersectionObserver scroll reveal engine
40. `Santa WordPress Website Conversions/theme/santa-towing/assets/images/*` — 42 verified image assets copied cleanly
41. `Santa WordPress Website Conversions/packages/santa-towing.zip` — Verified installable WordPress theme ZIP (`8.55 MB`, 81 entries)
42. `Santa WordPress Website Conversions/docs/THEME-INSTALL-GUIDE.md` — Created

### Stage 3 (`plugins/santa-towing-website-core/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/santa-towing-website-core.php` — Plugin bootstrap, constants, asset enqueuing, `stwcConfig` localization, activation/deactivation hooks
2. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/uninstall.php` — Clean uninstall transient/rewrite cleanup
3. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/readme.txt` — WordPress plugin directory readme
4. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/helpers.php` — Asset URL helper, `stwc_get_content()`, Lucide SVGs, WhatsApp deep-link builder, CPT query helpers w/ defaults
5. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/data-defaults.php` — Complete React source default mirrors (Site Content, 16 Ghana Regions / 232 Areas, 32 Towing Adverts, Cars, Rentals, Parts, FAQs, Gallery, Careers)
6. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-cpts.php` — 7 CPTs, 9 Taxonomies, Post Meta registration, Admin columns & Meta Boxes
7. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-settings.php` — 5-tab Site Settings, Elementor Kit color sync, Inquiries & Leads viewer + CSV export, Diagnostics & Permalink Flush
8. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-rewrites.php` — Legacy ID rewrites, 14 301 redirects, Virtual Area & Sub-Service templates, XML Sitemap, `robots.txt`, OpenGraph + `AutoRepair` Schema JSON-LD
9. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-forms.php` — `wp_santa_inquiries` DB table (`dbDelta`), nonce/honeypot/upload validation, `wp_mail()` alerts, exact WhatsApp dispatch links
10. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-elementor.php` — Elementor Free category + 13 custom widgets + companion shortcodes
11. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-base.php` — Abstract widget base with reusable Style & Responsive controls
12. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-hero-slider.php` — Hero Slider widget
13. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-services-grid.php` — Services Grid widget
14. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-haulage-showcase.php` — Haulage Showcase & Tema Port corridors widget
15. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-cars-catalog.php` — Cars for Sale catalog & filter widget
16. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-rental-fleet.php` — Rental Fleet & pricing widget
17. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-parts-showcase.php` — Auto Parts catalog & filter widget
18. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-why-choose-us.php` — Why Choose Us trust pillars widget
19. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-blog-grid.php` — Blog & Guides grid widget
20. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-faq-accordion.php` — FAQ Accordion widget
21. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-gallery-showcase.php` — Gallery Masonry Showcase & Lightbox widget
22. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-service-area-route.php` — Service Area Route & Searchable 16-Region Directory widget
23. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-towing-request-form.php` — Emergency Towing Request Form widget (16 Regions → 232 Areas)
24. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-enquiry-forms.php` — Multi-mode Haulage, Contact, Fleet, Inventory & Career CV Upload Form widget
25. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/templates/virtual-service-area.php` — Virtual 200 OK route template for 32 districts / 232 Ghana localities
26. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/templates/virtual-area-service.php` — Virtual 200 OK sub-service template (`/service-areas/{area}/{service}`)
27. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/css/santa-core.css` — Frontend widget, form & lightbox CSS
28. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/css/santa-core-admin.css` — WP Admin meta box & settings UI CSS
29. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/js/santa-core.js` — Vanilla JS slider, filter, accordion, lightbox, Ghana cascade & AJAX form controller
30. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/js/santa-core-admin.js` — Admin Media Library & WP Color Picker controller
31. `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip` — Verified installable WordPress plugin ZIP (`100.50 KB`, 30 archived entries)
32. `Santa WordPress Website Conversions/docs/PLUGIN-CORE-GUIDE.md` — Created

### Stage 4 Phase A — Pilot (`migration/`, `elementor-templates/`, `plugins/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/migration/pilot-pages.json` — Created (5 Pilot route manifests, SEO metadata, verbatim copy, taxonomy/meta & 14-redirect map)
2. `Santa WordPress Website Conversions/migration/class-st-media-sync.php` — Created (Idempotent WP Media Library attachment importer via `_st_source_filename` + recursive Elementor token hydrator)
3. `Santa WordPress Website Conversions/migration/santa-pilot-importer.php` — Created (WP-CLI & nonced WP Admin Tools UI importer for the 5 Pilot routes)
4. `Santa WordPress Website Conversions/elementor-templates/pilot-home.json` — Created (Elementor Free v0.4 template for Home `/`)
5. `Santa WordPress Website Conversions/elementor-templates/pilot-privacy-policy.json` — Created (Elementor Free v0.4 template for Privacy Policy `/privacy-policy/`)
6. `Santa WordPress Website Conversions/elementor-templates/pilot-services.json` — Created (Elementor Free v0.4 template for Our Services `/services/`)
7. `Santa WordPress Website Conversions/elementor-templates/pilot-fleet.json` — Created (Elementor Free v0.4 template for Our Fleet `/fleet/`)
8. `Santa WordPress Website Conversions/elementor-templates/pilot-service-area-accra.json` — Created (Elementor Free v0.4 template for Accra `/service-areas/accra/`)
9. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-rewrites.php` — Updated (14 React alias 301s, `_st_keywords_json` & verbatim `_st_seo_description` support on singular pages)
10. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-service-area-route.php` — Updated (`single_route` breadcrumbs, H1, Call/WhatsApp & Prev/Next area navigation)
11. `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip` — Rebuilt & verified (`103,631` bytes, 30 files)
12. `Santa WordPress Website Conversions/docs/PAGE-MIGRATION-CHECKLIST.md` — Created & Updated in Phase B
13. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Updated

### Stage 4 Phase B — Full Site & 261 Service Area Generator (`migration/`, `elementor-templates/`, `theme/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/migration/phase-b-pages.json` — Created (`22` Phase B page definitions: 11 marketing/directory/utility pages + 11 complete verbatim legal policy pages)
2. `Santa WordPress Website Conversions/migration/santa-full-importer.php` — Created (Idempotent WP-CLI & WP Admin Tools importer for media, settings, 4 menus, 70 CPT/blog seeds, 26 pages, and all 261 `st_service_area` routes w/ cPanel batching)
3. `Santa WordPress Website Conversions/migration/cpt-seeds.json` — Verified (`12 st_car`, `6 st_rental`, `6 st_part`, `6 post`, `28 st_faq`, `8 st_gallery`, `4 st_career`, `42` media assets, `4` menus)
4. `Santa WordPress Website Conversions/migration/service-areas-261.json` — Verified (`35` Local Districts + `226` Intercity Corridors = `261` unique routes)
5. `Santa WordPress Website Conversions/elementor-templates/page-*.json` & `service-area-single-template.json` — Verified (`9` inner page Elementor Free templates + `1` single service area Elementor Free template)
6. `Santa WordPress Website Conversions/theme/santa-towing/home.php` — Created (`is_home()` / `page_for_posts` `/blog/` template delegating to `archive.php`)
7. `Santa WordPress Website Conversions/packages/santa-towing.zip` — Rebuilt & verified (`8,967,405` bytes, `82` files including `home.php`)
8. `Santa WordPress Website Conversions/docs/PAGE-MIGRATION-CHECKLIST.md` — Updated all Phase B + 260 service-area statuses to `Ready for Live WP Import — Unverified Live` (`0` marked `Done` pre-live check)
9. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Updated

### Stage 5 (`plugins/santa-towing-business-manager/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/santa-towing-business-manager.php` — Created (Plugin bootstrap, asset resolver, activation/deactivation hooks)
2. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/readme.txt` — Created (WP plugin directory metadata & changelog)
3. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/uninstall.php` — Created (Clean plugin uninstall preserving shared site content & CPT posts)
4. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-roles.php` — Created (`st_manager` & `st_staff` roles, 7 capabilities, AJAX & admin-post security guards)
5. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-defaults.php` — Created (React default mirrors, dot-path options helpers, standalone CPT inventory CRUD)
6. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-db.php` — Created (5 `dbDelta()` tables, starter seeds, invoice/receipt totals & sync, Studio WP Media upload, backup/restore)
7. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-pdf.php` — Created (Zero-dependency pure-PHP A4 PDF 1.4 generator for Invoices & Receipts)
8. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-exports.php` — Created (Protected `admin-post.php` CSV, JSON backup & PDF download handlers)
9. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-ajax.php` — Created (25 nonce- and capability-protected `wp_ajax_stbm_*` handlers)
10. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-admin-ui.php` — Created (Top-level menu + 14 submenus, localized `STBM_BOOT` payload, scoped assets & shell mount)
11. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/css/stbm-admin.css` — Created (`#stbm-root` scoped dark sidebar, top bar, cards, tables, modals, preview & `@media print` A4 CSS)
12. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/js/stbm-app.js` — Created (Shell navigation, Dashboard, Inventory 6 tabs, Pages Text 14 tabs, Colors, Logo, Links, Images, Backup, Profile)
13. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/js/stbm-billing.js` — Created (Customers CRM, Pro Forma Invoices, Official Receipts, Doc Settings, Print & PDF Preview modal)
14. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/js/stbm-image-studio.js` — Created (HTML5 Canvas Crop, Rotate, Flip, Watermarks, Compress & WP Media Library Save/Overwrite)
15. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/images/*` — Copied (`25` standalone fallback assets: avatars, vehicles, parts, logo, hero images)
16. `Santa WordPress Website Conversions/packages/santa-towing-business-manager.zip` — Built & verified (`6,070,420` bytes, `40` archived entries, `testzip() = None`)
17. `Santa WordPress Website Conversions/docs/PLUGIN-BUSINESS-MANAGER-GUIDE.md` — Created (Technical & administrator guide)
18. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Updated

### Stage 5B — Data, Media & Legacy Browser Migration (`migration/`, `plugins/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/migration/media-manifest.json` — Created (`42` verified images with SHA-256, MIME, dimensions, preserved filenames, titles, captions & alt text)
2. `Santa WordPress Website Conversions/migration/business-manager-defaults.json` — Created (`30` `SITE_IMAGES` slots, `32` `towingAdverts`, `16` regions/`232` areas, `mockData` highlights, Doc Settings, `3` Customers, `3` Invoices, `2` Receipts, `3` Logs, `4` Studio Images)
3. `Santa WordPress Website Conversions/migration/class-st-media-sync.php` — Updated (Exact filename preservation, `_wp_attachment_image_alt`, `_st_source_sha256`, deduplication & `sync_all_verified_images()`)
4. `Santa WordPress Website Conversions/migration/santa-full-importer.php` — Updated (Uses `sync_all_verified_images()`, seeds all `30` image alt keys, aligns `_st_*` CPT meta, imports `section=defaults` & returns migration audit summary)
5. `Santa WordPress Website Conversions/migration/legacy-browser-export-snippet.js` — Created (Safe read-only JS snippet exporting all `7` `localStorage` keys & `santa_image_studio_db` IndexedDB into `santa-legacy-browser-export-v1`, excluding `sessionStorage` auth tokens)
6. `Santa WordPress Website Conversions/migration/legacy-browser-export-tool.html` — Created (Standalone HTML Copy & Bookmarklet exporter helper)
7. `Santa WordPress Website Conversions/migration/class-stbm-legacy-importer.php` — Created (Standalone `Tools -> Santa Legacy Browser Importer` & WP-CLI runner)
8. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-defaults.php` — Updated (`_st_rental_features` & `_st_alt` meta fallbacks)
9. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-legacy-importer.php` — Created (Core Business Manager JSON & Legacy Browser bundle importer with Merge/Replace, Dry-Run, relationship/missing-image/duplicate audit)
10. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-ajax.php` & `santa-towing-business-manager.php` & `assets/js/stbm-app.js` — Updated (`stbm_import_legacy_bundle` nonced AJAX route & Backup/Export Upload UI)
11. `Santa WordPress Website Conversions/packages/santa-towing-business-manager.zip` — Rebuilt & verified (`6,070,420` bytes, `40` files, `testzip() = None`)
12. `Santa WordPress Website Conversions/migration/migration-audit-report.json` — Generated (Static audit telemetry)
13. `Santa WordPress Website Conversions/docs/MIGRATION-REPORT.md` — Created (Successful, Failed, Pending, Duplicates, Missing Images, Broken Relationships & Runbook)
14. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Updated

---

## Stage 6 — Functional Parity Audit, Hardening & Automated Verification

1. **15 Functional Subsystems Verified & Finished:**
   - Verified and completed working behavior across **Forms & validation**, **Search (`Search.tsx` 1:1 parity w/ `?s=`/`?q=`, live CPTs + 32 districts + 232 Ghana localities + relevance scoring + `<mark>` highlight + quick cards)**, **Filters (empty-state notices)**, **Menus (`global.links.*` + dropdown accessibility/`Escape`)**, **Sliders (ArrowLeft/Right + pause/dots)**, **Gallery & Lightbox (Prev/Next/counter/keyboard/scroll-lock)**, **Modals (`bindModalDismiss` w/ `Escape` & backdrop dismiss + cleanup)**, **FAQ accordions**, **Blog (archive + copy-URL feedback)**, **WhatsApp links (`024...` -> `wa.me/23324...` normalization)**, **Sitemap (CPT default fallbacks + live route counter/empty state)**, **Invoice & Receipt generation (isolated `#stbm-inv-preview-mount` preserving focus on input, full React `CustomizableInvoiceModal` controls, unified React/PHP aliases, `Amount Paid`/`Balance Due`)**, **Pure-PHP PDF 1.4 output (proportional Helvetica glyph widths, `phoneSecondary`, custom sections & alias normalization)**, **Image Studio (8-handle + move crop engine with locked aspect-ratio math, live canvas watermark preview, color/scale/Undo/Redo/Download/Reset)**, and **Backup & Export (nonced CSV/JSON/PDF export + Merge/Replace restore)**.
2. **Automated Verification Executed:**
   - `node --check` passed on all **7/7** `.js` files across `theme/` and `plugins/`.
   - Node.js VM + mock-DOM test suite passed assertions on `stbm-image-studio.js` (8-handle hit testing & 16:9 crop ratio clamping), `stbm-billing.js` (`calcInvoiceLocal`, React aliases, preview output), `santa-main.js` (dropdowns, `Escape`, hero slider keys, filter empty state), and `santa-core.js` (email/phone/CV validation, fetch gating, FAQ accordion toggle).
   - Python static PHP audit passed across all **69** `.php` files (`0` delimiter imbalances, `0` missing plugin `ABSPATH` guards, `0` debug leftovers).
   - Rebuilt & integrity-checked all **3** WordPress ZIPs (`packages/santa-towing.zip`: 82 files, `8,972,428` bytes; `packages/santa-towing-website-core.zip`: 30 files, `105,303` bytes; `packages/santa-towing-business-manager.zip`: 40 files, `6,076,989` bytes; `ZipFile.testzip() = None`).
3. **Honest Container Limitation:**
   - `php` CLI and live WordPress + MySQL + browser runtimes are absent in this container; `php -l` linting, `admin-ajax.php` DB writes, and visual browser QA on a live WP/cPanel server remain to be run on deployment.

### Stage 6 (`theme/`, `plugins/`, `qa/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/theme/santa-towing/functions.php` — Updated (`st_build_whatsapp_url()`, `santaTowingConfig`)
2. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/navbar.php` — Updated (`global.links.menuLinks`, `aria-haspopup`)
3. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/footer-content.php` — Updated (`global.links.quickLinks/serviceLinks`, `st_build_whatsapp_url()`)
4. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/card-car.php` — Updated (`st_build_whatsapp_url()`)
5. `Santa WordPress Website Conversions/theme/santa-towing/template-parts/card-rental.php` — Updated (`st_build_whatsapp_url()`)
6. `Santa WordPress Website Conversions/theme/santa-towing/search.php` — Rewritten (full `Search.tsx` 1:1 parity)
7. `Santa WordPress Website Conversions/theme/santa-towing/page-templates/template-sitemap.php` — Updated (CPT default fallbacks, empty state, live count)
8. `Santa WordPress Website Conversions/theme/santa-towing/assets/js/santa-main.js` — Updated (dropdown/slider/filter/copy-URL/sitemap polish)
9. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/helpers.php` — Updated (`stwc_build_whatsapp_url()`)
10. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-rewrites.php` — Updated (`/search` virtual 200 OK route)
11. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/class-elementor.php` — Updated (`.stwc-close-apply` button)
12. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/css/santa-core.css` — Updated (lightbox nav/counter & empty filter styles)
13. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/js/santa-core.js` — Updated (client validation, safe JSON parse, prefill preservation, lightbox prev/next/keys, careers Escape/close)
14. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/js/stbm-image-studio.js` — Updated (8-handle crop engine, live watermark preview, Undo/Redo, Reset, Download, Escape/backdrop dismiss)
15. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/js/stbm-app.js` — Updated (`bindModalDismiss` modal accessibility & listener cleanup)
16. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/includes/class-stbm-pdf.php` — Updated (proportional Type1 Helvetica metrics, `phoneSecondary`, React/PHP `hiddenFields` & `sectionOrder` normalization, custom sections, `Amount Paid`/`Balance Due`)
17. `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/assets/js/stbm-billing.js` — Updated (isolated `#stbm-inv-preview-mount`, full React `CustomizableInvoiceModal` controls, unified React/PHP aliases, `bindModalDismiss`)
18. `Santa WordPress Website Conversions/qa/FUNCTIONALITY-TEST-REPORT.md` — Created (full 15-area test & audit report)
19. `Santa WordPress Website Conversions/packages/santa-towing.zip` — Rebuilt & verified (`8,972,428` bytes, 82 files)
20. `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip` — Rebuilt & verified (`105,303` bytes, 30 files)
21. `Santa WordPress Website Conversions/packages/santa-towing-business-manager.zip` — Rebuilt & verified (`6,076,989` bytes, 40 files)
22. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Updated

---

## Stage 6B — Elementor Free Public Page Editable Architecture

1. **Zero Raw HTML, Zero Shortcode Widgets, Zero Elementor Pro Dependencies:**
   - Audited and refactored all **15/15** Elementor templates in `elementor-templates/` (`pilot-home.json`, `pilot-services.json`, `pilot-fleet.json`, `pilot-privacy-policy.json`, `pilot-service-area-accra.json`, `page-towing.json`, `page-haulage.json`, `page-repairs.json`, `page-body-works.json`, `page-contact.json`, `page-careers.json`, `page-faqs.json`, `page-gallery.json`, `page-service-areas.json`, `service-area-single-template.json`).
   - Replaced every raw `html` widget, `[stwc_careers]` `shortcode` widget, and inline `<script>` block with native Elementor Free widgets (`heading`, `text-editor`, `image`, `button`, `divider`, `icon-box`, inner sections/columns) and **17** custom Elementor Free `stwc-*` widgets (adding `stwc-page-hero`, `stwc-feature-cards`, `stwc-spotlight-split`, and `stwc-careers-board`, plus upgrading `stwc-faq-accordion` with `faq_source=custom` and `compact_mode`).
   - Kept the admin plugin (`plugins/santa-towing-business-manager/`) **100% outside Elementor**.
2. **Responsiveness, Overflow & Image Resilience + Interactive JS Delegation:**
   - Added `.elementor-page` horizontal overflow guards, flex/grid `min-width: 0; overflow-wrap: break-word;`, aspect-ratio/object-fit image locks (`.st-el-img-4x3`, `aspect-[4/3]`, `aspect-[16/10]`, `aspect-video`, `aspect-square`), button row wrap helpers (`.st-el-btn-row`), and `.st-hours-card-col` / `.st-hours-list` helpers in `santa-core.css` and `theme/santa-towing/style.css`.
   - Moved FAQ live search (`[data-stwc-faq-search]`), Body Works Before/After tabs (`[data-stwc-bw-showcase]`), and Gallery Before/After slider (`[data-stwc-before-after]`) into idempotent handlers in `santa-core.js` with `elementor/frontend/init` live preview re-binding.
3. **Verification Results & Honest Container Limitations:**
   - **Elementor JSON Schema Audit:** Passed across `15/15` `.json` files (`version: "0.4"`, `106` total widgets = `39` Native Free + `67` custom `stwc-*`, `0` `html`, `0` `shortcode`, `0` `<script`, `0` Pro widgets, `154/154` valid bundled media tokens).
   - **JS & Mock-DOM Verification:** `node --check` passed on `santa-core.js`; Node mock-DOM test suite verified `initFaqLiveSearch`, `initBodyWorksShowcases`, and `initBeforeAfterSliders`.
   - **PHP & Package Verification:** Static PHP delimiter & guard audit passed across `77/77` `.php` files; rebuilt and `testzip()`-verified `packages/santa-towing.zip` (`82` files, `8,974,007` bytes) and `packages/santa-towing-website-core.zip` (`34` files, `128,102` bytes). Zero Elementor code/hooks in `plugins/santa-towing-business-manager/`.
   - **Honest Container Limitation:** Because `php` CLI and live WordPress + MySQL + Elementor Free browser runtimes are absent in this container, visual Elementor Editor rendering and live responsive edits must still be verified on a live WordPress/cPanel server.

### Stage 6B (`theme/`, `plugins/`, `elementor-templates/`, `packages/`, `docs/`)
1. `Santa WordPress Website Conversions/theme/santa-towing/functions.php` — Extended `st_icon()` SVG icons
2. `Santa WordPress Website Conversions/theme/santa-towing/style.css` — Added `.elementor-page` overflow, text wrap & `.st-el-relative-col` rules
3. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/helpers.php` — Extended `stwc_icon()` SVG icons
4. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-page-hero.php` — Created (`stwc-page-hero`)
5. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-feature-cards.php` — Created (`stwc-feature-cards`)
6. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-spotlight-split.php` — Created (`stwc-spotlight-split`)
7. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-careers-board.php` — Created (`stwc-careers-board`)
8. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-faq-accordion.php` — Updated (`faq_source=custom`, `custom_faqs` repeater, `show_header`, `compact_mode`)
9. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/includes/widgets/class-widget-base.php`, `includes/class-elementor.php`, & `santa-towing-website-core.php` — Updated to load and register all `17` `stwc-*` Elementor Free widgets
10. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/css/santa-core.css` — Added responsive grid classes, overflow/aspect-ratio locks, native Elementor helpers & slider divider CSS
11. `Santa WordPress Website Conversions/plugins/santa-towing-website-core/assets/js/santa-core.js` — Added `initFaqLiveSearch()`, `initBodyWorksShowcases()`, `initBeforeAfterSliders()`, and `elementor/frontend/init` hooks
12. `Santa WordPress Website Conversions/elementor-templates/*.json` (13 templates upgraded cleanly: `pilot-home.json`, `pilot-services.json`, `pilot-fleet.json`, `pilot-privacy-policy.json`, `page-towing.json`, `page-haulage.json`, `page-repairs.json`, `page-body-works.json`, `page-contact.json`, `page-careers.json`, `page-faqs.json`, `page-gallery.json`, `page-service-areas.json`; plus verified clean `pilot-service-area-accra.json`, `service-area-single-template.json`)
13. `Santa WordPress Website Conversions/packages/santa-towing.zip` — Rebuilt & verified (`8,974,007` bytes, `82` files)
14. `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip` — Rebuilt & verified (`128,102` bytes, `34` files)
15. `Santa WordPress Website Conversions/docs/ELEMENTOR-EDITING-GUIDE.md` — Created
16. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Updated

---

## Stage 7 — Final cPanel Packaging, Beginner Runbook & Complete Inventory

1. **Installable Importer Plugin & Elementor Templates Bundle:**
   - Fixed the "Sync Factory Defaults JSON" button handler and added `WP_PLUGIN_DIR` fallback resolution inside `migration/class-stbm-legacy-importer.php`.
   - Created standalone WordPress plugin `plugins/santa-towing-importer/` (`santa-towing-importer.php` + `readme.txt` + synced `migration/` + `elementor-templates/`) providing `Tools -> Santa Full Importer`, `Tools -> Santa Pilot Importer`, `Tools -> Santa Legacy Browser Importer`, plugin action links, and prerequisite/welcome admin notices.
   - Packaged & `testzip()`-verified all **5** installable/importable ZIP archives inside `packages/`:
     - `santa-towing.zip` (`82` files, `8,974,007` bytes)
     - `santa-towing-website-core.zip` (`34` files, `128,102` bytes)
     - `santa-towing-business-manager.zip` (`40` files, `6,076,989` bytes)
     - `santa-towing-importer.zip` (`29` files, `167,726` bytes)
     - `santa-towing-elementor-templates.zip` (`15` files, `38,017` bytes)
2. **Documentation & Runbook Suite (`docs/` & root `README.md`):**
   - Wrote beginner-friendly step-by-step `docs/CPANEL-DEPLOYMENT-GUIDE.md` covering all 10 required cPanel stages (staging subdomain, WordPress install, theme & plugins upload order, Elementor Free configuration, full migration + 42 media assets + legacy browser export, Homepage/Blog/Menus/Permalinks, AutoSSL HTTPS, pre-launch testing, staging-to-live cutover, and two-layer backup/restore).
   - Wrote `docs/FINAL-QA-REPORT.md` consolidating route accounting (`328` `<Route>` entries across `37` React page files), zero-Pro/zero-paid confirmation, zero-Node confirmation, static verification telemetry, and an honest list of live-pending checks.
   - Updated minor outdated metrics/labels across `docs/PLUGIN-CORE-GUIDE.md` (`13` -> `17` widgets), `docs/PLUGIN-BUSINESS-MANAGER-GUIDE.md` (`39` -> `40` files / `26` AJAX endpoints), and `docs/THEME-INSTALL-GUIDE.md`.
   - Created top-level `Santa WordPress Website Conversions/README.md` listing every folder and file inside the conversion workspace alongside architecture confirmations and honest unfinished items.

### Stage 7 (`migration/`, `plugins/`, `packages/`, `docs/`, root `README.md`)
1. `Santa WordPress Website Conversions/migration/class-stbm-legacy-importer.php` — Fixed defaults button submit bug & added `WP_PLUGIN_DIR` fallback
2. `Santa WordPress Website Conversions/plugins/santa-towing-importer/santa-towing-importer.php` — Created (WordPress Importer plugin bootstrap)
3. `Santa WordPress Website Conversions/plugins/santa-towing-importer/readme.txt` — Created
4. `Santa WordPress Website Conversions/plugins/santa-towing-importer/migration/*` — Synced migration PHP engines, JSON datasets, and export tools
5. `Santa WordPress Website Conversions/plugins/santa-towing-importer/elementor-templates/*.json` — Synced all 15 Elementor Free templates
6. `Santa WordPress Website Conversions/packages/santa-towing-importer.zip` — Built & `testzip()` verified (`29` files, `167,726` bytes)
7. `Santa WordPress Website Conversions/packages/santa-towing-elementor-templates.zip` — Built & `testzip()` verified (`15` files, `38,017` bytes)
8. `Santa WordPress Website Conversions/docs/PLUGIN-CORE-GUIDE.md` — Updated (`17` widgets list)
9. `Santa WordPress Website Conversions/docs/PLUGIN-BUSINESS-MANAGER-GUIDE.md` — Updated (`40` files, `26` AJAX endpoints)
10. `Santa WordPress Website Conversions/docs/THEME-INSTALL-GUIDE.md` — Updated Section 6 companion plugin descriptions
11. `Santa WordPress Website Conversions/docs/CPANEL-DEPLOYMENT-GUIDE.md` — Created (10-step beginner cPanel runbook)
12. `Santa WordPress Website Conversions/docs/FINAL-QA-REPORT.md` — Created (Consolidated QA & honest live-pending sign-off)
13. `Santa WordPress Website Conversions/README.md` — Created (Top-level complete file/folder index & confirmations)
14. `Santa WordPress Website Conversions/docs/CONVERSION-PROGRESS.md` — Updated

*(Zero files in `src/`, `public/`, `scripts/`, `index.html`, or `package.json` were modified.)*

