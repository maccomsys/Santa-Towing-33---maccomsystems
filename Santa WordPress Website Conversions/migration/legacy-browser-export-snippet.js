/**
 * Santa Towing — Legacy React Browser Storage Safe Export Snippet (Read-Only)
 *
 * Instructions:
 * 1. Open your OLD live Santa Towing React website (/admin) in Chrome, Edge, or Firefox.
 * 2. Open DevTools -> Console (F12 or Ctrl+Shift+J), paste this entire script, and press Enter.
 * 3. A single JSON archive `santa-towing-legacy-export-YYYY-MM-DD.json` downloads automatically.
 * 4. Upload that JSON file in WordPress -> Santa Manager -> Backup & Export -> Legacy React Importer
 *    OR Tools -> Santa Legacy Browser Importer.
 *
 * Safety Guarantees:
 * - Strictly READ-ONLY: Never writes, clears, or modifies localStorage, IndexedDB, or DOM state.
 * - Excludes sensitive sessionStorage login tokens (`santa_admin_*`, `santa_owner_edit_mode_active`).
 */
(async function exportSantaLegacyBrowserData() {
  'use strict';

  var SCHEMA = 'santa-legacy-browser-export-v1';
  var LOCAL_KEYS = {
    siteContentOverrides: 'santa_towing_custom_content_v1',
    docSettings: 'santa_doc_settings_v1',
    customers: 'santa_doc_customers_v1',
    invoices: 'santa_doc_invoices_v1',
    receipts: 'santa_doc_receipts_v1',
    activityLogs: 'santa_doc_activity_logs_v1',
    studioFallback: 'santa_studio_images_fallback_v1'
  };
  var EXCLUDED_SESSION_KEYS = [
    'santa_admin_authenticated_user',
    'santa_admin_session_expiry',
    'santa_admin_authenticated_session',
    'santa_owner_edit_mode_active'
  ];

  function safeJsonParse(raw, fallback) {
    if (typeof raw !== 'string' || !raw.trim()) return fallback;
    try {
      return JSON.parse(raw);
    } catch (err) {
      return fallback;
    }
  }

  function readLocal(key, fallback) {
    try {
      var val = window.localStorage.getItem(key);
      if (val === null) return fallback;
      return safeJsonParse(val, fallback);
    } catch (err) {
      return fallback;
    }
  }

  function readStudioIndexedDb() {
    return new Promise(function (resolve) {
      if (!('indexedDB' in window)) {
        resolve({ source: 'unavailable', images: [], warning: 'IndexedDB API unavailable in browser.' });
        return;
      }
      try {
        var req = window.indexedDB.open('santa_image_studio_db', 1);
        var settled = false;

        req.onupgradeneeded = function (ev) {
          // Do not mutate or initialize an empty store on read-only audit if it didn't exist.
          try {
            if (ev.target && ev.target.transaction) {
              ev.target.transaction.abort();
            }
          } catch (e) {}
          if (!settled) {
            settled = true;
            resolve({ source: 'none', images: [], warning: 'No santa_image_studio_db store found.' });
          }
        };

        req.onerror = function () {
          if (!settled) {
            settled = true;
            resolve({ source: 'error', images: [], warning: 'Could not open santa_image_studio_db.' });
          }
        };

        req.onsuccess = function () {
          if (settled) return;
          var db = req.result;
          if (!db.objectStoreNames.contains('studio_images')) {
            try { db.close(); } catch (e) {}
            settled = true;
            resolve({ source: 'none', images: [], warning: 'Object store studio_images not present.' });
            return;
          }
          try {
            var tx = db.transaction('studio_images', 'readonly');
            var store = tx.objectStore('studio_images');
            var getAll = store.getAll();
            getAll.onsuccess = function () {
              var rows = Array.isArray(getAll.result) ? getAll.result : [];
              try { db.close(); } catch (e) {}
              settled = true;
              resolve({ source: 'indexeddb', images: rows, warning: null });
            };
            getAll.onerror = function () {
              try { db.close(); } catch (e) {}
              settled = true;
              resolve({ source: 'error', images: [], warning: 'Failed reading studio_images rows.' });
            };
          } catch (e) {
            try { db.close(); } catch (err) {}
            settled = true;
            resolve({ source: 'error', images: [], warning: String(e && e.message ? e.message : e) });
          }
        };
      } catch (err) {
        resolve({ source: 'error', images: [], warning: String(err && err.message ? err.message : err) });
      }
    });
  }

  async function normalizeStudioImages(rawImages) {
    var out = [];
    for (var i = 0; i < rawImages.length; i++) {
      var img = rawImages[i] || {};
      var copy = Object.assign({}, img);
      var url = String(copy.dataUrl || '');

      // Convert same-origin blob: URLs safely via canvas without mutating IndexedDB
      if (url.indexOf('blob:') === 0) {
        try {
          var res = await fetch(url);
          var blob = await res.blob();
          copy.dataUrl = await new Promise(function (resData) {
            var reader = new FileReader();
            reader.onloadend = function () { resData(reader.result || url); };
            reader.onerror = function () { resData(url); };
            reader.readAsDataURL(blob);
          });
        } catch (e) {
          copy._blobWarning = 'Unresolvable blob URL excluded; keep original filename reference.';
        }
      }
      out.push(copy);
    }
    return out;
  }

  var rawOverrides = readLocal(LOCAL_KEYS.siteContentOverrides, {});
  if (!rawOverrides || typeof rawOverrides !== 'object' || Array.isArray(rawOverrides)) {
    rawOverrides = {};
  }

  var siteContentOverrides = {};
  var inventoryOverrides = {};
  var inventoryKeyMap = {
    'inventory.cars': 'cars',
    'inventory.rentals': 'rentals',
    'inventory.parts': 'parts',
    'inventory.gallery': 'gallery',
    'inventory.faqs': 'faqs',
    'inventory.careers': 'careers'
  };

  Object.keys(rawOverrides).forEach(function (k) {
    if (inventoryKeyMap[k] && Array.isArray(rawOverrides[k])) {
      inventoryOverrides[inventoryKeyMap[k]] = rawOverrides[k];
    } else {
      siteContentOverrides[k] = rawOverrides[k];
    }
  });

  var docSettings = readLocal(LOCAL_KEYS.docSettings, null);
  var customers = readLocal(LOCAL_KEYS.customers, []);
  var invoices = readLocal(LOCAL_KEYS.invoices, []);
  var receipts = readLocal(LOCAL_KEYS.receipts, []);
  var activityLogs = readLocal(LOCAL_KEYS.activityLogs, []);
  var fallbackStudio = readLocal(LOCAL_KEYS.studioFallback, []);

  var idbResult = await readStudioIndexedDb();
  var rawStudio = idbResult.images.length > 0 ? idbResult.images : (Array.isArray(fallbackStudio) ? fallbackStudio : []);
  var studioImages = await normalizeStudioImages(rawStudio);

  var presentLocalKeys = [];
  Object.keys(LOCAL_KEYS).forEach(function (alias) {
    try {
      if (window.localStorage.getItem(LOCAL_KEYS[alias]) !== null) {
        presentLocalKeys.push(LOCAL_KEYS[alias]);
      }
    } catch (e) {}
  });

  var payload = {
    schema: SCHEMA,
    exportedAt: new Date().toISOString(),
    sourceOrigin: window.location.origin,
    sourcePath: window.location.pathname,
    securityNotes: {
      readOnly: true,
      excludedSessionKeys: EXCLUDED_SESSION_KEYS
    },
    counts: {
      presentLocalKeys: presentLocalKeys.length,
      siteContentOverrideKeys: Object.keys(siteContentOverrides).length,
      inventoryCarOverrides: Array.isArray(inventoryOverrides.cars) ? inventoryOverrides.cars.length : 0,
      inventoryRentalOverrides: Array.isArray(inventoryOverrides.rentals) ? inventoryOverrides.rentals.length : 0,
      inventoryPartOverrides: Array.isArray(inventoryOverrides.parts) ? inventoryOverrides.parts.length : 0,
      inventoryGalleryOverrides: Array.isArray(inventoryOverrides.gallery) ? inventoryOverrides.gallery.length : 0,
      inventoryFaqOverrides: Array.isArray(inventoryOverrides.faqs) ? inventoryOverrides.faqs.length : 0,
      inventoryCareerOverrides: Array.isArray(inventoryOverrides.careers) ? inventoryOverrides.careers.length : 0,
      hasDocSettings: !!docSettings,
      customers: Array.isArray(customers) ? customers.length : 0,
      invoices: Array.isArray(invoices) ? invoices.length : 0,
      receipts: Array.isArray(receipts) ? receipts.length : 0,
      activityLogs: Array.isArray(activityLogs) ? activityLogs.length : 0,
      studioImages: studioImages.length,
      studioSource: idbResult.images.length > 0 ? 'indexeddb' : (Array.isArray(fallbackStudio) && fallbackStudio.length > 0 ? 'localStorage_fallback' : idbResult.source)
    },
    warnings: idbResult.warning ? [idbResult.warning] : [],
    data: {
      siteContentOverrides: siteContentOverrides,
      inventoryOverrides: inventoryOverrides,
      billing: {
        settings: docSettings,
        customers: Array.isArray(customers) ? customers : [],
        invoices: Array.isArray(invoices) ? invoices : [],
        receipts: Array.isArray(receipts) ? receipts : [],
        activityLogs: Array.isArray(activityLogs) ? activityLogs : []
      },
      studioImages: studioImages
    }
  };

  var dateStamp = new Date().toISOString().slice(0, 10);
  var filename = 'santa-towing-legacy-export-' + dateStamp + '.json';
  var jsonStr = JSON.stringify(payload, null, 2);
  var blob = new Blob([jsonStr], { type: 'application/json;charset=utf-8' });
  var dlUrl = URL.createObjectURL(blob);
  var anchor = document.createElement('a');
  anchor.href = dlUrl;
  anchor.download = filename;
  anchor.style.display = 'none';
  document.body.appendChild(anchor);
  anchor.click();
  setTimeout(function () {
    document.body.removeChild(anchor);
    URL.revokeObjectURL(dlUrl);
  }, 1500);

  if (typeof console !== 'undefined' && console.info) {
    console.info('[Santa Legacy Export] Downloaded ' + filename, payload.counts);
  }
  return payload.counts;
})();
