# Santa Towing Custom WordPress Theme — Installation & Configuration Guide

**Theme Package:** `Santa WordPress Website Conversions/packages/santa-towing.zip`  
**Theme Source Directory:** `Santa WordPress Website Conversions/theme/santa-towing/`  
**Target Environment:** Standard cPanel Shared/VPS Hosting (Apache/LiteSpeed + PHP 8.1+ + MySQL 5.7+ / MariaDB 10.3+)  
**Dependencies:** **Elementor Free** (optional; works standalone or with Elementor Free installed). **Zero Elementor Pro, zero paid plugins, zero Node.js, zero React runtime, zero iframes.**

---

## 1. Hosting & WordPress Prerequisites

| Requirement | Minimum Supported | Recommended |
|---|---|---|
| WordPress | `6.2` | `6.7+` |
| PHP | `8.1` | `8.2` or `8.3` |
| Database | MySQL `5.7` / MariaDB `10.3` | MySQL `8.0` / MariaDB `10.6+` |
| Upload Max Filesize (`upload_max_filesize`) | `16M` | `64M` |
| Memory Limit (`WP_MEMORY_LIMIT`) | `128M` | `256M` |
| Page Builder | Elementor **Free** (optional) | Latest Elementor Free `3.x` |

---

## 2. Installing the Theme

### Method A: WordPress Admin Dashboard (Recommended)
1. Download `Santa WordPress Website Conversions/packages/santa-towing.zip` to your computer.
2. Log in to **WordPress Admin** (`https://yourdomain.com/wp-admin`).
3. Navigate to **Appearance → Themes → Add New Theme → Upload Theme**.
4. Choose `santa-towing.zip` and click **Install Now**.
   - *Note:* If cPanel PHP `upload_max_filesize` is set below `10M` (the theme ZIP is ~8.55 MB because all 42 verified images are bundled cleanly inside `assets/images/`), either increase `upload_max_filesize` / `post_max_size` to `64M` in cPanel **Select PHP Version → Options** (or **MultiPHP INI Editor**) or use Method B below.
5. Once uploaded, click **Activate**.

### Method B: cPanel File Manager / FTP Upload
1. Log in to **cPanel → File Manager** and open your WordPress root directory (usually `public_html/`).
2. Navigate into `wp-content/themes/`.
3. Click **Upload**, upload `santa-towing.zip`, then right-click `santa-towing.zip` and choose **Extract** inside `wp-content/themes/`.
4. Verify the extracted path is `wp-content/themes/santa-towing/style.css` (not nested as `santa-towing/santa-towing/style.css`). Delete `santa-towing.zip` afterward to save disk space.
5. In **WordPress Admin → Appearance → Themes**, find **Santa Towing** and click **Activate**.

---

## 3. Essential Initial WordPress Configuration

1. **Permalinks (Critical for URL Parity):**
   - Go to **Settings → Permalinks**.
   - Select **Post name** (`/%postname%/`) and click **Save Changes**.
2. **Homepage & Blog Page Setup:**
   - Create two Pages if they do not exist yet: **Home** (slug `home`) and **Blog** (slug `blog`).
   - Go to **Settings → Reading**.
   - Set **Your homepage displays** → **A static page**:
     - **Homepage:** `Home`
     - **Posts page:** `Blog`
   - Click **Save Changes**.
   - *Note on `front-page.php`:* When the Homepage is NOT edited with Elementor Free, `front-page.php` automatically renders the full 11-section Santa Towing landing page (Hero Slider, Interactive Roadside Quote Placeholder CTA, Emergency Assistance, Why Trust Us, Haulage Showcase, Featured Cars with Live Category Filter Tabs, Car Rentals, Genuine Auto Parts, Why Choose Us, Blog Posts, and FAQ Accordions). When built/edited with Elementor Free, `front-page.php` detects Elementor via `st_is_elementor_page()` and renders full-bleed Elementor Free content seamlessly below the theme header.
3. **WordPress Navigation Menus (Optional Override):**
   - Go to **Appearance → Menus**.
   - By default, `template-parts/navbar.php` and `template-parts/footer-content.php` render the exact multi-breakpoint Santa Towing navigation bar (TopBar, Desktop `xl:flex`, Tablet `lg:flex xl:hidden` compact menu, Mobile accordion drawer `< 1024px`) and 5-column Footer (`Quick Links`, `Our Services`, `12 Legal & Policy Links`, `Newsletter Form`).
   - Four WordPress theme menu locations are registered (`primary`, `footer_quick`, `footer_services`, `footer_legal`) for future WordPress menu customization and plugin sync.
4. **Custom Logo & Site Icon:**
   - The bundled `assets/images/santa-logo.png` loads out of the box with zero extra configuration.
   - To replace it via WordPress Customizer: **Appearance → Customize → Site Identity → Select Logo** and upload a **Site Icon (Favicon)**.

---

## 4. Elementor Free Setup & Compatibility Rules

The `santa-towing` theme is engineered specifically for **Elementor Free** (never Elementor Pro):

