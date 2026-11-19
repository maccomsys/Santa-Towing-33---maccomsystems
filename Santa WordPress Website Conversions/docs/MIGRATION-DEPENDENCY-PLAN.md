# Migration Dependency Plan, Elementor Free Matrix & Asset Strategy

Execution roadmap, technical risk analysis, Elementor Free strict capability boundaries, and asset migration pipeline for converting Santa Towing into a zero-Node, cPanel-ready WordPress site.

---

## 1. Strict Elementor FREE Capability Boundaries

**Constraint:** Only **Elementor FREE** (latest stable v3.x from wordpress.org) is permitted. **No Elementor Pro**, no paid addons, no embedded React, no iframes.

| Feature Needed by Santa Towing | Available in Elementor FREE? | Exact Native WordPress Solution |
|---|---|---|
| Visual Drag & Drop Page Canvas (Sections, Flexbox Containers, Headings, Text, Images, Buttons, Icons, Spacers, Dividers) | **Yes** | Use Elementor Free templates (`elementor-templates/*.json`) for marketing pages (`/`, `/services`, `/towing`, `/haulage`, `/repairs`, `/body-works`, `/fleet`, `/contact`, `/careers`, `/faqs`, `/gallery`, 12 legal policies) |
| Global Brand Colors & Base Typography | **Yes** | Configure Elementor Kit Settings synced dynamically from `santa_towing_site_content['theme']` |
| Global Header (`TopBar` + sticky `NavBar` + Mobile Drawer) | **No** *(Pro Theme Builder)* | Built natively in custom theme `santa-towing/header.php` + `santa-main.js`; dynamically controlled via WP Options (`global.nav.menuLinks`) & Admin Console |
| Global Footer + Floating Call/WhatsApp/BackToTop | **No** *(Pro Theme Builder)* | Built natively in `santa-towing/footer.php` & `template-parts/floating-buttons.php` |
| Dynamic Custom Sections (Hero Slider + KPI Stats, Services 4x2 Grid, Towing Cost Calculator, FAQ Accordion, Featured Fleet Grid) | **Limited** *(Basic Carousel/Accordion exist, but not Santa's data-driven specs/styles)* | Build custom **Elementor Free Widgets** extending `\Elementor\Widget_Base` inside `santa-towing-core` (e.g., `ST_Hero_Widget`, `ST_Towing_Calculator_Widget`, `ST_Featured_Cars_Widget`, `ST_Haulage_Quote_Widget`, `ST_Service_Area_List_Widget`). Drag-and-drop inside Elementor Free UI with live server-side rendering! |
| Archive Templates (`/sales`, `/rental`, `/parts`, `/blog`, `/search`) & Single Templates (`/sales/1`, `/rental/1`, `/parts/1`, `/blog/1`, `/service-areas/*`) | **No** *(Pro Theme Builder / Loop Grid)* | Built natively in custom theme PHP templates (`archive-st_car.php`, `single-st_car.php`, `single-st_service_area.php`, `single-post.php`, etc.) styled identically with compiled Tailwind CSS |
| Interactive Forms (Contact, Haulage Quote, Car Inquiry, Rental Booking, Part Quote, Career CV Upload) | **No** *(Pro Form Widget)* | Native PHP + AJAX Form Shortcodes / Custom Elementor Free Widgets (`ST_Contact_Form_Widget`, `ST_Haulage_Form_Widget`, `ST_Career_Apply_Widget`) posting via `admin-ajax.php` / WP REST with nonce + `wp_mail()` + DB lead storage (`wp_santa_inquiries`) |
| Modals / Drawers (Car Inquiry, Rental Booking, Job Application Drawer, Gallery Lightbox) | **No** *(Pro Popup Builder)* | Native vanilla JS accessible modals (`aria-modal="true"`) inside theme + plugin JS (`santa-main.js`) |
| 261 Static Service Areas + 16 Regional + `32 × 4` Localized Service Pages | **Impractical manually** | Automated CPT + Rewrite importer (`st_service_area`) rendered via single crisp PHP template (`single-st_service_area.php`) enforcing 100% fidelity |

---

## 2. Dependency Graph & Sequenced Execution Roadmap

```text
Stage 1: Audit & Blueprints (DONE HERE — 7 Docs)
   │
   ├──► Stage 2: Custom Theme Foundation (`santa-towing`)
   │      ├── Pre-compile Tailwind v4 CSS tokens (`santa-styles.css`) + print CSS
   │      ├── Build `header.php` (TopBar + NavBar), `footer.php`, `floating-buttons.php`, `breadcrumbs.php`
   │      └── Build `santa-motion.js` (`IntersectionObserver`) + `santa-main.js`
   │
   ├──► Stage 3: Core Plugin (`santa-towing-core`) & Seed Importer (`santa-towing-importer`)
   │      ├── Register 7 CPTs + Taxonomies + Custom Options schema
   │      ├── Import 41+ local image assets into WP Media Library & update manifest
   │      ├── Seed 12 Cars, 6 Rentals, 6 Parts, 8 Gallery, 28 FAQs, 4 Careers, 6 Blog Posts, 261 Service Areas, 32 Adverts, & `site-content.json`
   │      └── Configure URL Rewrite Rules (numeric ID parity `/sales/1`, regional fallback, `32×5` service pages, `/sitemap.xml`) + 14 `301` redirects
   │
   ├──► Stage 4: Public Templates & Elementor Free Widgets / Templates
   │      ├── Implement Archive & Single PHP templates (`sales`, `rental`, `parts`, `blog`, `service-areas`, `search`, `404`)
   │      ├── Implement 13+ Custom Elementor Free Widgets (`\Elementor\Widget_Base`)
   │      ├── Export reusable Elementor Free JSON page templates (`elementor-templates/*.json`)
   │      └── Wire real PHP AJAX forms + Towing Fee Estimator + Sales/Parts/Gallery interactive filters
   │
   ├──► Stage 5: Billing Plugin (`santa-towing-billing`) & WP Admin Suite
   │      ├── Create MySQL tables (`wp_santa_*`) + migrate/seed demo Customers, Invoices, Receipts, Settings, Logs
   │      ├── Build WP Admin screens (Dashboard, Colors, Logo, Links, Pages-Text, Images, Inventory, Invoices Builder, Receipts, Customers, Doc Settings, Backup)
   │      ├── Integrate PHP **Dompdf** library for downloadable A4 PDFs + Print CSS
   │      ├── Port HTML5 Canvas Cropper/Watermark (`ImageCanvasEditor`) integrated directly with `wp_handle_upload`
   │      └── Enforce `st_admin` vs `st_staff` WP capability gates
   │
   ├──► Stage 6: Side-by-Side Visual, SEO & Functional QA
   │      ├── Compare React live viewport screenshots (1440px & 375px) vs WordPress render
   │      ├── Verify all 329+ routes HTTP status, canonicals, JSON-LD, XML sitemap, 301s, PDF outputs, zero PHP warnings
   │      └── Document results in `qa/`
   │
   └──► Stage 7: Packaging & cPanel Deployment Kit
          ├── Generate installable ZIPs in `packages/`: `santa-towing-theme.zip`, `santa-towing-core.zip`, `santa-towing-billing.zip`, `santa-towing-importer.zip`
          └── Write turnkey cPanel Installation & Migration Runbook in `docs/`
```

---

## 3. Technical Risk Register & Mitigations

| # | Risk | Severity | Impact | Planned Mitigation |
|---|---|---|---|---|
| 1 | **Numeric URL Parity vs. Native WP Slugs** (`/sales/1`, `/rental/1`, `/parts/1`, `/blog/1`) | **High** | Native WordPress uses slugs (`/sales/2021-nissan-almera/`), whereas React links & bookmarks use integer IDs (`/sales/1`). Breaking `/sales/1` causes 404s and SEO loss. | Add `_st_legacy_id` postmeta to every seeded CPT post + custom `add_rewrite_rule` matching `^sales/([0-9]+)/?$` -> resolves post by `_st_legacy_id` (and redirects or serves canonical cleanly). Both `/sales/1/` and `/sales/{slug}/` resolve with `200 OK`! |
| 2 | **cPanel PHP Limits During Bulk Seed of 325+ Posts & Media** | **Medium** | Importing 261 service areas + 41 images + 64 CPT items in one HTTP request can hit cPanel `max_execution_time = 30s` or memory limits. | Build batch/chunked AJAX importer (25 records per batch) + WP-CLI command (`wp santa import`) so cPanel shared hosting completes reliably without timeouts. |
| 3 | **Route Collisions Under `/service-areas/*`** | **High** | Three overlapping patterns exist: (a) 261 explicit CPT posts `/service-areas/tema`, (b) 16 regional hubs `/service-areas/greater-accra-region` or unlisted towns `/service-areas/osu`, and (c) `/service-areas/{areaSlug}/{serviceSlug}` (4 services + `vehicle-recovery` alias). | Strict WP rewrite resolution order: WordPress first checks exact `st_service_area` CPT slug match (all 261 explicit pages). Two-segment rule `^service-areas/([^/]+)/([^/]+)/?$` captures `/{area}/{service}`. Fallback single-segment rule catches unlisted towns from `ghanaLocations.ts` / `towingAdverts.ts` and renders dynamic template cleanly before 404! |
| 4 | **Static Mock Forms & Unwired Filters in React** | **Medium** | Blindly copying React's `setTimeout` mock forms or unwired sidebar filters in `/sales` & `/parts` would leave dead UI on WordPress. | Maintain 100% visual styling while backing Contact, Haulage Quote, Car Inquiry, Rental Booking, Part Quote, & Career CV forms with real WP AJAX + `wp_mail()` + DB logging, and making Sales/Parts filters query real WP CPT parameters. |
| 5 | **Tailwind CSS Build Dependency** | **Medium** | WordPress hosting has no Node.js to compile Tailwind utility classes dynamically. | Compile Tailwind CSS v4 cleanly into a static minified CSS file (`assets/css/santa-styles.css`) during Stage 2 packaging; keep runtime color edits powered strictly by CSS custom properties (`--st-primary`, etc.) injected cleanly in `wp_head`. |
| 6 | **Inline Base64 Images in React Content / Invoices** | **Medium** | React stores uploaded logos/photos as Base64 data URLs in `localStorage`. Storing huge Base64 strings in MySQL `wp_options` bloats autoloaded options and slows TTFB. | Never store Base64 uploads in `wp_options` in WordPress. Every upload routes through `wp_handle_upload` into `/wp-content/uploads/santa-towing/` and saves lightweight attachment ID/URL references. |
| 7 | **Server-Side PDF Generation Without Node/Browser Dependencies** | **High** | React uses client-side `jsPDF`. WordPress billing works better with server-side consistent PDFs OR client-side `jsPDF` parity. | Bundle Composer-free pure-PHP **Dompdf** inside `santa-towing-billing/vendor/` (works on PHP 8.1+ shared cPanel with zero shell/Node binaries) for instant crisp A4 PDF downloads including corporate logo + `#printable-document-root` CSS Print Parity! |
| 8 | **Unrecoverable Runtime Browser State** | **High** | If client has unexported edits/invoices/receipts in browser `localStorage` or `IndexedDB`, code conversion alone only contains seed defaults. | Provide explicit export checklist in Runbook (Section 3.2 of `WORDPRESS-MIGRATION-BLUEPRINT.md`) + dual JSON upload form in `santa-towing-importer`. |

---

## 4. Complete Asset Migration Plan (`assets/images/` & `image-manifest.json`)

**Verified Asset Inventory in `Santa WordPress Website Conversions/`:**
- **41 image files** are already staged inside `Santa WordPress Website Conversions/assets/images/` and indexed in `docs/image-manifest.json`:
  - `santa-logo.png` (corporate brand logo & favicon)
  - `santa-app-mobile-screen.png`
  - `welcome-towing-image.jpg`
  - 5 Admin/Team 256×256 avatars (`avatar-dispatch-director-256.jpg`, `avatar-fleet-manager-256.jpg`, `avatar-operations-lead-256.jpg`, `avatar-safety-specialist-256.jpg`, `avatar-support-officer-256.jpg`)
  - 16 numbered fleet/parts/service photos (`image-1-towing-vehicle.jpg` .. `image-16-accessories.jpg`)
  - 17 heavy haulage / logistics photos (`haulage-bulk-freight-800.jpg` .. `haulage-warehouse-cargo-800.jpg`)
- **Additional Source Images Referenced in React Assets/Data:**
  - Note `src/assets/images/` also contains `construction_haulage_1790967998627.jpg` (verified in directory listing — check during Stage 3 asset bundling whether used in `index.ts` or extra stock).
  - External Unsplash URLs used in `inventoryDefaults.ts`, `blogData.ts`, `siteImagesData.ts`, & `mockData.ts`: Audit exact count in Stage 3; download external assets into theme/uploads **with WP Media Library ingestion** so the WordPress site has zero broken third-party hotlinks on cPanel!

### WordPress Asset Ingestion Flow
1. Bundle all 41+ local images + downloaded catalog images into `santa-towing/assets/images/` (theme fallback) AND `migration/media-pack/`.
2. During `santa-towing-importer` activation/run, ingest every asset via `media_sideload_image()` / `wp_insert_attachment()` into `/wp-content/uploads/santa-towing/`.
3. Record a deterministic map `{ "logicalKey": attachment_id }` in `santa_towing_image_overrides` so every CPT post, `site-content` image slot, and Elementor template automatically links to local WordPress Media URLs (`wp_get_attachment_image_url()`).
