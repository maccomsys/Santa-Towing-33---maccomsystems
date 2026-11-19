# Santa Towing WordPress Data, Media & Legacy Browser Migration Report

**Source of Truth:** Original React/Vite project (`src/`, `public/`, `index.html`, `package.json` — 100% untouched)  
**Machine-Readable Audit:** `Santa WordPress Website Conversions/migration/migration-audit-report.json`  
**Execution Scope Notice:** This container runs a static workspace without a PHP CLI, MySQL server, WordPress runtime, or a live user browser session. Therefore, all conversion assets, structured manifests, WordPress importers, Media Library sync scripts, and the Business Manager plugin bundle are statically verified and packaged, while **live database writes into WordPress MySQL** and **extraction of any user-specific `localStorage` / `IndexedDB` runtime data from an old live React tab** remain **Pending** execution by the site administrator.

---

## Executive Summary

| Category | Audited Count | Status |
|---|---|---|
| Verified Local Images (`src/assets/images/` -> `assets/images/` & theme) | **42 / 42** | **Verified & Manifested** (0 missing, 0 SHA-256 mismatches, 42 alt texts mapped) |
| `site-content.json` Dot-Path Keys (`cpt-seeds.json` + defaults) | **258 / 258** | **Exported & Verified** (100% key parity) |
| TypeScript Data Registries (`siteImagesData`, `towingAdverts`, `ghanaLocations`, `mockData`) | **30 / 32 / 16 (232 areas) / 4+8** | **Exported (`business-manager-defaults.json`) & Wired** |
| Business Manager Billing & Image Studio Seed Records | **3 Customers, 3 Invoices, 2 Receipts, 3 Logs, 4 Studio Images** | **Exported & Wired into `STBM_DB` + `santa-full-importer.php`** |
| CPT & Blog Seed Records (`st_car`, `st_rental`, `st_part`, `post`, `st_faq`, `st_gallery`, `st_career`) | **70 / 70** | **Exported & Verified** (0 duplicate slugs, 0 broken image tokens) |
| Core & Policy WordPress Pages + Elementor Free Templates | **26 Pages / 15 Templates** | **Exported & Verified** (0 missing templates, 0 missing media refs) |
| Dynamic Service Area Routes (`st_service_area`) | **261 / 261** | **Exported & Verified** (`35` districts + `226` corridors, 0 broken prev/next links) |
| Live WordPress/MySQL Database Execution | **0 Executed Here** | **Pending Live WP Run** (`santa-full-importer.php` on cPanel/WP host) |
| Old Browser `localStorage` & `IndexedDB` User Runtime Data | **0 Browser Records Extracted** | **Pending Manual Browser Export** (Tool & importer delivered; NOT claimed migrated) |

---

## 1. Successful (Exported, Aligned & Statically Verified)

1. **Image Assets & Metadata Verification (`migration/media-manifest.json` & `class-st-media-sync.php`):**
   - Verified all **42 / 42** binary image files in `src/assets/images/` match `Santa WordPress Website Conversions/assets/images/` and `theme/santa-towing/assets/images/` byte-for-byte via SHA-256 (`0` corrupted or mismatched files). Note: `src/assets/images/` also contains `image-manifest.json` and `index.ts` (code/metadata files, not binary images).
   - Built `migration/media-manifest.json` cataloging all **42** images with exact preserved filenames, MIME types, byte sizes, SHA-256 hashes, pixel dimensions, titles, captions, and explicit **alt text** mapped from `src/data/siteImagesData.ts`, `st_gallery`, and descriptive defaults (`0` empty alt texts).
   - Updated `ST_Migration_Media_Sync::ensure_media_attachment()` and `sync_all_verified_images()` to preserve original filenames (`welcome-towing-image.jpg`, `santa-logo.png`, etc., deduplicating cleanly via `_st_source_filename` + `_wp_attached_file`) and populate `_wp_attachment_image_alt`, `post_title`, `post_excerpt`, and `_st_source_sha256`.

2. **`site-content.json` & Registry Data Extraction (`cpt-seeds.json` & `business-manager-defaults.json`):**
   - Verified **258 / 258** flattened keys from `src/data/site-content.json` match `migration/cpt-seeds.json['site_content']` (`0` missing keys).
   - Extracted and structured all additional TypeScript source data files into `migration/business-manager-defaults.json` (`schema: stwc-defaults-v1`):
     - `siteImagesData.ts`: **30** `SITE_IMAGES` slots with `storageKey`, `altStorageKey`, `defaultUrl`, `defaultAlt`, and page labels (`santa-full-importer.php` seeds all 30 `defaultAlt` values into `santa_towing_site_content`).
     - `towingAdverts.ts`: **32** hyper-local district towing adverts -> `stwc_towing_adverts`.
     - `ghanaLocations.ts`: **16** regions / **232** cities & towns -> `stwc_ghana_locations`.
     - `mockData.ts`: Home highlights (`stats` + `parts_categories`) -> `stwc_mock_highlights`.
     - `documentStorage.ts` & `imageStudioStorage.ts`: Company letterhead `doc_settings`, **3** sample customers, **3** pro forma invoices, **2** official receipts, **3** activity logs, and **4** Image Studio seed assets.

