# Admin Dashboard, Billing & Studio Inventory — Santa Towing → WordPress

Complete audit of the React `/admin/*` management console (`src/admin/`), Role-Based Access Control (`auth.ts`), Inventory Sub-Managers, Billing & Pro Forma Invoice/Receipt engine (`jsPDF` + A4 print), Image Studio Canvas Editor (`IndexedDB`), and exact native WordPress Admin mapping.

---

## 1. Authentication & Role-Based Access Control (RBAC)

Source: `src/admin/lib/auth.ts`, `src/admin/components/AdminProtectedRoute.tsx`, `src/admin/pages/AdminLogin.tsx`

| Aspect | React Implementation | WordPress Target Mapping |
|---|---|---|
| **Accounts & Roles** | 2 hardcoded demo accounts using salted SHA-256 hashes:<br>1. **`admin` Role (`demo.admin@example.com`)**: Full access to all 14 console modules.<br>2. **`staff` Role (`demo.staff@example.com`)**: Blocked ONLY from `/admin/colors` and `/admin/logo`; permitted across Links, Pages/Text, Images, Inventory, Invoices, Receipts, Customers, Image Studio, Doc Settings, Backup, Profile. | WordPress Native `wp_users` + Custom Roles:<br>1. `administrator` / `st_admin`: Granted all `manage_options` + custom `st_manage_brand`, `st_manage_content`, `st_manage_billing` caps.<br>2. `st_staff` (Dispatch & Accounts Staff): Granted `upload_files`, `edit_posts`, `st_manage_content`, `st_manage_inventory`, `st_manage_billing`; **denied** `st_manage_brand` (Blocks Colors & Logo admin pages cleanly). |
| **Session & Gate** | 8-hour `sessionStorage` token (`santa_admin_authenticated_user`, `santa_admin_session_expiry`). Demo credential quick-fill buttons on `/admin/login`. Password reset in `AdminProfile` writes `adminPasswordHash` to `localStorage`. | Native WP cookie authentication (`/wp-login.php` or custom branded `/admin/login/` rewrite routing into WP auth with `wp_signon()`). No plain-text or demo auto-fill buttons in production WP unless explicitly toggled for staging! |
| **Security Rules** | `AdminProtectedRoute` redirects unauthenticated users to `/admin/login` and unauthorized `staff` from `colors`/`logo` back to `/admin`. | Server-side `current_user_can()` + `check_admin_referer()` / `wp_verify_nonce()` on every admin screen, AJAX handler, REST endpoint, and PDF download endpoint. |

---

## 2. Admin Shell & Shared UI Components (`src/admin/layout/` & `src/admin/components/`)

| Component | File | Purpose & Behavior | WP Admin Implementation |
|---|---|---|---|
| `AdminLayout` | `layout/AdminLayout.tsx` | Responsive shell with collapsible left `AdminSidebar`, sticky `AdminTopBar`, `AdminToastProvider`, mobile backdrop drawer, `EditModeContext` link ("Visual Edit Website"). | Dedicated clean WordPress Admin Console views (using custom scoped CSS inside `wp-admin` menu `Santa Towing Suite` OR custom full-screen WP Admin app pages free of WP clutter, respecting WP user permissions). |
| `AdminSidebar` | `layout/AdminSidebar.tsx` | Grouped navigation: *Overview* (Dashboard), *Brand & Design* (`Colors`*, `Logo`*), *Content & Media* (`Links`, `Pages & Text`, `Images`, `Image Studio`), *Operations* (`Inventory`), *Billing & Finance* (`Invoices`, `Receipts`, `Customers`, `Doc Settings`, `Backup & Export`), *System* (`Profile`). (* = `admin` role only). | Top-level `Santa Towing` WP Admin menu + submenus respecting `st_manage_brand` capability hiding Colors & Logo from `st_staff`. |
| `AdminTopBar` | `layout/AdminTopBar.tsx` | Breadcrumb/screen title, "View Live Site" external link, quick search/notifications badge, current user avatar + dropdown (`Profile`, `Sign Out`). | Standard WP Admin header + custom profile badge matching user meta avatar. |
| `AdminCard`, `CollapsibleSection`, `AdminField`, `AdminToggle`, `AdminColorPicker`, `AdminImagePicker`, `UnifiedImageModal`, `ReorderableList`, `ConfirmDialog`, `EmptyState`, `AdminToast` | `src/admin/components/*` | Polished UI toolkit enforcing min `44px` touch targets, accessible `aria-*` labels, live contrast badges, array move up/down (`ReorderableList`), confirm dialogs for destructive deletes, and multi-tab image picker (`Upload & Crop`, `Web URL`, `Site Media Library`). | Native HTML5/vanilla JS + WP `wp.media` Modal + custom AJAX-backed SortableJS/up-down controls preserving identical admin UX workflows inside WP Admin pages. |

