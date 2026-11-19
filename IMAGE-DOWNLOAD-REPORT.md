# Image Download & Localization Report

## Summary Metrics

- **Unique Genuine External Image URLs Found:** 41 (19 `i.ibb.co` URLs + 22 `images.unsplash.com` URLs)
- **Pre-Existing Local Assets Preserved:** 1 (`src/assets/images/construction_haulage_1790967998627.jpg`)
- **Total Local Image Assets in `src/assets/images/`:** 42
- **Successfully Downloaded & Verified:** 41 / 41 (100% HTTP 200, non-empty, verified PNG/JPEG headers and pixel dimensions)
- **Failed Downloads:** 0
- **Total Codebase References to Local Assets:** 356 (351 references replaced from the 41 external URLs + 5 references to the 1 pre-existing local asset; excludes import statements in TS/TSX modules and `src/assets/images/index.ts`)
- **Duplicate External References Merged:** 310 (351 external references consolidated to 41 unique downloaded files; Unsplash variants with distinct resolution query strings such as `w=600`, `w=800`, `w=1200`, `w=1920`, and `w=256` were preserved individually per original URL)
- **Placeholders Intentionally Left Untouched:** 10 image-related placeholders across admin UI fallbacks and input hints (plus 2 non-image link hints/validators)
- **Non-Image External Links Left Untouched:** All `wa.me`, social links (`facebook.com`, `instagram.com`, etc.), `maps.google.com`, `santatowingservices.com`, `schema.org`, `w3.org`, and external API/font links remain unchanged.

---

## Implementation Pattern

1. **Centralized Vite Asset Registry (`src/assets/images/index.ts`):**
   - Statically imports all 42 verified images in `src/assets/images/` so Vite hashes and bundles them into `dist/assets/` for production and serves them locally during development.
   - Exports typed symbols (`santaLogo`, `image1TowingVehicle`, etc.) for `.ts` and `.tsx` modules.
   - Exports `LOCAL_IMAGE_PATH_MAP`, `resolveLocalImage()`, and `resolveLocalImagesDeep()` for `src/data/site-content.json` and `src/lib/contentStorage.ts` so JSON content referencing `/src/assets/images/...` resolves to bundled Vite URLs at runtime without breaking Admin Images or Image Studio.
2. **Inline Background Images & HTML Meta:**
   - Replaced external Tailwind arbitrary `bg-[url('https://...')]` utilities in page components with inline `style={{ backgroundImage: \`url(${...})\` }}` backed by local Vite imports.
   - Updated the favicon `<link rel="icon">` in `index.html` to `/src/assets/images/santa-logo.png`.
3. **Sitemap Generator (`scripts/generate-sitemap.ts`):**
   - Updated dynamic car sales and blog post route extraction to read IDs directly from `src/data/mockData.ts` and `src/data/blogData.ts` (matching the existing `Parts.tsx` pattern) so `npx tsx scripts/generate-sitemap.ts` runs cleanly in Node without attempting to import binary `.png`/`.jpg` assets.
4. **Backup & Conversion Package (`Santa WordPress Website Conversions/`):**
   - Copied `src/assets/images/image-manifest.json` and `IMAGE-DOWNLOAD-REPORT.md` to `Santa WordPress Website Conversions/docs/`.
   - Copied all 41 downloaded images to `Santa WordPress Website Conversions/assets/images/` (excluding `index.ts`, `image-manifest.json`, and pre-existing `construction_haulage_1790967998627.jpg`).

---

## Downloaded & Verified Asset Inventory