3. **CPT Meta Alignment Across Core Importer & Business Manager:**
   - Aligned `santa-full-importer.php` (`section=cpts` & `section=defaults`) so imported CPTs populate `_st_image_url`, `_st_sort_order`, `_st_hidden`, `_st_features_json`, and `_st_alt_text` matching Business Manager CRUD expectations.
   - Updated `STBM_Defaults::hydrate_inventory_post()` in `plugins/santa-towing-business-manager/includes/class-stbm-defaults.php` to fall back cleanly to `_st_rental_features` and `_st_alt`.

4. **Legacy Browser Export Tool & Business Manager Unified Importer:**
   - Created read-only `migration/legacy-browser-export-snippet.js` (`node --check` passed) and `migration/legacy-browser-export-tool.html` (one-click Copy + Bookmarklet). Extracts all **7** `localStorage` keys (`santa_towing_custom_content_v1`, `santa_doc_settings_v1`, `santa_doc_customers_v1`, `santa_doc_invoices_v1`, `santa_doc_receipts_v1`, `santa_doc_activity_logs_v1`, `santa_studio_images_fallback_v1`) plus **1** IndexedDB database (`santa_image_studio_db` -> `studio_images`) into `santa-legacy-browser-export-v1` JSON while excluding `sessionStorage` login tokens.
   - Built `STBM_Legacy_Importer` (`plugins/santa-towing-business-manager/includes/class-stbm-legacy-importer.php`), AJAX route `stbm_import_legacy_bundle` (`STBM_Roles::verify_ajax( STBM_Roles::CAP_BACKUPS )`), Backup & Export Upload UI in `assets/js/stbm-app.js` (`Merge` vs `Replace` + `Dry-Run Preview`), and standalone WP Tools / WP-CLI runner (`migration/class-stbm-legacy-importer.php`).
   - Rebuilt and verified `packages/santa-towing-business-manager.zip` (`6,070,420` bytes, `40` files, `testzip() = None`).

---

## 2. Failed

- **Static Build & Data Validation Failures:** **0** failures in JSON manifests, SHA-256 asset checks, JS syntax checks (`node --check`), or PHP syntax/security audits (`14 / 14` audited migration + Business Manager PHP files passed balanced-delimiter and `ABSPATH`/`WP_UNINSTALL_PLUGIN` checks; `26 / 26` AJAX routes passed `STBM_Roles::verify_ajax()` checks).
- **Runtime Environment Limitation (Disclosed Honestly):** Because `php` CLI and live MySQL/WordPress processes are not installed inside this build container, automated runtime import execution (`wp eval-file`) could not be run locally here and is listed under **Pending** below.

---

## 3. Pending (Must Run on Live WordPress / Old Browser Tab)

1. **Live WordPress + MySQL Import Execution (Pending):**
   - Upload and activate theme `santa-towing.zip`, core plugin `santa-towing-website-core.zip`, and `santa-towing-business-manager.zip` (plus Elementor Free) on target cPanel/WordPress server.
   - Run `Santa WordPress Website Conversions/migration/santa-full-importer.php` via **Tools -> Santa Full Importer** or WP-CLI (`wp eval-file "Santa WordPress Website Conversions/migration/santa-full-importer.php"` with `ST_IMPORT_SECTION=all`) to execute physical Media Library uploads (`42` files into `wp-content/uploads/`), `santa_towing_site_content`, `4` nav menus, `70` CPT/blog posts, `26` WP pages, `261` service-area CPTs, and Business Manager seed tables.

2. **Live Browser `localStorage` & `IndexedDB` Migration (Pending — NOT Claimed Migrated):**
   - **Important:** Only factory defaults from source code exist in this repository. Any custom edits made directly in a user's browser on the deployed React app (`/admin/*`) live exclusively inside that browser's `localStorage` and `IndexedDB`.
   - To migrate live browser data:
     1. Open the old live React website tab (`/admin`) and open `migration/legacy-browser-export-tool.html` (or copy `migration/legacy-browser-export-snippet.js`).
     2. Run the read-only snippet in DevTools Console to download `santa-towing-legacy-export-YYYY-MM-DD.json`.
     3. Upload the JSON file in **WordPress Admin -> Santa Manager -> Backup & Export -> Import Legacy React Browser Export** (recommended: run **Dry-Run Preview** first, then **Merge** mode).

---

## 4. Duplicates Audit

- **Media Library Deduplication (`0` binary duplicates):**
  - Across all `42` files in `assets/images/`, there are **0 duplicate filenames** and **0 duplicate binary files** (42 distinct SHA-256 digests).
  - Re-running `ST_Migration_Media_Sync::sync_all_verified_images()` on an already-seeded WordPress site deduplicates via `_st_source_filename` first and `_wp_attached_file` suffix second, reporting `duplicate_reused` instead of uploading duplicate `file-1.jpg` copies.