---

## 3. Complete Screen-by-Screen Admin Inventory (15 Routes)

| # | Route & Screen Title | Source File | Role Gate | Fields, Controls & Validation | Storage Target (React → WP) |
|---|---|---|---|---|---|
| 1 | `/admin/login` — Console Sign-In | `AdminLogin.tsx` | Public gate | Email, Password (show/hide toggle), Remember note, Demo Role quick-fill (`admin` / `staff`). Error banner on mismatch. | SessionStorage → WP Auth Cookie (`wp_signon()`) |
| 2 | `/admin` — Operations Overview | `AdminDashboardHome.tsx` | `admin`, `staff` | Greeting card, live KPI summary cards (Cars, Rentals, Parts, FAQs, Invoices, Revenue Collected, LocalStorage quota %), Quick Action shortcuts, Visual Edit Mode launcher. | Aggregates `contentStorage` + `documentStorage` → SQL `COUNT`/`SUM` queries + `WP_Count_Posts` |
| 3 | `/admin/colors` — Brand Colors & Contrast | `AdminColors.tsx` | **`admin` only** | 5 color pickers (`theme.primary`, `secondary`, `accent`, `dark`, `background`), preset swatches, hex copy, **Real-time WCAG 2.1 Contrast Ratio Evaluator** (`AA >= 4.5`, `AAA >= 7.0`, `AA Large >= 3.0`), 4-tab live component simulator (`Button`, `NavBar`, `Card`, `Footer`), Reset to factory defaults. | `content.theme` → `santa_towing_site_content['theme']` + `:root` CSS vars + Elementor Global Colors sync |
| 4 | `/admin/logo` — Corporate Logo & Favicon | `AdminLogo.tsx` | **`admin` only** | Upload PNG/JPG/SVG/WebP (client-side validated max 1 MB -> Base64 data URI) or enter Web URL, dark vs light header live preview, browser tab favicon live preview, localStorage quota telemetry. Updates `global.logo.url`. | `content.global.logo.url` → WP Media Library Attachment ID/URL saved in `santa_towing_site_content['global']['logo']` + WP Site Icon (`site_icon`) |
| 5 | `/admin/links` — Contact, Social & Menus | `AdminLinks.tsx` | `admin`, `staff` | Phone (`0244753849`), auto-generated `tel:` & `wa.me/233...` URLs with editable prefilled WhatsApp dispatch message, Business Email, Address, Hours, Coverage area, 6 Social profile URLs (`facebook`, `twitter`, `instagram`, `linkedin`, `tiktok`, `youtube`), and **3 Reorderable/Toggleable Menus**: `global.nav.menuLinks` (11), `global.footer.quickLinks` (11), `global.footer.serviceLinks` (8). | `content.global` → `santa_towing_site_content['global']` (optionally synced to WP `nav_menu` / custom theme arrays) |
| 6 | `/admin/pages-text` — Pages & Copy Editor | `AdminPagesText.tsx` | `admin`, `staff` | Global text search across all tabs; **14 Page Tabs** (`Home`, `Towing`, `Haulage`, `Rental`, `Sales`, `Repairs`, `Body Works`, `Parts`, `Service Areas`, `Blog`, `Contact`, `Careers`, `FAQs`, `Policies` [12 sub-policies]); char counters + SEO guidance warnings (`Title <= 60`, `Desc <= 160`); per-field Reset; embedded **Blog Post CRUD** (`blog.posts`); embedded **FAQ Manager** syncing both `faqs.items` & `inventory.faqs`; **9 `POPULAR_SERVICE_AREAS`** heading/desc customizer. *(Limitation: only 9 of 261 service areas exposed here!)* | `content.*` → `santa_towing_site_content` + native WP `post`, `st_faq`, & all 261 `st_service_area` posts editable directly via CPT screen too |
| 7 | `/admin/images` — Site Image Catalog | `AdminImages.tsx` | `admin`, `staff` | 11 Filter Tabs (`All`, `Home`, `Towing`, `Haulage`, `Rental`, `Repairs`, `Parts`, `Blog`, `Contact`, `Careers`, `Global`), **31 `SITE_IMAGES` slots** with thumbnails, `"Used In"` hints, `"Replace"` button opening `UnifiedImageModal` (`Upload & Crop` / `URL` / `Library`), **mandatory Alt-Text** field, per-slot reset. | `content` path overrides → `santa_towing_image_overrides` option storing WP attachment ID, URL, and required `alt` text |
| 8 | `/admin/inventory` — Inventory Suite (6 Sub-Managers) | `AdminInventory.tsx` + `src/admin/pages/inventory/*` | `admin`, `staff` | 6 Tabs (`Cars`, `Rentals`, `Parts`, `Gallery`, `FAQs`, `Careers`); live search/filter; Add/Edit Modal; **Hide/Show toggle**; **Move Up/Down ordering**; Auto-slug generator (`generateSlug`); reusable sub-fields: `ImageGalleryField`, `SpecsField`, `StringListField`. (Details in Section 4 below). | `content.inventory.*` → Dedicated WP CPT Admin Tables + Unified Quick Inventory WP Admin Screen |
| 9 | `/admin/invoices` — Pro Forma Invoices | `AdminInvoices.tsx` + `CustomizableInvoiceModal` + `DocumentPreviewModal` | `admin`, `staff` | Filter tabs (`All`, `Draft`, `Issued`, `Accepted`, `Cancelled`), search by doc #/customer, summary totals strip (Total Quoted, Settled, Outstanding), **CSV Export**, **Duplicate as Draft**, **Convert/Record Payment → Receipt**, **Customizable Invoice Builder Modal**, **A4 Preview / Browser Print / PDF Export**. | `santa_doc_invoices_v1` → `wp_santa_invoices` + `wp_santa_invoice_items` + Dompdf PDF generator |
| 10 | `/admin/receipts` — Official Receipts | `AdminReceipts.tsx` + `DocumentPreviewModal` | `admin`, `staff` | Search/filter, Issue Official Receipt modal, link to any non-cancelled Invoice with auto balance calculation, 6 Payment Methods (`Mobile Money (MoMo)`, `Bank Transfer`, `Cash`, `Cheque`, `Credit/Debit Card`, `Other`), reference/slip #, statuses (`Issued`, `Corrected`, `Cancelled`), **CSV Export**, **A4 Print / PDF**. | `santa_doc_receipts_v1` → `wp_santa_receipts` (transactional update on linked `wp_santa_invoices.amount_paid` & `balance_due`) |
| 11 | `/admin/customers` — Customer Directory | `AdminCustomers.tsx` | `admin`, `staff` | Searchable CRM list + Add/Edit modal (`name*`, `companyName`, `phone*`, `email`, `address`, `notes`), computed per-customer total billed, total paid, balance due, document count; **Safe Delete Guard** blocking deletion if linked invoices or receipts exist. | `santa_doc_customers_v1` → `wp_santa_customers` |
| 12 | `/admin/image-studio` — Image Studio & Cropper | `AdminImageStudio.tsx` + `ImageCanvasEditor.tsx` | `admin`, `staff` | Upload queue (multi-file drag & drop -> opens `ImageCanvasEditor`), 6 category filters (`Fleet`, `Towing`, `Haulage`, `Garage`, `Documents`, `General`), Original vs Edited Copy filter, search, rename, download, **Save as New Copy** vs **Overwrite Original**. | `santa_image_studio_db` (IndexedDB) → WP Media Library (`wp_posts` attachment) + `st_studio_category` taxonomy + built-in Crop/Rotate/Flip/Watermark JS Canvas editor posting binary blobs directly via WP REST/Media API |
| 13 | `/admin/doc-settings` — Document & Tax Defaults | `AdminDocSettings.tsx` | `admin`, `staff` | Company Name, Tagline, Address, Primary/Secondary Phones, Email, Website, TIN/Tax Registration Number, Document Header Logo (`AdminImagePicker`), Default Tax Rate (`15%`), Default Validity Days (`14`), Default Payment Instructions (Bank/MoMo), Default Terms, Default Notes, Footer Text, Live Document Letterhead Preview. | `santa_doc_settings_v1` → `santa_towing_doc_settings` WP Option |
| 14 | `/admin/backup` — Billing Backup & Audit Logs | `AdminBackupExport.tsx` | `admin`, `staff` | Full Billing/Customer/Settings/Logs JSON Export (`santa-towing-billing-backup-{date}.json`), File Restore (Replace), Clear Demo Seed Data button, Last 15 System Activity Logs viewer. *(Note: separate from Content JSON backup in `AdminProfile`!)*. | `documentStorage` → PHP SQL/JSON export & import endpoints (`wp_santa_*`) |
| 15 | `/admin/profile` — Admin Profile, Security & Content Backup | `AdminProfile.tsx` | `admin`, `staff` | **4 Tabs:**<br>1. `Profile`: Display name, role label, email, **5 preset avatars** + custom upload (`AdminImagePicker`).<br>2. `Business`: Brand full/short name, tagline, dispatch contact person, primary phone/email/address.<br>3. `Security`: Change password (requires current password; salted SHA-256).<br>4. `Backup`: Export `contentStorage` JSON (`content` + `metadata` + `exportedAt`), Import JSON (`Merge` vs `Full Replace` confirmation modal), **Factory Reset** (`site-content.json`). Includes Storage Quota progress bar. | `wp_users` / `wp_usermeta` + `santa_towing_site_content` + native WP password reset + PHP JSON Content Backup/Restore/Factory Reset |