1. Install & activate **Elementor** (Free) from **Plugins → Add New**.
2. Go to **Elementor → Settings → General**:
   - Check **Disable Default Colors** (ensures Tailwind theme CSS variables `--color-primary: #015557`, `--color-secondary: #192e2e`, `--color-accent: #d80000`, `--color-dark: #131514` govern all buttons, cards, and headings cleanly).
   - Check **Disable Default Fonts** (ensures system sans typography parity; the theme also disables Elementor Google Fonts via `elementor/frontend/print_google_fonts`).
3. **Page Templates Available in Page Attributes:**
   - **Default Template (`page.php`):** Inner page layout with Hero header, Breadcrumbs, and `.prose` typography styling.
   - **Elementor Full-Bleed Canvas (`page-templates/template-elementor-canvas.php`):** Preserves the theme TopBar + NavBar + 5-Column Footer + Emergency Floating Buttons while removing padding/containers inside `<main>` so Elementor Free sections go edge-to-edge (`100vw`). Ideal for custom Elementor Free Homepage / Services / Haulage / Towing landing pages.
   - **Elementor Full-Width Inner Page (`page-templates/template-elementor-fullwidth.php`):** Preserves theme Header + Footer + Floating Buttons and adds `pt-[72px]` top clearance for sticky/fixed header spacing above Elementor Free inner page heroes.
   - **Elementor Blank Canvas (`page-templates/template-elementor-blank.php`):** Omits header/footer (`template-elementor-blank.php`) cleanly for standalone campaigns.
   - **Legal & Policy Page (`page-templates/template-policy.php`):** Standardized container with Hero Banner, Breadcrumbs, `.prose` legal styling, and Quick Emergency Contact Callout (`Privacy Policy`, `Terms & Conditions`, etc.).
   - **Website Sitemap (`page-templates/template-sitemap.php`):** Full interactive live-filtering sitemap across core pages, garage services, legal policies, and CPT archives.

---

## 5. Theme Assets, CSS/JS Architecture & Customization

- **Static Pre-Compiled Tailwind CSS (`assets/css/santa-styles.css`):** Compiled offline at build time from the exact React `src/index.css` `@theme` tokens (`#015557`, `#192e2e`, `#d80000`, `#131514`). Zero Tailwind CDN runtime, zero Node.js required on cPanel.
- **Custom Stylesheet (`style.css`):** Houses the WP Theme header, smooth CSS-grid accordion transitions (`.st-accordion-panel`), `[data-st-reveal]` intersection animations, bobbing scroll arrow keyframes (`.st-animate-bob`), WordPress Admin Bar offset adjustments (`.st-fixed-header`), editorial `.prose` rules, and `.navigation.pagination` button styles.
- **Vanilla Interactive JS (`assets/js/santa-main.js` & `santa-motion.js`):** Zero jQuery bloat. Handles Home navbar scroll state, accessible mobile accordion drawer, Hero slideshow controls, Featured Cars / archive category tabs, vehicle detail image gallery + tabs, reading progress bar, URL copy buttons, sitemap live search filter, and AJAX footer newsletter submission (`wp_ajax_st_newsletter_subscribe`).

---

## 6. Standalone Theme vs. Companion Conversion Plugins

| Feature Area | Behavior With Theme Only (`santa-towing.zip`) | Enhanced With Companion Plugins (`website-core`, `business-manager`, `importer`) |
|---|---|---|
| Global Header, TopBar, Mobile Drawer, Footer, Floating CTAs | **100% Live** out of the box | Editable via **Santa Towing → Site Settings** & **Santa Manager → Links / Colors / Logo** |
| Default Homepage 11 Sections & Static Design Tokens | **100% Live** (`front-page.php` fallback data + bundled images) | Hydrated from live CPT posts & Elementor Free templates via **Tools → Santa Full Importer** |
| Footer Newsletter Subscription (`#st-footer-newsletter-form`) | **Live AJAX** saving to `wp_option` `santa_towing_newsletter_subscribers` | Managed alongside dedicated inquiry/customer records in WP Admin |
| Blog Posts, Archive, Search & Single Post (`archive.php`, `single.php`, `search.php`) | **100% Live** with native WordPress posts | Seeded with all 6 migrated blog posts, 261 service areas & Media Library assets |
| Custom Post Type Templates (`st_car`, `st_rental`, `st_part`, `st_service_area`) | Ready in theme (`single-st_*.php`, `archive-st_*.php`, `template-parts/service-area-template.php`) | Registered, routed (`_st_legacy_id`), and seeded across all 70 CPT items & 261 areas by **`santa-towing-website-core.zip`** + **`santa-towing-importer.zip`** |
| Elementor Free Custom Widgets, Interactive Towing Calculator & Quote Forms | Fallback links route cleanly to `/towing`, `/contact`, and WhatsApp | **17** `\Elementor\Widget_Base` widgets, `wp_mail()`, CV upload & `wp_santa_inquiries` table provided by **`santa-towing-website-core.zip`** |
