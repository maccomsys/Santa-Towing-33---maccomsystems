# WordPress Migration Blueprint — Santa Towing & Garage Services Ltd.

**Project:** Santa Towing React/Vite SPA → Native WordPress Conversion  
**Target Stack:** WordPress 6.x + PHP 8.1+ + MySQL 8.0 / MariaDB 10.6+ + Elementor **FREE** Only  
**Hosting Constraint:** Standard cPanel shared/VPS hosting (Apache/LiteSpeed + PHP-FPM + MySQL; **zero Node.js runtime**, **zero React embeds**, **zero iframes**, **zero Pro/paid plugins or themes**)  
**Source of Truth:** Untouched React/Vite codebase in `src/`, `public/`, `scripts/`, `index.html`, `package.json`

---

## 1. Architectural Strategy & Compliance Matrix

Converting a 329-route interactive React SPA + full client-side admin/billing/image-editing suite into a pure, native WordPress + PHP + MySQL website on cPanel requires strict adherence to these architectural rules:

| Requirement | React/Vite Source Implementation | Native WordPress Target Implementation | Verification Note |
|---|---|---|---|
| **Runtime Engine** | React 19 + React Router DOM v7 + Vite 6 | WordPress Core + Custom Theme (`santa-towing`) + Custom Modular Plugins | Zero Node.js or React runtime on server or frontend |
| **Page Builder** | TSX section components | **Elementor FREE Only** (marketing pages built/editable via Elementor Free + Custom Santa Elementor Free Widgets) | **No Elementor Pro** (no Theme Builder, no Loop Grid, no Forms widget, no Popups). Theme handles headers, footers, CPT archives/singles |
| **Styling Engine** | Tailwind CSS v4 (`@import "tailwindcss";` + `@theme`) | Pre-compiled static stylesheet (`assets/css/santa-styles.css`) + CSS custom properties (`:root`) dynamically injected from WP Options | Compiled locally during build/packaging; zero Node/JIT required on cPanel |
| **Typography** | System UI stack (`font-sans`, zero external Google Fonts) | Identical System UI font stack (`ui-sans-serif, system-ui, ...`); zero external font CDNs | 100% font fidelity + fast LCP |
| **Animations** | `motion/react` (`whileInView`, initial/animate opacity/transform) | Vanilla CSS `@keyframes` + lightweight `IntersectionObserver` (`assets/js/santa-motion.js`) | Mirrors `once: true`, durations, stagger delays, and ease curves without React |
| **Public Content CMS** | `ContentContext` + `site-content.json` + `localStorage` | Custom `santa_towing_site_content` WP Option (JSON mirror + sanitized PHP accessors) + WP Theme Customizer / Admin Console + Elementor post content | Preserves live color/logo/text/link editing natively in MySQL |
| **Inventory & Directory** | `inventoryDefaults.ts` (`CARS`, `RENTALS`, `PARTS`, `GALLERY`, `FAQS`, `CAREERS`) + 261 `.tsx` Service Areas + 32 `TOWING_ADVERTS` + `BLOG_POSTS` | Native Custom Post Types (`st_car`, `st_rental`, `st_part`, `st_gallery`, `st_career`, `st_faq`, `st_service_area`, native `post`) + Taxonomies | Full PHP archives, singles, REST/AJAX filters, & WP Admin management |
| **Billing & CRM Suite** | `documentStorage.ts` (`localStorage`) + `jsPDF` + `#printable-document-root` | Custom MySQL Tables (`wp_santa_*`) + WP Admin Billing Pages + Dompdf PHP PDF export + `@media print` A4 canvas | Multi-user persistent accounting instead of browser-isolated storage |
| **Media & Image Studio** | `imageStudioStorage.ts` (IndexedDB) + `ImageCanvasEditor` + `imageCompressor.ts` | WordPress Media Library (`wp_posts` attachments) + Custom Canvas Crop/Watermark Helper Modal (`santa-towing-billing`/`santa-towing-core`) saving directly to WP uploads | Eliminates browser 5 MB quota and IndexedDB volatility |
| **Authentication & RBAC** | `auth.ts` (sessionStorage + salted SHA-256 hardcoded demo hashes) | Native WordPress Authentication (`wp_users`) + Custom Roles (`st_admin` / `administrator` vs `st_staff`) | Enforces colors/logo restriction (`st_admin` only) via WP capabilities |

