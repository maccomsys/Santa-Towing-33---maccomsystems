=== Santa Towing Website Core ===
Contributors: Santa Towing & Garage Services Ltd.
Tags: towing, automotive, haulage, elementor, custom post types, ghana
Requires at least: 6.2
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Core WordPress plugin for Santa Towing & Garage Services Ltd. Registers all 7 Custom Post Types, 9 Taxonomies, 13 Elementor Free Custom Widgets, URL Rewrites, 301 Redirects, Site Settings, and Secure AJAX + WhatsApp Forms.

== Description ==

* **7 Custom Post Types**: Cars for Sale (`st_car`), Rental Fleet (`st_rental`), Auto Parts (`st_part`), Service Areas (`st_service_area`), Gallery (`st_gallery`), FAQs (`st_faq`), Careers (`st_career`).
* **9 Taxonomies**: Car Category, Car Make, Rental Category, Part Category, Part Brand, Gallery Category, FAQ Topic, Department, Ghana Region.
* **13 Elementor Free Widgets**: Hero Slider, Services Grid, Haulage Showcase, Cars Catalog, Rental Fleet, Auto Parts Catalog, Why Choose Us, Blog Grid, FAQ Accordion, Gallery Masonry Showcase, Service Area Route & Directory, Emergency Towing Request Form (16 Regions -> 232 Areas), and Multi-Mode Enquiry / Quote / Career Application Form.
* **URL Parity & 301 Redirects**: Preserves `/sales/{id}`, `/rental/{id}`, `/parts/{id}`, `/blog/{id}`, `/service-areas/{area}/{service}`, all 14 React alias 301 redirects, dynamic `/sitemap.xml`, and Schema.org `AutoRepair` JSON-LD.
* **Secure Forms**: Nonce validation, honeypot spam protection, CV uploads (`pdf/doc/docx` <= 5MB), MySQL inquiries table (`wp_santa_inquiries`), email delivery via `wp_mail()`, CSV export, and pre-filled WhatsApp click-to-chat deep links.

== Installation ==

1. Upload `santa-towing-website-core.zip` via **Plugins > Add New > Upload Plugin** and activate.
2. Go to **Settings > Permalinks** and click **Save Changes** (or use **Santa Towing > Routes & System > Flush Rewrite Rules**).
3. Install & activate the free **Elementor** plugin from WordPress.org to use the 13 custom Santa Towing widgets under the **Santa Towing** Elementor category.