---

## 4. Inventory Sub-Managers (`src/admin/pages/inventory/`)

| Manager | CPT Target | Fields & Sub-Components | Validation & Features |
|---|---|---|---|
| `CarManager.tsx` | `st_car` | `id`, `slug`, `title`, `make`, `model`, `year`, `price`, `currency`, `category`, `condition`, `mileage`, `transmission`, `fuelType`, `status` (`Available`/`Sold`/`Reserved`), `badge`, `featured`, `hidden`, `image` (`AdminImagePicker`), `images[]` (`ImageGalleryField`), `specs[]` (`SpecsField`), `features[]` (`StringListField`), `description` | Auto slug from make+model+year; multi-image gallery; move up/down array reorder; visibility toggle (`hidden` -> WP `draft`). |
| `RentalManager.tsx` | `st_rental` | `id`, `slug`, `title`, `category`, `dailyRate`, `weeklyRate`, `monthlyRate`, `deposit`, `transmission`, `fuelType`, `seats`, `withDriver`, `status`, `hidden`, `image`, `images[]`, `specs[]`, `features[]`, `description` | Rental tier rate fields; status (`Available`/`Rented Out`/`Maintenance`). |
| `PartManager.tsx` | `st_part` | `id`, `slug`, `name`, `sku`, `brand`, `category`, `price`, `stockStatus` (`In Stock`/`Low Stock`/`Out of Stock`), `condition`, `warranty`, `compatibleModels`, `hidden`, `image`, `images[]`, `specs[]`, `description` | Part SKU search; stock badge styling; compatibility notes. |
| `GalleryManager.tsx` | `st_gallery` | `id`, `title`, `category`, `mediaType` (`photo`/`video`), `location`, `date`, `image`, `videoUrl`, `description`, `hidden` | Conditional `videoUrl` field when `mediaType === 'video'`. |
| `FaqManager.tsx` | `st_faq` | `id`, `question`, `answer`, `category`, `hidden` | Dual-synced in React with `content.faqs.items` & `content.inventory.faqs`; unified cleanly into single `st_faq` CPT in WP. |
| `CareerManager.tsx` | `st_career` | `id`, `title`, `department`, `location`, `type` (`Full-time`/`Part-time`/`Contract`), `salary`, `description`, `requirements[]` (`StringListField`), `benefits[]` (`StringListField`), `status` (`Open`/`Closed`), `hidden` | Status toggle controls public visibility & Application Drawer access on `/careers/`. |

