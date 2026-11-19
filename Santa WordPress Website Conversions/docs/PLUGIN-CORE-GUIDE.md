# Santa Towing Website Core — Plugin Guide & Architecture Reference

**Plugin Name:** `Santa Towing Website Core`  
**Plugin Slug / Text Domain:** `santa-towing-website-core`  
**Package Path:** `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip`  
**Source Directory:** `Santa WordPress Website Conversions/plugins/santa-towing-website-core/`  
**Requirements:** WordPress 6.2+ · PHP 8.1+ · MySQL / MariaDB · Elementor Free (optional for core CPTs/forms, required for the 17 custom widgets)

---

## 1. Custom Post Types, Taxonomies & Meta Keys

All Custom Post Types are registered in `includes/class-cpts.php` with exact meta key parity to the `santa-towing` theme templates:

| Post Type | Rewrite / Slug | Taxonomies | Core Meta Keys |
|---|---|---|---|
| `st_car` | `/sales/` | `st_car_category`, `st_car_make` | `_st_legacy_id`, `_st_car_type`, `_st_price`, `_st_fuel`, `_st_transmission`, `_st_year`, `_st_mileage`, `_st_make`, `_st_model`, `_st_status`, `_st_featured`, `_st_gallery` |
| `st_rental` | `/rental/` | `st_rental_category` | `_st_legacy_id`, `_st_rental_type`, `_st_daily_rate`, `_st_weekly_rate`, `_st_monthly_rate`, `_st_seats`, `_st_transmission`, `_st_fuel`, `_st_deposit`, `_st_with_driver`, `_st_status`, `_st_gallery` |
| `st_part` | `/parts/` | `st_part_category`, `st_part_brand` | `_st_legacy_id`, `_st_brand`, `_st_category`, `_st_price`, `_st_sku`, `_st_rating`, `_st_reviews`, `_st_in_stock`, `_st_stock_status`, `_st_warranty`, `_st_condition`, `_st_compatible_models`, `_st_installation`, `_st_specs_json`, `_st_gallery` |
| `st_service_area` | `/service-areas/` | `st_region` | `_st_area_label`, `_st_origin`, `_st_destination`, `_st_heading`, `_st_description`, `_st_seo_title`, `_st_seo_description`, `_st_keywords_json`, `_st_prev_path`, `_st_prev_label`, `_st_next_path`, `_st_next_label` |
| `st_gallery` | Non-publicly queryable | `st_gallery_category` | `_st_media_type`, `_st_span`, `_st_location`, `_st_date_label`, `_st_video_url` |
| `st_faq` | Non-publicly queryable | `st_faq_category` | `_st_sort_order` |
| `st_career` | Non-publicly queryable | `st_department` | `_st_location`, `_st_employment_type`, `_st_responsibilities_json`, `_st_requirements_json`, `_st_benefits_json` |
| `post` (Blog) | Native WP Posts + `/blog/{id}` | Native `category`, `post_tag` | `_st_legacy_id`, `_st_read_time`, `_st_author`, `_st_author_role` |

---

## 2. Site Settings & Color Sync (`wp-admin > Santa Towing`)

Stored in `santa_towing_site_content` (synchronized directly with `st_get_content()` in the `santa-towing` theme):
- **Brand & Logo**: Full Legal Name, Short Name, Tagline, Operations Contact Person, Media Library Logo URL, Alt Text.
- **Theme Colors & Elementor Sync**: Primary (`#015557`), Secondary (`#192e2e`), Accent (`#d80000`), Dark (`#131514`), White (`#ffffff`), Black (`#000000`) — includes automatic sync into Elementor Free's Default Kit `system_colors`.
- **Contact & WhatsApp**: Hotline numbers (`0244753849`), `tel:` URI, `https://wa.me/233244753849`, business/leads email (`info@santatowing.com`), address, operating hours, coverage label.
- **Social, Footer & Floating UI**: Facebook, Twitter/X, Instagram, LinkedIn, footer copy, newsletter confirmation text, floating CTA tooltips.
- **SEO & Service Area Copy**: Canonical domain, homepage meta tags, and shared Service Area CTA headings/buttons.

---

## 3. URL Rewrites, 301 Redirects, Sitemap & Schema SEO