---

## 2. Custom Theme & Plugin Topology

All code artifacts will live inside `Santa WordPress Website Conversions/theme/` and `Santa WordPress Website Conversions/plugins/`.

### 2.1 Custom Theme: `santa-towing` (`/wp-content/themes/santa-towing/`)
Because **Elementor Free** lacks Theme Builder (`header`, `footer`, `single`, `archive`), the custom theme natively renders all global chrome and archive/single layouts with 100% visual parity to the React components, while declaring full Elementor Free support (`add_theme_support('elementor')`, `elementor/theme/register_locations` fallback compatibility, clean `the_content()` canvas for Elementor-built pages).

```text
santa-towing/
├── style.css                       # Theme header & metadata
├── functions.php                   # Theme setup, asset enqueueing, dynamic :root CSS vars, widget areas, nav walkers
├── header.php                      # Renders TopBar + sticky NavBar + Mobile drawer (matches TopBar.tsx & NavBar.tsx)
├── footer.php                      # Renders 4-column Footer + Service Area strip + Emergency FloatingButtons (Phone/WhatsApp/BackToTop)
├── front-page.php                  # Renders Home layout (or Elementor Free template if assigned + fallback PHP sections)
├── page.php                        # Standard page / Elementor Free canvas wrapper
├── page-templates/
│   ├── template-elementor-full.php # Full-bleed Elementor Free canvas keeping TopBar/NavBar/Footer
│   ├── template-service.php        # Towing, Haulage, Repairs, Body Works, Services layouts
│   ├── template-policy.php         # Shared 12 Legal/Policy layout with side quick links & contact callout
│   └── template-directory.php      # Service Areas hub, FAQs, Careers, Contact, Gallery, Sitemap
├── single-st_car.php               # /sales/{id} or /sales/{slug} — specs, gallery lightbox, inquiry modal, financing calc
├── single-st_rental.php            # /rental/{id} or /rental/{slug} — specs, pricing, booking modal
├── single-st_part.php              # /parts/{id} or /parts/{slug} — SKU, fitment specs, prev/next using live inventory
├── single-st_service_area.php      # Renders ServiceAreaPageTemplate for all 261 local/corridor pages + 16 region dynamic fallback
├── single-post.php                 # /blog/{id} or /blog/{slug} — Article JSON-LD, reading progress, prev/next, related posts
├── archive-st_car.php              # /sales — category tabs + functional sidebar filters (Make, Condition, Transmission, Fuel, Price) + search
├── archive-st_rental.php           # /rental — category grid + fleet cards
├── archive-st_part.php             # /parts — part category cards + live catalog + YMM/SKU filter
├── home.php / archive.php          # /blog — search, category pills, grid + featured article
├── search.php                      # /search — cross-CPT + Service Area + FAQ + Page search
├── 404.tsx -> 404.php              # NotFound parity (20+ quick recovery links & search box)
├── template-parts/
│   ├── breadcrumbs.php
│   ├── emergency-bar.php
│   ├── floating-buttons.php
│   ├── service-area-template.php   # Shared ServiceAreaPageTemplate HTML + CSS
│   └── towing-calculator.php       # Emergency Towing Cost & ETA Estimator widget
└── assets/
    ├── css/
    │   ├── santa-styles.css        # Pre-compiled Tailwind output + custom utilities
    │   └── print-a4.css            # #printable-document-root print rules
    ├── js/
    │   ├── santa-main.js           # Mobile nav, dropdowns, smooth hash scroll, gallery slider, accordions, calculators
    │   └── santa-motion.js         # IntersectionObserver reveal parity for framer-motion
    └── images/                     # Bundled fallback images from image-manifest.json
```

### 2.2 Custom Plugins (`/wp-content/plugins/`)