| Local Filename | Source Host | Format | Dimensions | Size (Bytes) | Status | References (Files) |
| --- | --- | --- | --- | --- | --- | --- |
| `santa-logo.png` | i.ibb.co | PNG | 1182x574 | 1,002,585 | downloaded | 19 (13 files) |
| `santa-app-mobile-screen.png` | i.ibb.co | PNG | 890x900 | 81,822 | downloaded | 3 (3 files) |
| `welcome-towing-image.jpg` | i.ibb.co | JPEG | 1536x1024 | 189,432 | downloaded | 3 (3 files) |
| `image-1-towing-vehicle.jpg` | i.ibb.co | JPEG | 1577x656 | 93,610 | downloaded | 21 (14 files) |
| `image-2-suv-kia-sportage-2024.png` | i.ibb.co | PNG | 1264x848 | 2,375,624 | downloaded | 31 (15 files) |
| `image-3-sedan-toyota-corolla.jpg` | i.ibb.co | JPEG | 2984x1999 | 279,994 | downloaded | 31 (14 files) |
| `image-4-van-hyundai-h1-2022.jpg` | i.ibb.co | JPEG | 618x414 | 28,182 | downloaded | 21 (12 files) |
| `image-5-economy-nissan-almera-2021.jpg` | i.ibb.co | JPEG | 1194x800 | 114,008 | downloaded | 19 (9 files) |
| `image-6-luxury-mercedes-benz.jpg` | i.ibb.co | JPEG | 1194x800 | 94,252 | downloaded | 30 (14 files) |
| `image-7-pickup-toyota-hilux-2023.jpg` | i.ibb.co | JPEG | 800x536 | 41,378 | downloaded | 25 (12 files) |
| `image-8-heavy-duty-recovery-vehicle.jpg` | i.ibb.co | JPEG | 1200x804 | 119,394 | downloaded | 16 (10 files) |
| `image-9-car-battery-maintenance.jpg` | i.ibb.co | JPEG | 1264x846 | 69,834 | downloaded | 9 (6 files) |
| `image-10-engine-oil-fluids-maintenance.jpg` | i.ibb.co | JPEG | 1264x847 | 106,322 | downloaded | 21 (9 files) |
| `image-11-brake-parts-disk-maintenance.jpg` | i.ibb.co | JPEG | 1264x847 | 60,514 | downloaded | 14 (9 files) |
| `image-12-tyre-maintenance.jpg` | i.ibb.co | JPEG | 674x452 | 41,288 | downloaded | 15 (8 files) |
| `image-13-filters-maintenance.jpg` | i.ibb.co | JPEG | 1000x671 | 85,700 | downloaded | 15 (9 files) |
| `image-14-suspension-parts-maintenance.jpg` | i.ibb.co | JPEG | 1264x847 | 109,479 | downloaded | 11 (9 files) |
| `image-15-car-lighting-maintenance.jpg` | i.ibb.co | JPEG | 1264x845 | 77,661 | downloaded | 15 (11 files) |
| `image-16-accessories.jpg` | i.ibb.co | JPEG | 1264x847 | 74,385 | downloaded | 9 (8 files) |
| `construction_haulage_1790967998627.jpg` | local (pre-existing) | JPEG | 1200x896 | 970,804 | pre-existing | 5 (3 files) |
| `avatar-operations-lead-256.jpg` | images.unsplash.com | JPEG | 256x256 | 11,323 | downloaded | 1 (1 file) |
| `avatar-dispatch-director-256.jpg` | images.unsplash.com | JPEG | 256x256 | 13,747 | downloaded | 1 (1 file) |
| `avatar-safety-specialist-256.jpg` | images.unsplash.com | JPEG | 256x256 | 16,303 | downloaded | 1 (1 file) |
| `avatar-fleet-manager-256.jpg` | images.unsplash.com | JPEG | 256x256 | 17,181 | downloaded | 1 (1 file) |
| `avatar-support-officer-256.jpg` | images.unsplash.com | JPEG | 256x256 | 13,442 | downloaded | 1 (1 file) |
| `haulage-highway-freight-800.jpg` | images.unsplash.com | JPEG | 800x530 | 88,419 | downloaded | 1 (1 file) |
| `haulage-highway-freight-1200.jpg` | images.unsplash.com | JPEG | 1200x795 | 178,804 | downloaded | 1 (1 file) |
| `haulage-highway-freight-1920.jpg` | images.unsplash.com | JPEG | 1920x1273 | 399,192 | downloaded | 1 (1 file) |
| `haulage-container-port-600.jpg` | images.unsplash.com | JPEG | 600x400 | 49,599 | downloaded | 1 (1 file) |
| `haulage-container-port-800.jpg` | images.unsplash.com | JPEG | 800x533 | 79,299 | downloaded | 1 (1 file) |
| `haulage-container-port-1920.jpg` | images.unsplash.com | JPEG | 1920x1280 | 342,184 | downloaded | 1 (1 file) |
| `haulage-heavy-logistics-600.jpg` | images.unsplash.com | JPEG | 600x400 | 27,668 | downloaded | 1 (1 file) |
| `haulage-heavy-logistics-800.jpg` | images.unsplash.com | JPEG | 800x533 | 43,194 | downloaded | 1 (1 file) |
| `haulage-industrial-machinery-800.jpg` | images.unsplash.com | JPEG | 800x533 | 71,514 | downloaded | 1 (1 file) |
| `haulage-warehouse-cargo-800.jpg` | images.unsplash.com | JPEG | 800x533 | 164,562 | downloaded | 1 (1 file) |
| `haulage-shipping-containers-600.jpg` | images.unsplash.com | JPEG | 600x400 | 73,849 | downloaded | 1 (1 file) |
| `haulage-shipping-containers-800.jpg` | images.unsplash.com | JPEG | 800x533 | 117,304 | downloaded | 1 (1 file) |
| `haulage-bulk-freight-800.jpg` | images.unsplash.com | JPEG | 800x415 | 52,395 | downloaded | 1 (1 file) |
| `haulage-semi-truck-600.jpg` | images.unsplash.com | JPEG | 600x400 | 49,741 | downloaded | 1 (1 file) |
| `haulage-semi-truck-800.jpg` | images.unsplash.com | JPEG | 800x533 | 81,296 | downloaded | 2 (2 files) |
| `haulage-semi-truck-1920.jpg` | images.unsplash.com | JPEG | 1920x1280 | 390,198 | downloaded | 1 (1 file) |
| `haulage-commercial-fleet-800.jpg` | images.unsplash.com | JPEG | 800x1199 | 127,196 | downloaded | 1 (1 file) |

