# URL & SEO Migration Map — Santa Towing React SPA → WordPress

Complete 1:1 URL parity blueprint, numeric ID rewrite strategy, `301` redirect matrix, SEO metadata / Schema JSON-LD architecture, `robots.txt`, and dynamic XML sitemap plan.

---

## 1. Core URL Parity & Rewrite Strategy

### 1.1 Solving Numeric Inventory & Blog URLs in WordPress (`/sales/1`, `/rental/1`, `/parts/1`, `/blog/1`)
WordPress natively routes custom post types and posts by URL slug (e.g., `/sales/2021-nissan-almera/`), whereas the React app routes detail views strictly by integer ID:
- `/sales/1` .. `/sales/12`
- `/rental/1` .. `/rental/6`
- `/parts/1` .. `/parts/6`
- `/blog/1` .. `/blog/6`

To guarantee **zero 404s** and **100% URL parity** without sacrificing SEO-friendly slugs:
1. Every migrated record stores its original integer ID in post meta (`_st_legacy_id = "1"`).
2. Custom rewrite rules in `santa-towing-core`:
   ```php
   add_rewrite_rule('^sales/([0-9]+)/?$', 'index.php?post_type=st_car&st_legacy_id=$matches[1]', 'top');
   add_rewrite_rule('^rental/([0-9]+)/?$', 'index.php?post_type=st_rental&st_legacy_id=$matches[1]', 'top');
   add_rewrite_rule('^parts/([0-9]+)/?$', 'index.php?post_type=st_part&st_legacy_id=$matches[1]', 'top');
   add_rewrite_rule('^blog/([0-9]+)/?$', 'index.php?post_type=post&st_legacy_id=$matches[1]', 'top');
   ```
3. On `pre_get_posts`, if `st_legacy_id` is set, WordPress resolves the matching CPT/post directly (`200 OK`, preserving exact legacy URL `/sales/1/`). **Alternatively**, if descriptive slugs (`/sales/toyota-corolla-2020/`) are used for newly added WP inventory items, both numeric legacy URLs and clean slug URLs work seamlessly side by side with accurate `<link rel="canonical">`.

### 1.2 Service Area Multi-Layer Routing Priority (`/service-areas/*`)
WordPress resolves `/service-areas/*` in strict deterministic order:
1. **Hub Index:** `/service-areas/` → Page (`ServiceAreas`).
2. **Localized Advert × Service Sub-Route:** `^service-areas/([^/]+)/([^/]+)/?$` → Query vars `st_area_slug=$matches[1]&st_service_slug=$matches[2]` resolving all **32 `TOWING_ADVERTS`** districts × **4 standard services** (`emergency-towing`, `scheduled-towing`, `car-recovery`, `long-distance-towing`) plus the `vehicle-recovery` → `car-recovery` 301/alias.
3. **261 Explicit District & Corridor Pages (`st_service_area` CPT):** Native CPT permalink `/service-areas/%st_service_area%/` resolves all **35 Local** + **226 Corridor** records directly via WP Query (`200 OK`).
4. **Dynamic Unlisted Town / 16-Region Fallback:** Via `template_redirect` / `404` intercept on `^service-areas/([^/]+)/?$`, if slug matches any of the **16 regions / 232 localities** in `GHANA_REGIONS_DATA` or **32 districts** in `TOWING_ADVERTS`, render `ServiceAreaDetails` template cleanly with `200 OK`; otherwise return genuine HTTP `404`.

---

## 2. Top-Level & Dynamic Public URL Mapping Table

| Source React URL | WordPress Target URL | Status | WP Object Type | Canonical Rule | SEO Title Source / Value | Schema JSON-LD |
|---|---|---|---|---|---|---|
| `/` | `/` | `200` | Static Front Page | `https://santatowingservices.com/` | `seo.home.title` | `AutoRepair` / `LocalBusiness` + `Organization` |
| `/services` | `/services/` | `200` | WP Page (`services`) | `/services/` | `seo.services.title` | `Service` |
| `/towing` | `/towing/` | `200` | WP Page (`towing`) | `/towing/` | `seo.towing.title` | `EmergencyService` |
| `/haulage` | `/haulage/` | `200` | WP Page (`haulage`) | `/haulage/` | `seo.haulage.title` | `Service` |
| `/rental` | `/rental/` | `200` | CPT Archive `st_rental` | `/rental/` | `seo.rental.title` | `CollectionPage` |
| `/rental/:id` (1..6) | `/rental/{id}/` | `200` | Single `st_rental` (`_st_legacy_id`) | `/rental/{id}/` | Dynamic Rental Title | `Product` / `Offer` |
| `/sales` | `/sales/` | `200` | CPT Archive `st_car` | `/sales/` | `seo.sales.title` | `CollectionPage` |
| `/sales/:id` (1..12) | `/sales/{id}/` | `200` | Single `st_car` (`_st_legacy_id`) | `/sales/{id}/` | `{Year} {Make} {Model} \| Santa Towing` | `Vehicle` / `Product` |
| `/repairs` | `/repairs/` | `200` | WP Page (`repairs`) | `/repairs/` | `seo.repairs.title` | `AutoRepair` |
| `/body-works` | `/body-works/` | `200` | WP Page (`body-works`) | `/body-works/` | `seo.bodyWorks.title` | `AutoBodyShop` |
| `/parts` | `/parts/` | `200` | CPT Archive `st_part` | `/parts/` | `seo.parts.title` | `CollectionPage` |
| `/parts/:id` (1..6) | `/parts/{id}/` | `200` | Single `st_part` (`_st_legacy_id`) | `/parts/{id}/` | `{Name} ({SKU}) \| Santa Towing` | `Product` |
| `/fleet` | `/fleet/` | `200` | WP Page (`fleet`) | `/fleet/` | `seo.fleet.title` | `Service` |
| `/blog` | `/blog/` | `200` | Posts Page (`blog`) | `/blog/` | `seo.blog.title` | `Blog` |
| `/blog/:id` (1..6) | `/blog/{id}/` | `200` | Single `post` (`_st_legacy_id`) | `/blog/{id}/` | Dynamic Post Title | **`Article` (existing in `BlogPost.tsx`)** |
| `/contact` | `/contact/` | `200` | WP Page (`contact`) | `/contact/` | `seo.contact.title` | `ContactPage` |
| `/search` | `/search/` | `200` | WP Search (`search.php`) | `/search/` | `Search Results \| Santa Towing` | `noindex, follow` |
| `/faqs` | `/faqs/` | `200` | WP Page (`faqs`) | `/faqs/` | `seo.faqs.title` | `FAQPage` (28 Q&A) |
| `/gallery` | `/gallery/` | `200` | WP Page (`gallery`) | `/gallery/` | `seo.gallery.title` | `ImageGallery` |
| `/careers` | `/careers/` | `200` | WP Page (`careers`) | `/careers/` | `seo.careers.title` | `JobPosting` (4 open roles) |
| `/sitemap` | `/sitemap/` | `200` | WP Page (`sitemap`) | `/sitemap/` | `Sitemap \| Santa Towing` | `WebPage` |
| `/service-areas` | `/service-areas/` | `200` | WP Page (`service-areas`) | `/service-areas/` | `seo.serviceAreas.title` | `CollectionPage` |
| `/service-areas/:areaSlug` | `/service-areas/{area}/` | `200` | `st_service_area` CPT (261) + 232 Town Fallback | `/service-areas/{area}/` | `Towing & Recovery Service in {Area}` | `Service` |
| `/service-areas/:areaSlug/:serviceSlug` | `/service-areas/{area}/{service}/` | `200` | Rewrite Endpoint (32 × 4 services; `vehicle-recovery` → `car-recovery`) | `/service-areas/{area}/{service}/` | `{Service} in {Area} \| Santa Towing` | `Service` |
| `/privacy-policy` .. `/accessibility-statement` (12 routes) | Identical slugs | `200` | 12 WP Pages (`template-policy.php`) | Exact slug match | `policies.*.title` | `WebPage` |
| `/admin/*` (15 screens) | `/wp-admin/` (plus optional `/admin/` redirect) | `302` Auth Gate | WP Admin Console | N/A | `noindex, nofollow` | Disallowed in `robots.txt` |
| `*` (Unknown URLs) | Unknown URLs | `404` | Theme `404.php` | None | `Page Not Found \| Santa Towing` | `noindex, nofollow` |