---

## 5. Deep Dive: Billing Engine, `jsPDF` Layout & Image Studio

### 5.1 `CustomizableInvoiceModal` Workflows
- **Split-Screen Responsive Editor:** Left 50% Form Builder (`Form Editor`) + Right 50% Real-Time A4 Document Preview (`Live A4 Preview` with Fit/100% zoom).
- **Document Banner Title:** Editable (e.g., `PRO FORMA INVOICE`, `COMMERCIAL QUOTATION`, `TAX INVOICE`).
- **7 Default Reorderable, Renameable, Toggleable Sections:** `meta`, `customer`, `items`, `calculations`, `payment`, `terms`, `notes`. Any section can be moved Up/Down, renamed inline, or toggled hidden (`hiddenFields`).
- **Dynamic Custom Fields & Custom Sections:** Users can inject arbitrary Custom Fields (`text`, `number`, `date`, `textarea`, `select` w/ comma options) rendered in Bill-To, AND arbitrary Custom Sections (`title` + multi-line `content`).
- **Line Items & 7 Towing Service Presets:** Add/remove/reorder line items, quantity × unit price auto-totaling, plus 1-click preset selector (e.g., `Flatbed Emergency Towing (Accra Metropolitan)` GH₵ 750, `Heavy Duty 30-Ton Low-Bed Haulage` GH₵ 4,500, etc.).
- **Financial Engine (`calculateInvoiceTotals`):**
  1. `subtotal = sum(qty * unitPrice)`
  2. Optional Discount (`percentage` or `fixed`, custom label, toggleable) -> `discountAmount`
  3. `taxableBase = max(0, subtotal - discountAmount)`
  4. Optional Tax (`taxRate` %, default 15%, custom label, toggleable) -> `taxAmount = taxableBase * (taxRate / 100)`
  5. Optional Shipping/Haulage Fee (`shippingAmount`, custom label, toggleable)
  6. Unlimited Additional Surcharges (`additionalCharges[]`, each with enabled checkbox, label, GH₵ amount)
  7. `grandTotal = taxableBase + taxAmount + shippingAmount + sum(enabled additionalCharges)`
  8. `balanceDue = grandTotal - amountPaid`