| Plugin Slug | Purpose & Responsibilities | Why Separate / Included |
|---|---|---|
| `santa-towing-core` | 1. Registers 7 CPTs (`st_car`, `st_rental`, `st_part`, `st_gallery`, `st_career`, `st_faq`, `st_service_area`) & taxonomies.<br>2. Manages `santa_towing_site_content` option (colors, logo, contact links, nav/footer link lists, 14-tab page text overrides, image catalog overrides).<br>3. Registers **Elementor FREE Custom Widgets** (Hero Banner, Quick Actions, Stats Counter, Service Cards Grid, Featured Cars Carousel/Grid, Rental Categories, Parts Categories, Towing Calculator, Haulage Quote Form, FAQ Accordion, Blog Grid, Service Area Directory, Emergency CTA Banner).<br>4. Custom Rewrite Rules supporting dual numeric legacy URLs (`/sales/1`, `/rental/1`, `/parts/1`, `/blog/1`), 16 regional `/service-areas/{areaSlug}`, and `32 × 5` localized service pages `/service-areas/{areaSlug}/{serviceSlug}`.<br>5. AJAX Public Form Handlers (Contact, Haulage Quote, Car Inquiry, Rental Booking, Part Quote, Career Application w/ real CV file upload -> `wp_mail()` + DB inquiry log).<br>6. SEO `<title>`, `<meta name="description">`, `<meta name="keywords">`, OpenGraph, `Article` JSON-LD, & Dynamic `/sitemap.xml` endpoint.<br>7. Visual Frontend Inline Text/Image Quick-Edit Bar (strictly gated behind `is_user_logged_in() && current_user_can('st_manage_content')` — mirrors React `EditModeContext` without touching anonymous visitors). | Encapsulates business data, URL rewrites, forms, and Elementor widgets independently of theme updates |
| `santa-towing-billing` | 1. Creates & maintains custom MySQL tables (`wp_santa_customers`, `wp_santa_invoices`, `wp_santa_invoice_items`, `wp_santa_receipts`, `wp_santa_activity_logs`).<br>2. Native WP Admin dashboard screens replicating React's Invoices (customizable builder, sections reordering, custom fields/sections, VAT/discount/shipping/surcharges), Official Receipts (MoMo/Bank/Cash/Cheque/Card + auto invoice balance sync), Customers CRM (with billing lock check before delete), Document Settings (`santa_towing_doc_settings`), and JSON/CSV Backup & Audit Logs.<br>3. Server-side PDF export (bundled **Dompdf** PHP library — zero external API/Node required) + `@media print` A4 preview parity.<br>4. Admin Dashboard KPI Home & Custom Role setup (`st_admin`, `st_staff`). | Keeps accounting tables, financial queries, and Dompdf cleanly isolated from public frontend |
| `santa-towing-importer` | One-click CLI/WP-Admin migration runner: reads `migration/*.json` seed packages, creates all 12 cars, 6 rentals, 6 parts, 8 gallery items, 28 FAQs, 4 careers, 6 blog posts, 261 static service area CPT posts, 32 advert records, 31 image attachments in Media Library, and populates `santa_towing_site_content` + `santa_towing_doc_settings`. Includes idempotent re-run guard (`_legacy_id` / `_migration_key`). | Ensures repeatable, zero-error deployment on any fresh cPanel WordPress install |

> **Honest Scope Note on Elementor Free Integration:**  
> Elementor Free supports editing individual Page (`page` CPT) bodies via its drag-and-drop canvas and allows custom PHP widget classes extending `\Elementor\Widget_Base`. Because Elementor Free **cannot** override Global Header/Footer, Single CPT views (`/sales/1`, `/rental/1`, `/parts/1`, `/blog/1`), Archive filters, or Dynamic Service Area templates without Elementor Pro, those structural parts MUST be rendered by our custom `santa-towing` PHP theme templates, while marketing pages (`/`, `/services`, `/towing`, `/haulage`, `/repairs`, `/body-works`, `/fleet`, `/contact`, `/careers`, `/faqs`, `/gallery`, 12 legal policies) will have exported Elementor Free JSON templates (`elementor-templates/*.json`) **plus** theme template fallbacks using the custom Santa Elementor Free widgets.

---

## 3. Complete Data Models & Storage Inventory

### 3.1 Inventory of Browser Storage Keys in React Source

The React app uses **7 `localStorage` keys**, **4 `sessionStorage` keys**, and **1 `IndexedDB` database**:

#### A. `localStorage` Keys
1. **`santa_towing_custom_content_v1`** (`src/lib/contentStorage.ts` & `src/context/ContentContext.tsx`)
   - **Shape:** `Partial<SiteContent>` nested JSON overrides merged on top of `src/data/site-content.json`. Includes:
     - `theme`: `{ primary, secondary, accent, dark, white, black }`
     - `global`: `{ brand, logo, links, social, images, nav: { menuLinks[] }, footer: { quickLinks[], serviceLinks[] }, floatingButtons }`
     - `seo`: `{ siteUrl, home, services, towing, haulage, rental, sales, repairs, bodyWorks, parts, fleet, blog, contact, faqs, gallery, careers, serviceAreas }`
     - `home`, `towing`, `haulage`, `services`, `sales`, `rental`, `repairs`, `bodyWorks`, `parts`, `fleet`, `contact`, `blog` (including `blog.posts[]`), `faqs` (including `faqs.items[]`), `gallery`, `careers`, `policies`, `serviceAreas` (including `serviceAreas.pages.{slug}`)
     - `inventory`: `{ cars[], rentals[], parts[], gallery[], faqs[], careers[] }`
     - `adminProfile`: `{ displayName, email, role, photoUrl, contactPerson, businessPhone, businessEmail, businessAddress }`
     - `adminPasswordHash`: `{ salt, hash }` (if user changed password via Security tab)
2. **`santa_doc_invoices_v1`** (`src/lib/documentStorage.ts`)
   - **Shape:** `ProFormaInvoice[]` (seeded with **3** sample invoices: `PFI-2026-0001`, `PFI-2026-0002`, `PFI-2026-0003` if empty).
   - Fields: `id, docNumber, docTitle, issueDate, validUntil, customerId, customerName, customerCompany, customerPhone, customerEmail, customerAddress, items[], subtotal, discountType, discountValue, discountAmount, taxRate, taxAmount, grandTotal, amountPaid, balanceDue, paymentInstructions, termsAndConditions, notes, status ('Draft'|'Issued'|'Accepted'|'Cancelled'), taxEnabled, taxLabel, discountEnabled, discountLabel, shippingEnabled, shippingLabel, shippingAmount, additionalCharges[], customFields[], customSections[], hiddenFields[], fieldLabels{}, sectionTitles{}, sectionOrder[], createdAt, updatedAt`.
3. **`santa_doc_receipts_v1`** (`src/lib/documentStorage.ts`)
   - **Shape:** `Receipt[]` (seeded with **2** sample receipts: `RCP-2026-0001`, `RCP-2026-0002`).
   - Fields: `id, receiptNumber, receiptDate, customerId, customerName, customerPhone, customerEmail, customerAddress, invoiceId, invoiceNumber, amountReceived, paymentMethod ('Mobile Money (MoMo)'|'Bank Transfer'|'Cash'|'Cheque'|'Credit/Debit Card'|'Other'), paymentReference, remainingBalance, notes, status ('Issued'|'Corrected'|'Cancelled'), createdAt, updatedAt`.
4. **`santa_doc_customers_v1`** (`src/lib/documentStorage.ts`)
   - **Shape:** `Customer[]` (seeded with **3** sample customers: `cust-1` Kwame Mensah, `cust-2` Accra Fleet Logistics Ltd, `cust-3` Abena Osei / Spintex Auto Traders).
   - Fields: `id, name, companyName, phone, email, address, notes, createdAt, updatedAt`.
5. **`santa_doc_settings_v1`** (`src/lib/documentStorage.ts`)
   - **Shape:** `DocumentSettings`: `{ companyName, tagline, address, phone, phoneSecondary, email, website, taxRegistrationNumber, logoUrl, defaultPaymentInstructions, defaultTerms, defaultNotes, defaultTaxRate (15), defaultValidityDays (14), footerText }`.
6. **`santa_doc_activity_logs_v1`** (`src/lib/documentStorage.ts`)
   - **Shape:** `DocumentActivityLog[]` (capped at last 100 events): `{ id, timestamp, action, docType ('invoice'|'receipt'|'customer'|'settings'|'system'), docNumber, summary }`.
7. **`santa_studio_images_fallback_v1`** (`src/lib/imageStudioStorage.ts`)
   - **Shape:** `StudioImage[]` JSON fallback used only if browser blocks IndexedDB.