---

## 3. Permanent HTTP `301` Redirect Matrix (14 Legacy/Alias React Routes + SEO Cleanup)

Configured cleanly at both Apache `.htaccess` (for instant zero-PHP redirect speed) AND WordPress `template_redirect` fallback:

| Source Path (React `<Navigate replace>`) | Target WordPress URL | HTTP Status | Reason |
|---|---|---|---|
| `/vehicle-damage-liability-policy` | `/car-damage-liability-policy/` | `301` | Preserves React alias |
| `/vehicles` | `/sales/` | `301` | Preserves React alias |
| `/cars` | `/sales/` | `301` | Preserves React alias |
| `/car-sales` | `/sales/` | `301` | Preserves React alias |
| `/vehicle-sales` | `/sales/` | `301` | Preserves React alias |
| `/vehicles/:id` | `/sales/` | `301` | Preserves React pattern redirect |
| `/cars/:id` | `/sales/` | `301` | Preserves React pattern redirect |
| `/vehicle-details/:id` | `/sales/` | `301` | Preserves React pattern redirect |
| `/car-details/:id` | `/sales/` | `301` | Preserves React pattern redirect |
| `/car-rentals` | `/rental/` | `301` | Preserves React alias |
| `/vehicle-rentals` | `/rental/` | `301` | Preserves React alias |
| `/car-rental` | `/rental/` | `301` | Preserves React alias |
| `/vehicle-rental` | `/rental/` | `301` | Preserves React alias |
| `/about` | `/#about` | `301` | Preserves Home anchor jump |
| `/service-areas/{area}/vehicle-recovery` | `/service-areas/{area}/car-recovery/` | `301` | Consolidates alias handled in `ServiceAreaTowingService.tsx` |

---

## 4. `robots.txt`, OpenGraph, & Dynamic XML Sitemap Migration

### 4.1 Honest Audit Findings on Existing React SEO (`SEO.tsx` & `generate-sitemap.ts`)
1. **`SEO.tsx` lacks dynamic OpenGraph/Twitter tags:** Only updates `<title>`, `description`, `keywords`, `canonical`, and `robots` via client-side `useEffect` (crawlers not running JS only see generic `<title>` in `index.html`). **WP upgrade:** Server-rendered `wp_head` injects dynamic `<title>`, `meta[name="description"]`, `meta[name="keywords"]`, `<link rel="canonical">`, `meta[name="robots"]`, AND full `og:title`, `og:description`, `og:url`, `og:image`, `twitter:card` on initial HTML response!
2. **`public/sitemap.xml` is missing & `generate-sitemap.ts` has 3 bugs:** The static XML file was never generated into `public/`, misses `/sales/{id}` & `/parts/{id}` due to `mockData.ts` refactoring, and leaks `<Navigate>` redirect paths. **WP upgrade:** Replace the Node script with a dynamic PHP `/sitemap.xml` handler emitting:
   - All published Pages (`/` + services + 12 legal policies + directories)
   - Live published `st_car`, `st_rental`, `st_part`, & `post` URLs (numeric IDs + slugs)
   - All **261** `st_service_area` URLs
   - **32 `TOWING_ADVERTS` × 4** service sub-routes (`128` URLs)
   - **Zero** redirect URLs, **zero** `/search`, **zero** `/admin` or `/wp-admin` URLs.

### 4.2 Target `robots.txt`
```text
User-agent: *
Allow: /
Allow: /service-areas/
Allow: /wp-admin/admin-ajax.php

Disallow: /wp-admin/
Disallow: /wp-login.php
Disallow: /admin
Disallow: /search

Crawl-delay: 1
Sitemap: https://santatowingservices.com/sitemap.xml
```

---

## 5. Complete URL Map — All 261 Static Service Area & Corridor Pages (`st_service_area` CPT)