### 5.2 `DocumentPreviewModal` (`jsPDF` Programmatic Spec + `#printable-document-root` A4 CSS)
When generating PDFs in React (`handleDownloadPdf()` in `DocumentPreviewModal.tsx`), `jsPDF` draws an exact A4 Portrait (`210mm × 297mm`) vector PDF using `helvetica` font:
- **Top Accent Bar:** Full-width `#015557` (`rgb(1, 85, 87)`) 6 mm top strip.
- **Left Company Block (`x=14mm`):** Company Name (16pt bold `#015557`), Tagline (8.5pt), Address + Primary/Secondary Phone, Email + Website + Tax Reg Number. *(Note: `jsPDF` omits the bitmap `logoUrl`, whereas `#printable-document-root` HTML print view renders the `<img src={settings.logoUrl}>` cleanly — WordPress **Dompdf** upgrades the PDF download to include the actual logo image too!).*
- **Right Document Badge (`x=196mm, right-aligned`):** `PRO FORMA INVOICE` or `OFFICIAL RECEIPT` (15pt bold `#015557`), Doc #, Date, Valid Until (invoices), Status uppercase.
- **Customer / Bill To Rounded Box (`y+6`, gray fill `rgb(248,250,252)`, width 182mm):** `CUSTOMER / BILL TO:` header + Customer Name + Phone/Email/Address line.
- **Body (Invoice vs Receipt):**
  - *Invoice:* `#015557` table header (`Description`, `Qty`, `Unit Price (GHS)`, `Total (GHS)`), zebra-striped rows, right-aligned totals column (`Subtotal`, conditional `Discount`, conditional `Taxes`, conditional `Shipping`, conditional `Additional Charges`, bold `Grand Total`, `Amount Paid`, red/teal `Balance Due`).
  - *Receipt:* Rounded summary box (`PAYMENT RECEIPT CONFIRMATION`, `AMOUNT RECEIVED: GH₵ X.XX` 14pt bold `#015557`, `Payment Method`, `Reference / Transaction ID`, `Linked Pro Forma Invoice`, `Remaining Balance`).