#### B. `sessionStorage` Keys
1. **`santa_owner_edit_mode_active`** (`EditModeContext.tsx`) — `'true'`/`'false'` boolean flag for frontend inline editing bar.
2. **`santa_admin_authenticated_user`** (`src/admin/lib/auth.ts`) — JSON `{ email, role, displayName }`.
3. **`santa_admin_session_expiry`** (`src/admin/lib/auth.ts`) — epoch ms (`Date.now() + 8 * 60 * 60 * 1000`).
4. **`santa_admin_authenticated_session`** (`src/admin/lib/auth.ts`) — legacy boolean flag `'true'`.

#### C. `IndexedDB` Database
1. **`santa_image_studio_db`** (`src/lib/imageStudioStorage.ts`, version `1`, objectStore `studio_images`, keyPath `id`)
   - **Shape:** `StudioImage`: `{ id, name, category ('Fleet'|'Towing'|'Haulage'|'Garage'|'Documents'|'General'), dataUrl (base64 data URI), width, height, size, format ('image/jpeg'|'image/png'|'image/webp'), createdAt, updatedAt, isEdited, originalId, originalName }`.
   - Seeded lazily via `ensureStudioInitialized()` from built-in site assets if empty.

### 3.2 What Cannot Be Recovered from the Codebase / ZIP

> **Critical Honest Disclosure for Stakeholder Sign-Off:**  
> Because the React application currently has **no backend server** and writes all mutations exclusively to the browser's `localStorage`, `sessionStorage`, and `IndexedDB`, a codebase ZIP contains **only factory defaults** (`site-content.json`, `inventoryDefaults.ts`, `blogData.ts`, `documentStorage.ts` seed records). The following items **cannot** be recovered from the project files alone:
> 1. Any live copy, color, logo, link, or image URL overrides a client/admin edited in their own browser (`santa_towing_custom_content_v1`).
> 2. Any live added/edited/hidden cars, rentals, parts, gallery items, FAQs, careers, or blog posts saved only in browser storage.
> 3. Real production Pro Forma Invoices, Official Receipts, Customers, Document Settings, and Activity Logs (`santa_doc_*_v1`) created in someone's browser session.
> 4. Uploaded/cropped/watermarked images stored inside a browser's `IndexedDB` (`santa_image_studio_db`).
> 5. Custom admin passwords changed in-browser via `AdminProfile` (`adminPasswordHash`), and live authentication sessions (`sessionStorage`).  
> **Migration Remedy:** Before switching DNS, if any staging/live browser holds live edits or billing records, the operator MUST export both JSON backups: (a) **Admin > Profile > Backup > Download JSON Backup File** (`contentStorage`) and (b) **Admin > Backup & Export > Export All Billing & Customer Data** (`documentStorage`), then upload both files into our Stage 3/5 WordPress migration importer!

---

## 4. WordPress Database & Content Architecture

### 4.1 Custom Post Types (CPTs) & Taxonomies