---

## Placeholders Left Intentionally Untouched

1. **`placehold.co` (`onError` preview fallbacks in Admin Inventory — 5 references):**
   - `src/admin/pages/inventory/CarManager.tsx:222` (`https://placehold.co/120x80?text=Car`)
   - `src/admin/pages/inventory/GalleryManager.tsx:196` (`https://placehold.co/120x80?text=Gallery`)
   - `src/admin/pages/inventory/ImageGalleryField.tsx:114` (`https://placehold.co/100x75?text=Image`)
   - `src/admin/pages/inventory/PartManager.tsx:226` (`https://placehold.co/120x80?text=Part`)
   - `src/admin/pages/inventory/RentalManager.tsx:226` (`https://placehold.co/120x80?text=Rental`)
2. **`example.com` (input field `placeholder` hints — 4 references):**
   - `src/admin/components/AdminImagePicker.tsx:157` (`https://example.com/image.jpg`)
   - `src/admin/components/UnifiedImageModal.tsx:388` (`https://example.com/images/tow-truck.jpg`)
   - `src/admin/pages/AdminLogo.tsx:374` (`https://example.com/logo.png`)
   - `src/components/editable/ImageEditModal.tsx:349` (`https://example.com/photo.jpg`)
3. **`https://...` (input field `placeholder` hint — 1 image reference):**
   - `src/admin/pages/inventory/ImageGalleryField.tsx:146` (`placeholder="https://..."`)

---

## Items Needing Manual Attention

1. **Existing Browser `localStorage` & `IndexedDB` Caches:**
   - Returning admin browsers that already wrote custom overrides to `localStorage` (`santa_towing_custom_content_v1`) or seeded Image Studio (`santa_image_studio_db` / `santa_studio_images_fallback_v1`) prior to this change may retain previously stored external image URLs until cleared or reset via **Admin -> Media & Images -> Reset all images**. Fresh sessions automatically use the local bundled images.
2. **Offline Admin `onError` Fallbacks (`placehold.co`):**
   - The 5 `placehold.co` URLs in `src/admin/pages/inventory/` remain external fallback URLs for invalid admin-entered image links. If strict air-gapped offline operation is needed for broken preview thumbnails, swap them to an inline data-URI SVG placeholder.
3. **Favicon & Large Logo File Size (`santa-logo.png`):**
   - `santa-logo.png` is preserved at its original 1182x574 PNG resolution (1,002,585 bytes) per instructions not to alter sizing or quality; consider generating an optimized `.ico` / 192x192 favicon separately if smaller initial `<head>` icon payloads are desired later.
4. **`image-manifest.json` Original URL Field Note:**
   - The 41 source external URLs were downloaded, verified on disk, and all code references were replaced prior to session compaction; `image-manifest.json` records each file's verified source host (`i.ibb.co` vs `images.unsplash.com`), exact format, dimensions, byte size, and complete codebase `whereUsed` mapping without fabricating exact URL hashes that were not retained verbatim post-compaction.