- **Remittance Instructions & Standard Service Terms** (multi-line wrapped via `splitTextToSize(..., 182)`).
- **Signature Lines (`y >= 248mm`):** Left `Customer Acceptance Signature` (`14..70mm`), Right `Santa Towing Authorised Signatory` (`135..196mm`), Centered Footer text at `y=290mm`.
- **File Download Name:** `{docNumber}.pdf`.

### 5.3 `ImageCanvasEditor` & `imageCompressor.ts`
- **Upload Pre-compression (`imageCompressor.ts`):** Defaults `maxWidth: 1440`, `maxHeight: 1080`, `quality: 0.82`, `mimeType: 'image/jpeg'` (fills white backdrop behind PNG transparency when exporting JPEG).
- **Canvas Editor (`ImageCanvasEditor.tsx`) Capabilities:**
  - 4 Tabs: `Crop`, `Transform` (`Rotate -90°`, `Rotate +90°`, `Flip H`, `Flip V`), `Overlay` (Watermark Text with font size/color/position/opacity OR Company Logo with scale 5–50%, preset or custom PNG upload, 7 anchor positions), `Export` (`JPEG`/`PNG`/`WebP`, compression slider `10–100%`, category tag, live estimated byte size).
  - Interactive 8-handle drag crop (`nw, n, ne, e, se, s, sw, w` + `move`), 7 aspect ratio modes (`original`, `1:1`, `3:2`, `2:3`, `16:9`, `9:16`, `custom`) with Padlock Lock/Unlock toggle, Undo/Redo stack, Before/After "Compare Original" toggle.
- **WordPress Parity Decision:** Since HTML5 `<canvas>` is **pure browser JavaScript** (no Node.js, no React dependency!), the exact `ImageCanvasEditor` crop/rotate/flip/watermark/format logic can be ported cleanly as a standalone **vanilla JS Canvas Editor modal** inside `santa-towing-core` / `santa-towing-studio`, posting the final `canvas.toBlob()` directly via `fetch()` to WP `wp_handle_upload` + `wp_insert_attachment` (`async-upload.php` / custom WP REST endpoint)!
