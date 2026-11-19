=== Santa Towing Full Site Importer ===
Contributors: santatowing
Requires at least: 6.2
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

One-click WordPress Admin & WP-CLI importer for the converted Santa Towing website.

== Description ==

Install and activate this helper plugin after activating:
1. The **Santa Towing** theme (`santa-towing.zip`)
2. **Elementor** (Free) from WordPress.org
3. **Santa Towing Website Core** (`santa-towing-website-core.zip`)
4. **Santa Towing Business Manager** (`santa-towing-business-manager.zip`)

Then go to **Tools -> Santa Full Importer** inside WordPress Admin to idempotently import:
* All 42 verified Media Library images (preserving exact filenames and alt text)
* 258 site settings keys, theme colors synced with Elementor Kit, and 30 image alt slots
* 4 navigation menus (`primary`, `footer_quick`, `footer_services`, `footer_legal`)
* 70 CPT & Blog items (12 Cars, 6 Rentals, 6 Parts, 6 Blog Posts, 28 FAQs, 8 Gallery items, 4 Careers)
* 26 core, directory, utility, and legal/policy pages + 15 Elementor Free JSON templates
* 261 localized Ghana Service Area pages (`st_service_area`) with batching support for cPanel hosts
* Business Manager factory defaults (`wp-admin > Santa Manager`)

Optional Legacy React Browser exports (`localStorage` / `IndexedDB` exported via `legacy-browser-export-tool.html`) can also be imported under **Tools -> Santa Legacy Browser Importer** (or directly inside **Santa Manager -> Backup & Export**).

Once your migration is complete and verified, you can safely **Deactivate** and **Delete** this importer plugin without affecting any imported pages, media, or settings.

== Changelog ==

= 1.0.0 =
* Initial release packaging ST_Full_Importer, ST_Pilot_Importer, ST_Migration_Media_Sync, ST_Migration_Legacy_Importer, all migration JSON datasets, and all 15 Elementor Free templates into a zero-CLI installable plugin.
