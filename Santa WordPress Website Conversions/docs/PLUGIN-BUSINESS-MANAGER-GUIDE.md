# Santa Towing Business Manager — Plugin Technical & User Guide

**Plugin Slug:** `santa-towing-business-manager`  
**Package ZIP:** `Santa WordPress Website Conversions/packages/santa-towing-business-manager.zip` (`40` files, `6,076,989` bytes / ~`5.80 MiB`)  
**Source Directory:** `Santa WordPress Website Conversions/plugins/santa-towing-business-manager/`  
**Compatibility:** WordPress 6.x+, PHP 8.1+, MySQL 5.7+ / MariaDB 10.3+, standard cPanel shared hosting.  
**Dependencies:** **Zero Elementor dependency**, **zero external Composer dependencies**, **zero Node.js runtime**, **zero iframes or React embeds**.

---

## 1. Architecture Overview

The **Santa Towing Business Manager** plugin recreates the complete React `/admin/*` operations console natively inside WordPress Admin (`wp-admin`) using pure PHP, MySQL (`dbDelta()`), WordPress Options & CPT APIs, WordPress Users/Roles/Capabilities, static CSS (`#stbm-root` scoped), and vanilla modular JavaScript (`stbm-app.js`, `stbm-billing.js`, `stbm-image-studio.js`).

### File Structure
```
santa-towing-business-manager/
├── santa-towing-business-manager.php   # Bootstrap, asset resolver, activation/deactivation hooks
├── readme.txt                          # WP plugin header & changelog
├── uninstall.php                       # Safe cleanup preserving shared theme content & CPT posts
├── includes/
│   ├── class-stbm-roles.php            # st_manager & st_staff roles, 7 caps, nonce/cap guards
│   ├── class-stbm-defaults.php         # React default mirrors, dot-path options API, standalone CPT CRUD
│   ├── class-stbm-db.php               # 5 MySQL tables, billing totals, receipt sync, Studio media upload
│   ├── class-stbm-pdf.php              # Zero-dependency pure-PHP A4 PDF 1.4 generator
│   ├── class-stbm-exports.php          # Protected admin-post.php CSV, JSON backup & PDF downloads
│   ├── class-stbm-legacy-importer.php  # Legacy React localStorage/IndexedDB JSON importer (Merge/Replace/Dry Run)
│   ├── class-stbm-ajax.php             # 26 nonce- & capability-protected wp_ajax_stbm_* handlers
│   └── class-stbm-admin-ui.php         # Top-level menu + 14 submenus, Media Library & localized boot JSON
└── assets/
    ├── css/stbm-admin.css              # Dark sidebar (#131514), #015557 emerald pill, modals, print CSS
    ├── js/
    │   ├── stbm-app.js                 # Shell nav, Dashboard, Inventory, Pages, Colors, Logo, Links, Images, Backup, Profile
    │   ├── stbm-billing.js             # Customers CRM, Pro Forma Invoices, Receipts, Doc Settings, Print/PDF Preview
    │   └── stbm-image-studio.js        # HTML5 Canvas editor (crop, rotate, flip, watermarks, compress, WP Media upload)
    └── images/                         # 25 bundled fallback assets (avatars, vehicle/parts images, logo)
```

---

## 2. Installation (WordPress Admin or cPanel)

1. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**.
2. Choose `Santa WordPress Website Conversions/packages/santa-towing-business-manager.zip` and click **Install Now → Activate Plugin**.
   - *(Alternative via cPanel File Manager)*: Upload and extract `santa-towing-business-manager.zip` directly into `/wp-content/plugins/`, then activate inside WP Admin.
3. On activation, the plugin automatically:
   - Creates custom MySQL tables via `dbDelta()` and seeds initial sample billing/customer records if empty.
   - Registers custom roles (`st_manager`, `st_staff`) and grants all `stbm_*` capabilities to `administrator`.
   - Initializes `santa_towing_site_content` (merging safely with existing theme/core plugin content if already imported) and `stbm_doc_settings`.
   - Ensures CPTs (`st_car`, `st_rental`, `st_part`, `st_gallery`, `st_faq`, `st_career`, `st_service_area`) are registered if `santa-towing-website-core` is not active, then flushes rewrite rules.

---

## 3. Roles, Capabilities & Security

Replaces the React `localStorage` session/login screen (`AdminLogin.tsx`) with native WordPress authentication (`wp-login.php`) and granular capabilities in `STBM_Roles`:

| Capability | Description | `administrator` | `st_manager` | `st_staff` |
|---|---|---|---|---|
| `stbm_access_console` | View Santa Manager shell & Dashboard | Yes | Yes | Yes |
| `stbm_manage_billing` | Customers, Invoices, Receipts, Doc Settings, Billing Exports | Yes | Yes | Yes |
| `stbm_manage_inventory` | Cars, Rentals, Parts, Gallery, FAQs, Careers | Yes | Yes | Yes |
| `stbm_manage_content` | Pages Text, Navigation Links, Site Images, Blog | Yes | Yes | Yes |
| `stbm_manage_studio` | Image Studio Canvas Editor & WP Media Library uploads | Yes | Yes | Yes |
| `stbm_manage_backups` | JSON Backup/Restore, CSV Exports & Audit Logs | Yes | Yes | Yes |
| `stbm_manage_brand` | **Colors & Logo** (and `theme.*` / `global.logo.*` keys) | Yes | Yes | **Blocked** |

### Security Enforcement
- Every one of the **26 AJAX endpoints** (`wp_ajax_stbm_*` in `includes/class-stbm-ajax.php`) enforces `STBM_Roles::verify_ajax( $cap )` (`check_ajax_referer( 'stbm_ajax_nonce', 'nonce' )` + `current_user_can()`).
- Generic content saves (`stbm_save_content`) inspect dot-paths and reject any attempt to mutate `theme.*` or `global.logo.*` unless the user holds `stbm_manage_brand`.
- All **3 download endpoints** (`admin-post.php` in `includes/class-stbm-exports.php`) enforce `STBM_Roles::verify_admin_post()` (`check_admin_referer` + `current_user_can()`).
- Password updates inside **Profile → Security** verify the user's current WordPress password with `wp_check_password()` before calling `wp_set_password()` + `wp_set_auth_cookie()`.

---

## 4. Database Tables & WordPress Storage

Replaces all React `localStorage` and `IndexedDB` stores with server-side MySQL storage:

| Table / Option | Replaces (React) | Purpose |
|---|---|---|
| `{$wpdb->prefix}stbm_customers` | `santa_billing_customers` | Customer CRM records (`uid`, `name`, `company`, `phone`, `email`, `address`, `notes`) with relational delete guard |
| `{$wpdb->prefix}stbm_invoices` | `santa_billing_invoices` | Pro Forma Invoices (`PFI-YYYYMM-0001`), line items JSON, tax/discount/dispatch fees, custom fields, section layout & payment balances |
| `{$wpdb->prefix}stbm_receipts` | `santa_billing_receipts` | Official Receipts (`REC-YYYYMM-0001`) linked by `invoice_uid`; automatically recalculates parent invoice `amount_paid`, `balance_due`, and `status` (`Partially Paid` / `Paid`) |
| `{$wpdb->prefix}stbm_activity_logs` | `santa_admin_activity` | Billing, brand, inventory, backup, and profile audit trail |
| `{$wpdb->prefix}stbm_studio_images` | `santa_image_studio_db` (IndexedDB) | Image Studio asset metadata linked to native WordPress Media attachments (`attachment_id`, dimensions, format, source tag) |
| Option: `santa_towing_site_content` | `santa_site_content` | Shared site text, colors, logo, links, nav menus, SEO copy & 31 image slots (synced directly with `santa-towing` theme & `santa-towing-website-core`) |
| Option: `stbm_doc_settings` | `santa_billing_doc_settings` | Company letterhead, TIN/Reg No., currency symbol (`GHS`), bank details, watermark text, signatures & invoice prefix |
| Custom Post Types (`st_*`) | `inventoryDefaults.ts` overrides | Cars (`st_car`), Rentals (`st_rental`), Parts (`st_part`), Gallery (`st_gallery`), FAQs (`st_faq`), Careers (`st_career`), Service Areas (`st_service_area`) |

---

## 5. All 14 Admin Modules Included

