# Beginner-Friendly cPanel Deployment Guide — Santa Towing WordPress Website

This step-by-step guide walks you through deploying the converted **Santa Towing** website onto any standard **cPanel** hosting account using **WordPress**, our custom theme & plugins, and **Elementor Free** (no coding, no Node.js server, and no paid plugins required).

> **Before You Start — Package Files You Will Upload:**  
> All installable files live inside `Santa WordPress Website Conversions/packages/`:
> 1. `santa-towing.zip` (~8.56 MiB) — Custom WordPress Theme (includes pre-compiled Tailwind CSS & all 42 verified images)
> 2. `santa-towing-website-core.zip` (~125 KiB) — Custom Post Types, 17 Elementor Free widgets, URL routes/redirects, sitemap & inquiry forms
> 3. `santa-towing-business-manager.zip` (~5.80 MiB) — Native WP Admin console (`Santa Manager`), Customers CRM, Pro Forma Invoices, Official Receipts, PDF generator & Canvas Image Studio
> 4. `santa-towing-importer.zip` (~164 KiB) — One-click WP Admin importer for pages, 261 service areas, CPTs, menus, settings & media
> 5. *(Optional)* `santa-towing-elementor-templates.zip` (~37 KiB) — Standalone bundle of all 15 Elementor Free `.json` page templates (only needed if you ever import templates manually inside Elementor)

---

## Step 1: Create a Staging Subdomain in cPanel

Never build or import directly onto your live public domain. Create a safe staging address first (for example: `staging.santatowingservices.com`).

1. Log in to your **cPanel** dashboard.
2. Under the **Domains** section, click **Domains** *(on older cPanel themes this is called **Subdomains**)*.
3. Click **Create A New Domain** (or enter a subdomain):
   - **Domain:** `staging.santatowingservices.com` *(replace with your real domain name)*.
   - **Uncheck** *"Share document root (/public_html) with domain"* so WordPress gets its own clean folder (for example `/home/youruser/staging.santatowingservices.com`).
4. Click **Submit**.
5. **Set Recommended PHP Version & Upload Limits** (so `santa-towing.zip` ~8.56 MB uploads cleanly without errors):
   - In cPanel, open **Select PHP Version** or **MultiPHP Manager** → select `staging.santatowingservices.com` → choose **PHP 8.1**, **8.2**, or **8.3** → click **Apply**.
   - Next open **MultiPHP INI Editor** (or **Select PHP Version → Options**) → select `staging.santatowingservices.com` and set at least:
     - `upload_max_filesize` = `64M`
     - `post_max_size` = `64M`
     - `memory_limit` = `256M`
     - `max_execution_time` = `300`
   - Click **Apply / Save**.

---

## Step 2: Install WordPress on the Staging Subdomain

### Method A: Softaculous (Fastest — 2 Minutes)
1. In cPanel, scroll to **Software** → **WordPress Manager by Softaculous** (or **Softaculous Apps Installer → WordPress**).
2. Click **Install**.
3. Configure the installation fields carefully:
   - **Choose Protocol:** `https://` *(if AutoSSL is not ready yet, you can pick `https://` anyway or switch in Step 7)*.
   - **Choose Domain:** `staging.santatowingservices.com`
   - **In Directory:** Leave **COMPLETELY BLANK** *(do NOT type `wp` unless you want `/wp` in the URL)*.
   - **Site Name:** `Santa Towing Services`
   - **Admin Username / Password / Email:** Pick a strong password (do NOT use `admin`). Save these credentials safely.
   - **Uncheck** extra bundled bloat plugins (like Limit Login / Classic Editor / AI assistants) if Softaculous lists any.
4. Click **Install**.
5. Log into `https://staging.santatowingservices.com/wp-admin/`.
6. **Hide Staging from Google Temporarily:** Go to **Settings → Reading**, check **"Discourage search engines from indexing this site"**, and click **Save Changes** *(we will uncheck this in Step 9 when going live)*.