| WP Post Type / Native Type | Source Data | URL Structure (Preserved) | Meta Fields & Taxonomies |
|---|---|---|---|
| `post` (Native WP Posts) | `blogData.ts` (6 defaults) + `site-content.json` `blog.posts` | `/blog/` (archive), `/blog/{id}/` & `/blog/{slug}/` | Tax: `category` (`Towing Safety`, `Fleet Maintenance`, `Heavy Haulage`, `Ghana Automotive`, `Buying Guide`, `Emergency Tips`). Meta: `_st_legacy_id`, `_st_read_time`, `_st_author`, `_st_author_role` |
| `st_car` (Cars for Sale) | `DEFAULT_CARS` (12) | `/sales/` (archive), `/sales/{id}/` & `/sales/{slug}/` | Tax: `st_car_category` (`SUVs`, `Sedans`, `Vans & Buses`, `Trucks & Pickups`, `Luxury`), `st_car_condition` (`Brand New`, `Foreign Used`, `Locally Used`). Meta: `_st_legacy_id`, `_st_make`, `_st_model`, `_st_year`, `_st_price`, `_st_currency`, `_st_mileage`, `_st_transmission`, `_st_fuel_type`, `_st_status`, `_st_badge`, `_st_featured`, `_st_specs_json`, `_st_features_json`, `_st_gallery_ids` |
| `st_rental` (Fleet Rentals) | `DEFAULT_RENTALS` (6) | `/rental/` (archive), `/rental/{id}/` & `/rental/{slug}/` | Tax: `st_rental_category` (`SUVs`, `Sedans`, `Luxury`, `Pickups & 4x4`, `Buses & Vans`). Meta: `_st_legacy_id`, `_st_daily_rate`, `_st_weekly_rate`, `_st_monthly_rate`, `_st_deposit`, `_st_transmission`, `_st_fuel_type`, `_st_seats`, `_st_status`, `_st_with_driver`, `_st_specs_json`, `_st_features_json`, `_st_gallery_ids` |
| `st_part` (Auto Parts) | `DEFAULT_PARTS` (6) | `/parts/` (archive), `/parts/{id}/` & `/parts/{slug}/` | Tax: `st_part_category` (`Batteries & Electrical`, `Engine Oil & Fluids`, `Brakes & Suspension`, `Tyres & Wheels`, `Filters & Ignition`, `Lighting & Accessories`). Meta: `_st_legacy_id`, `_st_sku`, `_st_brand`, `_st_price`, `_st_stock_status`, `_st_warranty`, `_st_condition`, `_st_compatible_models`, `_st_specs_json`, `_st_gallery_ids` |
| `st_gallery` (Work Showcase) | `DEFAULT_GALLERY` (8) | Rendered on `/gallery/` (no public single required; `publicly_queryable => false`) | Tax: `st_gallery_category` (`Towing`, `Haulage`, `Repairs`, `Body Works`, `Fleet`). Meta: `_st_legacy_id`, `_st_media_type` (`photo`|`video`), `_st_location`, `_st_date_label`, `_st_video_url` |
| `st_faq` (FAQs) | `DEFAULT_FAQS` (28) | Rendered on `/faqs/` & Home FAQ section | Tax: `st_faq_category` (`Emergency Towing`, `Heavy Haulage`, `Rentals & Sales`, `Garage & Parts`, `Payments & Policies`). Meta: `_st_legacy_id`, `_st_sort_order` |
| `st_career` (Job Openings) | `DEFAULT_CAREERS` (4) | Rendered on `/careers/` | Tax: `st_department` (`Dispatch & Recovery`, `Mechanical & Garage`, `Heavy Haulage`, `Administration`). Meta: `_st_legacy_id`, `_st_location`, `_st_employment_type`, `_st_requirements_json`, `_st_benefits_json` |
| `st_service_area` (Areas & Corridors) | `src/pages/service-areas/*.tsx` (261 static) + 16 Regions / 232 Town directory | `/service-areas/{slug}/` | Tax: `st_area_type` (`local-district`, `intercity-corridor`), `st_region`. Meta: `_st_area_label`, `_st_origin`, `_st_destination`, `_st_heading`, `_st_description` (exact source copy preserved verbatim), `_st_seo_title`, `_st_seo_description`, `_st_keywords_json`, `_st_prev_path`, `_st_prev_label`, `_st_next_path`, `_st_next_label` |
| `st_advert_area` (or WP Option/Table) | `towingAdverts.ts` (32 areas × 4 services + `vehicle-recovery` alias) | `/service-areas/{areaSlug}/` & `/service-areas/{areaSlug}/{serviceSlug}/` | Rewrite endpoint resolving regional fallback (16 Ghana regions / 232 towns) and advert service landing pages |

### 4.2 Custom MySQL Tables (Billing, Accounting & Inquiry Leads)

All custom tables use `$wpdb->prefix` (`wp_santa_*`) with `InnoDB` engine and `utf8mb4_unicode_ci` collation:

1. **`wp_santa_customers`**
   - Columns: `id BIGINT UNSIGNED PK AI`, `legacy_id VARCHAR(64) UNIQUE`, `name VARCHAR(255) NOT NULL`, `company_name VARCHAR(255)`, `phone VARCHAR(50) NOT NULL`, `email VARCHAR(190)`, `address TEXT`, `notes TEXT`, `created_at DATETIME`, `updated_at DATETIME`.