- **CPT, Pages & Service Areas Deduplication (`0` duplicates):**
  - `cpt-seeds.json`: **0** duplicate slugs or `_st_legacy_id` collisions across all `70` records.
  - `pilot-pages.json` + `phase-b-pages.json`: **0** duplicate page slugs across all `26` WP pages.
  - `service-areas-261.json`: **0** duplicate slugs across all `261` districts & corridors.
- **Business Manager Merge Protection:**
  - When importing a `santa-legacy-browser-export-v1` archive in **Merge** mode, `STBM_Legacy_Importer` matches existing customers by `uid`, invoices by `uid`/`docNumber`, receipts by `uid`/`receiptNumber`, CPTs by `id`/`slug`/`title`, and Studio images by `uid`, recording merged items under `skipped_duplicates` in the import report rather than creating duplicate rows.

---

## 5. Missing Images Audit

- **Verified Workspace Images (`0` missing in `assets/images/` & Theme):**
  - `src/assets/images/` (**42** images) vs `Santa WordPress Website Conversions/assets/images/` (**42** images) vs `theme/santa-towing/assets/images/` (**42** images): **0 missing files**.
  - Cross-reference across all `15` Elementor Free templates (`elementor-templates/*.json`) and all `70` CPT/blog seed records (`cpt-seeds.json`): **0 unresolved `__ST_MEDIA_URL__*__` or `__ST_MEDIA_ID__*__` tokens**.
- **Business Manager Plugin Standalone Folder (`17` haulage photos intentionally not duplicated inside plugin ZIP):**
  - `plugins/santa-towing-business-manager/assets/images/` bundles **25** UI/avatar/fleet/logo/part images directly (`6.07 MB` ZIP). The **17** haulage/highway images (`haulage-*.jpg`, `construction_haulage_*.jpg`) live in `assets/images/` and `theme/santa-towing/assets/images/` and are resolved via `ST_Migration_Media_Sync` into the WP Media Library (`wp_get_attachment_url`), avoiding bloating the plugin ZIP unnecessarily.
- **Potential Live Browser `blob:` URLs:**
  - If a user's browser IndexedDB (`santa_image_studio_db`) contains ephemeral `blob:...` URLs that were never saved as `data:image/*` base64 strings, `legacy-browser-export-snippet.js` attempts in-browser `FileReader` conversion and flags any unresolvable expired `blob:` references so `STBM_Legacy_Importer` logs them under `missing_images` without corrupting DB rows.

---

## 6. Broken Relationships Audit

- **Billing Seed Relational Integrity (`0` broken links):**
  - Verified all **3** seed invoices reference valid seed `customerId` values (`cust-1`, `cust-2`, `cust-3`), and both seed receipts (`rec-1`, `rec-2`) reference valid `customerId` and `invoiceId` (`inv-1`, `inv-2`) records with reconciled totals (`6,227.25` paid on `inv-1` -> `0.00` balance; `700.00` paid on `inv-2`).
- **261 Service Area Navigation Graph (`0` broken links):**
  - Verified all `prev_path` and `next_path` links across all **261** entries in `service-areas-261.json` resolve cleanly to valid `/service-areas/{slug}` canonical targets (`0` broken links).
- **Safe Import Guardrails for User Browser Exports:**
  - When importing user JSON exports in `STBM_Legacy_Importer`, any invoice or receipt referencing a deleted `customerId` or deleted `invoiceId` preserves customer snapshot details cleanly (`customerName`, `customerPhone`) and explicitly reports the orphan ID in `broken_relationships` in the Migration Report UI/CLI response.

---

## Step-by-Step Admin Runbook (Live Site Cutover)

1. Install & activate `santa-towing.zip`, `santa-towing-website-core.zip`, **Elementor Free**, and `santa-towing-business-manager.zip`.
2. Open **Tools -> Santa Full Importer** (or run `wp eval-file "Santa WordPress Website Conversions/migration/santa-full-importer.php"` with `ST_IMPORT_SECTION=all`) to import all `42` media images cleanly (preserving filenames & alt text), site content, `30` image alt slots, `4` menus, `70` CPTs/posts, `26` pages, `261` service areas, and default Business Manager records.
3. *(Only if you have customized data in the old React app's browser storage)* Open the old React site -> run `legacy-browser-export-snippet.js` in DevTools Console -> upload `santa-towing-legacy-export-YYYY-MM-DD.json` in **WordPress Admin -> Santa Manager -> Backup & Export -> Legacy Browser Export** (run **Dry-Run Preview** first, then **Merge**).
4. Review the inline report output (`Successful`, `Duplicates Handled`, `Missing Images`, `Broken Relationships`, `Failed`) and inspect Permalinks (`Settings -> Permalinks -> Save`).