1. **Dashboard:** KPI summary cards (Cars, Rentals, Parts, Pro Forma Invoices, Official Receipts, Customers, Recent Activity), Quick Configuration card (`Phone`, `WhatsApp`, `Address`, `Hero Title`), and live Desktop/Mobile website preview frame.
2. **Customers:** Full CRM create/edit/delete, instant search, billing history modal per customer, and safe-delete guard blocking deletion of customers linked to existing invoices/receipts.
3. **Inventory:** 6 tabs (**Cars for Sale**, **Car Rentals**, **Auto Parts**, **Gallery**, **FAQs**, **Careers**) with search, drag/arrow reorder (`menu_order`), visibility hide/show toggle, create/edit modals using WP Media Library, delete, and factory reset per tab.
4. **Pro Forma Invoices:** Status filter pills (`All`, `Draft`, `Sent`, `Partially Paid`, `Paid`, `Cancelled`), search, duplicate invoice, status dropdown, print & pure-PHP A4 PDF download, and split customizable editor (line items, service presets, tax/discount/shipping toggles, custom fields/sections, and section hide/reorder).
5. **Official Receipts:** Linked invoice selector with auto-filled customer/balance details, payment method tracking (`Mobile Money`, `Bank Transfer`, `Cash`, `Card`, `Cheque`), atomic parent invoice recalculation, print & pure-PHP A4 PDF download.
6. **Image Studio:** HTML5 `<canvas>` editor with live aspect-ratio crop (`Free`, `1:1`, `4:3`, `16:9`, `3:2`, `4:5`, `9:16`) + padlock lock + rule-of-thirds overlay, Rotate ±90°, Flip H/V, Text & Logo Watermarks, JPEG/PNG/WebP quality compression, Undo/Redo, Original Comparison toggle, and **Save Copy / Overwrite** directly into WordPress Media Library + `stbm_studio_images`.
7. **Doc Settings:** Company name, tagline, address, phones, email, website, currency (`GHS`), invoice/receipt number prefixes, default tax/VAT rate (`15%`), bank account & payment instructions, terms, signature labels, and live A4 letterhead preview.
8. **Pages Text:** 14-tab editor (`Home`, `About`, `Services`, `Towing`, `Haulage`, `Sales`, `Rentals`, `Parts`, `Blog`, `FAQs`, `Careers`, `Contact`, `Service Areas`, `Legal/SEO`) with global keyword search across sections, SEO Title/Description character counters, and Blog Post + Service Area override editors.
9. **Colors** *(Manager/Admin only)*: Live Primary/Secondary/Accent/Neutral palette pickers, WCAG 2.1 AA/AAA contrast checker (`>= 4.5:1` / `>= 3.0:1`), and 4-tab live UI simulator.
10. **Logo** *(Manager/Admin only)*: Primary logo & dark/inverse logo pickers via WP Media Library, alt text, sizing slider, and live Header/Footer/Document preview.
11. **Links:** Phone/WhatsApp link builder, social profile URLs, Floating Action CTA config, and drag/arrow-sortable link editors for Top Navbar, Footer Quick Links, and Footer Services.
12. **Images:** 31-slot site media manager grouped by page/section with mandatory Alt Text editor, preview thumbnails, WP Media Library picker, and slot-level reset.
13. **Backup & Export:** Full JSON exports (Site Content, Billing Data, Complete Bundle), CSV downloads (Customers, Invoices, Receipts), JSON Backup Restore (`Merge` or `Replace` mode), Audit Log table, and guarded Factory Reset actions.
14. **Profile:** 4-tab user settings (`Account Details`, `Avatar` with 5 built-in presets + WP Media upload, `Security` WP password change with live strength meter, and `Activity & Reset`).

---

## 6. Pure-PHP PDF & Print Engine

- `includes/class-stbm-pdf.php` generates standards-compliant **PDF 1.4 (A4 `595 × 842 pt`)** documents natively in PHP without requiring `Dompdf`, `TCPDF`, `wkhtmltopdf`, or client-side `jsPDF`.
- Both Pro Forma Invoices and Official Receipts include an interactive **Print / PDF Modal** that supports native browser A4 printing (`@media print`) and one-click server-generated PDF download via `wp-admin/admin-post.php`.

---

## 7. Verification Status & Known Environment Notes

- **Verified Automatically Inside Build Container:**
  - `node --check` passed with zero syntax errors on `stbm-app.js`, `stbm-billing.js`, and `stbm-image-studio.js`.
  - Python static PHP lexer/security audit passed across all `10` PHP files (`<?php` headers, `ABSPATH`/`WP_UNINSTALL_PLUGIN` guards, balanced delimiters, zero Elementor/React/iframe/localStorage runtime dependencies, all `26` AJAX endpoints guarded by `STBM_Roles::verify_ajax()`, all `3` `admin_post` endpoints guarded by `STBM_Roles::verify_admin_post()`, and `$wpdb->prepare()` parameterization verified).
  - Package ZIP `Santa WordPress Website Conversions/packages/santa-towing-business-manager.zip` built (`6,076,989 bytes`, `40` archived files) and verified clean via `zipfile.ZipFile.testzip()`.
- **Requires Live Server Verification:**
  - Because this build container does not include a `php` CLI binary or a running WordPress/MySQL runtime, live activation, `dbDelta()` table creation, `wp-admin` visual QA across all 14 screens, Image Studio Media Library upload, and PDF download rendering must be tested on a live PHP 8.1+ WordPress/cPanel installation.