2. **`wp_santa_invoices`**
   - Columns: `id BIGINT UNSIGNED PK AI`, `legacy_id VARCHAR(64) UNIQUE`, `doc_number VARCHAR(50) UNIQUE NOT NULL`, `doc_title VARCHAR(100) DEFAULT 'PRO FORMA INVOICE'`, `issue_date DATE`, `valid_until DATE`, `customer_id BIGINT UNSIGNED FK`, `customer_name VARCHAR(255)`, `customer_company VARCHAR(255)`, `customer_phone VARCHAR(50)`, `customer_email VARCHAR(190)`, `customer_address TEXT`, `subtotal DECIMAL(12,2)`, `discount_enabled TINYINT(1)`, `discount_label VARCHAR(120)`, `discount_type ENUM('percentage','fixed')`, `discount_value DECIMAL(12,2)`, `discount_amount DECIMAL(12,2)`, `tax_enabled TINYINT(1)`, `tax_label VARCHAR(120)`, `tax_rate DECIMAL(5,2)`, `tax_amount DECIMAL(12,2)`, `shipping_enabled TINYINT(1)`, `shipping_label VARCHAR(120)`, `shipping_amount DECIMAL(12,2)`, `grand_total DECIMAL(12,2)`, `amount_paid DECIMAL(12,2)`, `balance_due DECIMAL(12,2)`, `status ENUM('Draft','Issued','Accepted','Cancelled')`, `payment_instructions TEXT`, `terms_and_conditions TEXT`, `notes TEXT`, `additional_charges_json LONGTEXT`, `custom_fields_json LONGTEXT`, `custom_sections_json LONGTEXT`, `hidden_fields_json TEXT`, `field_labels_json LONGTEXT`, `section_titles_json LONGTEXT`, `section_order_json TEXT`, `created_at DATETIME`, `updated_at DATETIME`.
3. **`wp_santa_invoice_items`**
   - Columns: `id BIGINT UNSIGNED PK AI`, `invoice_id BIGINT UNSIGNED NOT NULL (INDEX)`, `sort_order INT`, `description TEXT NOT NULL`, `quantity DECIMAL(10,2)`, `unit_price DECIMAL(12,2)`, `total DECIMAL(12,2)`.
4. **`wp_santa_receipts`**
   - Columns: `id BIGINT UNSIGNED PK AI`, `legacy_id VARCHAR(64) UNIQUE`, `receipt_number VARCHAR(50) UNIQUE NOT NULL`, `receipt_date DATE`, `customer_id BIGINT UNSIGNED`, `customer_name VARCHAR(255)`, `customer_phone VARCHAR(50)`, `customer_email VARCHAR(190)`, `customer_address TEXT`, `invoice_id BIGINT UNSIGNED NULL`, `invoice_number VARCHAR(50) NULL`, `amount_received DECIMAL(12,2)`, `payment_method VARCHAR(80)`, `payment_reference VARCHAR(120)`, `remaining_balance DECIMAL(12,2) NULL`, `notes TEXT`, `status ENUM('Issued','Corrected','Cancelled')`, `created_at DATETIME`, `updated_at DATETIME`.
5. **`wp_santa_activity_logs`**
   - Columns: `id BIGINT UNSIGNED PK AI`, `user_id BIGINT UNSIGNED`, `action VARCHAR(80)`, `doc_type VARCHAR(40)`, `doc_number VARCHAR(80)`, `summary TEXT`, `created_at DATETIME`.
6. **`wp_santa_inquiries`** *(New upgrade over React local-only state so Contact, Haulage Quote, Car Inquiry, Rental Booking, Part Quote, & Career Applications actually persist on server!)*
   - Columns: `id BIGINT UNSIGNED PK AI`, `inquiry_type ENUM('contact','haulage','car','rental','part','career')`, `customer_name VARCHAR(255)`, `customer_phone VARCHAR(50)`, `customer_email VARCHAR(190)`, `subject VARCHAR(255)`, `payload_json LONGTEXT`, `attachment_url VARCHAR(500) NULL`, `status ENUM('new','in_progress','quoted','closed')`, `created_at DATETIME`.