| # | Legacy React Route | WordPress Target URL | HTTP Status & Handler | Source SEO Title |
|---|---|---|---|---|
| 1 | `/service-areas/abeka` | `/service-areas/abeka/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Abeka |
| 2 | `/service-areas/abuakwa-to-accra` | `/service-areas/abuakwa-to-accra/` | `200` (CPT `st_service_area`) | Abuakwa to Accra Towing & Car Recovery Service |
| 3 | `/service-areas/aburi-to-accra` | `/service-areas/aburi-to-accra/` | `200` (CPT `st_service_area`) | Aburi to Accra Towing & Car Recovery Service |
| 4 | `/service-areas/accra` | `/service-areas/accra/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Accra |
| 5 | `/service-areas/accra-to-abuakwa` | `/service-areas/accra-to-abuakwa/` | `200` (CPT `st_service_area`) | Accra to Abuakwa Towing & Car Recovery Service |
| 6 | `/service-areas/accra-to-aburi` | `/service-areas/accra-to-aburi/` | `200` (CPT `st_service_area`) | Accra to Aburi Towing & Car Recovery Service |
| 7 | `/service-areas/accra-to-aflao` | `/service-areas/accra-to-aflao/` | `200` (CPT `st_service_area`) | Accra to Aflao Towing & Car Recovery Service |
| 8 | `/service-areas/accra-to-agona` | `/service-areas/accra-to-agona/` | `200` (CPT `st_service_area`) | Accra to Agona Towing & Car Recovery Service |
| 9 | `/service-areas/accra-to-agona-nkwanta` | `/service-areas/accra-to-agona-nkwanta/` | `200` (CPT `st_service_area`) | Accra to Agona Nkwanta Towing & Car Recovery Service |
| 10 | `/service-areas/accra-to-akontombra` | `/service-areas/accra-to-akontombra/` | `200` (CPT `st_service_area`) | Accra to Akontombra Towing & Car Recovery Service |
| 11 | `/service-areas/accra-to-akropong` | `/service-areas/accra-to-akropong/` | `200` (CPT `st_service_area`) | Accra to Akropong Towing & Car Recovery Service |
| 12 | `/service-areas/accra-to-akwatia` | `/service-areas/accra-to-akwatia/` | `200` (CPT `st_service_area`) | Accra to Akwatia Towing & Car Recovery Service |
| 13 | `/service-areas/accra-to-akyem-oda` | `/service-areas/accra-to-akyem-oda/` | `200` (CPT `st_service_area`) | Accra to Akyem Oda Towing & Car Recovery Service |
| 14 | `/service-areas/accra-to-apam` | `/service-areas/accra-to-apam/` | `200` (CPT `st_service_area`) | Accra to Apam Towing & Car Recovery Service |
| 15 | `/service-areas/accra-to-asamankese` | `/service-areas/accra-to-asamankese/` | `200` (CPT `st_service_area`) | Accra to Asamankese Towing & Car Recovery Service |
| 16 | `/service-areas/accra-to-asankragwa` | `/service-areas/accra-to-asankragwa/` | `200` (CPT `st_service_area`) | Accra to Asankragwa Towing & Car Recovery Service |
| 17 | `/service-areas/accra-to-asante-akim` | `/service-areas/accra-to-asante-akim/` | `200` (CPT `st_service_area`) | Accra to Asante Akim Towing & Car Recovery Service |
| 18 | `/service-areas/accra-to-assin-fosu` | `/service-areas/accra-to-assin-fosu/` | `200` (CPT `st_service_area`) | Accra to Assin Fosu Towing & Car Recovery Service |
| 19 | `/service-areas/accra-to-atebubu` | `/service-areas/accra-to-atebubu/` | `200` (CPT `st_service_area`) | Accra to Atebubu Towing & Car Recovery Service |
| 20 | `/service-areas/accra-to-atonsu` | `/service-areas/accra-to-atonsu/` | `200` (CPT `st_service_area`) | Accra to Atonsu Towing & Car Recovery Service |
| 21 | `/service-areas/accra-to-axim` | `/service-areas/accra-to-axim/` | `200` (CPT `st_service_area`) | Accra to Axim Towing & Car Recovery Service |
| 22 | `/service-areas/accra-to-bawdie` | `/service-areas/accra-to-bawdie/` | `200` (CPT `st_service_area`) | Accra to Bawdie Towing & Car Recovery Service |
| 23 | `/service-areas/accra-to-bawku` | `/service-areas/accra-to-bawku/` | `200` (CPT `st_service_area`) | Accra to Bawku Towing & Car Recovery Service |
| 24 | `/service-areas/accra-to-bechem` | `/service-areas/accra-to-bechem/` | `200` (CPT `st_service_area`) | Accra to Bechem Towing & Car Recovery Service |
| 25 | `/service-areas/accra-to-begoro` | `/service-areas/accra-to-begoro/` | `200` (CPT `st_service_area`) | Accra to Begoro Towing & Car Recovery Service |
| 26 | `/service-areas/accra-to-bekwai` | `/service-areas/accra-to-bekwai/` | `200` (CPT `st_service_area`) | Accra to Bekwai Towing & Car Recovery Service |
| 27 | `/service-areas/accra-to-berekum` | `/service-areas/accra-to-berekum/` | `200` (CPT `st_service_area`) | Accra to Berekum Towing & Car Recovery Service |
| 28 | `/service-areas/accra-to-bibiani` | `/service-areas/accra-to-bibiani/` | `200` (CPT `st_service_area`) | Accra to Bibiani Towing & Car Recovery Service |
| 29 | `/service-areas/accra-to-bodi` | `/service-areas/accra-to-bodi/` | `200` (CPT `st_service_area`) | Accra to Bodi Towing & Car Recovery Service |
| 30 | `/service-areas/accra-to-bogoso` | `/service-areas/accra-to-bogoso/` | `200` (CPT `st_service_area`) | Accra to Bogoso Towing & Car Recovery Service |
| 31 | `/service-areas/accra-to-bole` | `/service-areas/accra-to-bole/` | `200` (CPT `st_service_area`) | Accra to Bole Towing & Car Recovery Service |
| 32 | `/service-areas/accra-to-bolgatanga` | `/service-areas/accra-to-bolgatanga/` | `200` (CPT `st_service_area`) | Accra to Bolgatanga Towing & Car Recovery Service |
| 33 | `/service-areas/accra-to-buipe` | `/service-areas/accra-to-buipe/` | `200` (CPT `st_service_area`) | Accra to Buipe Towing & Car Recovery Service |
| 34 | `/service-areas/accra-to-cape-coast` | `/service-areas/accra-to-cape-coast/` | `200` (CPT `st_service_area`) | Accra to Cape Coast Towing & Car Recovery Service |
| 35 | `/service-areas/accra-to-daboase` | `/service-areas/accra-to-daboase/` | `200` (CPT `st_service_area`) | Accra to Daboase Towing & Car Recovery Service |
| 36 | `/service-areas/accra-to-daboya` | `/service-areas/accra-to-daboya/` | `200` (CPT `st_service_area`) | Accra to Daboya Towing & Car Recovery Service |
| 37 | `/service-areas/accra-to-dadieso` | `/service-areas/accra-to-dadieso/` | `200` (CPT `st_service_area`) | Accra to Dadieso Towing & Car Recovery Service |
| 38 | `/service-areas/accra-to-dambai` | `/service-areas/accra-to-dambai/` | `200` (CPT `st_service_area`) | Accra to Dambai Towing & Car Recovery Service |
| 39 | `/service-areas/accra-to-damongo` | `/service-areas/accra-to-damongo/` | `200` (CPT `st_service_area`) | Accra to Damongo Towing & Car Recovery Service |
| 40 | `/service-areas/accra-to-donkorkrom` | `/service-areas/accra-to-donkorkrom/` | `200` (CPT `st_service_area`) | Accra to Donkorkrom Towing & Car Recovery Service |
| 41 | `/service-areas/accra-to-dormaa-ahenkro` | `/service-areas/accra-to-dormaa-ahenkro/` | `200` (CPT `st_service_area`) | Accra to Dormaa Ahenkro Towing & Car Recovery Service |
| 42 | `/service-areas/accra-to-duayaw-nkwanta` | `/service-areas/accra-to-duayaw-nkwanta/` | `200` (CPT `st_service_area`) | Accra to Duayaw Nkwanta Towing & Car Recovery Service |
| 43 | `/service-areas/accra-to-dunkwa-on-offin` | `/service-areas/accra-to-dunkwa-on-offin/` | `200` (CPT `st_service_area`) | Accra to Dunkwa-on-Offin Towing & Car Recovery Service |
| 44 | `/service-areas/accra-to-ejisu` | `/service-areas/accra-to-ejisu/` | `200` (CPT `st_service_area`) | Accra to Ejisu Towing & Car Recovery Service |
| 45 | `/service-areas/accra-to-elmina` | `/service-areas/accra-to-elmina/` | `200` (CPT `st_service_area`) | Accra to Elmina Towing & Car Recovery Service |
| 46 | `/service-areas/accra-to-elubo` | `/service-areas/accra-to-elubo/` | `200` (CPT `st_service_area`) | Accra to Elubo Towing & Car Recovery Service |
| 47 | `/service-areas/accra-to-enchi` | `/service-areas/accra-to-enchi/` | `200` (CPT `st_service_area`) | Accra to Enchi Towing & Car Recovery Service |
| 48 | `/service-areas/accra-to-fomena` | `/service-areas/accra-to-fomena/` | `200` (CPT `st_service_area`) | Accra to Fomena Towing & Car Recovery Service |
| 49 | `/service-areas/accra-to-fumbisi` | `/service-areas/accra-to-fumbisi/` | `200` (CPT `st_service_area`) | Accra to Fumbisi Towing & Car Recovery Service |
| 50 | `/service-areas/accra-to-gambaga` | `/service-areas/accra-to-gambaga/` | `200` (CPT `st_service_area`) | Accra to Gambaga Towing & Car Recovery Service |
| 51 | `/service-areas/accra-to-goaso` | `/service-areas/accra-to-goaso/` | `200` (CPT `st_service_area`) | Accra to Goaso Towing & Car Recovery Service |
| 52 | `/service-areas/accra-to-half-assini` | `/service-areas/accra-to-half-assini/` | `200` (CPT `st_service_area`) | Accra to Half Assini Towing & Car Recovery Service |
| 53 | `/service-areas/accra-to-hamile` | `/service-areas/accra-to-hamile/` | `200` (CPT `st_service_area`) | Accra to Hamile Towing & Car Recovery Service |
| 54 | `/service-areas/accra-to-ho` | `/service-areas/accra-to-ho/` | `200` (CPT `st_service_area`) | Accra to Ho Towing & Car Recovery Service |
| 55 | `/service-areas/accra-to-hohoe` | `/service-areas/accra-to-hohoe/` | `200` (CPT `st_service_area`) | Accra to Hohoe Towing & Car Recovery Service |
| 56 | `/service-areas/accra-to-hwidiem` | `/service-areas/accra-to-hwidiem/` | `200` (CPT `st_service_area`) | Accra to Hwidiem Towing & Car Recovery Service |
| 57 | `/service-areas/accra-to-jasikan` | `/service-areas/accra-to-jasikan/` | `200` (CPT `st_service_area`) | Accra to Jasikan Towing & Car Recovery Service |
| 58 | `/service-areas/accra-to-jirapa` | `/service-areas/accra-to-jirapa/` | `200` (CPT `st_service_area`) | Accra to Jirapa Towing & Car Recovery Service |
| 59 | `/service-areas/accra-to-juaben` | `/service-areas/accra-to-juaben/` | `200` (CPT `st_service_area`) | Accra to Juaben Towing & Car Recovery Service |
| 60 | `/service-areas/accra-to-juaboso` | `/service-areas/accra-to-juaboso/` | `200` (CPT `st_service_area`) | Accra to Juaboso Towing & Car Recovery Service |
| 61 | `/service-areas/accra-to-kadjebi` | `/service-areas/accra-to-kadjebi/` | `200` (CPT `st_service_area`) | Accra to Kadjebi Towing & Car Recovery Service |
| 62 | `/service-areas/accra-to-karaga` | `/service-areas/accra-to-karaga/` | `200` (CPT `st_service_area`) | Accra to Karaga Towing & Car Recovery Service |
| 63 | `/service-areas/accra-to-kenyasi` | `/service-areas/accra-to-kenyasi/` | `200` (CPT `st_service_area`) | Accra to Kenyasi Towing & Car Recovery Service |
| 64 | `/service-areas/accra-to-keta` | `/service-areas/accra-to-keta/` | `200` (CPT `st_service_area`) | Accra to Keta Towing & Car Recovery Service |
| 65 | `/service-areas/accra-to-kete-krachi` | `/service-areas/accra-to-kete-krachi/` | `200` (CPT `st_service_area`) | Accra to Kete Krachi Towing & Car Recovery Service |
| 66 | `/service-areas/accra-to-kintampo` | `/service-areas/accra-to-kintampo/` | `200` (CPT `st_service_area`) | Accra to Kintampo Towing & Car Recovery Service |
| 67 | `/service-areas/accra-to-koforidua` | `/service-areas/accra-to-koforidua/` | `200` (CPT `st_service_area`) | Accra to Koforidua Towing & Car Recovery Service |
| 68 | `/service-areas/accra-to-konongo` | `/service-areas/accra-to-konongo/` | `200` (CPT `st_service_area`) | Accra to Konongo Towing & Car Recovery Service |
| 69 | `/service-areas/accra-to-kumasi` | `/service-areas/accra-to-kumasi/` | `200` (CPT `st_service_area`) | Accra to Kumasi Towing & Car Recovery Service |
| 70 | `/service-areas/accra-to-kwame-danso` | `/service-areas/accra-to-kwame-danso/` | `200` (CPT `st_service_area`) | Accra to Kwame Danso Towing & Car Recovery Service |
| 71 | `/service-areas/accra-to-lawra` | `/service-areas/accra-to-lawra/` | `200` (CPT `st_service_area`) | Accra to Lawra Towing & Car Recovery Service |
| 72 | `/service-areas/accra-to-mampong` | `/service-areas/accra-to-mampong/` | `200` (CPT `st_service_area`) | Accra to Mampong Towing & Car Recovery Service |
| 73 | `/service-areas/accra-to-mankessim` | `/service-areas/accra-to-mankessim/` | `200` (CPT `st_service_area`) | Accra to Mankessim Towing & Car Recovery Service |
| 74 | `/service-areas/accra-to-mim` | `/service-areas/accra-to-mim/` | `200` (CPT `st_service_area`) | Accra to Mim Towing & Car Recovery Service |
| 75 | `/service-areas/accra-to-mpohor` | `/service-areas/accra-to-mpohor/` | `200` (CPT `st_service_area`) | Accra to Mpohor Towing & Car Recovery Service |
| 76 | `/service-areas/accra-to-nalerigu` | `/service-areas/accra-to-nalerigu/` | `200` (CPT `st_service_area`) | Accra to Nalerigu Towing & Car Recovery Service |
| 77 | `/service-areas/accra-to-nandom` | `/service-areas/accra-to-nandom/` | `200` (CPT `st_service_area`) | Accra to Nandom Towing & Car Recovery Service |
| 78 | `/service-areas/accra-to-navrongo` | `/service-areas/accra-to-navrongo/` | `200` (CPT `st_service_area`) | Accra to Navrongo Towing & Car Recovery Service |
| 79 | `/service-areas/accra-to-nkawie` | `/service-areas/accra-to-nkawie/` | `200` (CPT `st_service_area`) | Accra to Nkawie Towing & Car Recovery Service |
| 80 | `/service-areas/accra-to-nkawkaw` | `/service-areas/accra-to-nkawkaw/` | `200` (CPT `st_service_area`) | Accra to Nkawkaw Towing & Car Recovery Service |
| 81 | `/service-areas/accra-to-nkoranza` | `/service-areas/accra-to-nkoranza/` | `200` (CPT `st_service_area`) | Accra to Nkoranza Towing & Car Recovery Service |
| 82 | `/service-areas/accra-to-nkwanta` | `/service-areas/accra-to-nkwanta/` | `200` (CPT `st_service_area`) | Accra to Nkwanta Towing & Car Recovery Service |
| 83 | `/service-areas/accra-to-nkwanta-south` | `/service-areas/accra-to-nkwanta-south/` | `200` (CPT `st_service_area`) | Accra to Nkwanta South Towing & Car Recovery Service |
| 84 | `/service-areas/accra-to-nsawam` | `/service-areas/accra-to-nsawam/` | `200` (CPT `st_service_area`) | Accra to Nsawam Towing & Car Recovery Service |
| 85 | `/service-areas/accra-to-obuasi` | `/service-areas/accra-to-obuasi/` | `200` (CPT `st_service_area`) | Accra to Obuasi Towing & Car Recovery Service |
| 86 | `/service-areas/accra-to-odumase-krobo` | `/service-areas/accra-to-odumase-krobo/` | `200` (CPT `st_service_area`) | Accra to Odumase-Krobo Towing & Car Recovery Service |
| 87 | `/service-areas/accra-to-offinso` | `/service-areas/accra-to-offinso/` | `200` (CPT `st_service_area`) | Accra to Offinso Towing & Car Recovery Service |
| 88 | `/service-areas/accra-to-paga` | `/service-areas/accra-to-paga/` | `200` (CPT `st_service_area`) | Accra to Paga Towing & Car Recovery Service |
| 89 | `/service-areas/accra-to-prang` | `/service-areas/accra-to-prang/` | `200` (CPT `st_service_area`) | Accra to Prang Towing & Car Recovery Service |
| 90 | `/service-areas/accra-to-prestea` | `/service-areas/accra-to-prestea/` | `200` (CPT `st_service_area`) | Accra to Prestea Towing & Car Recovery Service |
| 91 | `/service-areas/accra-to-salaga` | `/service-areas/accra-to-salaga/` | `200` (CPT `st_service_area`) | Accra to Salaga Towing & Car Recovery Service |
| 92 | `/service-areas/accra-to-samreboi` | `/service-areas/accra-to-samreboi/` | `200` (CPT `st_service_area`) | Accra to Samreboi Towing & Car Recovery Service |
| 93 | `/service-areas/accra-to-sandema` | `/service-areas/accra-to-sandema/` | `200` (CPT `st_service_area`) | Accra to Sandema Towing & Car Recovery Service |
| 94 | `/service-areas/accra-to-savelugu` | `/service-areas/accra-to-savelugu/` | `200` (CPT `st_service_area`) | Accra to Savelugu Towing & Car Recovery Service |
| 95 | `/service-areas/accra-to-sefwi-bekwai` | `/service-areas/accra-to-sefwi-bekwai/` | `200` (CPT `st_service_area`) | Accra to Sefwi Bekwai Towing & Car Recovery Service |
| 96 | `/service-areas/accra-to-sefwi-wiawso` | `/service-areas/accra-to-sefwi-wiawso/` | `200` (CPT `st_service_area`) | Accra to Sefwi Wiawso Towing & Car Recovery Service |
| 97 | `/service-areas/accra-to-sekondi` | `/service-areas/accra-to-sekondi/` | `200` (CPT `st_service_area`) | Accra to Sekondi Towing & Vehicle Recovery Service |
| 98 | `/service-areas/accra-to-shama` | `/service-areas/accra-to-shama/` | `200` (CPT `st_service_area`) | Accra to Shama Towing & Vehicle Recovery Service |
| 99 | `/service-areas/accra-to-sogakope` | `/service-areas/accra-to-sogakope/` | `200` (CPT `st_service_area`) | Accra to Sogakope Towing & Vehicle Recovery Service |
| 100 | `/service-areas/accra-to-somanya` | `/service-areas/accra-to-somanya/` | `200` (CPT `st_service_area`) | Accra to Somanya Towing & Vehicle Recovery Service |
| 101 | `/service-areas/accra-to-suhum` | `/service-areas/accra-to-suhum/` | `200` (CPT `st_service_area`) | Accra to Suhum Towing & Vehicle Recovery Service |
| 102 | `/service-areas/accra-to-sunyani` | `/service-areas/accra-to-sunyani/` | `200` (CPT `st_service_area`) | Accra to Sunyani Towing & Vehicle Recovery Service |
| 103 | `/service-areas/accra-to-takoradi` | `/service-areas/accra-to-takoradi/` | `200` (CPT `st_service_area`) | Accra to Takoradi Towing & Vehicle Recovery Service |
| 104 | `/service-areas/accra-to-tamale` | `/service-areas/accra-to-tamale/` | `200` (CPT `st_service_area`) | Accra to Tamale Towing & Vehicle Recovery Service |
| 105 | `/service-areas/accra-to-tarkwa` | `/service-areas/accra-to-tarkwa/` | `200` (CPT `st_service_area`) | Accra to Tarkwa Towing & Vehicle Recovery Service |
| 106 | `/service-areas/accra-to-techiman` | `/service-areas/accra-to-techiman/` | `200` (CPT `st_service_area`) | Accra to Techiman Towing & Vehicle Recovery Service |
| 107 | `/service-areas/accra-to-tepa` | `/service-areas/accra-to-tepa/` | `200` (CPT `st_service_area`) | Accra to Tepa Towing & Vehicle Recovery Service |
| 108 | `/service-areas/accra-to-tolon` | `/service-areas/accra-to-tolon/` | `200` (CPT `st_service_area`) | Accra to Tolon Towing & Vehicle Recovery Service |
| 109 | `/service-areas/accra-to-tumu` | `/service-areas/accra-to-tumu/` | `200` (CPT `st_service_area`) | Accra to Tumu Towing & Vehicle Recovery Service |
| 110 | `/service-areas/accra-to-wa` | `/service-areas/accra-to-wa/` | `200` (CPT `st_service_area`) | Accra to Wa Towing & Vehicle Recovery Service |
| 111 | `/service-areas/accra-to-walewale` | `/service-areas/accra-to-walewale/` | `200` (CPT `st_service_area`) | Accra to Walewale Towing & Vehicle Recovery Service |
| 112 | `/service-areas/accra-to-wassa-akropong` | `/service-areas/accra-to-wassa-akropong/` | `200` (CPT `st_service_area`) | Accra to Wassa Akropong Towing & Vehicle Recovery Service |
| 113 | `/service-areas/accra-to-wenchi` | `/service-areas/accra-to-wenchi/` | `200` (CPT `st_service_area`) | Accra to Wenchi Towing & Vehicle Recovery Service |
| 114 | `/service-areas/accra-to-winneba` | `/service-areas/accra-to-winneba/` | `200` (CPT `st_service_area`) | Accra to Winneba Towing & Vehicle Recovery Service |
| 115 | `/service-areas/accra-to-yeji` | `/service-areas/accra-to-yeji/` | `200` (CPT `st_service_area`) | Accra to Yeji Towing & Vehicle Recovery Service |
| 116 | `/service-areas/accra-to-yendi` | `/service-areas/accra-to-yendi/` | `200` (CPT `st_service_area`) | Accra to Yendi Towing & Vehicle Recovery Service |
| 117 | `/service-areas/accra-to-zebilla` | `/service-areas/accra-to-zebilla/` | `200` (CPT `st_service_area`) | Accra to Zebilla Towing & Vehicle Recovery Service |
| 118 | `/service-areas/achimota` | `/service-areas/achimota/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Achimota |
| 119 | `/service-areas/adenta` | `/service-areas/adenta/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Adenta |
| 120 | `/service-areas/aflao-to-accra` | `/service-areas/aflao-to-accra/` | `200` (CPT `st_service_area`) | Aflao to Accra Towing & Vehicle Recovery Service |
| 121 | `/service-areas/agona-nkwanta-to-accra` | `/service-areas/agona-nkwanta-to-accra/` | `200` (CPT `st_service_area`) | Agona Nkwanta to Accra Towing & Vehicle Recovery Service |
| 122 | `/service-areas/agona-to-accra` | `/service-areas/agona-to-accra/` | `200` (CPT `st_service_area`) | Agona to Accra Towing & Vehicle Recovery Service |
| 123 | `/service-areas/airport-residential` | `/service-areas/airport-residential/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Airport Residential |
| 124 | `/service-areas/akontombra-to-accra` | `/service-areas/akontombra-to-accra/` | `200` (CPT `st_service_area`) | Akontombra to Accra Towing & Vehicle Recovery Service |
| 125 | `/service-areas/akropong-to-accra` | `/service-areas/akropong-to-accra/` | `200` (CPT `st_service_area`) | Akropong to Accra Towing & Vehicle Recovery Service |
| 126 | `/service-areas/akwatia-to-accra` | `/service-areas/akwatia-to-accra/` | `200` (CPT `st_service_area`) | Akwatia to Accra Towing & Vehicle Recovery Service |
| 127 | `/service-areas/akyem-oda-to-accra` | `/service-areas/akyem-oda-to-accra/` | `200` (CPT `st_service_area`) | Akyem Oda to Accra Towing & Vehicle Recovery Service |
| 128 | `/service-areas/amasaman` | `/service-areas/amasaman/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Amasaman |
| 129 | `/service-areas/apam-to-accra` | `/service-areas/apam-to-accra/` | `200` (CPT `st_service_area`) | Apam to Accra Towing & Vehicle Recovery Service |
| 130 | `/service-areas/asamankese-to-accra` | `/service-areas/asamankese-to-accra/` | `200` (CPT `st_service_area`) | Asamankese to Accra Towing & Vehicle Recovery Service |
| 131 | `/service-areas/asankragwa-to-accra` | `/service-areas/asankragwa-to-accra/` | `200` (CPT `st_service_area`) | Asankragwa to Accra Towing & Vehicle Recovery Service |
| 132 | `/service-areas/asante-akim-to-accra` | `/service-areas/asante-akim-to-accra/` | `200` (CPT `st_service_area`) | Asante Akim to Accra Towing & Vehicle Recovery Service |
| 133 | `/service-areas/ashaiman` | `/service-areas/ashaiman/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Ashaiman |
| 134 | `/service-areas/assin-fosu-to-accra` | `/service-areas/assin-fosu-to-accra/` | `200` (CPT `st_service_area`) | Assin Fosu to Accra Towing & Vehicle Recovery Service |
| 135 | `/service-areas/atebubu-to-accra` | `/service-areas/atebubu-to-accra/` | `200` (CPT `st_service_area`) | Atebubu to Accra Towing & Vehicle Recovery Service |
| 136 | `/service-areas/atonsu-to-accra` | `/service-areas/atonsu-to-accra/` | `200` (CPT `st_service_area`) | Atonsu to Accra Towing & Vehicle Recovery Service |
| 137 | `/service-areas/axim-to-accra` | `/service-areas/axim-to-accra/` | `200` (CPT `st_service_area`) | Axim to Accra Towing & Vehicle Recovery Service |
| 138 | `/service-areas/ayawaso` | `/service-areas/ayawaso/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Ayawaso |
| 139 | `/service-areas/bawdie-to-accra` | `/service-areas/bawdie-to-accra/` | `200` (CPT `st_service_area`) | Bawdie to Accra Towing & Vehicle Recovery Service |
| 140 | `/service-areas/bawku-to-accra` | `/service-areas/bawku-to-accra/` | `200` (CPT `st_service_area`) | Bawku to Accra Towing & Vehicle Recovery Service |
| 141 | `/service-areas/bechem-to-accra` | `/service-areas/bechem-to-accra/` | `200` (CPT `st_service_area`) | Bechem to Accra Towing & Vehicle Recovery Service |
| 142 | `/service-areas/begoro-to-accra` | `/service-areas/begoro-to-accra/` | `200` (CPT `st_service_area`) | Begoro to Accra Towing & Vehicle Recovery Service |
| 143 | `/service-areas/bekwai-to-accra` | `/service-areas/bekwai-to-accra/` | `200` (CPT `st_service_area`) | Bekwai to Accra Towing & Vehicle Recovery Service |
| 144 | `/service-areas/berekum-to-accra` | `/service-areas/berekum-to-accra/` | `200` (CPT `st_service_area`) | Berekum to Accra Towing & Vehicle Recovery Service |
| 145 | `/service-areas/bibiani-to-accra` | `/service-areas/bibiani-to-accra/` | `200` (CPT `st_service_area`) | Bibiani to Accra Towing & Vehicle Recovery Service |
| 146 | `/service-areas/bodi-to-accra` | `/service-areas/bodi-to-accra/` | `200` (CPT `st_service_area`) | Bodi to Accra Towing & Vehicle Recovery Service |
| 147 | `/service-areas/bogoso-to-accra` | `/service-areas/bogoso-to-accra/` | `200` (CPT `st_service_area`) | Bogoso to Accra Towing & Vehicle Recovery Service |
| 148 | `/service-areas/bolgatanga-to-accra` | `/service-areas/bolgatanga-to-accra/` | `200` (CPT `st_service_area`) | Bolgatanga to Accra Towing & Vehicle Recovery Service |
| 149 | `/service-areas/buipe-to-accra` | `/service-areas/buipe-to-accra/` | `200` (CPT `st_service_area`) | Buipe to Accra Towing & Vehicle Recovery Service |
| 150 | `/service-areas/burma-camp` | `/service-areas/burma-camp/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Burma Camp |
| 151 | `/service-areas/cape-coast-to-accra` | `/service-areas/cape-coast-to-accra/` | `200` (CPT `st_service_area`) | Cape Coast to Accra Towing & Vehicle Recovery Service |
| 152 | `/service-areas/circle` | `/service-areas/circle/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Circle |
| 153 | `/service-areas/daboase-to-accra` | `/service-areas/daboase-to-accra/` | `200` (CPT `st_service_area`) | Daboase to Accra Towing & Vehicle Recovery Service |
| 154 | `/service-areas/daboya-to-accra` | `/service-areas/daboya-to-accra/` | `200` (CPT `st_service_area`) | Daboya to Accra Towing & Vehicle Recovery Service |
| 155 | `/service-areas/dadieso-to-accra` | `/service-areas/dadieso-to-accra/` | `200` (CPT `st_service_area`) | Dadieso to Accra Towing & Vehicle Recovery Service |
| 156 | `/service-areas/dambai-to-accra` | `/service-areas/dambai-to-accra/` | `200` (CPT `st_service_area`) | Dambai to Accra Towing & Vehicle Recovery Service |
| 157 | `/service-areas/damongo-to-accra` | `/service-areas/damongo-to-accra/` | `200` (CPT `st_service_area`) | Damongo to Accra Towing & Vehicle Recovery Service |
| 158 | `/service-areas/dansoman` | `/service-areas/dansoman/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Dansoman |
| 159 | `/service-areas/dawhenya` | `/service-areas/dawhenya/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Dawhenya |
| 160 | `/service-areas/dodowa` | `/service-areas/dodowa/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Dodowa |
| 161 | `/service-areas/donkorkrom-to-accra` | `/service-areas/donkorkrom-to-accra/` | `200` (CPT `st_service_area`) | Donkorkrom to Accra Towing & Vehicle Recovery Service |
| 162 | `/service-areas/dormaa-ahenkro-to-accra` | `/service-areas/dormaa-ahenkro-to-accra/` | `200` (CPT `st_service_area`) | Dormaa Ahenkro to Accra Towing & Vehicle Recovery Service |
| 163 | `/service-areas/duayaw-nkwanta-to-accra` | `/service-areas/duayaw-nkwanta-to-accra/` | `200` (CPT `st_service_area`) | Duayaw Nkwanta to Accra Towing & Vehicle Recovery Service |
| 164 | `/service-areas/dunkwa-on-offin-to-accra` | `/service-areas/dunkwa-on-offin-to-accra/` | `200` (CPT `st_service_area`) | Dunkwa-on-Offin to Accra Towing & Vehicle Recovery Service |
| 165 | `/service-areas/dzorwulu` | `/service-areas/dzorwulu/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Dzorwulu |
| 166 | `/service-areas/east-legon` | `/service-areas/east-legon/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in East Legon |
| 167 | `/service-areas/ejisu-to-accra` | `/service-areas/ejisu-to-accra/` | `200` (CPT `st_service_area`) | Ejisu to Accra Towing & Vehicle Recovery Service |
| 168 | `/service-areas/elmina-to-accra` | `/service-areas/elmina-to-accra/` | `200` (CPT `st_service_area`) | Elmina to Accra Towing & Vehicle Recovery Service |
| 169 | `/service-areas/elubo-to-accra` | `/service-areas/elubo-to-accra/` | `200` (CPT `st_service_area`) | Elubo to Accra Towing & Vehicle Recovery Service |
| 170 | `/service-areas/enchi-to-accra` | `/service-areas/enchi-to-accra/` | `200` (CPT `st_service_area`) | Enchi to Accra Towing & Vehicle Recovery Service |
| 171 | `/service-areas/fomena-to-accra` | `/service-areas/fomena-to-accra/` | `200` (CPT `st_service_area`) | Fomena to Accra Towing & Vehicle Recovery Service |
| 172 | `/service-areas/fumbisi-to-accra` | `/service-areas/fumbisi-to-accra/` | `200` (CPT `st_service_area`) | Fumbisi to Accra Towing & Vehicle Recovery Service |
| 173 | `/service-areas/gambaga-to-accra` | `/service-areas/gambaga-to-accra/` | `200` (CPT `st_service_area`) | Gambaga to Accra Towing & Vehicle Recovery Service |
| 174 | `/service-areas/goaso-to-accra` | `/service-areas/goaso-to-accra/` | `200` (CPT `st_service_area`) | Goaso to Accra Towing & Vehicle Recovery Service |
| 175 | `/service-areas/gushegu-to-accra` | `/service-areas/gushegu-to-accra/` | `200` (CPT `st_service_area`) | Gushegu to Accra Towing & Vehicle Recovery Service |
| 176 | `/service-areas/half-assini-to-accra` | `/service-areas/half-assini-to-accra/` | `200` (CPT `st_service_area`) | Half Assini to Accra Towing & Vehicle Recovery Service |
| 177 | `/service-areas/hamile-to-accra` | `/service-areas/hamile-to-accra/` | `200` (CPT `st_service_area`) | Hamile to Accra Towing & Vehicle Recovery Service |
| 178 | `/service-areas/ho-to-accra` | `/service-areas/ho-to-accra/` | `200` (CPT `st_service_area`) | Ho to Accra Towing & Vehicle Recovery Service |
| 179 | `/service-areas/hohoe-to-accra` | `/service-areas/hohoe-to-accra/` | `200` (CPT `st_service_area`) | Hohoe to Accra Towing & Vehicle Recovery Service |
| 180 | `/service-areas/hwidiem-to-accra` | `/service-areas/hwidiem-to-accra/` | `200` (CPT `st_service_area`) | Hwidiem to Accra Towing & Vehicle Recovery Service |
| 181 | `/service-areas/jasikan-to-accra` | `/service-areas/jasikan-to-accra/` | `200` (CPT `st_service_area`) | Jasikan to Accra Towing & Vehicle Recovery Service |
| 182 | `/service-areas/jirapa-to-accra` | `/service-areas/jirapa-to-accra/` | `200` (CPT `st_service_area`) | Jirapa to Accra Towing & Vehicle Recovery Service |
| 183 | `/service-areas/juaben-to-accra` | `/service-areas/juaben-to-accra/` | `200` (CPT `st_service_area`) | Juaben to Accra Towing & Vehicle Recovery Service |
| 184 | `/service-areas/juaboso-to-accra` | `/service-areas/juaboso-to-accra/` | `200` (CPT `st_service_area`) | Juaboso to Accra Towing & Vehicle Recovery Service |
| 185 | `/service-areas/kadjebi-to-accra` | `/service-areas/kadjebi-to-accra/` | `200` (CPT `st_service_area`) | Kadjebi to Accra Towing & Vehicle Recovery Service |
| 186 | `/service-areas/kaneshie` | `/service-areas/kaneshie/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Kaneshie |
| 187 | `/service-areas/karaga-to-accra` | `/service-areas/karaga-to-accra/` | `200` (CPT `st_service_area`) | Karaga to Accra Towing & Vehicle Recovery Service |
| 188 | `/service-areas/kasoa` | `/service-areas/kasoa/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Kasoa |
| 189 | `/service-areas/kenyasi-to-accra` | `/service-areas/kenyasi-to-accra/` | `200` (CPT `st_service_area`) | Kenyasi to Accra Towing & Vehicle Recovery Service |
| 190 | `/service-areas/keta-to-accra` | `/service-areas/keta-to-accra/` | `200` (CPT `st_service_area`) | Keta to Accra Towing & Vehicle Recovery Service |
| 191 | `/service-areas/kete-krachi-to-accra` | `/service-areas/kete-krachi-to-accra/` | `200` (CPT `st_service_area`) | Kete Krachi to Accra Towing & Vehicle Recovery Service |
| 192 | `/service-areas/kintampo-to-accra` | `/service-areas/kintampo-to-accra/` | `200` (CPT `st_service_area`) | Kintampo to Accra Towing & Vehicle Recovery Service |
| 193 | `/service-areas/koforidua-to-accra` | `/service-areas/koforidua-to-accra/` | `200` (CPT `st_service_area`) | Koforidua to Accra Towing & Vehicle Recovery Service |
| 194 | `/service-areas/konongo-to-accra` | `/service-areas/konongo-to-accra/` | `200` (CPT `st_service_area`) | Konongo to Accra Towing & Vehicle Recovery Service |
| 195 | `/service-areas/korle-klottey` | `/service-areas/korle-klottey/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Korle Klottey |
| 196 | `/service-areas/kpone` | `/service-areas/kpone/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Kpone |
| 197 | `/service-areas/kumasi-to-accra` | `/service-areas/kumasi-to-accra/` | `200` (CPT `st_service_area`) | Kumasi to Accra Towing & Vehicle Recovery Service |
| 198 | `/service-areas/kwame-danso-to-accra` | `/service-areas/kwame-danso-to-accra/` | `200` (CPT `st_service_area`) | Kwame Danso to Accra Towing & Vehicle Recovery Service |
| 199 | `/service-areas/kwashieman` | `/service-areas/kwashieman/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Kwashieman |
| 200 | `/service-areas/la` | `/service-areas/la/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in La |
| 201 | `/service-areas/labadi` | `/service-areas/labadi/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Labadi |
| 202 | `/service-areas/lakeside` | `/service-areas/lakeside/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Lakeside |
| 203 | `/service-areas/lawra-to-accra` | `/service-areas/lawra-to-accra/` | `200` (CPT `st_service_area`) | Lawra to Accra Towing & Vehicle Recovery Service |
| 204 | `/service-areas/madina` | `/service-areas/madina/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Madina |
| 205 | `/service-areas/mampong-to-accra` | `/service-areas/mampong-to-accra/` | `200` (CPT `st_service_area`) | Mampong to Accra Towing & Vehicle Recovery Service |
| 206 | `/service-areas/mankessim-to-accra` | `/service-areas/mankessim-to-accra/` | `200` (CPT `st_service_area`) | Mankessim to Accra Towing & Vehicle Recovery Service |
| 207 | `/service-areas/mim-to-accra` | `/service-areas/mim-to-accra/` | `200` (CPT `st_service_area`) | Mim to Accra Towing & Vehicle Recovery Service |
| 208 | `/service-areas/mpohor-to-accra` | `/service-areas/mpohor-to-accra/` | `200` (CPT `st_service_area`) | Mpohor to Accra Towing & Vehicle Recovery Service |
| 209 | `/service-areas/nalerigu-to-accra` | `/service-areas/nalerigu-to-accra/` | `200` (CPT `st_service_area`) | Nalerigu to Accra Towing & Vehicle Recovery Service |
| 210 | `/service-areas/nandom-to-accra` | `/service-areas/nandom-to-accra/` | `200` (CPT `st_service_area`) | Nandom to Accra Towing & Vehicle Recovery Service |
| 211 | `/service-areas/navrongo-to-accra` | `/service-areas/navrongo-to-accra/` | `200` (CPT `st_service_area`) | Navrongo to Accra Towing & Vehicle Recovery Service |
| 212 | `/service-areas/nima` | `/service-areas/nima/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Nima |
| 213 | `/service-areas/ningo-prampram` | `/service-areas/ningo-prampram/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Ningo-Prampram |
| 214 | `/service-areas/nkawie-to-accra` | `/service-areas/nkawie-to-accra/` | `200` (CPT `st_service_area`) | Nkawie to Accra Towing & Vehicle Recovery Service |
| 215 | `/service-areas/nkawkaw-to-accra` | `/service-areas/nkawkaw-to-accra/` | `200` (CPT `st_service_area`) | Nkawkaw to Accra Towing & Vehicle Recovery Service |
| 216 | `/service-areas/nkoranza-to-accra` | `/service-areas/nkoranza-to-accra/` | `200` (CPT `st_service_area`) | Nkoranza to Accra Towing & Vehicle Recovery Service |
| 217 | `/service-areas/nkwanta-south-to-accra` | `/service-areas/nkwanta-south-to-accra/` | `200` (CPT `st_service_area`) | Nkwanta South to Accra Towing & Vehicle Recovery Service |
| 218 | `/service-areas/nkwanta-to-accra` | `/service-areas/nkwanta-to-accra/` | `200` (CPT `st_service_area`) | Nkwanta to Accra Towing & Vehicle Recovery Service |
| 219 | `/service-areas/nsawam-to-accra` | `/service-areas/nsawam-to-accra/` | `200` (CPT `st_service_area`) | Nsawam to Accra Towing & Vehicle Recovery Service |
| 220 | `/service-areas/nungua` | `/service-areas/nungua/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Nungua |
| 221 | `/service-areas/obuasi-to-accra` | `/service-areas/obuasi-to-accra/` | `200` (CPT `st_service_area`) | Obuasi to Accra Towing & Vehicle Recovery Service |
| 222 | `/service-areas/odumase-krobo-to-accra` | `/service-areas/odumase-krobo-to-accra/` | `200` (CPT `st_service_area`) | Odumase-Krobo to Accra Towing & Vehicle Recovery Service |
| 223 | `/service-areas/offinso-to-accra` | `/service-areas/offinso-to-accra/` | `200` (CPT `st_service_area`) | Offinso to Accra Towing & Vehicle Recovery Service |
| 224 | `/service-areas/osu` | `/service-areas/osu/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Osu |
| 225 | `/service-areas/paga-to-accra` | `/service-areas/paga-to-accra/` | `200` (CPT `st_service_area`) | Paga to Accra Towing & Vehicle Recovery Service |
| 226 | `/service-areas/pokuase` | `/service-areas/pokuase/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Pokuase |
| 227 | `/service-areas/prampram` | `/service-areas/prampram/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Prampram |
| 228 | `/service-areas/prang-to-accra` | `/service-areas/prang-to-accra/` | `200` (CPT `st_service_area`) | Prang to Accra Towing & Vehicle Recovery Service |
| 229 | `/service-areas/prestea-to-accra` | `/service-areas/prestea-to-accra/` | `200` (CPT `st_service_area`) | Prestea to Accra Towing & Vehicle Recovery Service |
| 230 | `/service-areas/salaga-to-accra` | `/service-areas/salaga-to-accra/` | `200` (CPT `st_service_area`) | Salaga to Accra Towing & Vehicle Recovery Service |
| 231 | `/service-areas/samreboi-to-accra` | `/service-areas/samreboi-to-accra/` | `200` (CPT `st_service_area`) | Samreboi to Accra Towing & Vehicle Recovery Service |
| 232 | `/service-areas/sandema-to-accra` | `/service-areas/sandema-to-accra/` | `200` (CPT `st_service_area`) | Sandema to Accra Towing & Vehicle Recovery Service |
| 233 | `/service-areas/savelugu-to-accra` | `/service-areas/savelugu-to-accra/` | `200` (CPT `st_service_area`) | Savelugu to Accra Towing & Vehicle Recovery Service |
| 234 | `/service-areas/sefwi-bekwai-to-accra` | `/service-areas/sefwi-bekwai-to-accra/` | `200` (CPT `st_service_area`) | Sefwi Bekwai to Accra Towing & Vehicle Recovery Service |
| 235 | `/service-areas/sefwi-wiawso-to-accra` | `/service-areas/sefwi-wiawso-to-accra/` | `200` (CPT `st_service_area`) | Sefwi Wiawso to Accra Towing & Vehicle Recovery Service |
| 236 | `/service-areas/sekondi-to-accra` | `/service-areas/sekondi-to-accra/` | `200` (CPT `st_service_area`) | Sekondi to Accra Towing & Vehicle Recovery Service |
| 237 | `/service-areas/shama-to-accra` | `/service-areas/shama-to-accra/` | `200` (CPT `st_service_area`) | Shama to Accra Towing & Vehicle Recovery Service |
| 238 | `/service-areas/sogakope-to-accra` | `/service-areas/sogakope-to-accra/` | `200` (CPT `st_service_area`) | Sogakope to Accra Towing & Vehicle Recovery Service |
| 239 | `/service-areas/somanya-to-accra` | `/service-areas/somanya-to-accra/` | `200` (CPT `st_service_area`) | Somanya to Accra Towing & Vehicle Recovery Service |
| 240 | `/service-areas/spintex` | `/service-areas/spintex/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Spintex |
| 241 | `/service-areas/suhum-to-accra` | `/service-areas/suhum-to-accra/` | `200` (CPT `st_service_area`) | Suhum to Accra Towing & Vehicle Recovery Service |
| 242 | `/service-areas/sunyani-to-accra` | `/service-areas/sunyani-to-accra/` | `200` (CPT `st_service_area`) | Sunyani to Accra Towing & Vehicle Recovery Service |
| 243 | `/service-areas/takoradi-to-accra` | `/service-areas/takoradi-to-accra/` | `200` (CPT `st_service_area`) | Takoradi to Accra Towing & Vehicle Recovery Service |
| 244 | `/service-areas/tamale-to-accra` | `/service-areas/tamale-to-accra/` | `200` (CPT `st_service_area`) | Tamale to Accra Towing & Vehicle Recovery Service |
| 245 | `/service-areas/tarkwa-to-accra` | `/service-areas/tarkwa-to-accra/` | `200` (CPT `st_service_area`) | Tarkwa to Accra Towing & Vehicle Recovery Service |
| 246 | `/service-areas/techiman-to-accra` | `/service-areas/techiman-to-accra/` | `200` (CPT `st_service_area`) | Techiman to Accra Towing & Vehicle Recovery Service |
| 247 | `/service-areas/tema` | `/service-areas/tema/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Tema |
| 248 | `/service-areas/tepa-to-accra` | `/service-areas/tepa-to-accra/` | `200` (CPT `st_service_area`) | Tepa to Accra Towing & Vehicle Recovery Service |
| 249 | `/service-areas/tesano` | `/service-areas/tesano/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Tesano |
| 250 | `/service-areas/teshie` | `/service-areas/teshie/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Teshie |
| 251 | `/service-areas/tolon-to-accra` | `/service-areas/tolon-to-accra/` | `200` (CPT `st_service_area`) | Tolon to Accra Towing & Vehicle Recovery Service |
| 252 | `/service-areas/tumu-to-accra` | `/service-areas/tumu-to-accra/` | `200` (CPT `st_service_area`) | Tumu to Accra Towing & Vehicle Recovery Service |
| 253 | `/service-areas/wa-to-accra` | `/service-areas/wa-to-accra/` | `200` (CPT `st_service_area`) | Wa to Accra Towing & Vehicle Recovery Service |
| 254 | `/service-areas/walewale-to-accra` | `/service-areas/walewale-to-accra/` | `200` (CPT `st_service_area`) | Walewale to Accra Towing & Vehicle Recovery Service |
| 255 | `/service-areas/wassa-akropong-to-accra` | `/service-areas/wassa-akropong-to-accra/` | `200` (CPT `st_service_area`) | Wassa Akropong to Accra Towing & Vehicle Recovery Service |
| 256 | `/service-areas/weija-gbawe` | `/service-areas/weija-gbawe/` | `200` (CPT `st_service_area`) | Towing & Recovery Service in Weija-Gbawe |
| 257 | `/service-areas/wenchi-to-accra` | `/service-areas/wenchi-to-accra/` | `200` (CPT `st_service_area`) | Wenchi to Accra Towing & Vehicle Recovery Service |
| 258 | `/service-areas/winneba-to-accra` | `/service-areas/winneba-to-accra/` | `200` (CPT `st_service_area`) | Winneba to Accra Towing & Vehicle Recovery Service |
| 259 | `/service-areas/yeji-to-accra` | `/service-areas/yeji-to-accra/` | `200` (CPT `st_service_area`) | Yeji to Accra Towing & Vehicle Recovery Service |
| 260 | `/service-areas/yendi-to-accra` | `/service-areas/yendi-to-accra/` | `200` (CPT `st_service_area`) | Yendi to Accra Towing & Vehicle Recovery Service |
| 261 | `/service-areas/zebilla-to-accra` | `/service-areas/zebilla-to-accra/` | `200` (CPT `st_service_area`) | Zebilla to Accra Towing & Vehicle Recovery Service |

---

## 6. Dynamic Localized Service Area × Service Sub-Routes (32 Districts × 4 Services = 128 URLs)

Handled via `^service-areas/([^/]+)/([^/]+)/?$` rewrite rule + `TOWING_ADVERTS` catalog (`emergency-towing`, `scheduled-towing`, `car-recovery`, `long-distance-towing`, plus `301`/alias from `vehicle-recovery` → `car-recovery`).

Districts: `tema`, `ashaiman`, `spintex`, `adenta`, `achimota`, `pokuase`, `airport-residential`, `accra-to-kumasi`, `kumasi-to-accra`, `accra-to-cape-coast`, `cape-coast-to-accra`, `accra-to-takoradi`, `takoradi-to-accra`, `accra-to-koforidua`, `koforidua-to-accra`, `accra-to-ho`, `ho-to-accra`, `ningo-prampram`, `korle-klottey`, `ayawaso`, `weija-gbawe`, `nsawam`, `suhum`, `madina`, `dodowa`, `amasaman`, `circle`, `nima`, `teshi`, `labadi`, `lakeside`, `burma-camp`.
