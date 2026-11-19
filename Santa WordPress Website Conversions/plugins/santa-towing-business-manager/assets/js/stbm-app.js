/**
 * Santa Towing Business Manager — Core Console Shell & Website/Operations Modules
 * Pure vanilla JS (no React, no iframes, no external runtime dependencies).
 */
(function () {
  'use strict';

  const boot = window.STBM_BOOT || {};
  const state = {
    boot: boot,
    activeView: boot.activeView || 'dashboard',
    siteContent: JSON.parse(JSON.stringify(boot.siteContent || {})),
    docSettings: JSON.parse(JSON.stringify(boot.docSettings || {})),
    customers: Array.isArray(boot.customers) ? boot.customers : [],
    invoices: Array.isArray(boot.invoices) ? boot.invoices : [],
    receipts: Array.isArray(boot.receipts) ? boot.receipts : [],
    studioImages: Array.isArray(boot.studioImages) ? boot.studioImages : [],
    logs: Array.isArray(boot.logs) ? boot.logs : [],
    inventory: boot.inventory || { cars: [], rentals: [], parts: [], gallery: [], faqs: [], careers: [] },
    user: boot.user || {},
    previewDevice: 'desktop',
    ui: {
      invTab: 'cars',
      invSearch: '',
      invFilter: 'All',
      pagesTab: 'Home',
      pagesSearch: '',
      imagesPageFilter: 'All',
      profileTab: 'profile',
      simulatorTab: 'buttons',
      mobileSidebarOpen: false,
      userMenuOpen: false,
    },
  };

  function esc(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function money(num) {
    const cur = (state.docSettings && state.docSettings.currency) || 'GH₵';
    const val = Number(num || 0);
    return cur + ' ' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function dotGet(obj, path, fallback) {
    if (!obj || !path) return fallback;
    const parts = String(path).split('.');
    let cur = obj;
    for (let i = 0; i < parts.length; i++) {
      const k = parts[i];
      if (cur === null || cur === undefined || typeof cur !== 'object') return fallback;
      const key = /^\d+$/.test(k) && Array.isArray(cur) ? Number(k) : k;
      if (!(key in cur)) return fallback;
      cur = cur[key];
    }
    return cur === undefined || cur === null ? fallback : cur;
  }

  function dotSet(obj, path, val) {
    const parts = String(path).split('.');
    let cur = obj;
    for (let i = 0; i < parts.length - 1; i++) {
      const k = parts[i];
      const nextIsNum = /^\d+$/.test(parts[i + 1]);
      if (!cur[k] || typeof cur[k] !== 'object') {
        cur[k] = nextIsNum ? [] : {};
      }
      cur = cur[k];
    }
    const last = parts[parts.length - 1];
    cur[/^\d+$/.test(last) && Array.isArray(cur) ? Number(last) : last] = val;
  }

  function toast(message, type) {
    const stack = document.getElementById('stbm-toast-root');
    if (!stack) return;
    const el = document.createElement('div');
    el.className = 'stbm-toast ' + (type || 'info');
    el.innerHTML = '<span>' + esc(message) + '</span>';
    stack.appendChild(el);
    setTimeout(function () {
      el.style.opacity = '0';
      el.style.transition = 'opacity 0.25s ease';
      setTimeout(function () {
        if (el.parentNode) el.parentNode.removeChild(el);
      }, 260);
    }, 3400);
  }

  function ajax(action, payload) {
    const form = new URLSearchParams();
    form.append('action', action);
    form.append('nonce', state.boot.nonce || '');
    form.append('payload', JSON.stringify(payload || {}));

    return fetch(state.boot.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: form.toString(),
    })
      .then(function (r) { return r.json(); })
      .then(function (json) {
        if (!json || !json.success) {
          const msg = (json && json.data && json.data.message) || 'Operation failed.';
          throw new Error(msg);
        }
        if (json.data.siteContent) state.siteContent = json.data.siteContent;
        if (json.data.docSettings) state.docSettings = json.data.docSettings;
        if (json.data.customers) state.customers = json.data.customers;
        if (json.data.invoices) state.invoices = json.data.invoices;
        if (json.data.receipts) state.receipts = json.data.receipts;
        if (json.data.studioImages) state.studioImages = json.data.studioImages;
        if (json.data.logs) state.logs = json.data.logs;
        return json.data;
      });
  }

  function exportUrl(action, params) {
    const u = new URL(state.boot.adminPostUrl, window.location.origin);
    u.searchParams.set('action', action);
    u.searchParams.set('_wpnonce', state.boot.exportNonce || '');
    Object.keys(params || {}).forEach(function (k) {
      u.searchParams.set(k, params[k]);
    });
    return u.toString();
  }

  let activeModalCleanup = null;
  function bindModalDismiss(onClose) {
    if (typeof activeModalCleanup === 'function') activeModalCleanup();
    const root = document.getElementById('stbm-modal-root');
    if (!root) return function () {};

    function dismiss() {
      if (typeof activeModalCleanup === 'function') activeModalCleanup();
      activeModalCleanup = null;
      root.innerHTML = '';
      if (typeof onClose === 'function') onClose();
    }
    function onKey(ev) {
      if (ev.key === 'Escape') dismiss();
    }
    const backdrop = root.querySelector('.stbm-modal-backdrop');
    if (backdrop) {
      backdrop.addEventListener('click', function (ev) {
        if (ev.target === backdrop) dismiss();
      });
    }
    document.addEventListener('keydown', onKey);
    activeModalCleanup = function () {
      document.removeEventListener('keydown', onKey);
    };
    return dismiss;
  }

  function confirmDialog(opts) {
    const root = document.getElementById('stbm-modal-root');
    if (!root) return;
    const html =
      '<div class="stbm-modal-backdrop" id="stbm-confirm-backdrop">' +
      '  <div class="stbm-modal" style="max-width:460px">' +
      '    <div class="stbm-modal-head"><h3>' + esc(opts.title || 'Confirm Action') + '</h3>' +
      '      <button type="button" class="stbm-btn-icon" data-close-confirm>&times;</button></div>' +
      '    <div class="stbm-modal-body"><p style="margin:0;color:#475569;font-size:13.5px">' + esc(opts.description || '') + '</p></div>' +
      '    <div class="stbm-modal-foot">' +
      '      <button type="button" class="stbm-btn stbm-btn-secondary" data-close-confirm>' + esc(opts.cancelLabel || 'Cancel') + '</button>' +
      '      <button type="button" class="stbm-btn ' + (opts.isDestructive ? 'stbm-btn-danger' : 'stbm-btn-primary') + '" id="stbm-confirm-yes">' + esc(opts.confirmLabel || 'Confirm') + '</button>' +
      '    </div>' +
      '  </div>' +
      '</div>';
    root.innerHTML = html;
    const dismiss = bindModalDismiss();
    root.querySelectorAll('[data-close-confirm]').forEach(function (btn) {
      btn.addEventListener('click', dismiss);
    });
    document.getElementById('stbm-confirm-yes').addEventListener('click', function () {
      dismiss();
      if (typeof opts.onConfirm === 'function') opts.onConfirm();
    });
  }

  function openWpMediaPicker(onSelect, title) {
    if (window.wp && window.wp.media) {
      const frame = window.wp.media({
        title: title || 'Select or Upload Image',
        button: { text: 'Use Selected Image' },
        multiple: false,
        library: { type: 'image' },
      });
      frame.on('select', function () {
        const att = frame.state().get('selection').first().toJSON();
        if (onSelect && att && att.url) {
          onSelect(att.url, att.alt || att.title || '');
        }
      });
      frame.open();
      return;
    }
    toast('WordPress Media Library not available on this screen; paste an image URL instead.', 'info');
  }

  function openUnifiedImageModal(opts) {
    const root = document.getElementById('stbm-modal-root');
    if (!root) return;
    let url = opts.currentUrl || '';
    let alt = opts.currentAlt || '';

    function draw() {
      const studioThumbs = (state.studioImages || []).slice(0, 8).map(function (img) {
        return '<button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-pick-studio="' + esc(img.dataUrl) + '" style="padding:4px;flex-direction:column;width:105px">' +
          '<img src="' + esc(img.dataUrl) + '" style="width:94px;height:56px;object-fit:cover;border-radius:6px">' +
          '<span style="font-size:10px;max-width:94px;overflow:hidden;text-overflow:ellipsis">' + esc(img.name) + '</span></button>';
      }).join('');

      root.innerHTML =
        '<div class="stbm-modal-backdrop">' +
        '  <div class="stbm-modal" style="max-width:680px">' +
        '    <div class="stbm-modal-head"><h3>' + esc(opts.title || 'Replace Image') + '</h3>' +
        '      <button type="button" class="stbm-btn-icon" id="stbm-imgm-close">&times;</button></div>' +
        '    <div class="stbm-modal-body">' +
        '      <div style="background:#0f172a;border-radius:12px;height:210px;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:14px">' +
        (url ? '<img src="' + esc(url) + '" style="max-width:100%;max-height:100%;object-fit:contain">' : '<span style="color:#94a3b8">No image selected</span>') +
        '      </div>' +
        '      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">' +
        '        <button type="button" class="stbm-btn stbm-btn-primary stbm-btn-sm" id="stbm-imgm-wp">Open WordPress Media Library</button>' +
        '        <label class="stbm-btn stbm-btn-secondary stbm-btn-sm" style="cursor:pointer">Upload Local File<input type="file" id="stbm-imgm-file" accept="image/*" style="display:none"></label>' +
        '      </div>' +
        '      <div class="stbm-field"><label class="stbm-field-label">Image URL</label><input class="stbm-input" id="stbm-imgm-url" value="' + esc(url) + '"></div>' +
        (opts.showAlt ? '<div class="stbm-field"><label class="stbm-field-label">SEO Alt Text (Mandatory for Accessibility)</label><input class="stbm-input" id="stbm-imgm-alt" value="' + esc(alt) + '"></div>' : '') +
        (studioThumbs ? '<div class="stbm-field"><label class="stbm-field-label">Or Pick from Image Studio</label><div style="display:flex;flex-wrap:wrap;gap:8px">' + studioThumbs + '</div></div>' : '') +
        '    </div>' +
        '    <div class="stbm-modal-foot">' +
        '      <button type="button" class="stbm-btn stbm-btn-secondary" id="stbm-imgm-cancel">Cancel</button>' +
        '      <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-imgm-apply">Apply Image</button>' +
        '    </div>' +
        '  </div>' +
        '</div>';

      const dismiss = bindModalDismiss();
      document.getElementById('stbm-imgm-close').onclick = dismiss;
      document.getElementById('stbm-imgm-cancel').onclick = dismiss;
      document.getElementById('stbm-imgm-wp').onclick = function () {
        openWpMediaPicker(function (pickedUrl, pickedAlt) {
          url = pickedUrl;
          if (!alt && pickedAlt) alt = pickedAlt;
          draw();
        }, opts.title);
      };
      document.getElementById('stbm-imgm-file').onchange = function (e) {
        const f = e.target.files && e.target.files[0];
        if (!f) return;
        const reader = new FileReader();
        reader.onload = function () {
          // Upload into Image Studio / WP Media so URL is persistent
          ajax('stbm_save_studio_image', {
            name: f.name.replace(/\.[^.]+$/, ''),
            dataUrl: reader.result,
            category: 'General',
            format: f.type || 'image/jpeg',
          }).then(function (data) {
            if (data.image && data.image.dataUrl) {
              url = data.image.dataUrl;
              toast('Uploaded to WordPress Media Library', 'success');
              draw();
            }
          }).catch(function (err) { toast(err.message, 'error'); });
        };
        reader.readAsDataURL(f);
      };
      root.querySelectorAll('[data-pick-studio]').forEach(function (btn) {
        btn.onclick = function () {
          url = btn.getAttribute('data-pick-studio');
          draw();
        };
      });
      document.getElementById('stbm-imgm-apply').onclick = function () {
        const finalUrl = document.getElementById('stbm-imgm-url').value.trim();
        const finalAlt = opts.showAlt ? document.getElementById('stbm-imgm-alt').value.trim() : '';
        if (opts.showAlt && !finalAlt) {
          toast('Alt text is mandatory for SEO & Accessibility.', 'error');
          return;
        }
        dismiss();
        if (typeof opts.onApply === 'function') opts.onApply(finalUrl, finalAlt);
      };
    }
    draw();
  }

  /* ---------------- Icon SVGs ---------------- */
  function svg(name) {
    const map = {
      'dashboard': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>',
      'customers': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
      'inventory': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
      'invoices': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>',
      'receipts': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M8 7h8"/><path d="M8 11h8"/><path d="M8 15h5"/></svg>',
      'image-studio': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2Z"/></svg>',
      'doc-settings': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>',
      'pages-text': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>',
      'colors': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 22 1-1h3l9-9"/><path d="M3 21v-3l9-9"/><path d="m15 6 3.4-3.4a2.1 2.1 0 1 1 3 3L18 9l.4.4a2.1 2.1 0 1 1-3 3l-3.8-3.8a2.1 2.1 0 1 1 3-3l.4.4Z"/></svg>',
      'logo': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
      'links': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
      'images': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>',
      'backup': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>',
      'profile': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
      'search': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
      'menu': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>',
      'external': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>',
    };
    return map[name] || map['dashboard'];
  }

  /* ---------------- Shell Rendering (Sidebar + TopBar) ---------------- */
  function renderSidebar() {
    const el = document.getElementById('stbm-sidebar');
    if (!el) return;
    const routes = state.boot.routes || [];
    const caps = (state.user && state.user.caps) || {};
    const canBrand = !!caps.brand;
    const logoUrl = dotGet(state.siteContent, 'global.logo.url', state.boot.pluginUrl + 'assets/images/santa-logo.png');

    const groups = {};
    routes.forEach(function (r) {
      if ((r.view === 'colors' || r.view === 'logo') && !canBrand) return;
      if (!groups[r.group]) groups[r.group] = [];
      groups[r.group].push(r);
    });

    let navHtml = '';
    Object.keys(groups).forEach(function (grp) {
      navHtml += '<div class="stbm-nav-group-label">' + esc(grp) + '</div>';
      groups[grp].forEach(function (r) {
        const isActive = state.activeView === r.view;
        let badge = '';
        if (r.view === 'invoices') badge = '<span class="stbm-nav-badge">' + state.invoices.length + '</span>';
        if (r.view === 'receipts') badge = '<span class="stbm-nav-badge">' + state.receipts.length + '</span>';
        if (r.view === 'customers') badge = '<span class="stbm-nav-badge">' + state.customers.length + '</span>';
        navHtml += '<button type="button" class="stbm-nav-item ' + (isActive ? 'active' : '') + '" data-nav="' + esc(r.view) + '" data-slug="' + esc(r.slug) + '">' +
          svg(r.view) + '<span>' + esc(r.label) + '</span>' + badge + '</button>';
      });
    });

    el.className = 'stbm-sidebar' + (state.ui.mobileSidebarOpen ? ' open' : '');
    el.innerHTML =
      '<div class="stbm-sidebar-brand">' +
      '  <img src="' + esc(logoUrl) + '" alt="Santa Towing">' +
      '  <div><div class="stbm-sidebar-title">Santa Manager</div><div class="stbm-sidebar-sub">Operations Console</div></div>' +
      '</div>' +
      '<nav class="stbm-sidebar-nav">' + navHtml + '</nav>' +
      '<div class="stbm-sidebar-footer">' +
      '  <a href="' + esc(state.boot.homeUrl || '/') + '" target="_blank" class="stbm-sidebar-site-btn">' + svg('external') + ' View Live Website</a>' +
      '</div>';

    el.querySelectorAll('[data-nav]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        navigate(btn.getAttribute('data-nav'), btn.getAttribute('data-slug'));
      });
    });
  }

  function renderTopBar() {
    const el = document.getElementById('stbm-topbar');
    if (!el) return;
    const routes = state.boot.routes || [];
    const current = routes.find(function (r) { return r.view === state.activeView; }) || routes[0] || { label: 'Dashboard', group: 'Overview' };
    const u = state.user || {};

    el.innerHTML =
      '<div class="stbm-topbar-left">' +
      '  <button type="button" class="stbm-mobile-menu-btn" id="stbm-mobile-toggle">' + svg('menu') + '</button>' +
      '  <div><div class="stbm-topbar-crumb">' + esc(current.group || 'Console') + '</div><h1 class="stbm-topbar-title">' + esc(current.label) + '</h1></div>' +
      '</div>' +
      '<div class="stbm-topbar-right">' +
      '  <a href="' + esc(state.boot.homeUrl || '/') + '" target="_blank" class="stbm-btn stbm-btn-secondary stbm-btn-sm">' + svg('external') + ' Preview Site</a>' +
      '  <div class="stbm-user-pill">' +
      '    <button type="button" class="stbm-user-btn" id="stbm-user-toggle">' +
      '      <img src="' + esc(u.avatar || '') + '" alt="">' +
      '      <span class="stbm-user-meta"><span class="stbm-user-name">' + esc(u.displayName || 'Admin') + '</span><span class="stbm-user-role">' + esc(u.roleLabel || 'Administrator') + '</span></span>' +
      '    </button>' +
      '    <div class="stbm-dropdown ' + (state.ui.userMenuOpen ? '' : 'stbm-hidden') + '" id="stbm-user-dropdown">' +
      '      <button type="button" class="stbm-dropdown-item" data-goto-profile>' + svg('profile') + ' My Profile & Security</button>' +
      '      <a href="' + esc(state.boot.logoutUrl || '#') + '" class="stbm-dropdown-item danger">Sign Out of WordPress</a>' +
      '    </div>' +
      '  </div>' +
      '</div>';

    document.getElementById('stbm-mobile-toggle').addEventListener('click', function () {
      state.ui.mobileSidebarOpen = !state.ui.mobileSidebarOpen;
      renderSidebar();
      document.getElementById('stbm-mobile-backdrop').classList.toggle('stbm-hidden', !state.ui.mobileSidebarOpen);
    });
    document.getElementById('stbm-user-toggle').addEventListener('click', function (e) {
      e.stopPropagation();
      state.ui.userMenuOpen = !state.ui.userMenuOpen;
      document.getElementById('stbm-user-dropdown').classList.toggle('stbm-hidden', !state.ui.userMenuOpen);
    });
    el.querySelector('[data-goto-profile]').addEventListener('click', function () {
      state.ui.userMenuOpen = false;
      navigate('profile', 'stbm-profile');
    });
  }

  function navigate(view, slug) {
    const caps = (state.user && state.user.caps) || {};
    if ((view === 'colors' || view === 'logo') && !caps.brand) {
      toast('Access Restricted: Only Administrators / Business Managers can access Brand Colors & Logo.', 'error');
      return;
    }
    state.activeView = view;
    state.ui.mobileSidebarOpen = false;
    document.getElementById('stbm-mobile-backdrop').classList.add('stbm-hidden');
    if (slug && window.history && window.history.replaceState) {
      const nextUrl = new URL(window.location.href);
      nextUrl.searchParams.set('page', slug);
      window.history.replaceState({}, '', nextUrl.toString());
    }
    renderShell();
  }

  function renderShell() {
    renderSidebar();
    renderTopBar();
    renderActiveModule();
  }

  /* ---------------- Pure-CSS Website Live Preview Widget ---------------- */
  function buildWebsitePreviewHtml(opts) {
    const c = state.siteContent || {};
    const t = c.theme || {};
    const primary = t.primary || '#015557';
    const secondary = t.secondary || '#192e2e';
    const accent = t.accent || '#d80000';
    const logoUrl = dotGet(c, 'global.logo.url', '');
    const phone = dotGet(c, 'global.links.phone', '0244753849');
    const heroHeadline = dotGet(c, 'home.hero.slides.0.headline', '24/7 Emergency Towing Services');
    const heroSub = dotGet(c, 'home.hero.slides.0.subheadline', 'Fast response, safe car transport, affordability, and reliability.');
    const heroImg = dotGet(c, 'home.hero.slides.0.image', '');
    const isMobile = state.previewDevice === 'mobile';

    return (
      '<div class="stbm-preview-sticky">' +
      '  <div class="stbm-card">' +
      '    <div class="stbm-card-head">' +
      '      <div><h3>' + esc((opts && opts.title) || 'Live Website Preview') + '</h3><p class="stbm-card-sub">Real-time design reflection</p></div>' +
      '      <div style="display:flex;gap:6px">' +
      '        <button type="button" class="stbm-btn stbm-btn-sm ' + (!isMobile ? 'stbm-btn-primary' : 'stbm-btn-secondary') + '" data-prev-dev="desktop">Desktop</button>' +
      '        <button type="button" class="stbm-btn stbm-btn-sm ' + (isMobile ? 'stbm-btn-primary' : 'stbm-btn-secondary') + '" data-prev-dev="mobile">Mobile</button>' +
      '      </div>' +
      '    </div>' +
      '    <div class="stbm-card-body" style="background:#f1f5f9;padding:14px">' +
      '      <div class="stbm-device-frame ' + (isMobile ? 'mobile' : '') + '">' +
      '        <div style="background:' + esc(secondary) + ';color:#fff;padding:6px 12px;font-size:10px;display:flex;justify-content:space-between">' +
      '          <span>' + esc(dotGet(c, 'global.links.hours', '24/7 Towing Service')) + '</span><span>Tel: ' + esc(phone) + '</span>' +
      '        </div>' +
      '        <div style="background:#fff;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0">' +
      '          <img src="' + esc(logoUrl) + '" alt="Logo" style="height:26px;max-width:120px;object-fit:contain">' +
      '          <span style="background:' + esc(accent) + ';color:#fff;padding:4px 10px;border-radius:999px;font-size:10px;font-weight:700">Call ' + esc(phone) + '</span>' +
      '        </div>' +
      '        <div style="position:relative;padding:28px 18px;color:#fff;background:' + esc(secondary) + ';background-image:linear-gradient(120deg, rgba(1,85,87,0.88), rgba(19,21,20,0.85)), url(\'' + esc(heroImg) + '\');background-size:cover;background-position:center">' +
      '          <span style="display:inline-block;background:' + esc(primary) + ';padding:3px 9px;border-radius:999px;font-size:9px;font-weight:700;margin-bottom:8px">' + esc(dotGet(c, 'global.brand.name', 'Santa Towing')) + '</span>' +
      '          <h4 style="margin:0 0 6px;font-size:16px;font-weight:800;line-height:1.2;color:#fff">' + esc(heroHeadline) + '</h4>' +
      '          <p style="margin:0 0 12px;font-size:11px;color:#e2e8f0;line-height:1.4">' + esc(heroSub) + '</p>' +
      '          <div style="display:flex;gap:8px;flex-wrap:wrap">' +
      '            <span style="background:' + esc(primary) + ';color:#fff;padding:6px 12px;border-radius:999px;font-size:10px;font-weight:700">Call Now</span>' +
      '            <span style="background:#fff;color:' + esc(primary) + ';padding:6px 12px;border-radius:999px;font-size:10px;font-weight:700">WhatsApp Dispatch</span>' +
      '          </div>' +
      '        </div>' +
      '        <div style="padding:14px;display:grid;grid-template-columns:' + (isMobile ? '1fr' : 'repeat(3,1fr)') + ';gap:8px;background:#fff">' +
      '          <div style="padding:10px;border:1px solid #e2e8f0;border-radius:10px;font-size:11px"><strong style="color:' + esc(primary) + '">24/7 Towing</strong><div style="color:#64748b;font-size:10px">Nationwide Fleet</div></div>' +
      '          <div style="padding:10px;border:1px solid #e2e8f0;border-radius:10px;font-size:11px"><strong style="color:' + esc(primary) + '">Auto Repairs</strong><div style="color:#64748b;font-size:10px">Certified Garage</div></div>' +
      '          <div style="padding:10px;border:1px solid #e2e8f0;border-radius:10px;font-size:11px"><strong style="color:' + esc(primary) + '">Car Rentals</strong><div style="color:#64748b;font-size:10px">Daily & Corporate</div></div>' +
      '        </div>' +
      '      </div>' +
      '    </div>' +
      '  </div>' +
      '</div>'
    );
  }

  function bindPreviewButtons(container) {
    container.querySelectorAll('[data-prev-dev]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        state.previewDevice = btn.getAttribute('data-prev-dev');
        renderActiveModule();
      });
    });
  }

  /* =====================================================================
   * MODULE 1: DASHBOARD (`AdminDashboardHome` parity)
   * ===================================================================== */
  function renderDashboard(vp) {
    const totalCollected = state.receipts
      .filter(function (r) { return r.status !== 'Cancelled'; })
      .reduce(function (s, r) { return s + Number(r.amountReceived || 0); }, 0);
    const totalInvoiced = state.invoices
      .filter(function (i) { return i.status !== 'Cancelled'; })
      .reduce(function (s, i) { return s + Number(i.grandTotal || 0); }, 0);
    const pendingBalance = state.invoices
      .filter(function (i) { return i.status !== 'Cancelled'; })
      .reduce(function (s, i) { return s + Number(i.balanceDue || 0); }, 0);
    const totalInventory =
      state.inventory.cars.length +
      state.inventory.rentals.length +
      state.inventory.parts.length;

    const recentLogs = (state.logs || []).slice(0, 6).map(function (l) {
      return '<tr><td><strong>' + esc(l.entityTitle || l.entityType) + '</strong><div style="font-size:11px;color:#64748b">' + esc(l.details) + '</div></td>' +
        '<td><span class="stbm-badge stbm-badge-gray">' + esc(l.action) + '</span></td>' +
        '<td style="font-size:12px;color:#64748b;white-space:nowrap">' + esc(l.user) + '</td></tr>';
    }).join('') || '<tr><td colspan="3" style="text-align:center;color:#94a3b8">No recent audit entries</td></tr>';

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Operations Dashboard</h2><p>Billing performance, fleet inventory status, and live website configuration.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <button type="button" class="stbm-btn stbm-btn-secondary" data-jump="receipts">Issue Official Receipt</button>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" data-jump="invoices">+ New Pro Forma Invoice</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-grid stbm-grid-4" style="margin-bottom:22px">' +
      '  <div class="stbm-kpi-card"><div><div class="stbm-kpi-label">Revenue Collected</div><div class="stbm-kpi-val">' + esc(money(totalCollected)) + '</div><div class="stbm-kpi-sub">' + state.receipts.length + ' official receipts</div></div><div class="stbm-kpi-icon emerald">' + svg('receipts') + '</div></div>' +
      '  <div class="stbm-kpi-card"><div><div class="stbm-kpi-label">Active Pro Forma Value</div><div class="stbm-kpi-val">' + esc(money(totalInvoiced)) + '</div><div class="stbm-kpi-sub">Balance due: ' + esc(money(pendingBalance)) + '</div></div><div class="stbm-kpi-icon teal">' + svg('invoices') + '</div></div>' +
      '  <div class="stbm-kpi-card"><div><div class="stbm-kpi-label">Customers & Inventory</div><div class="stbm-kpi-val">' + state.customers.length + ' / ' + totalInventory + '</div><div class="stbm-kpi-sub">CRM clients / Vehicles & Parts</div></div><div class="stbm-kpi-icon indigo">' + svg('customers') + '</div></div>' +
      '  <div class="stbm-kpi-card"><div><div class="stbm-kpi-label">Image Studio Library</div><div class="stbm-kpi-val">' + state.studioImages.length + ' Assets</div><div class="stbm-kpi-sub">' + state.inventory.faqs.length + ' FAQs · ' + state.inventory.careers.length + ' Careers</div></div><div class="stbm-kpi-icon amber">' + svg('image-studio') + '</div></div>' +
      '</div>' +
      '<div class="stbm-split-screen">' +
      '  <div style="display:flex;flex-direction:column;gap:20px">' +
      '    <div class="stbm-card">' +
      '      <div class="stbm-card-head"><div><h3>Quick Website Identity & Dispatch Hotline</h3><p class="stbm-card-sub">Updates save directly to WordPress database</p></div>' +
      '        <button type="button" class="stbm-btn stbm-btn-primary stbm-btn-sm" id="stbm-dash-save">Save Quick Settings</button></div>' +
      '      <div class="stbm-card-body">' +
      '        <div class="stbm-grid stbm-grid-2">' +
      '          <div class="stbm-field"><label class="stbm-field-label">Company Name</label><input class="stbm-input" id="stbm-d-name" value="' + esc(dotGet(state.siteContent, 'global.brand.name', '')) + '"></div>' +
      '          <div class="stbm-field"><label class="stbm-field-label">24/7 Hotline Number</label><input class="stbm-input" id="stbm-d-phone" value="' + esc(dotGet(state.siteContent, 'global.links.phone', '')) + '"></div>' +
      '          <div class="stbm-field"><label class="stbm-field-label">WhatsApp Number</label><input class="stbm-input" id="stbm-d-wa" value="' + esc(dotGet(state.siteContent, 'global.links.whatsappNumber', '')) + '"></div>' +
      '          <div class="stbm-field"><label class="stbm-field-label">Dispatch Email</label><input class="stbm-input" id="stbm-d-email" value="' + esc(dotGet(state.siteContent, 'global.links.email', '')) + '"></div>' +
      '        </div>' +
      '        <div class="stbm-field"><label class="stbm-field-label">Office / Garage Address</label><input class="stbm-input" id="stbm-d-addr" value="' + esc(dotGet(state.siteContent, 'global.links.address', '')) + '"></div>' +
      '        <div class="stbm-field" style="margin-bottom:0"><label class="stbm-field-label">Homepage Slide 1 Headline</label><input class="stbm-input" id="stbm-d-hero" value="' + esc(dotGet(state.siteContent, 'home.hero.slides.0.headline', '')) + '"></div>' +
      '      </div>' +
      '    </div>' +
      '    <div class="stbm-card">' +
      '      <div class="stbm-card-head"><div><h3>Recent Operations Audit Log</h3><p class="stbm-card-sub">Latest actions across billing, studio & website</p></div>' +
      '        <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-jump="backup">Full Audit Log</button></div>' +
      '      <div class="stbm-table-wrap"><table class="stbm-table"><thead><tr><th>Activity</th><th>Action</th><th>User</th></tr></thead><tbody>' + recentLogs + '</tbody></table></div>' +
      '    </div>' +
      '  </div>' +
      '  <div>' + buildWebsitePreviewHtml({ title: 'Live Public Site Snapshot' }) + '</div>' +
      '</div>';

    vp.querySelectorAll('[data-jump]').forEach(function (b) {
      b.addEventListener('click', function () {
        const v = b.getAttribute('data-jump');
        const r = (state.boot.routes || []).find(function (x) { return x.view === v; });
        navigate(v, r ? r.slug : undefined);
      });
    });
    document.getElementById('stbm-dash-save').addEventListener('click', function () {
      const updates = {
        'global.brand.name': document.getElementById('stbm-d-name').value.trim(),
        'global.links.phone': document.getElementById('stbm-d-phone').value.trim(),
        'global.links.phoneDisplay': document.getElementById('stbm-d-phone').value.trim(),
        'global.links.whatsappNumber': document.getElementById('stbm-d-wa').value.trim(),
        'global.links.email': document.getElementById('stbm-d-email').value.trim(),
        'global.links.address': document.getElementById('stbm-d-addr').value.trim(),
        'home.hero.slides.0.headline': document.getElementById('stbm-d-hero').value.trim(),
      };
      ajax('stbm_save_content', { updates: updates }).then(function () {
        toast('Quick configuration saved to WordPress database!', 'success');
        renderDashboard(vp);
      }).catch(function (err) { toast(err.message, 'error'); });
    });
    bindPreviewButtons(vp);
  }

  /* =====================================================================
   * MODULE 3: UNIFIED INVENTORY (Cars, Rentals, Parts, Gallery, FAQs, Careers)
   * ===================================================================== */
  function renderInventory(vp) {
    const tab = state.ui.invTab || 'cars';
    const items = state.inventory[tab] || [];
    const search = (state.ui.invSearch || '').toLowerCase();
    const filter = state.ui.invFilter || 'All';

    const tabsMeta = [
      { id: 'cars', label: 'Cars for Sale', count: state.inventory.cars.length },
      { id: 'rentals', label: 'Rental Fleet', count: state.inventory.rentals.length },
      { id: 'parts', label: 'Auto Parts', count: state.inventory.parts.length },
      { id: 'gallery', label: 'Work Gallery', count: state.inventory.gallery.length },
      { id: 'faqs', label: 'FAQs', count: state.inventory.faqs.length },
      { id: 'careers', label: 'Careers', count: state.inventory.careers.length },
    ];

    const filtered = items.filter(function (it) {
      const text = JSON.stringify(it).toLowerCase();
      if (search && text.indexOf(search) === -1) return false;
      if (filter !== 'All') {
        const cat = it.type || it.category || it.department || it.status || '';
        if (String(cat) !== filter) return false;
      }
      return true;
    });

    let filterOpts = ['All'];
    items.forEach(function (it) {
      const val = it.type || it.category || it.department || '';
      if (val && filterOpts.indexOf(val) === -1) filterOpts.push(val);
    });

    const rowsHtml = filtered.map(function (it, idx) {
      const title = it.name || it.title || it.q || 'Untitled';
      const sub = tab === 'cars' ? (it.type + ' · ' + it.year + ' · ' + it.transmission) :
                  tab === 'rentals' ? (it.category + ' · ' + it.seats + ' seats · ' + it.daily + '/day') :
                  tab === 'parts' ? (it.brand + ' · ' + it.category + ' · SKU ' + (it.sku || '-')) :
                  tab === 'gallery' ? (it.category + ' · ' + (it.span || '')) :
                  tab === 'faqs' ? ('Category: ' + it.category) :
                  (it.department + ' · ' + it.location + ' · ' + it.type);
      const thumb = it.image || it.url || (it.images && it.images[0] && it.images[0].url) || '';
      const statusBadge = it.hidden ? '<span class="stbm-badge stbm-badge-gray">Hidden</span>' :
        '<span class="stbm-badge stbm-badge-green">' + esc(it.status || 'Published') + '</span>';

      return '<tr>' +
        '<td>' + (thumb ? '<img class="stbm-table-thumb" src="' + esc(thumb) + '">' : '<span class="stbm-badge stbm-badge-gray">Item</span>') + '</td>' +
        '<td><strong>' + esc(title) + '</strong><div style="font-size:11.5px;color:#64748b">' + esc(sub) + '</div></td>' +
        '<td>' + esc(it.price || it.daily || it.location || it.category || '-') + '</td>' +
        '<td>' + statusBadge + '</td>' +
        '<td style="text-align:right;white-space:nowrap">' +
        '  <button type="button" class="stbm-btn-icon" data-inv-up="' + esc(it.id) + '" title="Move Up" ' + (idx === 0 ? 'disabled' : '') + '>&uarr;</button> ' +
        '  <button type="button" class="stbm-btn-icon" data-inv-down="' + esc(it.id) + '" title="Move Down" ' + (idx === filtered.length - 1 ? 'disabled' : '') + '>&darr;</button> ' +
        '  <button type="button" class="stbm-btn-icon" data-inv-vis="' + esc(it.id) + '" title="Toggle Visibility">' + (it.hidden ? 'Show' : 'Hide') + '</button> ' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-inv-edit="' + esc(it.id) + '">Edit</button> ' +
        '  <button type="button" class="stbm-btn-icon danger" data-inv-del="' + esc(it.id) + '">&times;</button>' +
        '</td>' +
        '</tr>';
    }).join('') || '<tr><td colspan="5" style="text-align:center;padding:30px;color:#64748b">No items match your search or filter.</td></tr>';

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Inventory & Catalogs</h2><p>Manage Cars, Rental Fleet, Auto Parts, Gallery, FAQs & Careers stored in WordPress CPTs.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <button type="button" class="stbm-btn stbm-btn-secondary" id="stbm-inv-reset">Reset Default ' + esc(tab.toUpperCase()) + '</button>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-inv-add">+ Add New Item</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-tabs">' +
      tabsMeta.map(function (t) {
        return '<button type="button" class="stbm-tab ' + (tab === t.id ? 'active' : '') + '" data-inv-tab="' + t.id + '">' + esc(t.label) + ' <span class="stbm-badge stbm-badge-gray">' + t.count + '</span></button>';
      }).join('') +
      '</div>' +
      '<div class="stbm-card">' +
      '  <div class="stbm-card-body">' +
      '    <div class="stbm-toolbar">' +
      '      <div class="stbm-search-box">' + svg('search') + '<input class="stbm-input" id="stbm-inv-search" placeholder="Search ' + esc(tab) + '..." value="' + esc(state.ui.invSearch) + '"></div>' +
      '      <select class="stbm-select" id="stbm-inv-filter" style="width:auto;min-width:170px">' +
      filterOpts.map(function (o) { return '<option value="' + esc(o) + '" ' + (filter === o ? 'selected' : '') + '>' + esc(o) + '</option>'; }).join('') +
      '      </select>' +
      '    </div>' +
      '    <div class="stbm-table-wrap"><table class="stbm-table"><thead><tr><th style="width:70px">Media</th><th>Details</th><th>Price / Group</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead><tbody>' + rowsHtml + '</tbody></table></div>' +
      '  </div>' +
      '</div>';

    vp.querySelectorAll('[data-inv-tab]').forEach(function (b) {
      b.onclick = function () {
        state.ui.invTab = b.getAttribute('data-inv-tab');
        state.ui.invFilter = 'All';
        state.ui.invSearch = '';
        renderInventory(vp);
      };
    });
    document.getElementById('stbm-inv-search').oninput = function (e) {
      state.ui.invSearch = e.target.value;
      renderInventory(vp);
      const inp = document.getElementById('stbm-inv-search');
      if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
    };
    document.getElementById('stbm-inv-filter').onchange = function (e) {
      state.ui.invFilter = e.target.value;
      renderInventory(vp);
    };
    document.getElementById('stbm-inv-add').onclick = function () {
      openInventoryEditor(tab, null, vp);
    };
    document.getElementById('stbm-inv-reset').onclick = function () {
      confirmDialog({
        title: 'Restore Factory Default ' + tab.toUpperCase() + '?',
        description: 'This replaces current items in this category with the original starter catalog.',
        confirmLabel: 'Restore Defaults',
        isDestructive: true,
        onConfirm: function () {
          ajax('stbm_reset_inventory', { type: tab }).then(function (data) {
            state.inventory[tab] = data.items || [];
            toast('Default ' + tab + ' restored!', 'success');
            renderInventory(vp);
          }).catch(function (err) { toast(err.message, 'error'); });
        },
      });
    };

    vp.querySelectorAll('[data-inv-edit]').forEach(function (b) {
      b.onclick = function () {
        const id = Number(b.getAttribute('data-inv-edit'));
        const item = items.find(function (x) { return Number(x.id) === id; });
        openInventoryEditor(tab, item, vp);
      };
    });
    vp.querySelectorAll('[data-inv-vis]').forEach(function (b) {
      b.onclick = function () {
        const id = Number(b.getAttribute('data-inv-vis'));
        const item = items.find(function (x) { return Number(x.id) === id; });
        if (!item) return;
        const clone = JSON.parse(JSON.stringify(item));
        clone.hidden = !clone.hidden;
        ajax('stbm_save_inventory_item', { type: tab, item: clone }).then(function (data) {
          state.inventory[tab] = data.items || [];
          toast(clone.hidden ? 'Item hidden from public website.' : 'Item published live.', 'success');
          renderInventory(vp);
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    });
    vp.querySelectorAll('[data-inv-del]').forEach(function (b) {
      b.onclick = function () {
        const id = Number(b.getAttribute('data-inv-del'));
        confirmDialog({
          title: 'Delete Inventory Item?',
          description: 'Are you sure you want to permanently delete this item?',
          confirmLabel: 'Delete Item',
          isDestructive: true,
          onConfirm: function () {
            ajax('stbm_delete_inventory_item', { type: tab, id: id }).then(function (data) {
              state.inventory[tab] = data.items || [];
              toast('Item deleted.', 'info');
              renderInventory(vp);
            }).catch(function (err) { toast(err.message, 'error'); });
          },
        });
      };
    });
    vp.querySelectorAll('[data-inv-up],[data-inv-down]').forEach(function (b) {
      b.onclick = function () {
        const isUp = b.hasAttribute('data-inv-up');
        const id = Number(b.getAttribute(isUp ? 'data-inv-up' : 'data-inv-down'));
        const pos = items.findIndex(function (x) { return Number(x.id) === id; });
        if (pos < 0) return;
        const swap = isUp ? pos - 1 : pos + 1;
        if (swap < 0 || swap >= items.length) return;
        const reordered = items.slice();
        const tmp = reordered[pos];
        reordered[pos] = reordered[swap];
        reordered[swap] = tmp;
        ajax('stbm_reorder_inventory', { type: tab, ids: reordered.map(function (x) { return x.id; }) }).then(function (data) {
          state.inventory[tab] = data.items || [];
          renderInventory(vp);
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    });
  }

  function openInventoryEditor(tab, existing, vp) {
    const root = document.getElementById('stbm-modal-root');
    const item = existing ? JSON.parse(JSON.stringify(existing)) : {
      name: '', title: '', type: tab === 'cars' ? 'New' : (tab === 'careers' ? 'Full-time' : 'Sedan'),
      category: tab === 'parts' ? 'Car Batteries' : (tab === 'gallery' ? 'Workshop' : (tab === 'faqs' ? 'Towing' : 'Sedan')),
      status: tab === 'careers' ? 'Open' : (tab === 'faqs' || tab === 'gallery' ? 'Published' : (tab === 'parts' ? 'In Stock' : 'Available')),
      price: '', year: 2024, fuel: 'Petrol', transmission: 'Automatic', mileage: '0 km',
      daily: 'GH₵ 500', weekly: 'GH₵ 3,200', monthly: 'GH₵ 12,000', seats: 5,
      brand: '', sku: '', warranty: '', compatibility: '', installation: '',
      department: 'Towing & Recovery', location: 'Accra Main Workshop',
      q: '', a: '', description: '', image: '', url: '', alt: '', span: 'col-span-1 row-span-1',
      images: [], features: [], responsibilities: [], requirements: [], benefits: [],
      specs: tab === 'parts' ? [] : { engine: [], performance: [], dimensions: [], features: [] },
      seoTitle: '', seoDescription: '', hidden: false,
    };

    function drawModal() {
      let specificHtml = '';
      if (tab === 'cars') {
        const fList = (item.specs && item.specs.features) || [];
        specificHtml =
          '<div class="stbm-grid stbm-grid-2">' +
          '  <div class="stbm-field"><label class="stbm-field-label">Car Name</label><input class="stbm-input" id="inv-name" value="' + esc(item.name) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Condition</label><select class="stbm-select" id="inv-type"><option ' + (item.type === 'New' ? 'selected' : '') + '>New</option><option ' + (item.type === 'Foreign Used' ? 'selected' : '') + '>Foreign Used</option><option ' + (item.type === 'Ghana Used' ? 'selected' : '') + '>Ghana Used</option></select></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Price</label><input class="stbm-input" id="inv-price" value="' + esc(item.price) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Status</label><select class="stbm-select" id="inv-status"><option ' + (item.status === 'Available' ? 'selected' : '') + '>Available</option><option ' + (item.status === 'Reserved' ? 'selected' : '') + '>Reserved</option><option ' + (item.status === 'Sold' ? 'selected' : '') + '>Sold</option><option ' + (item.status === 'Coming Soon' ? 'selected' : '') + '>Coming Soon</option></select></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Year</label><input type="number" class="stbm-input" id="inv-year" value="' + esc(item.year) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Mileage</label><input class="stbm-input" id="inv-mileage" value="' + esc(item.mileage) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Fuel</label><input class="stbm-input" id="inv-fuel" value="' + esc(item.fuel) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Transmission</label><input class="stbm-input" id="inv-trans" value="' + esc(item.transmission) + '"></div>' +
          '</div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Key Features (one per line)</label><textarea class="stbm-textarea" id="inv-car-features">' + esc(fList.join('\n')) + '</textarea></div>';
      } else if (tab === 'rentals') {
        specificHtml =
          '<div class="stbm-grid stbm-grid-2">' +
          '  <div class="stbm-field"><label class="stbm-field-label">Vehicle Name</label><input class="stbm-input" id="inv-name" value="' + esc(item.name) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Category</label><select class="stbm-select" id="inv-cat"><option>Economy</option><option>Sedan</option><option>SUV</option><option>Pickup</option><option>Luxury</option><option>Van</option></select></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Daily Rate</label><input class="stbm-input" id="inv-daily" value="' + esc(item.daily) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Weekly Rate</label><input class="stbm-input" id="inv-weekly" value="' + esc(item.weekly) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Monthly Rate</label><input class="stbm-input" id="inv-monthly" value="' + esc(item.monthly) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Seats</label><input type="number" class="stbm-input" id="inv-seats" value="' + esc(item.seats) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Transmission</label><input class="stbm-input" id="inv-trans" value="' + esc(item.transmission) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Status</label><select class="stbm-select" id="inv-status"><option>Available</option><option>Rented</option><option>Maintenance</option><option>Reserved</option></select></div>' +
          '</div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Rental Features (one per line)</label><textarea class="stbm-textarea" id="inv-r-features">' + esc((item.features || []).join('\n')) + '</textarea></div>';
      } else if (tab === 'parts') {
        const specLines = (Array.isArray(item.specs) ? item.specs : []).map(function (s) { return s.label + ': ' + s.value; }).join('\n');
        specificHtml =
          '<div class="stbm-grid stbm-grid-2">' +
          '  <div class="stbm-field"><label class="stbm-field-label">Part Name</label><input class="stbm-input" id="inv-name" value="' + esc(item.name) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Brand</label><input class="stbm-input" id="inv-brand" value="' + esc(item.brand) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Category</label><input class="stbm-input" id="inv-cat" value="' + esc(item.category) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Price</label><input class="stbm-input" id="inv-price" value="' + esc(item.price) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">SKU Code</label><input class="stbm-input" id="inv-sku" value="' + esc(item.sku) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Stock Status</label><select class="stbm-select" id="inv-status"><option>In Stock</option><option>Low Stock</option><option>Out of Stock</option></select></div>' +
          '</div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Warranty</label><input class="stbm-input" id="inv-warranty" value="' + esc(item.warranty) + '"></div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Compatibility</label><textarea class="stbm-textarea" id="inv-compat">' + esc(item.compatibility) + '</textarea></div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Specs (Label: Value per line)</label><textarea class="stbm-textarea" id="inv-p-specs">' + esc(specLines) + '</textarea></div>';
      } else if (tab === 'gallery') {
        specificHtml =
          '<div class="stbm-grid stbm-grid-2">' +
          '  <div class="stbm-field"><label class="stbm-field-label">Title</label><input class="stbm-input" id="inv-title" value="' + esc(item.title) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Category</label><input class="stbm-input" id="inv-cat" value="' + esc(item.category) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Grid Span Class</label><input class="stbm-input" id="inv-span" value="' + esc(item.span) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Status</label><select class="stbm-select" id="inv-status"><option>Published</option><option>Draft</option></select></div>' +
          '</div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Alt Text</label><input class="stbm-input" id="inv-alt" value="' + esc(item.alt) + '"></div>';
      } else if (tab === 'faqs') {
        specificHtml =
          '<div class="stbm-grid stbm-grid-2">' +
          '  <div class="stbm-field"><label class="stbm-field-label">Category</label><input class="stbm-input" id="inv-cat" value="' + esc(item.category) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Status</label><select class="stbm-select" id="inv-status"><option>Published</option><option>Draft</option></select></div>' +
          '</div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Question</label><input class="stbm-input" id="inv-q" value="' + esc(item.q) + '"></div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Answer</label><textarea class="stbm-textarea" id="inv-a" rows="4">' + esc(item.a) + '</textarea></div>';
      } else if (tab === 'careers') {
        specificHtml =
          '<div class="stbm-grid stbm-grid-2">' +
          '  <div class="stbm-field"><label class="stbm-field-label">Job Title</label><input class="stbm-input" id="inv-title" value="' + esc(item.title) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Department</label><input class="stbm-input" id="inv-dept" value="' + esc(item.department) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Location</label><input class="stbm-input" id="inv-loc" value="' + esc(item.location) + '"></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Employment Type</label><select class="stbm-select" id="inv-type"><option>Full-time</option><option>Part-time</option><option>Contract</option></select></div>' +
          '  <div class="stbm-field"><label class="stbm-field-label">Status</label><select class="stbm-select" id="inv-status"><option>Open</option><option>Closed</option><option>Draft</option></select></div>' +
          '</div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Responsibilities (one per line)</label><textarea class="stbm-textarea" id="inv-resp">' + esc((item.responsibilities || []).join('\n')) + '</textarea></div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Requirements (one per line)</label><textarea class="stbm-textarea" id="inv-req">' + esc((item.requirements || []).join('\n')) + '</textarea></div>' +
          '<div class="stbm-field"><label class="stbm-field-label">Benefits (one per line)</label><textarea class="stbm-textarea" id="inv-ben">' + esc((item.benefits || []).join('\n')) + '</textarea></div>';
      }

      const showMedia = tab !== 'faqs';
      const imgUrl = item.image || item.url || '';

      root.innerHTML =
        '<div class="stbm-modal-backdrop">' +
        '  <div class="stbm-modal" style="max-width:780px">' +
        '    <div class="stbm-modal-head"><h3>' + (existing ? 'Edit' : 'Create') + ' ' + esc(tab.toUpperCase()) + ' Item</h3>' +
        '      <button type="button" class="stbm-btn-icon" id="inv-m-close">&times;</button></div>' +
        '    <div class="stbm-modal-body">' +
        specificHtml +
        (tab !== 'faqs' ? '<div class="stbm-field"><label class="stbm-field-label">Description</label><textarea class="stbm-textarea" id="inv-desc">' + esc(item.description) + '</textarea></div>' : '') +
        (showMedia ?
          '<div class="stbm-field"><label class="stbm-field-label">Primary Image URL</label>' +
          '  <div style="display:flex;gap:8px"><input class="stbm-input" id="inv-image" value="' + esc(imgUrl) + '">' +
          '  <button type="button" class="stbm-btn stbm-btn-secondary" id="inv-pick-img">Select Media</button></div>' +
          '</div>' : '') +
        '      <div class="stbm-grid stbm-grid-2">' +
        '        <div class="stbm-field"><label class="stbm-field-label">SEO Title</label><input class="stbm-input" id="inv-seot" value="' + esc(item.seoTitle) + '"></div>' +
        '        <div class="stbm-field"><label class="stbm-field-label">SEO Description</label><input class="stbm-input" id="inv-seod" value="' + esc(item.seoDescription) + '"></div>' +
        '      </div>' +
        '    </div>' +
        '    <div class="stbm-modal-foot">' +
        '      <button type="button" class="stbm-btn stbm-btn-secondary" id="inv-m-cancel">Cancel</button>' +
        '      <button type="button" class="stbm-btn stbm-btn-primary" id="inv-m-save">Save Item</button>' +
        '    </div>' +
        '  </div>' +
        '</div>';

      if (document.getElementById('inv-cat') && item.category) document.getElementById('inv-cat').value = item.category;
      if (document.getElementById('inv-status') && item.status) document.getElementById('inv-status').value = item.status;
      if (document.getElementById('inv-type') && item.type) document.getElementById('inv-type').value = item.type;

      document.getElementById('inv-m-close').onclick = function () { root.innerHTML = ''; };
      document.getElementById('inv-m-cancel').onclick = function () { root.innerHTML = ''; };
      if (document.getElementById('inv-pick-img')) {
        document.getElementById('inv-pick-img').onclick = function () {
          openWpMediaPicker(function (u) {
            document.getElementById('inv-image').value = u;
          }, 'Choose Inventory Image');
        };
      }
      document.getElementById('inv-m-save').onclick = function () {
        if (tab === 'cars') {
          item.name = document.getElementById('inv-name').value.trim();
          item.type = document.getElementById('inv-type').value;
          item.price = document.getElementById('inv-price').value.trim();
          item.status = document.getElementById('inv-status').value;
          item.year = Number(document.getElementById('inv-year').value || 2024);
          item.mileage = document.getElementById('inv-mileage').value.trim();
          item.fuel = document.getElementById('inv-fuel').value.trim();
          item.transmission = document.getElementById('inv-trans').value.trim();
          item.specs = item.specs || {};
          item.specs.features = document.getElementById('inv-car-features').value.split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
        } else if (tab === 'rentals') {
          item.name = document.getElementById('inv-name').value.trim();
          item.category = document.getElementById('inv-cat').value;
          item.type = item.category;
          item.daily = document.getElementById('inv-daily').value.trim();
          item.price = item.daily + ' / day';
          item.weekly = document.getElementById('inv-weekly').value.trim();
          item.monthly = document.getElementById('inv-monthly').value.trim();
          item.seats = Number(document.getElementById('inv-seats').value || 5);
          item.transmission = document.getElementById('inv-trans').value.trim();
          item.status = document.getElementById('inv-status').value;
          item.features = document.getElementById('inv-r-features').value.split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
        } else if (tab === 'parts') {
          item.name = document.getElementById('inv-name').value.trim();
          item.brand = document.getElementById('inv-brand').value.trim();
          item.category = document.getElementById('inv-cat').value.trim();
          item.price = document.getElementById('inv-price').value.trim();
          item.sku = document.getElementById('inv-sku').value.trim();
          item.status = document.getElementById('inv-status').value;
          item.warranty = document.getElementById('inv-warranty').value.trim();
          item.compatibility = document.getElementById('inv-compat').value.trim();
          item.specs = document.getElementById('inv-p-specs').value.split('\n').map(function (line) {
            const idx = line.indexOf(':');
            return idx > 0 ? { label: line.slice(0, idx).trim(), value: line.slice(idx + 1).trim() } : null;
          }).filter(Boolean);
        } else if (tab === 'gallery') {
          item.title = document.getElementById('inv-title').value.trim();
          item.category = document.getElementById('inv-cat').value.trim();
          item.span = document.getElementById('inv-span').value.trim();
          item.status = document.getElementById('inv-status').value;
          item.alt = document.getElementById('inv-alt').value.trim();
        } else if (tab === 'faqs') {
          item.category = document.getElementById('inv-cat').value.trim();
          item.status = document.getElementById('inv-status').value;
          item.q = document.getElementById('inv-q').value.trim();
          item.a = document.getElementById('inv-a').value.trim();
        } else if (tab === 'careers') {
          item.title = document.getElementById('inv-title').value.trim();
          item.department = document.getElementById('inv-dept').value.trim();
          item.location = document.getElementById('inv-loc').value.trim();
          item.type = document.getElementById('inv-type').value;
          item.status = document.getElementById('inv-status').value;
          item.responsibilities = document.getElementById('inv-resp').value.split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
          item.requirements = document.getElementById('inv-req').value.split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
          item.benefits = document.getElementById('inv-ben').value.split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
        }
        if (document.getElementById('inv-desc')) item.description = document.getElementById('inv-desc').value;
        if (document.getElementById('inv-image')) {
          const im = document.getElementById('inv-image').value.trim();
          item.image = im;
          item.url = im;
        }
        item.seoTitle = document.getElementById('inv-seot').value.trim();
        item.seoDescription = document.getElementById('inv-seod').value.trim();

        ajax('stbm_save_inventory_item', { type: tab, item: item }).then(function (data) {
          state.inventory[tab] = data.items || [];
          root.innerHTML = '';
          toast('Inventory item saved!', 'success');
          renderInventory(vp);
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    }
    drawModal();
  }

  /* =====================================================================
   * MODULE 8: PAGES & TEXT (`AdminPagesText` 14 Page Tabs + Blog + Service Areas)
   * ===================================================================== */
  const PAGE_FIELDS = {
    'Home': [
      { section: 'SEO Metadata', fields: [{ key: 'seo.home.title', label: 'SEO Page Title', max: 60 }, { key: 'seo.home.description', label: 'SEO Meta Description', type: 'textarea', max: 160 }] },
      { section: 'Hero Carousel Slides', fields: [
        { key: 'home.hero.slides.0.headline', label: 'Slide 1 Headline' }, { key: 'home.hero.slides.0.subheadline', label: 'Slide 1 Subheadline', type: 'textarea' },
        { key: 'home.hero.slides.1.headline', label: 'Slide 2 Headline' }, { key: 'home.hero.slides.1.subheadline', label: 'Slide 2 Subheadline', type: 'textarea' },
        { key: 'home.hero.slides.2.headline', label: 'Slide 3 Headline' }, { key: 'home.hero.slides.3.headline', label: 'Slide 4 Headline' }, { key: 'home.hero.slides.4.headline', label: 'Slide 5 Headline' }
      ]},
      { section: 'Welcome & About Section', fields: [
        { key: 'home.about.badge', label: 'Badge' }, { key: 'home.about.heading', label: 'Heading' },
        { key: 'home.about.p1', label: 'Paragraph 1', type: 'textarea' }, { key: 'home.about.p2', label: 'Paragraph 2', type: 'textarea' },
        { key: 'home.about.discoverBtn', label: 'Discover Button' }, { key: 'home.about.callBtn', label: 'Call Button' }
      ]},
      { section: 'Homepage Sections & CTA Banner', fields: [
        { key: 'home.servicesSection.heading', label: 'Services Heading' }, { key: 'home.haulage.heading', label: 'Haulage Heading' },
        { key: 'home.featuredCars.heading', label: 'Featured Cars Heading' }, { key: 'home.rentalFleet.heading', label: 'Rental Fleet Heading' },
        { key: 'home.autoParts.heading', label: 'Auto Parts Heading' }, { key: 'home.whyChooseUs.heading', label: 'Why Choose Us Heading' },
        { key: 'home.bottomSections.ctaBannerHeading', label: 'Bottom Emergency Banner Heading' }, { key: 'home.bottomSections.ctaBannerDescription', label: 'Bottom Emergency Banner Subtext', type: 'textarea' }
      ]}
    ],
    'Towing': [
      { section: 'SEO & Hero', fields: [{ key: 'seo.towing.title', label: 'SEO Title', max: 60 }, { key: 'seo.towing.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'towing.badge', label: 'Badge' }, { key: 'towing.title', label: 'Hero Title' }, { key: 'towing.description', label: 'Hero Description', type: 'textarea' }, { key: 'towing.hotline', label: 'Hotline' }, { key: 'towing.averageTime', label: 'Arrival Estimate' }] }
    ],
    'Haulage': [
      { section: 'SEO & Hero', fields: [{ key: 'seo.haulage.title', label: 'SEO Title', max: 60 }, { key: 'seo.haulage.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'haulage.badge', label: 'Badge' }, { key: 'haulage.title', label: 'Title' }, { key: 'haulage.description', label: 'Description', type: 'textarea' }, { key: 'haulage.callCta', label: 'Call CTA' }, { key: 'haulage.whatsappCta', label: 'WhatsApp CTA' }] }
    ],
    'Rental': [
      { section: 'SEO & Hero', fields: [{ key: 'seo.rental.title', label: 'SEO Title', max: 60 }, { key: 'seo.rental.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'rental.badge', label: 'Badge' }, { key: 'rental.title', label: 'Title' }, { key: 'rental.description', label: 'Description', type: 'textarea' }] }
    ],
    'Sales': [
      { section: 'SEO & Hero', fields: [{ key: 'seo.sales.title', label: 'SEO Title', max: 60 }, { key: 'seo.sales.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'sales.badge', label: 'Badge' }, { key: 'sales.title', label: 'Title' }, { key: 'sales.description', label: 'Description', type: 'textarea' }] }
    ],
    'Repairs': [
      { section: 'SEO & Hero', fields: [{ key: 'seo.repairs.title', label: 'SEO Title', max: 60 }, { key: 'seo.repairs.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'repairs.badge', label: 'Badge' }, { key: 'repairs.title', label: 'Title' }, { key: 'repairs.description', label: 'Description', type: 'textarea' }] }
    ],
    'Body Works': [
      { section: 'SEO & Hero', fields: [{ key: 'seo.bodyWorks.title', label: 'SEO Title', max: 60 }, { key: 'seo.bodyWorks.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'bodyWorks.badge', label: 'Badge' }, { key: 'bodyWorks.title', label: 'Title' }, { key: 'bodyWorks.description', label: 'Description', type: 'textarea' }] }
    ],
    'Parts': [
      { section: 'SEO & Hero', fields: [{ key: 'seo.parts.title', label: 'SEO Title', max: 60 }, { key: 'seo.parts.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'parts.badge', label: 'Badge' }, { key: 'parts.title', label: 'Title' }, { key: 'parts.description', label: 'Description', type: 'textarea' }] }
    ],
    'Service Areas': [
      { section: 'Directory & Single Area Template Labels', fields: [
        { key: 'seo.serviceAreas.title', label: 'SEO Title', max: 60 }, { key: 'seo.serviceAreas.description', label: 'SEO Description', type: 'textarea', max: 160 },
        { key: 'serviceAreas.title', label: 'Directory Title' }, { key: 'serviceAreas.description', label: 'Directory Subtitle', type: 'textarea' },
        { key: 'serviceAreas.template.ctaHeading', label: 'Area Template CTA Heading' }, { key: 'serviceAreas.template.callButton', label: 'Call Button Text' }, { key: 'serviceAreas.template.whatsappButton', label: 'WhatsApp Button Text' }
      ]}
    ],
    'Blog': [
      { section: 'Blog Header & SEO', fields: [{ key: 'seo.blog.title', label: 'SEO Title', max: 60 }, { key: 'seo.blog.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'blog.badge', label: 'Badge' }, { key: 'blog.title', label: 'Title' }, { key: 'blog.description', label: 'Description', type: 'textarea' }] }
    ],
    'Contact': [
      { section: 'Contact Page & SEO', fields: [{ key: 'seo.contact.title', label: 'SEO Title', max: 60 }, { key: 'seo.contact.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'contact.badge', label: 'Badge' }, { key: 'contact.title', label: 'Title' }, { key: 'contact.description', label: 'Description', type: 'textarea' }, { key: 'contact.phone', label: 'Phone' }, { key: 'contact.address', label: 'Address' }, { key: 'contact.hours', label: 'Hours' }] }
    ],
    'Careers': [
      { section: 'Careers Page & SEO', fields: [{ key: 'seo.careers.title', label: 'SEO Title', max: 60 }, { key: 'seo.careers.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'careers.badge', label: 'Badge' }, { key: 'careers.title', label: 'Title' }, { key: 'careers.description', label: 'Description', type: 'textarea' }] }
    ],
    'FAQs': [
      { section: 'FAQs Header & Support Box', fields: [{ key: 'seo.faqs.title', label: 'SEO Title', max: 60 }, { key: 'seo.faqs.description', label: 'SEO Description', type: 'textarea', max: 160 }, { key: 'faqs.badge', label: 'Badge' }, { key: 'faqs.title', label: 'Title' }, { key: 'faqs.description', label: 'Description', type: 'textarea' }, { key: 'faqs.stillHaveQuestionsTitle', label: 'Still Have Questions Title' }, { key: 'faqs.stillHaveQuestionsText', label: 'Still Have Questions Text', type: 'textarea' }] }
    ],
    'Policies': [
      { section: 'Legal & Policies (12 Documents)', fields: [
        { key: 'policies.privacy.title', label: '1. Privacy Policy Title' }, { key: 'policies.privacy.subtitle', label: '1. Privacy Subtitle' },
        { key: 'policies.terms.title', label: '2. Terms & Conditions Title' }, { key: 'policies.terms.subtitle', label: '2. Terms Subtitle' },
        { key: 'policies.towingTerms.title', label: '3. Towing Terms Title' }, { key: 'policies.cancellation.title', label: '4. Cancellation Policy Title' },
        { key: 'policies.pricing.title', label: '5. Pricing & Payment Policy Title' }, { key: 'policies.damageLiability.title', label: '6. Vehicle Damage & Liability Title' },
        { key: 'policies.emergencyService.title', label: '7. Emergency Service Policy Title' }, { key: 'policies.serviceAreaPolicy.title', label: '8. Service Area Policy Title' },
        { key: 'policies.disclaimer.title', label: '9. Website Disclaimer Title' }, { key: 'policies.cookie.title', label: '10. Cookie Policy Title' },
        { key: 'policies.complaints.title', label: '11. Complaints Policy Title' }, { key: 'policies.accessibility.title', label: '12. Accessibility Statement Title' }
      ]}
    ],
  };

  function renderPagesText(vp) {
    const activeTab = state.ui.pagesTab || 'Home';
    const q = (state.ui.pagesSearch || '').trim().toLowerCase();
    const sections = PAGE_FIELDS[activeTab] || [];

    // Global Search results across all 14 pages
    let searchResultsHtml = '';
    if (q) {
      const hits = [];
      Object.keys(PAGE_FIELDS).forEach(function (pg) {
        PAGE_FIELDS[pg].forEach(function (sec) {
          sec.fields.forEach(function (f) {
            const val = String(dotGet(state.siteContent, f.key, ''));
            if (f.label.toLowerCase().indexOf(q) !== -1 || f.key.toLowerCase().indexOf(q) !== -1 || val.toLowerCase().indexOf(q) !== -1) {
              hits.push({ pg: pg, label: f.label, val: val });
            }
          });
        });
      });
      searchResultsHtml = '<div class="stbm-card" style="margin-bottom:18px"><div class="stbm-card-head"><h3>Search Matches Across All Pages (' + hits.length + ')</h3></div><div class="stbm-card-body" style="max-height:200px;overflow-y:auto">' +
        (hits.length ? hits.slice(0, 18).map(function (h) {
          return '<div style="display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f1f5f9"><div><span class="stbm-badge stbm-badge-teal">' + esc(h.pg) + '</span> <strong>' + esc(h.label) + '</strong><div style="font-size:11.5px;color:#64748b">' + esc(h.val.slice(0, 90)) + '</div></div><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-pt-jump="' + esc(h.pg) + '">Go to Tab</button></div>';
        }).join('') : '<p style="margin:0;color:#64748b">No matching text fields found.</p>') + '</div></div>';
    }

    const sectionsHtml = sections.map(function (sec) {
      const fieldsHtml = sec.fields.map(function (f) {
        const val = String(dotGet(state.siteContent, f.key, ''));
        let countHtml = '';
        if (f.max) {
          const cls = val.length > f.max ? 'over' : (val.length > f.max * 0.85 ? 'warn' : 'ok');
          countHtml = '<span class="stbm-char-count ' + cls + '">' + val.length + ' / ' + f.max + ' chars</span>';
        }
        return '<div class="stbm-field">' +
          '<div class="stbm-field-label"><span>' + esc(f.label) + '</span><span style="display:flex;align-items:center;gap:10px">' + countHtml +
          '<button type="button" class="stbm-btn stbm-btn-ghost stbm-btn-sm" data-pt-reset="' + esc(f.key) + '">Reset</button></span></div>' +
          (f.type === 'textarea' ?
            '<textarea class="stbm-textarea" data-pt-key="' + esc(f.key) + '">' + esc(val) + '</textarea>' :
            '<input class="stbm-input" data-pt-key="' + esc(f.key) + '" value="' + esc(val) + '">') +
          '</div>';
      }).join('');
      return '<div class="stbm-collapse"><div class="stbm-collapse-head"><span>' + esc(sec.section) + '</span></div><div class="stbm-collapse-body">' + fieldsHtml + '</div></div>';
    }).join('');

    // Blog manager inline block when Blog tab active
    let extraTabHtml = '';
    if (activeTab === 'Blog') {
      const posts = dotGet(state.siteContent, 'blog.posts', []);
      extraTabHtml = '<div class="stbm-card" style="margin-top:18px"><div class="stbm-card-head"><div><h3>Blog Articles Manager (' + posts.length + ')</h3><p class="stbm-card-sub">Create, edit, reorder or remove blog articles</p></div>' +
        '<button type="button" class="stbm-btn stbm-btn-primary stbm-btn-sm" id="stbm-blog-add">+ Add Article</button></div>' +
        '<div class="stbm-table-wrap"><table class="stbm-table"><thead><tr><th>Article</th><th>Category</th><th>Date</th><th style="text-align:right">Actions</th></tr></thead><tbody>' +
        posts.map(function (p, i) {
          return '<tr><td><strong>' + esc(p.title) + '</strong><div style="font-size:11.5px;color:#64748b">' + esc(p.author) + ' · ' + esc(p.readTime) + '</div></td>' +
            '<td><span class="stbm-badge stbm-badge-teal">' + esc(p.category) + '</span></td><td>' + esc(p.date) + '</td>' +
            '<td style="text-align:right"><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-blog-edit="' + i + '">Edit</button> ' +
            '<button type="button" class="stbm-btn-icon danger" data-blog-del="' + i + '">&times;</button></td></tr>';
        }).join('') +
        '</tbody></table></div></div>';
    } else if (activeTab === 'Service Areas') {
      extraTabHtml = '<div class="stbm-card" style="margin-top:18px"><div class="stbm-card-head"><div><h3>Quick Route Corridor Copy Customizer</h3><p class="stbm-card-sub">Override headline or description for any specific area slug (e.g. accra, tema, accra-to-kumasi)</p></div></div>' +
        '<div class="stbm-card-body"><div class="stbm-grid stbm-grid-3">' +
        '<div class="stbm-field"><label class="stbm-field-label">Area Slug</label><input class="stbm-input" id="stbm-sa-slug" placeholder="e.g. accra"></div>' +
        '<div class="stbm-field"><label class="stbm-field-label">Custom Heading</label><input class="stbm-input" id="stbm-sa-h" placeholder="Custom emergency heading..."></div>' +
        '<div class="stbm-field"><label class="stbm-field-label">Custom Intro</label><input class="stbm-input" id="stbm-sa-d" placeholder="Localized dispatch copy..."></div>' +
        '</div><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="stbm-sa-save">Save Area Override</button></div></div>';
    }

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Pages & Text Content</h2><p>Edit copy and SEO metadata across all 14 public page sections.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <div class="stbm-search-box" style="min-width:250px">' + svg('search') + '<input class="stbm-input" id="stbm-pt-search" placeholder="Search copy across all pages..." value="' + esc(state.ui.pagesSearch) + '"></div>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-pt-save">Save ' + esc(activeTab) + ' Changes</button>' +
      '  </div>' +
      '</div>' +
      searchResultsHtml +
      '<div class="stbm-tabs">' +
      Object.keys(PAGE_FIELDS).map(function (pg) {
        return '<button type="button" class="stbm-tab ' + (activeTab === pg ? 'active' : '') + '" data-pt-tab="' + esc(pg) + '">' + esc(pg) + '</button>';
      }).join('') +
      '</div>' +
      sectionsHtml +
      extraTabHtml;

    document.getElementById('stbm-pt-search').oninput = function (e) {
      state.ui.pagesSearch = e.target.value;
      renderPagesText(vp);
      const inp = document.getElementById('stbm-pt-search');
      if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
    };
    vp.querySelectorAll('[data-pt-tab],[data-pt-jump]').forEach(function (b) {
      b.onclick = function () {
        state.ui.pagesTab = b.getAttribute('data-pt-tab') || b.getAttribute('data-pt-jump');
        renderPagesText(vp);
      };
    });
    document.getElementById('stbm-pt-save').onclick = function () {
      const updates = {};
      vp.querySelectorAll('[data-pt-key]').forEach(function (inp) {
        updates[inp.getAttribute('data-pt-key')] = inp.value;
      });
      ajax('stbm_save_content', { updates: updates }).then(function () {
        toast('Saved ' + activeTab + ' copy & SEO settings!', 'success');
        renderPagesText(vp);
      }).catch(function (err) { toast(err.message, 'error'); });
    };
    vp.querySelectorAll('[data-pt-reset]').forEach(function (b) {
      b.onclick = function () {
        const key = b.getAttribute('data-pt-reset');
        confirmDialog({
          title: 'Reset Field to Original?',
          description: 'Restore default factory text for "' + key + '"?',
          confirmLabel: 'Reset Field',
          onConfirm: function () {
            ajax('stbm_reset_content_key', { key: key }).then(function () {
              toast('Field restored to default.', 'info');
              renderPagesText(vp);
            }).catch(function (err) { toast(err.message, 'error'); });
          },
        });
      };
    });

    if (activeTab === 'Blog') {
      document.getElementById('stbm-blog-add').onclick = function () { openBlogPostModal(null, vp); };
      vp.querySelectorAll('[data-blog-edit]').forEach(function (b) {
        b.onclick = function () { openBlogPostModal(Number(b.getAttribute('data-blog-edit')), vp); };
      });
      vp.querySelectorAll('[data-blog-del]').forEach(function (b) {
        b.onclick = function () {
          const idx = Number(b.getAttribute('data-blog-del'));
          const posts = dotGet(state.siteContent, 'blog.posts', []).slice();
          const target = posts[idx];
          confirmDialog({
            title: 'Delete Blog Article?',
            description: 'Remove "' + (target ? target.title : '') + '" from the blog list?',
            confirmLabel: 'Delete',
            isDestructive: true,
            onConfirm: function () {
              posts.splice(idx, 1);
              ajax('stbm_save_content', { updates: { 'blog.posts': posts } }).then(function () {
                toast('Blog post deleted.', 'info');
                renderPagesText(vp);
              }).catch(function (err) { toast(err.message, 'error'); });
            },
          });
        };
      });
    }
    if (activeTab === 'Service Areas' && document.getElementById('stbm-sa-save')) {
      document.getElementById('stbm-sa-save').onclick = function () {
        const slug = document.getElementById('stbm-sa-slug').value.trim().toLowerCase();
        if (!slug) { toast('Enter a valid area slug first.', 'error'); return; }
        const h = document.getElementById('stbm-sa-h').value.trim();
        const d = document.getElementById('stbm-sa-d').value.trim();
        ajax('stbm_save_content', { updates: { ['serviceAreas.overrides.' + slug]: { title: h, description: d } } }).then(function () {
          toast('Service area corridor override saved for "' + slug + '"!', 'success');
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    }
  }

  function openBlogPostModal(idx, vp) {
    const posts = dotGet(state.siteContent, 'blog.posts', []).slice();
    const existing = idx !== null && posts[idx] ? posts[idx] : null;
    const p = existing ? JSON.parse(JSON.stringify(existing)) : {
      id: Date.now(), title: '', category: 'Towing Tips', author: 'David Mensah',
      date: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      readTime: '5 min read', excerpt: '', image: '', tags: ['Towing', 'Safety'],
    };
    const root = document.getElementById('stbm-modal-root');
    root.innerHTML =
      '<div class="stbm-modal-backdrop"><div class="stbm-modal">' +
      '  <div class="stbm-modal-head"><h3>' + (existing ? 'Edit Article' : 'New Blog Article') + '</h3><button type="button" class="stbm-btn-icon" id="bm-close">&times;</button></div>' +
      '  <div class="stbm-modal-body">' +
      '    <div class="stbm-field"><label class="stbm-field-label">Title</label><input class="stbm-input" id="bm-title" value="' + esc(p.title) + '"></div>' +
      '    <div class="stbm-grid stbm-grid-2">' +
      '      <div class="stbm-field"><label class="stbm-field-label">Category</label><input class="stbm-input" id="bm-cat" value="' + esc(p.category) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Author</label><input class="stbm-input" id="bm-author" value="' + esc(p.author) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Publish Date</label><input class="stbm-input" id="bm-date" value="' + esc(p.date) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Read Time</label><input class="stbm-input" id="bm-time" value="' + esc(p.readTime) + '"></div>' +
      '    </div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Featured Image URL</label><div style="display:flex;gap:8px"><input class="stbm-input" id="bm-img" value="' + esc(p.image) + '"><button type="button" class="stbm-btn stbm-btn-secondary" id="bm-pick">Pick</button></div></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Excerpt / Summary</label><textarea class="stbm-textarea" id="bm-exc">' + esc(p.excerpt) + '</textarea></div>' +
      '  </div>' +
      '  <div class="stbm-modal-foot"><button type="button" class="stbm-btn stbm-btn-secondary" id="bm-cancel">Cancel</button><button type="button" class="stbm-btn stbm-btn-primary" id="bm-save">Save Article</button></div>' +
      '</div></div>';

    document.getElementById('bm-close').onclick = function () { root.innerHTML = ''; };
    document.getElementById('bm-cancel').onclick = function () { root.innerHTML = ''; };
    document.getElementById('bm-pick').onclick = function () {
      openWpMediaPicker(function (u) { document.getElementById('bm-img').value = u; });
    };
    document.getElementById('bm-save').onclick = function () {
      p.title = document.getElementById('bm-title').value.trim();
      p.category = document.getElementById('bm-cat').value.trim();
      p.author = document.getElementById('bm-author').value.trim();
      p.date = document.getElementById('bm-date').value.trim();
      p.readTime = document.getElementById('bm-time').value.trim();
      p.image = document.getElementById('bm-img').value.trim();
      p.excerpt = document.getElementById('bm-exc').value.trim();
      if (!p.title) { toast('Title is required.', 'error'); return; }
      if (existing) posts[idx] = p; else posts.unshift(p);
      ajax('stbm_save_content', { updates: { 'blog.posts': posts } }).then(function () {
        root.innerHTML = '';
        toast('Blog article saved!', 'success');
        renderPagesText(vp);
      }).catch(function (err) { toast(err.message, 'error'); });
    };
  }

  /* =====================================================================
   * MODULE 9: THEME COLORS (`AdminColors` w/ WCAG 2.1 Contrast + 4-Tab Simulator)
   * ===================================================================== */
  function hexToLuminance(hex) {
    const clean = String(hex || '#000000').replace('#', '');
    const full = clean.length === 3 ? clean.split('').map(function (c) { return c + c; }).join('') : clean;
    if (!/^[0-9a-fA-F]{6}$/.test(full)) return 0;
    const rgb = [0, 2, 4].map(function (i) {
      const c = parseInt(full.slice(i, i + 2), 16) / 255;
      return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * rgb[0] + 0.7152 * rgb[1] + 0.0722 * rgb[2];
  }
  function contrastRatio(hexA, hexB) {
    const l1 = hexToLuminance(hexA);
    const l2 = hexToLuminance(hexB);
    return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
  }

  function renderColors(vp) {
    const theme = dotGet(state.siteContent, 'theme', {});
    const colorDefs = [
      { key: 'primary', label: 'Primary Brand Color', desc: 'Main buttons, active sidebar pills, headings & accents', def: '#015557' },
      { key: 'secondary', label: 'Secondary Surface Color', desc: 'Dark footer, announcement bar & hero overlays', def: '#192e2e' },
      { key: 'accent', label: 'Emergency CTA Accent', desc: 'Emergency callouts, badges & hotlines', def: '#d80000' },
      { key: 'dark', label: 'Dark Charcoal Base', desc: 'Admin shell & deep contrast containers', def: '#131514' },
      { key: 'background', label: 'Light Canvas Background', desc: 'Public section alternating surface', def: '#f8fafc' },
    ];

    const cPrimary = theme.primary || '#015557';
    const cSecondary = theme.secondary || '#192e2e';
    const cAccent = theme.accent || '#d80000';
    const cBg = theme.background || '#f8fafc';

    const wcagChecks = [
      { label: 'White Text on Primary', fg: '#ffffff', bg: cPrimary },
      { label: 'White Text on Secondary', fg: '#ffffff', bg: cSecondary },
      { label: 'White Text on Accent', fg: '#ffffff', bg: cAccent },
      { label: 'Primary Text on Background', fg: cPrimary, bg: cBg },
    ];

    const simTab = state.ui.simulatorTab || 'buttons';
    let simBody = '';
    if (simTab === 'buttons') {
      simBody = '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center">' +
        '<span style="background:' + esc(cPrimary) + ';color:#fff;padding:10px 18px;border-radius:999px;font-weight:700;font-size:13px">Primary Action</span>' +
        '<span style="background:' + esc(cAccent) + ';color:#fff;padding:10px 18px;border-radius:999px;font-weight:700;font-size:13px">Emergency Tow CTA</span>' +
        '<span style="border:2px solid ' + esc(cPrimary) + ';color:' + esc(cPrimary) + ';padding:8px 16px;border-radius:999px;font-weight:700;font-size:13px">Outlined Button</span>' +
        '<span style="background:' + esc(cSecondary) + ';color:#fff;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700">24/7 Verified</span></div>';
    } else if (simTab === 'header') {
      simBody = '<div style="border-radius:12px;overflow:hidden;border:1px solid #cbd5e1">' +
        '<div style="background:' + esc(cSecondary) + ';color:#fff;padding:7px 14px;font-size:11px;display:flex;justify-content:space-between"><span>24/7 Emergency Towing</span><span>0244753849</span></div>' +
        '<div style="background:#fff;padding:12px 16px;display:flex;justify-content:space-between;align-items:center"><strong style="color:' + esc(cPrimary) + '">Santa Towing</strong><span style="background:' + esc(cAccent) + ';color:#fff;padding:6px 12px;border-radius:999px;font-size:11px;font-weight:700">Call Dispatch</span></div></div>';
    } else if (simTab === 'cards') {
      simBody = '<div style="background:' + esc(cBg) + ';padding:16px;border-radius:12px"><div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0"><span style="color:' + esc(cPrimary) + ';font-size:11px;font-weight:800">FLATBED RECOVERY</span><h4 style="margin:6px 0;color:' + esc(cSecondary) + '">Hydraulic Tilt-Tray Transport</h4><p style="margin:0 0 10px;font-size:12px;color:#64748b">Damage-free roadside recovery nationwide.</p><span style="color:' + esc(cAccent) + ';font-size:12px;font-weight:700">Request Dispatch &rarr;</span></div></div>';
    } else {
      simBody = '<div style="background:' + esc(cPrimary) + ';color:#fff;padding:14px;border-radius:10px;font-size:12.5px"><strong>Live Dispatch Ready:</strong> Drivers stationed across Accra, Tema, Kumasi &amp; Takoradi.</div>';
    }

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Theme Colors & WCAG Contrast</h2><p>Administrator-only palette controls with live accessibility scoring.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <button type="button" class="stbm-btn stbm-btn-secondary" id="stbm-col-reset">Reset All Colors</button>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-col-save">Save Brand Palette</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-split-screen">' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Palette Tokens</h3></div><div class="stbm-card-body">' +
      colorDefs.map(function (c) {
        const val = theme[c.key] || c.def;
        return '<div class="stbm-field" style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">' +
          '<div><strong>' + esc(c.label) + '</strong><div style="font-size:11.5px;color:#64748b">' + esc(c.desc) + '</div></div>' +
          '<div style="display:flex;align-items:center;gap:8px">' +
          '<input type="color" data-col-pick="' + c.key + '" value="' + esc(val) + '" style="width:42px;height:36px;border:0;background:transparent;cursor:pointer">' +
          '<input class="stbm-input" data-col-hex="' + c.key + '" value="' + esc(val) + '" style="width:104px;font-family:monospace">' +
          '</div></div>';
      }).join('') +
      '  </div></div>' +
      '  <div style="display:flex;flex-direction:column;gap:18px">' +
      '    <div class="stbm-card"><div class="stbm-card-head"><h3>WCAG 2.1 Accessibility Checks</h3></div><div class="stbm-card-body">' +
      wcagChecks.map(function (w) {
        const ratio = contrastRatio(w.fg, w.bg);
        const pass = ratio >= 4.5;
        return '<div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9">' +
          '<span style="font-size:12.5px;font-weight:600">' + esc(w.label) + '</span>' +
          '<span class="stbm-badge ' + (pass ? 'stbm-badge-green' : 'stbm-badge-red') + '">' + ratio.toFixed(2) + ':1 ' + (ratio >= 7 ? 'AAA' : pass ? 'AA' : 'FAIL') + '</span></div>';
      }).join('') +
      '    </div></div>' +
      '    <div class="stbm-card"><div class="stbm-card-head"><h3>Component Simulator</h3></div><div class="stbm-card-body">' +
      '      <div class="stbm-tabs">' +
      ['buttons', 'header', 'cards', 'alerts'].map(function (st) {
        return '<button type="button" class="stbm-tab ' + (simTab === st ? 'active' : '') + '" data-sim-tab="' + st + '">' + st.toUpperCase() + '</button>';
      }).join('') +
      '      </div>' + simBody +
      '    </div></div>' +
      '  </div>' +
      '</div>';

    vp.querySelectorAll('[data-col-pick]').forEach(function (inp) {
      inp.oninput = function () {
        const k = inp.getAttribute('data-col-pick');
        dotSet(state.siteContent, 'theme.' + k, inp.value);
        renderColors(vp);
      };
    });
    vp.querySelectorAll('[data-col-hex]').forEach(function (inp) {
      inp.onchange = function () {
        const k = inp.getAttribute('data-col-hex');
        if (/^#[0-9a-fA-F]{6}$/.test(inp.value.trim())) {
          dotSet(state.siteContent, 'theme.' + k, inp.value.trim());
          renderColors(vp);
        }
      };
    });
    vp.querySelectorAll('[data-sim-tab]').forEach(function (b) {
      b.onclick = function () { state.ui.simulatorTab = b.getAttribute('data-sim-tab'); renderColors(vp); };
    });
    document.getElementById('stbm-col-save').onclick = function () {
      const updates = {};
      colorDefs.forEach(function (c) {
        updates['theme.' + c.key] = dotGet(state.siteContent, 'theme.' + c.key, c.def);
      });
      ajax('stbm_save_brand', { updates: updates }).then(function () {
        toast('Theme palette saved!', 'success');
        renderColors(vp);
      }).catch(function (err) { toast(err.message, 'error'); });
    };
    document.getElementById('stbm-col-reset').onclick = function () {
      confirmDialog({
        title: 'Reset All Theme Colors?',
        description: 'Restore Emerald Teal (#015557), Deep Teal (#192e2e), and Crimson (#d80000)?',
        confirmLabel: 'Reset Colors',
        onConfirm: function () {
          ajax('stbm_reset_brand', { scope: 'colors' }).then(function () {
            toast('Brand colors reset to defaults.', 'info');
            renderColors(vp);
          }).catch(function (err) { toast(err.message, 'error'); });
        },
      });
    };
  }

  /* =====================================================================
   * MODULE 10: LOGO & IDENTITY (`AdminLogo`)
   * ===================================================================== */
  function renderLogo(vp) {
    const logoUrl = dotGet(state.siteContent, 'global.logo.url', '');
    const logoAlt = dotGet(state.siteContent, 'global.logo.alt', '');
    const faviconUrl = dotGet(state.siteContent, 'global.images.logo', '');

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Logo & Brand Identity</h2><p>Manage company wordmark, favicon, and alt attributes across light & dark headers.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <button type="button" class="stbm-btn stbm-btn-secondary" id="stbm-logo-reset">Reset Default Logo</button>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-logo-save">Save Logo Settings</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-split-screen">' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Upload or Set Brand Marks</h3></div><div class="stbm-card-body">' +
      '    <div class="stbm-field"><label class="stbm-field-label">Primary Wordmark Logo URL</label>' +
      '      <div style="display:flex;gap:8px"><input class="stbm-input" id="stbm-l-url" value="' + esc(logoUrl) + '"><button type="button" class="stbm-btn stbm-btn-secondary" id="stbm-l-pick">Media Library</button></div>' +
      '    </div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Logo Alt Text</label><input class="stbm-input" id="stbm-l-alt" value="' + esc(logoAlt) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Browser Favicon URL (192x192 Square)</label>' +
      '      <div style="display:flex;gap:8px"><input class="stbm-input" id="stbm-f-url" value="' + esc(faviconUrl) + '"><button type="button" class="stbm-btn stbm-btn-secondary" id="stbm-f-pick">Media Library</button></div>' +
      '    </div>' +
      '  </div></div>' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Light, Dark & Browser Tab Preview</h3></div><div class="stbm-card-body" style="display:flex;flex-direction:column;gap:14px">' +
      '    <div style="padding:18px;border:1px solid #e2e8f0;border-radius:12px;background:#ffffff;text-align:center"><div style="font-size:11px;color:#64748b;margin-bottom:8px">LIGHT HEADER BAR</div><img src="' + esc(logoUrl) + '" style="height:54px;object-fit:contain"></div>' +
      '    <div style="padding:18px;border-radius:12px;background:#131514;text-align:center"><div style="font-size:11px;color:#94a3b8;margin-bottom:8px">DARK FOOTER SURFACE</div><img src="' + esc(logoUrl) + '" style="height:54px;object-fit:contain"></div>' +
      '    <div style="background:#e2e8f0;padding:10px 12px 0;border-radius:12px">' +
      '      <div style="background:#fff;padding:8px 14px;border-radius:10px 10px 0 0;display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:600">' +
      '        <img src="' + esc(faviconUrl) + '" style="width:16px;height:16px;object-fit:contain"> Santa Towing | 24/7 Emergency Towing' +
      '      </div>' +
      '    </div>' +
      '  </div></div>' +
      '</div>';

    document.getElementById('stbm-l-pick').onclick = function () {
      openWpMediaPicker(function (u) { document.getElementById('stbm-l-url').value = u; });
    };
    document.getElementById('stbm-f-pick').onclick = function () {
      openWpMediaPicker(function (u) { document.getElementById('stbm-f-url').value = u; });
    };
    document.getElementById('stbm-logo-save').onclick = function () {
      ajax('stbm_save_brand', {
        updates: {
          'global.logo.url': document.getElementById('stbm-l-url').value.trim(),
          'global.logo.alt': document.getElementById('stbm-l-alt').value.trim(),
          'global.images.logo': document.getElementById('stbm-f-url').value.trim(),
        },
      }).then(function () {
        toast('Logo & Favicon saved!', 'success');
        renderSidebar();
        renderLogo(vp);
      }).catch(function (err) { toast(err.message, 'error'); });
    };
    document.getElementById('stbm-logo-reset').onclick = function () {
      ajax('stbm_reset_brand', { scope: 'logo' }).then(function () {
        toast('Default Santa Towing logo restored.', 'info');
        renderSidebar();
        renderLogo(vp);
      }).catch(function (err) { toast(err.message, 'error'); });
    };
  }

  /* =====================================================================
   * MODULE 11: LINKS & NAVIGATION (`AdminLinks`)
   * ===================================================================== */
  function buildWaPreview(num, msg) {
    let digits = String(num || '').replace(/\D/g, '');
    if (digits.startsWith('0') && digits.length === 10) digits = '233' + digits.slice(1);
    else if (!digits.startsWith('233') && digits.length === 9) digits = '233' + digits;
    return 'https://wa.me/' + digits + (msg ? '?text=' + encodeURIComponent(msg) : '');
  }

  function renderLinks(vp) {
    const defLists = state.boot.defaultNavLists || {};
    const menuLinks = dotGet(state.siteContent, 'global.links.menuLinks', defLists.menuLinks || []);
    const quickLinks = dotGet(state.siteContent, 'global.links.quickLinks', defLists.quickLinks || []);
    const serviceLinks = dotGet(state.siteContent, 'global.links.serviceLinks', defLists.serviceLinks || []);

    const waNum = dotGet(state.siteContent, 'global.links.whatsappNumber', '0244753849');
    const waMsg = dotGet(state.siteContent, 'global.links.whatsappMessage', 'Hello Santa Towing, I need 24/7 emergency roadside assistance in Ghana.');

    function renderLinkList(title, key, arr) {
      return '<div class="stbm-card" style="margin-bottom:18px"><div class="stbm-card-head"><h3>' + esc(title) + ' (' + arr.length + ')</h3>' +
        '<button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-add-link="' + key + '">+ Add Link</button></div>' +
        '<div class="stbm-table-wrap"><table class="stbm-table"><tbody>' +
        arr.map(function (it, idx) {
          return '<tr>' +
            '<td><input class="stbm-input" data-lnk-label="' + key + ':' + idx + '" value="' + esc(it.label) + '"></td>' +
            '<td><input class="stbm-input" data-lnk-path="' + key + ':' + idx + '" value="' + esc(it.path) + '" style="font-family:monospace"></td>' +
            '<td style="text-align:right;white-space:nowrap">' +
            '<button type="button" class="stbm-btn-icon" data-lnk-vis="' + key + ':' + idx + '">' + (it.visible !== false ? 'Visible' : 'Hidden') + '</button> ' +
            '<button type="button" class="stbm-btn-icon" data-lnk-up="' + key + ':' + idx + '" ' + (idx === 0 ? 'disabled' : '') + '>&uarr;</button> ' +
            '<button type="button" class="stbm-btn-icon" data-lnk-down="' + key + ':' + idx + '" ' + (idx === arr.length - 1 ? 'disabled' : '') + '>&darr;</button> ' +
            '<button type="button" class="stbm-btn-icon danger" data-lnk-del="' + key + ':' + idx + '">&times;</button>' +
            '</td></tr>';
        }).join('') +
        '</tbody></table></div></div>';
    }

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Contact Links & Navigation</h2><p>Configure phone hotlines, WhatsApp deep links, social URLs, and header/footer menus.</p></div>' +
      '  <div class="stbm-head-actions"><button type="button" class="stbm-btn stbm-btn-primary" id="stbm-links-save">Save All Links & Menus</button></div>' +
      '</div>' +
      '<div class="stbm-grid stbm-grid-2" style="margin-bottom:20px">' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Hotlines & Business Contact</h3></div><div class="stbm-card-body">' +
      '    <div class="stbm-grid stbm-grid-2">' +
      '      <div class="stbm-field"><label class="stbm-field-label">Primary Phone</label><input class="stbm-input" id="lnk-phone" value="' + esc(dotGet(state.siteContent, 'global.links.phone', '')) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Email Address</label><input class="stbm-input" id="lnk-email" value="' + esc(dotGet(state.siteContent, 'global.links.email', '')) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Operating Hours</label><input class="stbm-input" id="lnk-hours" value="' + esc(dotGet(state.siteContent, 'global.links.hours', '')) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Coverage Badge</label><input class="stbm-input" id="lnk-cov" value="' + esc(dotGet(state.siteContent, 'global.links.coverage', '')) + '"></div>' +
      '    </div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Garage Address</label><input class="stbm-input" id="lnk-addr" value="' + esc(dotGet(state.siteContent, 'global.links.address', '')) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">WhatsApp Dispatch Number</label><input class="stbm-input" id="lnk-wanum" value="' + esc(waNum) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Default Pre-filled WhatsApp Message</label><textarea class="stbm-textarea" id="lnk-wamsg">' + esc(waMsg) + '</textarea></div>' +
      '    <div class="stbm-hint">Generated Link: <code id="lnk-waprev">' + esc(buildWaPreview(waNum, waMsg)) + '</code></div>' +
      '  </div></div>' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Social Media Profiles</h3></div><div class="stbm-card-body">' +
      '    <div class="stbm-field"><label class="stbm-field-label">Facebook URL</label><input class="stbm-input" id="lnk-fb" value="' + esc(dotGet(state.siteContent, 'global.social.facebook', '')) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Twitter / X URL</label><input class="stbm-input" id="lnk-tw" value="' + esc(dotGet(state.siteContent, 'global.social.twitter', '')) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Instagram URL</label><input class="stbm-input" id="lnk-ig" value="' + esc(dotGet(state.siteContent, 'global.social.instagram', '')) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">LinkedIn URL</label><input class="stbm-input" id="lnk-li" value="' + esc(dotGet(state.siteContent, 'global.social.linkedin', '')) + '"></div>' +
      '  </div></div>' +
      '</div>' +
      renderLinkList('Header Main Menu Links', 'menuLinks', menuLinks) +
      renderLinkList('Footer Quick Links', 'quickLinks', quickLinks) +
      renderLinkList('Footer Service Links', 'serviceLinks', serviceLinks);

    function syncWaPrev() {
      const p = document.getElementById('lnk-waprev');
      if (p) p.textContent = buildWaPreview(document.getElementById('lnk-wanum').value, document.getElementById('lnk-wamsg').value);
    }
    document.getElementById('lnk-wanum').oninput = syncWaPrev;
    document.getElementById('lnk-wamsg').oninput = syncWaPrev;

    function getList(k) {
      return dotGet(state.siteContent, 'global.links.' + k, defLists[k] || []).slice();
    }
    function setList(k, arr) {
      dotSet(state.siteContent, 'global.links.' + k, arr);
      renderLinks(vp);
    }

    vp.querySelectorAll('[data-add-link]').forEach(function (b) {
      b.onclick = function () {
        const k = b.getAttribute('data-add-link');
        const list = getList(k);
        list.push({ id: 'lnk-' + Date.now(), label: 'New Link', path: '/', visible: true });
        setList(k, list);
      };
    });
    vp.querySelectorAll('[data-lnk-vis],[data-lnk-up],[data-lnk-down],[data-lnk-del]').forEach(function (b) {
      b.onclick = function () {
        const attr = b.hasAttribute('data-lnk-vis') ? 'data-lnk-vis' :
                     b.hasAttribute('data-lnk-up') ? 'data-lnk-up' :
                     b.hasAttribute('data-lnk-down') ? 'data-lnk-down' : 'data-lnk-del';
        const parts = b.getAttribute(attr).split(':');
        const k = parts[0], idx = Number(parts[1]);
        const list = getList(k);
        if (attr === 'data-lnk-vis') list[idx].visible = list[idx].visible === false;
        else if (attr === 'data-lnk-del') list.splice(idx, 1);
        else if (attr === 'data-lnk-up' && idx > 0) { const t = list[idx]; list[idx] = list[idx - 1]; list[idx - 1] = t; }
        else if (attr === 'data-lnk-down' && idx < list.length - 1) { const t = list[idx]; list[idx] = list[idx + 1]; list[idx + 1] = t; }
        setList(k, list);
      };
    });
    vp.querySelectorAll('[data-lnk-label],[data-lnk-path]').forEach(function (inp) {
      inp.onchange = function () {
        const isL = inp.hasAttribute('data-lnk-label');
        const parts = inp.getAttribute(isL ? 'data-lnk-label' : 'data-lnk-path').split(':');
        const list = getList(parts[0]);
        if (list[Number(parts[1])]) list[Number(parts[1])][isL ? 'label' : 'path'] = inp.value;
        dotSet(state.siteContent, 'global.links.' + parts[0], list);
      };
    });

    document.getElementById('stbm-links-save').onclick = function () {
      const wn = document.getElementById('lnk-wanum').value.trim();
      const wm = document.getElementById('lnk-wamsg').value.trim();
      const updates = {
        'global.links.phone': document.getElementById('lnk-phone').value.trim(),
        'global.links.phoneDisplay': document.getElementById('lnk-phone').value.trim(),
        'global.links.email': document.getElementById('lnk-email').value.trim(),
        'global.links.address': document.getElementById('lnk-addr').value.trim(),
        'global.links.hours': document.getElementById('lnk-hours').value.trim(),
        'global.links.coverage': document.getElementById('lnk-cov').value.trim(),
        'global.links.whatsappNumber': wn,
        'global.links.whatsappMessage': wm,
        'global.links.whatsapp': buildWaPreview(wn, wm),
        'global.social.facebook': document.getElementById('lnk-fb').value.trim(),
        'global.social.twitter': document.getElementById('lnk-tw').value.trim(),
        'global.social.instagram': document.getElementById('lnk-ig').value.trim(),
        'global.social.linkedin': document.getElementById('lnk-li').value.trim(),
        'global.links.menuLinks': getList('menuLinks'),
        'global.links.quickLinks': getList('quickLinks'),
        'global.links.serviceLinks': getList('serviceLinks'),
      };
      ajax('stbm_save_content', { updates: updates }).then(function () {
        toast('Contact details & navigation menus saved!', 'success');
        renderLinks(vp);
      }).catch(function (err) { toast(err.message, 'error'); });
    };
  }

  /* =====================================================================
   * MODULE 12: SITE IMAGES (`AdminImages` — 31 Slots w/ Mandatory Alt Validation)
   * ===================================================================== */
  function renderImages(vp) {
    const slots = state.boot.siteImages || [];
    const pages = ['All'];
    slots.forEach(function (s) { if (pages.indexOf(s.page) === -1) pages.push(s.page); });
    const pf = state.ui.imagesPageFilter || 'All';
    const filtered = pf === 'All' ? slots : slots.filter(function (s) { return s.page === pf; });

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Website Images (' + slots.length + ' Slots)</h2><p>Replace website photography with mandatory SEO Alt Text validation.</p></div>' +
      '  <div class="stbm-head-actions"><button type="button" class="stbm-btn stbm-btn-secondary" id="stbm-imgs-resetall">Reset All Images</button></div>' +
      '</div>' +
      '<div class="stbm-tabs">' +
      pages.map(function (p) { return '<button type="button" class="stbm-tab ' + (pf === p ? 'active' : '') + '" data-im-page="' + esc(p) + '">' + esc(p) + '</button>'; }).join('') +
      '</div>' +
      '<div class="stbm-studio-grid">' +
      filtered.map(function (s) {
        const curUrl = dotGet(state.siteContent, s.storageKey, s.defaultUrl);
        const curAlt = dotGet(state.siteContent, s.altStorageKey, s.defaultAlt);
        return '<div class="stbm-studio-card">' +
          '<div class="stbm-studio-thumb"><img src="' + esc(curUrl) + '" alt="' + esc(curAlt) + '"></div>' +
          '<div style="padding:14px;flex:1;display:flex;flex-direction:column;gap:6px">' +
          '  <span class="stbm-badge stbm-badge-teal" style="align-self:flex-start">' + esc(s.page) + ' · ' + esc(s.aspectRatio) + '</span>' +
          '  <strong style="font-size:13.5px">' + esc(s.name) + '</strong>' +
          '  <div style="font-size:11.5px;color:#64748b;flex:1">' + esc(s.usedIn) + '</div>' +
          '  <div style="font-size:11px;color:#475569;background:#f8fafc;padding:6px 8px;border-radius:6px"><strong>Alt:</strong> ' + esc(curAlt) + '</div>' +
          '  <div style="display:flex;gap:8px;margin-top:6px">' +
          '    <button type="button" class="stbm-btn stbm-btn-primary stbm-btn-sm" style="flex:1" data-im-replace="' + esc(s.id) + '">Replace / Edit Alt</button>' +
          '    <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-im-reset="' + esc(s.id) + '">Reset</button>' +
          '  </div>' +
          '</div></div>';
      }).join('') +
      '</div>';

    vp.querySelectorAll('[data-im-page]').forEach(function (b) {
      b.onclick = function () { state.ui.imagesPageFilter = b.getAttribute('data-im-page'); renderImages(vp); };
    });
    vp.querySelectorAll('[data-im-replace]').forEach(function (b) {
      b.onclick = function () {
        const s = slots.find(function (x) { return x.id === b.getAttribute('data-im-replace'); });
        if (!s) return;
        openUnifiedImageModal({
          title: 'Replace: ' + s.name,
          currentUrl: dotGet(state.siteContent, s.storageKey, s.defaultUrl),
          currentAlt: dotGet(state.siteContent, s.altStorageKey, s.defaultAlt),
          showAlt: true,
          onApply: function (newUrl, newAlt) {
            const updates = {};
            updates[s.storageKey] = newUrl;
            updates[s.altStorageKey] = newAlt;
            ajax('stbm_save_content', { updates: updates }).then(function () {
              toast('Updated ' + s.name, 'success');
              renderImages(vp);
            }).catch(function (err) { toast(err.message, 'error'); });
          },
        });
      };
    });
    vp.querySelectorAll('[data-im-reset]').forEach(function (b) {
      b.onclick = function () {
        const s = slots.find(function (x) { return x.id === b.getAttribute('data-im-reset'); });
        if (!s) return;
        const updates = {};
        updates[s.storageKey] = s.defaultUrl;
        updates[s.altStorageKey] = s.defaultAlt;
        ajax('stbm_save_content', { updates: updates }).then(function () {
          toast('Slot restored to default image.', 'info');
          renderImages(vp);
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    });
    document.getElementById('stbm-imgs-resetall').onclick = function () {
      confirmDialog({
        title: 'Reset All 31 Website Images?',
        description: 'Restore every site image slot and Alt Text back to factory defaults.',
        confirmLabel: 'Reset All',
        isDestructive: true,
        onConfirm: function () {
          const updates = {};
          slots.forEach(function (s) {
            updates[s.storageKey] = s.defaultUrl;
            updates[s.altStorageKey] = s.defaultAlt;
          });
          ajax('stbm_save_content', { updates: updates }).then(function () {
            toast('All website image slots restored.', 'success');
            renderImages(vp);
          }).catch(function (err) { toast(err.message, 'error'); });
        },
      });
    };
  }

  /* =====================================================================
   * MODULE 13: BACKUP & EXPORT (`AdminBackupExport`)
   * ===================================================================== */
  function renderBackupExport(vp) {
    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Backup, Export & Audit Log</h2><p>Download JSON database backups, export CSV financial ledgers, or inspect system activity.</p></div>' +
      '</div>' +
      '<div class="stbm-grid stbm-grid-3" style="margin-bottom:22px">' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Full Billing JSON Backup</h3></div><div class="stbm-card-body">' +
      '    <p style="font-size:12.5px;color:#64748b">Exports all Customers, Pro Forma Invoices, Official Receipts, Doc Settings, and Logs.</p>' +
      '    <div style="display:flex;gap:8px;flex-wrap:wrap">' +
      '      <a class="stbm-btn stbm-btn-primary stbm-btn-sm" href="' + esc(exportUrl('stbm_export_json', { scope: 'billing' })) + '">Download Billing JSON</a>' +
      '      <label class="stbm-btn stbm-btn-secondary stbm-btn-sm" style="cursor:pointer">Restore Billing JSON<input type="file" id="stbm-restore-billing" accept=".json" style="display:none"></label>' +
      '    </div>' +
      '  </div></div>' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>CSV Financial Ledgers</h3></div><div class="stbm-card-body">' +
      '    <p style="font-size:12.5px;color:#64748b">Spreadsheet-ready UTF-8 CSV exports protected by WordPress capability checks.</p>' +
      '    <div style="display:flex;gap:8px;flex-wrap:wrap">' +
      '      <a class="stbm-btn stbm-btn-secondary stbm-btn-sm" href="' + esc(exportUrl('stbm_export_csv', { dataset: 'invoices' })) + '">Invoices CSV</a>' +
      '      <a class="stbm-btn stbm-btn-secondary stbm-btn-sm" href="' + esc(exportUrl('stbm_export_csv', { dataset: 'receipts' })) + '">Receipts CSV</a>' +
      '      <a class="stbm-btn stbm-btn-secondary stbm-btn-sm" href="' + esc(exportUrl('stbm_export_csv', { dataset: 'customers' })) + '">Customers CSV</a>' +
      '    </div>' +
      '  </div></div>' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Seed Data Recovery</h3></div><div class="stbm-card-body">' +
      '    <p style="font-size:12.5px;color:#64748b">Reset billing CRM tables &amp; Image Studio seeds to starter sample records.</p>' +
      '    <button type="button" class="stbm-btn stbm-btn-danger stbm-btn-sm" id="stbm-reset-seeds">Reset Billing &amp; Studio Seeds</button>' +
      '  </div></div>' +
      '</div>' +
      '<div class="stbm-card" style="margin-bottom:22px"><div class="stbm-card-head"><h3>Import Legacy React Browser Export (<code>localStorage</code> + <code>IndexedDB</code>)</h3></div><div class="stbm-card-body">' +
      '  <p style="font-size:12.5px;color:#64748b;margin-top:0">Upload JSON generated by <code>legacy-browser-export-snippet.js</code> (schema <code>santa-legacy-browser-export-v1</code>), or any billing/content backup archive. Safe deduplication, invoice recalculation, and broken-link audits run automatically.</p>' +
      '  <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">' +
      '    <select id="stbm-legacy-mode" class="stbm-input" style="width:auto;min-width:190px"><option value="merge">Merge (Keep &amp; Upsert Existing)</option><option value="replace">Replace Matching Datasets</option></select>' +
      '    <label style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;color:#334155"><input type="checkbox" id="stbm-legacy-dryrun"> Dry-Run Preview Only</label>' +
      '    <label class="stbm-btn stbm-btn-primary stbm-btn-sm" style="cursor:pointer">Upload &amp; Import Legacy JSON<input type="file" id="stbm-import-legacy-file" accept=".json" style="display:none"></label>' +
      '  </div>' +
      '  <div id="stbm-legacy-report" style="margin-top:14px"></div>' +
      '</div></div>' +
      '<div class="stbm-card"><div class="stbm-card-head"><h3>System Audit Trail (' + state.logs.length + ' Latest Events)</h3></div>' +
      '  <div class="stbm-table-wrap"><table class="stbm-table"><thead><tr><th>Timestamp</th><th>Action</th><th>Entity</th><th>User</th><th>Details</th></tr></thead><tbody>' +
      state.logs.map(function (l) {
        return '<tr><td style="font-size:11.5px;color:#64748b;white-space:nowrap">' + esc(String(l.timestamp).slice(0, 19).replace('T', ' ')) + '</td>' +
          '<td><span class="stbm-badge stbm-badge-teal">' + esc(l.action) + '</span></td>' +
          '<td><strong>' + esc(l.entityTitle || l.entityType) + '</strong></td>' +
          '<td>' + esc(l.user) + '</td><td>' + esc(l.details) + '</td></tr>';
      }).join('') +
      '  </tbody></table></div></div>';

    document.getElementById('stbm-restore-billing').onchange = function (e) {
      const f = e.target.files && e.target.files[0];
      if (!f) return;
      const r = new FileReader();
      r.onload = function () {
        try {
          const parsed = JSON.parse(r.result);
          confirmDialog({
            title: 'Restore Billing Database from JSON?',
            description: 'This replaces current customers, invoices, and receipts with the uploaded JSON archive.',
            confirmLabel: 'Restore Billing Data',
            isDestructive: true,
            onConfirm: function () {
              ajax('stbm_restore_backup', { scope: 'billing', data: parsed }).then(function () {
                toast('Billing backup restored successfully!', 'success');
                renderShell();
              }).catch(function (err) { toast(err.message, 'error'); });
            },
          });
        } catch (err) {
          toast('Invalid JSON file.', 'error');
        }
      };
      r.readAsText(f);
    };

    document.getElementById('stbm-import-legacy-file').onchange = function (e) {
      const f = e.target.files && e.target.files[0];
      if (!f) return;
      const mode = document.getElementById('stbm-legacy-mode').value || 'merge';
      const dryRun = !!document.getElementById('stbm-legacy-dryrun').checked;
      const reportBox = document.getElementById('stbm-legacy-report');
      const r = new FileReader();
      r.onload = function () {
        try {
          const parsed = JSON.parse(r.result);
          const runImport = function () {
            ajax('stbm_import_legacy_bundle', { bundle: parsed, mode: mode, dryRun: dryRun }).then(function (res) {
              const rep = res.report || {};
              const lines = [];
              if (rep.successful && rep.successful.length) lines.push('<div><strong style="color:#0f766e">Successful:</strong> ' + esc(rep.successful.join(' | ')) + '</div>');
              if (rep.skipped_duplicates && rep.skipped_duplicates.length) lines.push('<div><strong style="color:#0369a1">Duplicates Handled (' + rep.skipped_duplicates.length + '):</strong> ' + esc(rep.skipped_duplicates.slice(0, 6).join(' | ')) + '</div>');
              if (rep.missing_images && rep.missing_images.length) lines.push('<div><strong style="color:#b45309">Missing Images (' + rep.missing_images.length + '):</strong> ' + esc(rep.missing_images.slice(0, 6).join(' | ')) + '</div>');
              if (rep.broken_relationships && rep.broken_relationships.length) lines.push('<div><strong style="color:#b45309">Broken Relationships (' + rep.broken_relationships.length + '):</strong> ' + esc(rep.broken_relationships.slice(0, 6).join(' | ')) + '</div>');
              if (rep.failed && rep.failed.length) lines.push('<div><strong style="color:#dc2626">Failed (' + rep.failed.length + '):</strong> ' + esc(rep.failed.join(' | ')) + '</div>');
              reportBox.innerHTML = '<div style="background:#f8fafc;border:1px solid #cbd5e1;border-radius:8px;padding:12px;font-size:12.5px;display:grid;gap:6px">' +
                '<div><strong>Schema:</strong> ' + esc(rep.schema || 'unknown') + ' &bull; <strong>Mode:</strong> ' + esc(rep.mode || mode) + (rep.dry_run ? ' (DRY RUN PREVIEW)' : ' (APPLIED)') + '</div>' +
                lines.join('') + '</div>';
              toast(dryRun ? 'Dry-run inspection complete.' : 'Legacy export imported cleanly!', 'success');
              if (!dryRun) {
                setTimeout(function () { renderShell(); }, 900);
              }
            }).catch(function (err) { toast(err.message, 'error'); });
          };

          if (mode === 'replace' && !dryRun) {
            confirmDialog({
              title: 'Replace Existing Data with Legacy Export?',
              description: 'Replace mode overwrites matching sections before importing. Use Merge mode to preserve and upsert safely.',
              confirmLabel: 'Proceed with Replace Import',
              isDestructive: true,
              onConfirm: runImport,
            });
          } else {
            runImport();
          }
        } catch (err) {
          toast('Invalid JSON file.', 'error');
        }
      };
      r.readAsText(f);
    };

    document.getElementById('stbm-reset-seeds').onclick = function () {
      confirmDialog({
        title: 'Reset Billing & Studio Seeds?',
        description: 'Re-initializes customers, invoices, receipts, and image studio starter data.',
        confirmLabel: 'Reset Seeds',
        isDestructive: true,
        onConfirm: function () {
          ajax('stbm_reset_billing_seed', {}).then(function () {
            toast('Billing & Image Studio reset to starter seeds.', 'info');
            renderShell();
          }).catch(function (err) { toast(err.message, 'error'); });
        },
      });
    };
  }

  /* =====================================================================
   * MODULE 14: PROFILE, WP SECURITY & CONTENT BACKUP (`AdminProfile` 4 Tabs)
   * ===================================================================== */
  function pwdStrength(pwd) {
    const t = String(pwd || '').trim();
    if (!t) return { pct: 0, label: 'Enter password', color: '#cbd5e1' };
    let score = 0;
    if (t.length >= 8) score++;
    if (/[a-z]/.test(t) && /[A-Z]/.test(t)) score++;
    if (/\d/.test(t)) score++;
    if (/[^a-zA-Z0-9]/.test(t)) score++;
    if (t.length < 8) return { pct: 25, label: 'Too Short (Min 8 chars)', color: '#ef4444' };
    if (score <= 2) return { pct: 55, label: 'Medium', color: '#f59e0b' };
    if (score === 3) return { pct: 80, label: 'Strong', color: '#10b981' };
    return { pct: 100, label: 'Very Strong', color: '#015557' };
  }

  function renderProfile(vp) {
    const tab = state.ui.profileTab || 'profile';
    const u = state.user || {};
    const presets = state.boot.avatarPresets || [];
    let bodyHtml = '';

    if (tab === 'profile') {
      bodyHtml =
        '<div class="stbm-split-screen">' +
        '  <div class="stbm-card"><div class="stbm-card-head"><h3>WordPress User Profile</h3></div><div class="stbm-card-body">' +
        '    <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px">' +
        '      <img src="' + esc(u.avatar) + '" id="stbm-prof-avatar-prev" style="width:72px;height:72px;border-radius:999px;object-fit:cover;border:3px solid var(--stbm-primary)">' +
        '      <div><div style="font-size:16px;font-weight:800">' + esc(u.displayName) + '</div><div style="font-size:12.5px;color:#64748b">@' + esc(u.username) + ' · ' + esc(u.roleLabel) + '</div>' +
        '      <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" style="margin-top:6px" id="stbm-prof-upload">Upload Custom Avatar</button></div>' +
        '    </div>' +
        '    <div class="stbm-grid stbm-grid-2">' +
        '      <div class="stbm-field"><label class="stbm-field-label">Display Name</label><input class="stbm-input" id="stbm-prof-name" value="' + esc(u.displayName) + '"></div>' +
        '      <div class="stbm-field"><label class="stbm-field-label">WordPress Email</label><input class="stbm-input" id="stbm-prof-email" value="' + esc(u.email) + '"></div>' +
        '    </div>' +
        '    <div class="stbm-field"><label class="stbm-field-label">Role Title / Job Designation</label><input class="stbm-input" id="stbm-prof-job" value="' + esc(u.jobTitle) + '"></div>' +
        '    <div class="stbm-field"><label class="stbm-field-label">Avatar Image URL</label><input class="stbm-input" id="stbm-prof-av" value="' + esc(u.avatar) + '"></div>' +
        '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-prof-save">Save WordPress Profile</button>' +
        '  </div></div>' +
        '  <div class="stbm-card"><div class="stbm-card-head"><h3>5 Professional Preset Avatars</h3></div><div class="stbm-card-body" style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px">' +
        presets.map(function (p) {
          const active = u.avatar === p.url;
          return '<button type="button" class="stbm-btn ' + (active ? 'stbm-btn-primary' : 'stbm-btn-secondary') + '" data-av-preset="' + esc(p.url) + '" style="justify-content:flex-start;padding:8px">' +
            '<img src="' + esc(p.url) + '" style="width:34px;height:34px;border-radius:999px;object-fit:cover"> <span style="font-size:12px">' + esc(p.label) + '</span></button>';
        }).join('') +
        '  </div></div>' +
        '</div>';
    } else if (tab === 'business') {
      bodyHtml =
        '<div class="stbm-card"><div class="stbm-card-head"><h3>Business Identity Summary</h3></div><div class="stbm-card-body">' +
        '  <div class="stbm-grid stbm-grid-2">' +
        '    <div class="stbm-field"><label class="stbm-field-label">Legal Entity Name</label><input class="stbm-input" id="pb-name" value="' + esc(dotGet(state.siteContent, 'global.brand.name', '')) + '"></div>' +
        '    <div class="stbm-field"><label class="stbm-field-label">Short Brand Name</label><input class="stbm-input" id="pb-short" value="' + esc(dotGet(state.siteContent, 'global.brand.shortName', '')) + '"></div>' +
        '    <div class="stbm-field"><label class="stbm-field-label">Lead Contact Person</label><input class="stbm-input" id="pb-contact" value="' + esc(dotGet(state.siteContent, 'global.brand.contactPerson', '')) + '"></div>' +
        '    <div class="stbm-field"><label class="stbm-field-label">Tagline</label><input class="stbm-input" id="pb-tag" value="' + esc(dotGet(state.siteContent, 'global.brand.tagline', '')) + '"></div>' +
        '  </div>' +
        '  <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-pb-save">Save Business Details</button>' +
        '</div></div>';
    } else if (tab === 'security') {
      bodyHtml =
        '<div class="stbm-card" style="max-width:620px"><div class="stbm-card-head"><h3>WordPress Password &amp; Access Security</h3></div><div class="stbm-card-body">' +
        '  <p style="font-size:12.5px;color:#64748b">Authenticated via native WordPress User Sessions &amp; Capability RBAC (<code>' + esc(u.roleLabel) + '</code>).</p>' +
        '  <div class="stbm-field"><label class="stbm-field-label">Current WordPress Password</label><input type="password" class="stbm-input" id="sec-cur"></div>' +
        '  <div class="stbm-field"><label class="stbm-field-label">New Password (min 8 chars)</label><input type="password" class="stbm-input" id="sec-new">' +
        '    <div style="height:6px;background:#e2e8f0;border-radius:999px;margin-top:8px;overflow:hidden"><div id="sec-bar" style="height:100%;width:0%;transition:all 0.2s"></div></div>' +
        '    <div class="stbm-hint" id="sec-lbl">Enter password</div>' +
        '  </div>' +
        '  <div class="stbm-field"><label class="stbm-field-label">Confirm New Password</label><input type="password" class="stbm-input" id="sec-conf"></div>' +
        '  <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-sec-save">Update WordPress Password</button>' +
        '</div></div>';
    } else {
      bodyHtml =
        '<div class="stbm-grid stbm-grid-2">' +
        '  <div class="stbm-card"><div class="stbm-card-head"><h3>Site Content &amp; Inventory JSON Backup</h3></div><div class="stbm-card-body">' +
        '    <p style="font-size:12.5px;color:#64748b">Export all pages text, theme colors, links, images, and CPT inventory to a single portable JSON file.</p>' +
        '    <a href="' + esc(exportUrl('stbm_export_json', { scope: 'content' })) + '" class="stbm-btn stbm-btn-primary">Download Full Content JSON</a>' +
        '    <hr style="margin:18px 0;border:0;border-top:1px solid #e2e8f0">' +
        '    <div class="stbm-field"><label class="stbm-field-label">Restore Content JSON Mode</label><select class="stbm-select" id="stbm-cb-mode"><option value="merge">Merge with Existing Content</option><option value="replace">Replace All Content &amp; Inventory</option></select></div>' +
        '    <label class="stbm-btn stbm-btn-secondary" style="cursor:pointer">Upload &amp; Restore Content JSON<input type="file" id="stbm-cb-file" accept=".json" style="display:none"></label>' +
        '  </div></div>' +
        '  <div class="stbm-card"><div class="stbm-card-head"><h3>Factory Reset Website Content</h3></div><div class="stbm-card-body">' +
        '    <p style="font-size:12.5px;color:#64748b">Restore site text, colors, links, and image defaults back to clean starter configuration.</p>' +
        '    <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-bottom:14px"><input type="checkbox" id="stbm-fr-inv"> Also reset CPT Inventory (Cars, Rentals, Parts, Gallery, FAQs, Careers)</label>' +
        '    <button type="button" class="stbm-btn stbm-btn-danger" id="stbm-fr-btn">Factory Reset Website Content</button>' +
        '  </div></div>' +
        '</div>';
    }

    vp.innerHTML =
      '<div class="stbm-page-head"><div><h2>My Profile &amp; System Preferences</h2><p>WordPress user identity, password security, business details, and full content backup.</p></div></div>' +
      '<div class="stbm-tabs">' +
      [
        { id: 'profile', label: 'My Profile & Avatars' },
        { id: 'business', label: 'Business Details' },
        { id: 'security', label: 'Password & Security' },
        { id: 'backup', label: 'Content Backup & Reset' },
      ].map(function (t) {
        return '<button type="button" class="stbm-tab ' + (tab === t.id ? 'active' : '') + '" data-prof-tab="' + t.id + '">' + esc(t.label) + '</button>';
      }).join('') +
      '</div>' + bodyHtml;

    vp.querySelectorAll('[data-prof-tab]').forEach(function (b) {
      b.onclick = function () { state.ui.profileTab = b.getAttribute('data-prof-tab'); renderProfile(vp); };
    });

    if (tab === 'profile') {
      vp.querySelectorAll('[data-av-preset]').forEach(function (b) {
        b.onclick = function () {
          const av = b.getAttribute('data-av-preset');
          document.getElementById('stbm-prof-av').value = av;
          document.getElementById('stbm-prof-avatar-prev').src = av;
        };
      });
      document.getElementById('stbm-prof-upload').onclick = function () {
        openWpMediaPicker(function (url) {
          document.getElementById('stbm-prof-av').value = url;
          document.getElementById('stbm-prof-avatar-prev').src = url;
        }, 'Select Profile Avatar');
      };
      document.getElementById('stbm-prof-save').onclick = function () {
        ajax('stbm_update_profile', {
          displayName: document.getElementById('stbm-prof-name').value.trim(),
          email: document.getElementById('stbm-prof-email').value.trim(),
          jobTitle: document.getElementById('stbm-prof-job').value.trim(),
          avatar: document.getElementById('stbm-prof-av').value.trim(),
        }).then(function (data) {
          if (data.user) Object.assign(state.user, data.user);
          toast('WordPress user profile updated!', 'success');
          renderShell();
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    } else if (tab === 'business') {
      document.getElementById('stbm-pb-save').onclick = function () {
        ajax('stbm_save_content', {
          updates: {
            'global.brand.name': document.getElementById('pb-name').value.trim(),
            'global.brand.shortName': document.getElementById('pb-short').value.trim(),
            'global.brand.contactPerson': document.getElementById('pb-contact').value.trim(),
            'global.brand.tagline': document.getElementById('pb-tag').value.trim(),
          },
        }).then(function () {
          toast('Business details saved!', 'success');
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    } else if (tab === 'security') {
      document.getElementById('sec-new').oninput = function (e) {
        const s = pwdStrength(e.target.value);
        document.getElementById('sec-bar').style.width = s.pct + '%';
        document.getElementById('sec-bar').style.background = s.color;
        document.getElementById('sec-lbl').textContent = s.label;
      };
      document.getElementById('stbm-sec-save').onclick = function () {
        const cur = document.getElementById('sec-cur').value;
        const np = document.getElementById('sec-new').value;
        const cf = document.getElementById('sec-conf').value;
        if (np !== cf) { toast('New password and confirmation do not match.', 'error'); return; }
        ajax('stbm_update_password', { currentPassword: cur, newPassword: np }).then(function (data) {
          toast(data.message || 'Password updated!', 'success');
          renderProfile(vp);
        }).catch(function (err) { toast(err.message, 'error'); });
      };
    } else {
      document.getElementById('stbm-cb-file').onchange = function (e) {
        const f = e.target.files && e.target.files[0];
        if (!f) return;
        const mode = document.getElementById('stbm-cb-mode').value;
        const r = new FileReader();
        r.onload = function () {
          try {
            const parsed = JSON.parse(r.result);
            confirmDialog({
              title: 'Restore Website Content (' + mode.toUpperCase() + ')?',
              description: 'Apply uploaded JSON archive to WordPress site content & CPT inventory.',
              confirmLabel: 'Apply Restore',
              isDestructive: mode === 'replace',
              onConfirm: function () {
                ajax('stbm_restore_backup', { scope: 'content', mode: mode, data: parsed }).then(function (data) {
                  if (data.inventory) state.inventory = data.inventory;
                  toast('Website content restored!', 'success');
                  renderShell();
                }).catch(function (err) { toast(err.message, 'error'); });
              },
            });
          } catch (err) {
            toast('Invalid JSON file.', 'error');
          }
        };
        r.readAsText(f);
      };
      document.getElementById('stbm-fr-btn').onclick = function () {
        const withInv = document.getElementById('stbm-fr-inv').checked;
        confirmDialog({
          title: 'Factory Reset Website Content?',
          description: 'Restore default website copy, colors, links & images' + (withInv ? ' plus all 6 CPT inventory catalogs' : '') + '?',
          confirmLabel: 'Factory Reset',
          isDestructive: true,
          onConfirm: function () {
            ajax('stbm_factory_reset', { includeInventory: withInv }).then(function (data) {
              if (data.inventory) state.inventory = data.inventory;
              toast('Factory defaults restored!', 'info');
              renderShell();
            }).catch(function (err) { toast(err.message, 'error'); });
          },
        });
      };
    }
  }

  /* ---------------- View Router Dispatcher ---------------- */
  function renderActiveModule() {
    const vp = document.getElementById('stbm-viewport');
    if (!vp) return;
    const mod = window.STBM_MODULES || {};
    const v = state.activeView;

    if (v === 'dashboard') return renderDashboard(vp);
    if (v === 'inventory') return renderInventory(vp);
    if (v === 'pages-text') return renderPagesText(vp);
    if (v === 'colors') return renderColors(vp);
    if (v === 'logo') return renderLogo(vp);
    if (v === 'links') return renderLinks(vp);
    if (v === 'images') return renderImages(vp);
    if (v === 'backup') return renderBackupExport(vp);
    if (v === 'profile') return renderProfile(vp);

    if (v === 'customers' && mod.renderCustomers) return mod.renderCustomers(vp);
    if (v === 'invoices' && mod.renderInvoices) return mod.renderInvoices(vp);
    if (v === 'receipts' && mod.renderReceipts) return mod.renderReceipts(vp);
    if (v === 'doc-settings' && mod.renderDocSettings) return mod.renderDocSettings(vp);
    if (v === 'image-studio' && mod.renderImageStudio) return mod.renderImageStudio(vp);

    vp.innerHTML = '<div class="stbm-card"><div class="stbm-card-body">Module "' + esc(v) + '" loading...</div></div>';
  }

  window.STBM = {
    state: state,
    esc: esc,
    money: money,
    dotGet: dotGet,
    dotSet: dotSet,
    toast: toast,
    ajax: ajax,
    exportUrl: exportUrl,
    confirmDialog: confirmDialog,
    bindModalDismiss: bindModalDismiss,
    openWpMediaPicker: openWpMediaPicker,
    openUnifiedImageModal: openUnifiedImageModal,
    svg: svg,
    navigate: navigate,
    renderShell: renderShell,
    renderActiveModule: renderActiveModule,
  };
  window.STBM_MODULES = window.STBM_MODULES || {};

  document.addEventListener('click', function () {
    if (state.ui.userMenuOpen) {
      state.ui.userMenuOpen = false;
      const dd = document.getElementById('stbm-user-dropdown');
      if (dd) dd.classList.add('stbm-hidden');
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const backdrop = document.getElementById('stbm-mobile-backdrop');
    if (backdrop) {
      backdrop.addEventListener('click', function () {
        state.ui.mobileSidebarOpen = false;
        backdrop.classList.add('stbm-hidden');
        renderSidebar();
      });
    }
    renderShell();
  });
})();