### 4.3 WordPress Options (`wp_options`) Mapping
- **`santa_towing_site_content`** (autoload = `yes`): Complete merged structure matching `src/data/site-content.json` (`theme`, `global`, `seo`, `home`, `towing`, `haulage`, `services`, `sales`, `rental`, `repairs`, `bodyWorks`, `parts`, `fleet`, `contact`, `blog` meta, `faqs` meta, `gallery` meta, `careers` meta, `policies`, `serviceAreas`).
- **`santa_towing_image_overrides`**: Overrides for the 31 `SITE_IMAGES` catalog entries (`url`, `alt`, WP attachment ID).
- **`santa_towing_doc_settings`**: Exact `DocumentSettings` object from `documentStorage.ts`.
- **`santa_towing_version`**: Schema version string for safe plugin upgrades.

---

## 5. Design System, Tokens, Typography, Breakpoints & Motion Mapping

### 5.1 Theme Color Tokens (`src/index.css` `@theme`)
| React Token | Hex Default | WP `:root` CSS Var | Elementor Global Color Slug | Usage Across Site |
|---|---|---|---|---|
| `--color-primary` | `#015557` | `--st-primary` | `primary` | Header TopBar, CTAs, section accents, footer background gradient, primary buttons, Admin primary |
| `--color-secondary` | `#192e2e` | `--st-secondary` | `secondary` | Dark teal hero overlays, sticky header highlights, card dark headers |
| `--color-accent` | `#d80000` | `--st-accent` | `accent` | Emergency call badge, urgent alerts, destructive actions, active call buttons |
| `--color-dark` | `#131514` | `--st-dark` | `text` | Body text, dark sections, charcoal backgrounds |
| `--color-black` | `#000000` | `--st-black` | `st-black` | Pure black contrast bands |
| `--color-white` | `#ffffff` | `--st-white` | `st-white` | Main page canvas (`bg-white`), light typography |

> **Dynamic Color Updating:** Whenever an admin saves colors in WP Admin (mirroring `AdminColors.tsx`), PHP updates `santa_towing_site_content['theme']`, writes inline `:root { --st-primary: ...; }` overrides into `wp_head`, and optionally syncs Elementor's `_elementor_page_settings` active kit global colors so Elementor widgets automatically reflect palette changes.

### 5.2 Typography & Font Stack
- **Audit Finding:** `index.html` loads **zero** external font stylesheets (`fonts.googleapis.com` is NOT used anywhere). `src/index.css` relies strictly on Tailwind v4 `font-sans` system UI font stack (`ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"`). Document previews (`DocumentPreviewModal`, `CustomizableInvoiceModal`) use `font-mono` (`ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace`) for invoice/currency tabular figures.
- **WordPress Mapping:** Set theme `body` and Elementor Default Font to System UI (`-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`) and disable Elementor's default Google Fonts loading to prevent unfaithful typography shifts and save network latency.

### 5.3 Breakpoints (Tailwind v4 Parity)
- `sm`: `40rem` (`640px`)
- `md`: `48rem` (`768px`)
- `lg`: `64rem` (`1024px`) — primary NavBar desktop vs hamburger breakpoint; split Admin invoice editor vs preview breakpoint
- `xl`: `80rem` (`1280px`)
- `2xl`: `96rem` (`1536px`)
- Core container max-width: `max-w-7xl` (`80rem` / `1280px`) with `px-4 sm:px-6 lg:px-8` horizontal gutters.

### 5.4 Animations (`motion/react` → Native CSS + `IntersectionObserver`)
All animations in `src/` use `motion/react` (`motion.div`, `motion.section`, `motion.article`) cleanly convertible without React:
1. **Scroll-reveal fade/slide-up** (`initial={{ opacity: 0, y: 20..30 }}`, `whileInView={{ opacity: 1, y: 0 }}`, `viewport={{ once: true }}`, duration `0.5s–0.7s`): Replaced with `[data-st-reveal]` HTML attribute + `IntersectionObserver` adding `.is-visible` class triggering CSS `@keyframes` transitions.
2. **Staggered card grids** (`delay: index * 0.05..0.1`): Implemented via inline `style="--st-delay: 100ms;"` (`transition-delay: var(--st-delay)`).
3. **Hero slide transitions / Floating pulse**: Pure CSS `@keyframes` (`animate-pulse`, smooth CSS transform transitions).
4. **Accordion & Modal transitions**: Vanilla JS height/class toggles with `transition-all duration-200`.