Handled by `includes/class-rewrites.php`:
- **Numeric Legacy ID Routing**: `/sales/{id}`, `/rental/{id}`, `/parts/{id}`, `/blog/{id}` resolve via `_st_legacy_id` using the WordPress `request` filter (cleanly returning 404 when no matching legacy ID exists).
- **Virtual Service Area Fallback Routes**: `/service-areas/{areaSlug}` and `/service-areas/{areaSlug}/{serviceSlug}` (`emergency-towing`, `scheduled-towing`, `car-recovery`, `long-distance-towing`) render HTTP 200 across all **32 districts**, **232 Ghana localities**, and `{origin}-to-{destination}` routes even before importing static CPT posts.
- **14 React `<Navigate replace>` 301 Redirects**: `/services-areas`, `/rentals`, `/about`, `/privacy`, `/terms`, `/cookies`, `/repair`, `/bodyworks`, `/part`, `/cars`, `/towing-services`, `/haulage-services`, `/faq`, `/career`, plus `/service-areas/{area}/vehicle-recovery` → `car-recovery`.
- **Dynamic `/sitemap.xml` & `robots.txt`**: Automatically outputs pages, published CPTs, blog posts, and all 32 districts × 4 sub-services.
- **SEO Meta & Schema.org**: Injects `<meta name="description">`, localized `<meta name="keywords">`, `<link rel="canonical">`, OpenGraph tags, Twitter cards, and `AutoRepair` JSON-LD.

---

## 4. Elementor Free Custom Widgets (Category: `Santa Towing`)

Every widget extends `\Elementor\Widget_Base` using strictly **Elementor Free** APIs and exposes Content, Media/Image, Style (Colors, Typography, Borders), and Responsive (`add_responsive_control`) controls:

1. `stwc-hero-slider` — **Santa Hero Slider** (`HeroSlider.tsx`)
2. `stwc-services-grid` — **Santa Services Grid** (`ServicesSection.tsx`)
3. `stwc-haulage-showcase` — **Santa Haulage Showcase** (`HaulageShowcase.tsx` & Tema Port corridors)
4. `stwc-cars-catalog` — **Santa Cars Catalog** (with condition filter tabs)
5. `stwc-rental-fleet` — **Santa Rental Fleet** (Daily/Weekly/Monthly rates & class filter)
6. `stwc-parts-showcase` — **Santa Auto Parts Catalog** (with category filters & star ratings)
7. `stwc-why-choose-us` — **Santa Why Choose Us** (trust pillars & workshop showcase)
8. `stwc-blog-grid` — **Santa Blog & Guides Grid**
9. `stwc-faq-accordion` — **Santa FAQ Accordion** (topic filter pills + expandable panels)
10. `stwc-gallery-showcase` — **Santa Gallery Showcase** (masonry grid + lightbox modal)
11. `stwc-service-area-route` — **Santa Service Area Route / Directory** (single route mode + searchable 16-Region / 232-Town directory mode)
12. `stwc-towing-request-form` — **Santa Emergency Towing Form** (16 Ghana Regions → 232 Areas cascade + WhatsApp dispatch)
13. `stwc-enquiry-forms` — **Santa Enquiry / Quote / Application Form** (`haulage`, `contact`, `fleet`, `car_inquiry`, `rental_booking`, `part_inquiry`, `career` with CV upload)
14. `stwc-page-hero` — **Santa Inner Page Hero** (Reusable hero banner with breadcrumb links & responsive aspect controls)
15. `stwc-feature-cards` — **Santa Feature & Service Cards Grid** (Editable multi-column cards for Repairs, Body Works, Towing & Services)
16. `stwc-spotlight-split` — **Santa Spotlight Split Banner** (Two-column image/copy spotlight with CTA buttons & badge pills)
17. `stwc-careers-board` — **Santa Careers & Open Positions Board** (Interactive job board + native application modal w/ CV upload)

Fallback shortcodes are also registered (`[stwc_towing_form]`, `[stwc_enquiry_form mode="haulage"]`, `[stwc_faqs]`, `[stwc_gallery]`, `[stwc_careers]`).

---

## 5. Secure Form Processing & Inquiries Manager

- **Storage:** Custom MySQL table `{$wpdb->prefix}santa_inquiries` created via `dbDelta()`.
- **Security:** WordPress AJAX nonce (`stwc_form_nonce`), honeypot anti-spam field (`website_hp`), server-side field sanitization/validation, and CV upload validation (`wp_check_filetype_and_ext` + `wp_handle_upload`, PDF/DOC/DOCX ≤ 5MB).
- **Email & WhatsApp Parity:** Delivers structured plain-text notifications to the configured admin email via `wp_mail()` and returns pre-filled `https://wa.me/...` deep links matching the exact React Towing & Haulage WhatsApp message formatting.
- **Admin Viewer & Export:** `wp-admin > Santa Towing > Inquiries & Leads` includes filtering by form type/status, status transitions (`Contacted`, `Resolved`), CV download links, and one-click CSV export.

---

## 6. Installation Steps on cPanel / WordPress

1. Log in to WordPress Admin → **Plugins → Add New → Upload Plugin**.
2. Upload `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip`, click **Install Now**, and click **Activate Plugin**.
3. Go to **Settings → Permalinks** and click **Save Changes** (or visit **Santa Towing → Routes & System → Flush Rewrite Rules & Permalinks Now**).
4. *(Recommended)* Install & activate **Elementor** (Free) from WordPress.org to unlock all 17 **Santa Towing** widgets inside Elementor Editor.
5. Configure brand colors, phone/WhatsApp links, and lead email under **Santa Towing → Site Settings**.