### Method B: Manual Installation (If Softaculous Is Not Available)
1. Download WordPress from [wordpress.org/download](https://wordpress.org/download/).
2. In cPanel → **MySQL® Database Wizard**, create a database (e.g. `user_santastg`), create a MySQL user + strong password, assign **ALL PRIVILEGES**, and click **Next Step**.
3. Open cPanel → **File Manager**, navigate to `/staging.santatowingservices.com/`, upload `wordpress-latest.zip`, extract it, and move all files out of the `wordpress/` subfolder directly into `/staging.santatowingservices.com/`.
4. Visit `https://staging.santatowingservices.com/` in your browser and follow the 5-minute WordPress setup wizard using the database name, username, and password from step 2.
5. Under **Settings → Reading**, check **"Discourage search engines from indexing this site"** during staging.

---

## Step 3: Install the Custom Theme and Plugins (Follow Exact Order)

Always install and activate in this exact order so custom post types and image files are ready before the importer runs.

### 3A. Install the Custom Theme (`santa-towing.zip`)
1. In WordPress Admin, go to **Appearance → Themes → Add New Theme → Upload Theme**.
2. Choose `Santa WordPress Website Conversions/packages/santa-towing.zip` and click **Install Now**.
3. Click **Activate**.
   - *(Fallback if browser upload times out or says "The uploaded file exceeds upload_max_filesize"):* Open **cPanel → File Manager** → `staging.santatowingservices.com/wp-content/themes/` → **Upload** `santa-towing.zip` → right-click and **Extract** → confirm the folder path is `wp-content/themes/santa-towing/style.css` → delete the `.zip` file → activate **Santa Towing** under **Appearance → Themes**.

### 3B. Install Elementor FREE (Zero Pro / Zero Paid Addons)
See **Step 4** right below to install and configure **Elementor (Free)** from WordPress.org before running the importer.

### 3C. Install the Three Custom Plugins
For each plugin below, go to **Plugins → Add New Plugin → Upload Plugin**, choose the ZIP file, click **Install Now**, and click **Activate Plugin**:

1. **Upload & Activate Plugin 1:** `Santa WordPress Website Conversions/packages/santa-towing-website-core.zip`  
   *(Registers all 7 CPTs, 17 custom Elementor Free widgets, 301 legacy redirects, virtual service-area routes, XML sitemap, and contact/towing/career forms.)*
2. **Upload & Activate Plugin 2:** `Santa WordPress Website Conversions/packages/santa-towing-business-manager.zip`  
   *(Creates the native **Santa Manager** back-office dashboard, Customers CRM, Pro Forma Invoices, Receipts, PDF engine & Image Studio inside WordPress Admin — zero Elementor or external dependencies.)*
3. **Upload & Activate Plugin 3:** `Santa WordPress Website Conversions/packages/santa-towing-importer.zip`  
   *(Adds **Tools → Santa Full Importer** and **Tools → Santa Legacy Browser Importer** cleanly inside WordPress Admin.)*

> **cPanel Fallback:** Just like the theme, any plugin ZIP can also be uploaded and extracted directly into `wp-content/plugins/` via **cPanel → File Manager**, then activated under **Plugins → Installed Plugins**.

---

## Step 4: Configure Elementor Free (Required Styling Parity Settings)

The entire conversion uses **Elementor Free only**. Do **not** install Elementor Pro or any third-party Elementor widget packs.

1. In WordPress Admin, go to **Plugins → Add New Plugin**.
2. Search for **Elementor** (author: *Elementor.com*).
3. Click **Install Now**, then **Activate**. *(Skip any Elementor onboarding popups/wizards or click the "X" / Skip links — you do NOT need an Elementor Pro account.)*
4. Go to **Elementor → Settings → General**:
   - Check **Disable Default Colors** *(prevents Elementor from overriding Santa Towing's `--color-primary: #015557` teal, `#192e2e` dark green, `#d80000` red, and `#131514` dark neutral palette)*.
   - Check **Disable Default Fonts** *(preserves clean system typography parity)*.
   - Click **Save Changes**.
5. *(Optional)* Go to **Elementor → Settings → Features**: leave default stable features as-is. **Flexbox Container** is NOT required because our 15 templates use standard Elementor Free Sections/Columns with full responsive styling for maximum backwards compatibility.

---

## Step 5: Import Content, Pages, 261 Service Areas & 42 Media Assets

### 5A. Run a Safe Dry-Run Preview First
1. In WordPress Admin, go to **Tools → Santa Full Importer** *(or click **Full Site Importer** under **Plugins**).*
2. Leave **Import Scope** set to `Complete Site (Media + Settings + Menus + 70 CPTs + 26 Pages + 261 Areas + BM Defaults)`.
3. Check **Dry Run (Validate files and preview counts without writing to MySQL)**.
4. Click **Execute Full Migration**.
5. Confirm a green notice appears showing expected counts (`42` media assets, `4` menus, `70` CPTs/blog posts, `26` pages, `261` service areas, zero file errors).

### 5B. Run the Actual Migration
1. Uncheck **Dry Run**.
2. Click **Execute Full Migration** and wait 10–40 seconds for the page to finish reloading.
3. Verify the summary box reports:
   - `media_synced`: **42**
   - `settings`: **updated**
   - `menus_synced`: **4**
   - `cpts_synced`: `st_car: 12`, `st_rental: 6`, `st_part: 6`, `post: 6`, `st_faq: 28`, `st_gallery: 8`, `st_career: 4` (Total: **70**)
   - `pages_synced`: **26**
   - `areas_synced`: **261**
4. **If your shared cPanel host times out on 261 areas at once (HTTP 504 / Gateway Timeout):**
   - Run **Step 1 through Step 5** individually using the **Import Scope** dropdown (`media` → `settings` → `menus` → `cpts` → `pages` → `defaults`).
   - Then select **Step 6: 261 Service Area Pages Only** and run in 3 small batches:
     - Batch 1: `Offset: 0`, `Limit: 90`
     - Batch 2: `Offset: 90`, `Limit: 90`
     - Batch 3: `Offset: 180`, `Limit: 90`
   - Because the importer is **100% idempotent**, re-running any step will safely update existing items without creating duplicates.

### 5C. Verify Media Library
1. Go to **Media → Library**.
2. Confirm all **42** images (logo, avatars, vehicles, spare parts, haulage corridors, workshop gallery) appear cleanly with proper filenames (`santa-logo.png`, `image-1-towing-vehicle.jpg`, etc.) and Alt Text populated.

### 5D. Optional: Migrate Existing Browser Edits From the Old React Site
If you previously edited text, images, invoices, or receipts inside the old React app's browser `/admin` panel (which stored data in `localStorage` & `IndexedDB`):
1. Open the old React site in the same browser where your edits exist.
2. Open `Santa WordPress Website Conversions/migration/legacy-browser-export-tool.html` on your computer and follow its 1-click instructions to download `santa-legacy-browser-export.json`.
3. In WordPress Admin, go to **Tools → Santa Legacy Browser Importer** *(or **Santa Manager → Backup & Export**)*, upload that `.json` file, choose **Merge**, and click **Import Uploaded JSON**.

### 5E. Deactivate the Temporary Importer Plugin
Once the import finishes cleanly, go to **Plugins → Installed Plugins**, find **Santa Towing Full Site Importer**, and click **Deactivate** (and optionally **Delete**). Leaving it off keeps your admin clean while keeping 100% of your imported pages, media, CPTs, and settings intact.

---

## Step 6: Configure Homepage, Blog Page, Menus & Permalinks

The full importer automatically sets most of these options, but always double-check these 4 screens:

1. **Homepage & Blog Page (`Settings → Reading`):**
   - **Your homepage displays:** **A static page (select below)**
   - **Homepage:** `Home`
   - **Posts page:** `Blog`
   - Click **Save Changes**.
2. **Site Title & Email (`Settings → General`):**
   - **Site Title:** `Santa Towing Services`
   - **Administration Email Address:** Your active operations email (used for WordPress recovery & lead alerts; also verify lead email under **Santa Towing → Site Settings**).
   - **Timezone:** `UTC+0` or `Africa/Accra`.
   - Click **Save Changes**.
3. **Navigation Menus (`Appearance → Menus → Manage Locations`):**
   - Verify all 4 theme menu locations are assigned:
     - **Primary Navigation Menu** → `Primary Navigation`
     - **Footer Quick Links Menu** → `Footer Quick Links`
     - **Footer Services Menu** → `Footer Services`
     - **Footer Legal & Policy Menu** → `Footer Legal`
   - Click **Save Changes**.
4. **Permalinks & Rewrite Rules (`Settings → Permalinks`):**
   - Select **Post name** (`https://staging.santatowingservices.com/sample-post/`).
   - Click **Save Changes** twice to flush `.htaccess` rules.
   - Then open **Santa Towing → Routes & System** and click **Flush Rewrite Rules & Permalinks Now**.

---

## Step 7: Enable Free SSL (HTTPS) in cPanel

Modern browsers block uploads, admin cookies, and GPS/WhatsApp links cleanly only over HTTPS.

1. In **cPanel**, open **SSL/TLS Status** *(under **Security**)*.
2. Check the box next to `staging.santatowingservices.com` (and `www.staging...` if listed) and click **Run AutoSSL**.
   - Wait 1–3 minutes for **AutoSSL / Let's Encrypt** to issue a green padlock certificate. *(If DNS was only just added, wait 15–30 minutes for DNS propagation and retry.)*
3. In **cPanel → Domains**, turn ON **Force HTTPS Redirect** for `staging.santatowingservices.com`.
4. In **WordPress Admin → Settings → General**, verify BOTH URLs start with `https://`:
   - **WordPress Address (URL):** `https://staging.santatowingservices.com`
   - **Site Address (URL):** `https://staging.santatowingservices.com`
5. Save changes and confirm your browser shows a secure padlock with zero "Mixed Content" warnings in DevTools (`F12` → Console).

---

## Step 8: Complete Pre-Launch Testing Checklist

Before pointing your real domain to WordPress, test every critical area on staging:

1. **Public Pages & Elementor Templates:**
   - Visit `/`, `/services/`, `/towing/`, `/haulage/`, `/repairs/`, `/body-works/`, `/fleet/`, `/sales/`, `/rental/`, `/parts/`, `/blog/`, `/gallery/`, `/faqs/`, `/careers/`, `/contact/`, `/service-areas/`, `/service-areas/accra/`, `/privacy-policy/`, `/sitemap/`.
   - Open 2–3 pages in **Edit with Elementor** and verify all `17` **Santa Towing** widgets and styles render cleanly without console errors or horizontal scrolling on mobile (`375px`), tablet (`768px`), and desktop (`1440px`).
2. **Legacy Routes & 301 Redirects:**
   - Visit `/about/` → should `301` redirect to `/services/` (or open `/services/` cleanly).
   - Visit `/cars/` → `/sales/`; `/rentals/` → `/rental/`; `/part/` → `/parts/`; `/privacy/` → `/privacy-policy/`.
   - Visit `/sales/1`, `/rental/1`, `/parts/1`, `/blog/1` → should load their individual vehicle/part/article detail pages.
   - Visit `/service-areas/tema/` and `/service-areas/accra/emergency-towing/` → should return HTTP 200.
3. **Interactive Forms, WhatsApp & Email Delivery:**
   - Test **Towing Request Form** (`/towing/`): select a Region (e.g., *Greater Accra*) → verify the Town dropdown populates automatically → submit a test request → verify it saves under **Santa Towing → Inquiries & Leads**, delivers an email (if cPanel mail is active), and opens a valid `https://wa.me/233244753849?text=...` message.
   - Test **Haulage Quote**, **Contact Form**, **Footer Newsletter**, and **Careers Job Application** (with a small test `.pdf` CV upload).
4. **Search & XML Sitemap:**
   - Visit `/search?q=Accra` → verify matching service areas, vehicles, and blog posts highlight cleanly.
   - Visit `/sitemap.xml` → verify valid XML opens with pages, CPTs, and regional service-area routes.
5. **Business Manager (`wp-admin` → `Santa Manager`):**
   - Open **Customers**, **Pro Forma Invoices**, **Official Receipts**, **Image Studio**, **Pages Text**, **Colors**, **Logo**, **Links**, **Images**, **Backup & Export**, and **Profile**.
   - Create 1 test Customer + 1 test Invoice + click **Download PDF** (confirm clean A4 PDF opens) + create a linked Receipt (confirm parent invoice updates to `Paid` / `Partially Paid`), then delete the test records when done.
   - Open **Image Studio** → load a photo → crop & apply text watermark → **Save Copy to Media Library** → confirm it appears in **Media → Library**.

---

## Step 9: Going Live (Staging Subdomain → Main Domain Cutover)

When staging passes every test in Step 8, cut over to your live primary domain (e.g. `https://santatowingservices.com`):

1. **Take a Full Safety Backup First** (see **Step 10** below).
2. **Option A — Move Files or Point Primary Domain Document Root in cPanel (Recommended Zero-Downtime Cutover):**
   - If your old site was static React/HTML in `/public_html/`: archive/backup `/public_html/` into `/home/youruser/old-react-backup.zip`, then copy all staging WordPress files from `/staging.santatowingservices.com/` into `/public_html/` (or point your primary domain's Document Root in cPanel **Domains** if supported).
3. **Update WordPress Site URLs Safely (Preserving Elementor JSON):**
   - **Never** run raw SQL `REPLACE()` across `wp_postmeta` — PHP serialized data and Elementor URLs can break!
   - Instead:
     1. Temporarily set `WP_HOME` and `WP_SITEURL` in `wp-config.php` OR log into `wp-admin` → **Settings → General** and update both URLs from `https://staging.santatowingservices.com` to `https://santatowingservices.com`.
     2. Install the free **Better Search Replace** plugin from WordPress.org (or use WP-CLI `wp search-replace 'https://staging.santatowingservices.com' 'https://santatowingservices.com' --all-tables`), select all tables, uncheck *Run as dry run*, and run it cleanly *(it handles serialized arrays safely)*. Delete Better Search Replace afterward.
4. **CRITICAL — Enable Search Engine Indexing:**
   - Go to **Settings → Reading**.
   - **UNCHECK** *"Discourage search engines from indexing this site"*!
   - Click **Save Changes**.
5. **Refresh Permalinks & SSL on Live Domain:**
   - Run **AutoSSL** on `santatowingservices.com` + `www.santatowingservices.com` in cPanel **SSL/TLS Status**.
   - Go to **Settings → Permalinks** → click **Save Changes**.
   - Go to **Santa Towing → Routes & System** → click **Flush Rewrite Rules & Permalinks Now**.
   - Go to **Elementor → Tools → General** → click **Regenerate Files & Data**.
6. **Submit Sitemap to Google Search Console:**
   - Verify `https://santatowingservices.com` in Google Search Console and submit `https://santatowingservices.com/sitemap.xml`.

---

## Step 10: Backup & Restore Guide (Protect Your Site)

Keep **two layers** of backups before major edits or plugin updates:

### Layer 1: Complete cPanel Server Backup (Files + MySQL Database)
1. **Quick Full Backup (cPanel → Backup or JetBackup):**
   - Open **cPanel → Backup** → click **Download a Full Account Backup** (or use **JetBackup → Full Backups / Home Directory / Databases**) before major updates.
2. **Manual Files + Database Snapshot:**
   - **Database:** In **cPanel → phpMyAdmin**, select your WordPress database → click **Export → Quick (SQL) / Custom (gzipped)** → click **Export** to save `santa_db_YYYYMMDD.sql.gz`.
   - **Files:** In **cPanel → File Manager**, select `wp-content/` and `wp-config.php` → click **Compress (Zip Archive)** → download `wp-content-backup.zip` to your computer *(do not leave backup ZIPs sitting publicly inside `public_html/`)*.

### Layer 2: Instant Santa Towing Business Manager JSON Backup
1. Inside WordPress Admin, open **Santa Manager → Backup & Export**.
2. Click **Download Complete Backup JSON** (saves all site text, colors, logo, navigation links, image slot assignments, CRM customers, pro forma invoices, receipts, doc settings, and activity logs into a single portable `.json` file).
3. **To Restore Business Data:** Upload that `.json` file under **Santa Manager → Backup & Export** and select **Merge** (adds missing records) or **Replace** (restores exact snapshot).

### Emergency Rollback Steps
- If a bad edit or update ever breaks the live site:
  1. Restore `wp-content/` from your downloaded `wp-content-backup.zip` via **cPanel → File Manager**.
  2. In **cPanel → phpMyAdmin**, select all tables in your WordPress database → **Drop** them → click **Import** and upload `santa_db_YYYYMMDD.sql.gz`.
  3. Clear any hosting cache (LiteSpeed Cache / cPanel cache) and visit `https://santatowingservices.com/`.
