/**
 * Santa Towing Business Manager — HTML5 Canvas Image Studio Module
 * Pure browser <canvas> editor: 8-handle interactive crop (corners + edges + move),
 * 7 aspect ratios + padlock aspect-ratio locking, Rotate +/-90 deg, Flip H/V,
 * live Text & Logo watermark canvas preview, color/opacity/scale/position controls,
 * JPEG/PNG/WebP compression, full Undo/Redo, Reset, Hold Compare, Local Download,
 * and Save Copy / Overwrite directly to WordPress Media Library + MySQL.
 */
(function () {
  'use strict';

  window.STBM_MODULES = window.STBM_MODULES || {};

  function app() {
    return window.STBM;
  }

  const CATEGORIES = ['All', 'Fleet', 'Towing', 'Haulage', 'Garage', 'Documents', 'General'];
  const RATIOS = [
    { label: 'Free', value: null },
    { label: '1:1', value: 1 },
    { label: '4:3', value: 4 / 3 },
    { label: '3:4', value: 3 / 4 },
    { label: '16:9', value: 16 / 9 },
    { label: '9:16', value: 9 / 16 },
    { label: '3:2', value: 3 / 2 },
  ];

  let activeCat = 'All', statusFilter = 'all', searchQ = '';

  function formatBytes(bytes) {
    const b = Number(bytes || 0);
    if (b < 1024) return b + ' B';
    if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
    return (b / (1024 * 1024)).toFixed(2) + ' MB';
  }

  /**
   * Compute updated normalized crop box {x, y, w, h} in [0..1] for any of 8 handles or 'move'.
   */
  function computeNextCrop(startCrop, mode, dx, dy, targetAspect, imgAspect) {
    const minSize = 0.08;
    const sc = startCrop;
    const c = { x: sc.x, y: sc.y, w: sc.w, h: sc.h };

    if (mode === 'move') {
      c.x = Math.max(0, Math.min(1 - sc.w, sc.x + dx));
      c.y = Math.max(0, Math.min(1 - sc.h, sc.y + dy));
      return c;
    }

    let left = sc.x;
    let right = sc.x + sc.w;
    let top = sc.y;
    let bottom = sc.y + sc.h;

    if (mode.indexOf('w') !== -1) {
      left = Math.max(0, Math.min(right - minSize, sc.x + dx));
    }
    if (mode.indexOf('e') !== -1) {
      right = Math.min(1, Math.max(left + minSize, sc.x + sc.w + dx));
    }
    if (mode.indexOf('n') !== -1) {
      top = Math.max(0, Math.min(bottom - minSize, sc.y + dy));
    }
    if (mode.indexOf('s') !== -1) {
      bottom = Math.min(1, Math.max(top + minSize, sc.y + sc.h + dy));
    }

    let nw = right - left;
    let nh = bottom - top;

    // Normalized height ratio factor: (nw * imgW) / (nh * imgH) = targetAspect => normRatio = targetAspect / imgAspect
    if (targetAspect && targetAspect > 0) {
      const safeImgAspect = imgAspect && imgAspect > 0 ? imgAspect : 1;
      const normRatio = targetAspect / safeImgAspect;
      if (mode === 'n' || mode === 's') {
        nw = nh * normRatio;
        if (left + nw > 1) {
          nw = 1 - left;
          nh = nw / normRatio;
          if (mode === 'n') top = bottom - nh;
        }
      } else {
        nh = nw / normRatio;
        if (mode.indexOf('n') !== -1) {
          top = bottom - nh;
          if (top < 0) {
            top = 0;
            nh = bottom;
            nw = nh * normRatio;
            if (mode.indexOf('w') !== -1) left = right - nw;
          }
        } else {
          if (top + nh > 1) {
            nh = 1 - top;
            nw = nh * normRatio;
            if (mode.indexOf('w') !== -1) left = right - nw;
          }
        }
      }
      if (left < 0) { left = 0; nw = right - left; nh = nw / normRatio; }
      if (top < 0) { top = 0; nh = bottom - top; nw = nh * normRatio; }
    }

    c.x = Math.max(0, Math.min(1 - minSize, left));
    c.y = Math.max(0, Math.min(1 - minSize, top));
    c.w = Math.max(minSize, Math.min(1 - c.x, nw));
    c.h = Math.max(minSize, Math.min(1 - c.y, nh));
    return c;
  }

  /**
   * Hit-test cursor in normalized [0..1] canvas coordinates against 8 crop handles + interior box.
   */
  function hitTestCropHandle(nx, ny, crop) {
    const tol = 0.04;
    const mx = crop.x + crop.w / 2;
    const my = crop.y + crop.h / 2;
    const nearL = Math.abs(nx - crop.x) < tol;
    const nearR = Math.abs(nx - (crop.x + crop.w)) < tol;
    const nearT = Math.abs(ny - crop.y) < tol;
    const nearB = Math.abs(ny - (crop.y + crop.h)) < tol;
    const nearMx = Math.abs(nx - mx) < tol * 1.4;
    const nearMy = Math.abs(ny - my) < tol * 1.4;

    if (nearL && nearT) return 'nw';
    if (nearR && nearT) return 'ne';
    if (nearR && nearB) return 'se';
    if (nearL && nearB) return 'sw';
    if (nearT && nearMx) return 'n';
    if (nearB && nearMx) return 's';
    if (nearR && nearMy) return 'e';
    if (nearL && nearMy) return 'w';
    if (nx >= crop.x && nx <= crop.x + crop.w && ny >= crop.y && ny <= crop.y + crop.h) return 'move';
    return null;
  }

  window.STBM_MODULES._imageStudioHelpers = {
    computeNextCrop: computeNextCrop,
    hitTestCropHandle: hitTestCropHandle,
  };

  function renderImageStudio(vp) {
    const A = app();
    const list = (A.state.studioImages || []).filter(function (img) {
      if (activeCat !== 'All' && img.category !== activeCat) return false;
      if (statusFilter === 'edited' && !img.isEdited) return false;
      if (statusFilter === 'originals' && img.isEdited) return false;
      if (searchQ && (img.name + ' ' + img.category).toLowerCase().indexOf(searchQ.toLowerCase()) === -1) return false;
      return true;
    });

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Image Studio &amp; Crop Editor</h2><p>Crop, rotate, flip, watermark, compress, and save high-res assets directly to WordPress Media Library.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <label class="stbm-btn stbm-btn-primary" style="cursor:pointer">+ Upload Photo to Edit<input type="file" id="stbm-studio-upload" accept="image/*" style="display:none"></label>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-tabs">' +
      CATEGORIES.map(function (c) {
        return '<button type="button" class="stbm-tab ' + (activeCat === c ? 'active' : '') + '" data-st-cat="' + c + '">' + c + '</button>';
      }).join('') +
      '</div>' +
      '<div class="stbm-toolbar">' +
      '  <div class="stbm-search-box">' + A.svg('search') + '<input class="stbm-input" id="stbm-st-search" placeholder="Search studio images..." value="' + A.esc(searchQ) + '"></div>' +
      '  <div style="display:flex;gap:6px">' +
      ['all', 'originals', 'edited'].map(function (f) {
        return '<button type="button" class="stbm-btn stbm-btn-sm ' + (statusFilter === f ? 'stbm-btn-primary' : 'stbm-btn-secondary') + '" data-st-sf="' + f + '">' + f.toUpperCase() + '</button>';
      }).join('') +
      '  </div>' +
      '</div>' +
      '<div class="stbm-studio-grid">' +
      (list.map(function (img) {
        return '<div class="stbm-studio-card">' +
          '<div class="stbm-studio-thumb"><img src="' + A.esc(img.dataUrl) + '" alt="' + A.esc(img.name) + '"></div>' +
          '<div style="padding:14px;display:flex;flex-direction:column;gap:6px;flex:1">' +
          '  <div style="display:flex;justify-content:space-between;align-items:center;gap:6px">' +
          '    <span class="stbm-badge stbm-badge-teal">' + A.esc(img.category) + '</span>' +
          (img.isEdited ? '<span class="stbm-badge stbm-badge-amber">Edited Copy</span>' : '<span class="stbm-badge stbm-badge-gray">Original</span>') +
          '  </div>' +
          '  <strong style="font-size:13.5px">' + A.esc(img.name) + '</strong>' +
          '  <div style="font-size:11.5px;color:#64748b">' + img.width + ' &times; ' + img.height + 'px &middot; ' + formatBytes(img.size) + '</div>' +
          '  <div style="display:flex;gap:6px;margin-top:8px">' +
          '    <button type="button" class="stbm-btn stbm-btn-primary stbm-btn-sm" style="flex:1" data-st-edit="' + A.esc(img.id) + '">Open Studio Editor</button>' +
          '    <a class="stbm-btn stbm-btn-secondary stbm-btn-sm" href="' + A.esc(img.dataUrl) + '" download="' + A.esc(img.name) + '">Save</a>' +
          '    <button type="button" class="stbm-btn-icon danger" data-st-del="' + A.esc(img.id) + '">&times;</button>' +
          '  </div>' +
          '</div></div>';
      }).join('') || '<div style="grid-column:1/-1;padding:40px;text-align:center;color:#64748b;background:#fff;border-radius:14px;border:1px solid #e2e8f0">No studio assets match your filter.</div>') +
      '</div>';

    vp.querySelectorAll('[data-st-cat]').forEach(function (b) {
      b.onclick = function () { activeCat = b.getAttribute('data-st-cat'); renderImageStudio(vp); };
    });
    vp.querySelectorAll('[data-st-sf]').forEach(function (b) {
      b.onclick = function () { statusFilter = b.getAttribute('data-st-sf'); renderImageStudio(vp); };
    });
    document.getElementById('stbm-st-search').oninput = function (e) {
      searchQ = e.target.value;
      renderImageStudio(vp);
      const inp = document.getElementById('stbm-st-search');
      if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
    };
    document.getElementById('stbm-studio-upload').onchange = function (e) {
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = function () {
        openCanvasEditor({
          id: null,
          name: file.name.replace(/\.[^.]+$/, ''),
          dataUrl: reader.result,
          category: 'General',
          format: file.type || 'image/jpeg',
        }, vp);
      };
      reader.readAsDataURL(file);
    };
    vp.querySelectorAll('[data-st-edit]').forEach(function (b) {
      b.onclick = function () {
        const img = A.state.studioImages.find(function (x) { return x.id === b.getAttribute('data-st-edit'); });
        if (img) openCanvasEditor(img, vp);
      };
    });
    vp.querySelectorAll('[data-st-del]').forEach(function (b) {
      b.onclick = function () {
        const id = b.getAttribute('data-st-del');
        A.confirmDialog({
          title: 'Remove Studio Asset?',
          description: 'Remove this image from the Image Studio catalog?',
          confirmLabel: 'Delete Image',
          isDestructive: true,
          onConfirm: function () {
            A.ajax('stbm_delete_studio_image', { id: id }).then(function () {
              A.toast('Image removed from Image Studio.', 'info');
              renderImageStudio(vp);
            }).catch(function (err) { A.toast(err.message, 'error'); });
          },
        });
      };
    });
  }

  /* =====================================================================
   * HTML5 CANVAS IMAGE EDITOR MODAL
   * ===================================================================== */
  function openCanvasEditor(asset, vp) {
    const A = app();
    const root = document.getElementById('stbm-modal-root');

    const ed = {
      name: asset.name || 'Edited Image',
      category: asset.category || 'General',
      format: asset.format || 'image/jpeg',
      quality: 0.88,
      rotation: 0,
      flipH: false,
      flipV: false,
      crop: { x: 0.05, y: 0.05, w: 0.9, h: 0.9 },
      aspect: null,
      locked: false,
      wmType: 'none', // none | text | logo
      wmText: 'SANTA TOWING GHANA',
      wmColor: '#ffffff',
      wmOpacity: 0.72,
      wmScale: 1.0,
      wmPos: 'bottom-right',
      comparingOriginal: false,
      history: [],
      redoStack: [],
    };

    const sourceImg = new Image();
    sourceImg.crossOrigin = 'anonymous';
    const logoImg = new Image();
    logoImg.crossOrigin = 'anonymous';
    logoImg.src = A.dotGet(A.state.siteContent, 'global.logo.url', A.state.boot.pluginUrl + 'assets/images/santa-logo.png');

    function snapshot() {
      return JSON.stringify({
        rotation: ed.rotation,
        flipH: ed.flipH,
        flipV: ed.flipV,
        crop: Object.assign({}, ed.crop),
        aspect: ed.aspect,
        locked: ed.locked,
        wmType: ed.wmType,
        wmText: ed.wmText,
        wmColor: ed.wmColor,
        wmOpacity: ed.wmOpacity,
        wmScale: ed.wmScale,
        wmPos: ed.wmPos,
      });
    }

    function pushHistory(customSnap) {
      ed.history.push(customSnap || snapshot());
      if (ed.history.length > 25) ed.history.shift();
      ed.redoStack = [];
    }

    function computeOrientedCanvas() {
      const c = document.createElement('canvas');
      const rot90 = (Math.abs(ed.rotation) % 180) === 90;
      const w = sourceImg.naturalWidth || 800;
      const h = sourceImg.naturalHeight || 600;
      c.width = rot90 ? h : w;
      c.height = rot90 ? w : h;
      const ctx = c.getContext('2d');
      ctx.translate(c.width / 2, c.height / 2);
      ctx.rotate((ed.rotation * Math.PI) / 180);
      ctx.scale(ed.flipH ? -1 : 1, ed.flipV ? -1 : 1);
      ctx.drawImage(sourceImg, -w / 2, -h / 2, w, h);
      return c;
    }

    function drawWatermarkOnCtx(ctx, boxX, boxY, boxW, boxH) {
      if (ed.wmType === 'none') return;
      ctx.save();
      ctx.globalAlpha = ed.wmOpacity;
      const pad = Math.max(8, boxW * 0.03);
      if (ed.wmType === 'text' && ed.wmText) {
        const fSize = Math.round(Math.max(10, boxW * 0.036 * ed.wmScale));
        ctx.font = '800 ' + fSize + 'px sans-serif';
        ctx.fillStyle = ed.wmColor || '#ffffff';
        ctx.shadowColor = 'rgba(0,0,0,0.65)';
        ctx.shadowBlur = 6;
        let tx = boxX + boxW - pad, ty = boxY + boxH - pad;
        ctx.textAlign = 'right';
        if (ed.wmPos === 'top-left') { tx = boxX + pad; ty = boxY + pad + fSize; ctx.textAlign = 'left'; }
        else if (ed.wmPos === 'top-right') { tx = boxX + boxW - pad; ty = boxY + pad + fSize; ctx.textAlign = 'right'; }
        else if (ed.wmPos === 'bottom-left') { tx = boxX + pad; ty = boxY + boxH - pad; ctx.textAlign = 'left'; }
        else if (ed.wmPos === 'center') { tx = boxX + boxW / 2; ty = boxY + boxH / 2 + (fSize * 0.35); ctx.textAlign = 'center'; }
        ctx.fillText(ed.wmText, tx, ty);
      } else if (ed.wmType === 'logo' && logoImg.complete && logoImg.naturalWidth > 0) {
        const lw = Math.round(boxW * 0.22 * ed.wmScale);
        const lh = Math.round(lw * (logoImg.naturalHeight / logoImg.naturalWidth));
        let lx = boxX + boxW - lw - pad, ly = boxY + boxH - lh - pad;
        if (ed.wmPos === 'top-left') { lx = boxX + pad; ly = boxY + pad; }
        else if (ed.wmPos === 'top-right') { lx = boxX + boxW - lw - pad; ly = boxY + pad; }
        else if (ed.wmPos === 'bottom-left') { lx = boxX + pad; ly = boxY + boxH - lh - pad; }
        else if (ed.wmPos === 'center') { lx = boxX + (boxW - lw) / 2; ly = boxY + (boxH - lh) / 2; }
        ctx.drawImage(logoImg, lx, ly, lw, lh);
      }
      ctx.restore();
    }

    function exportCroppedDataUrl(applyWm) {
      const base = computeOrientedCanvas();
      const sx = Math.round(ed.crop.x * base.width);
      const sy = Math.round(ed.crop.y * base.height);
      const sw = Math.max(16, Math.round(ed.crop.w * base.width));
      const sh = Math.max(16, Math.round(ed.crop.h * base.height));

      const out = document.createElement('canvas');
      out.width = sw;
      out.height = sh;
      const ctx = out.getContext('2d');
      ctx.drawImage(base, sx, sy, sw, sh, 0, 0, sw, sh);

      if (applyWm) {
        drawWatermarkOnCtx(ctx, 0, 0, sw, sh);
      }
      return { dataUrl: out.toDataURL(ed.format, ed.quality), width: sw, height: sh };
    }

    function drawPreviewCanvas() {
      const cvs = document.getElementById('stbm-ed-canvas');
      if (!cvs || !sourceImg.complete || !sourceImg.naturalWidth) return;
      const base = ed.comparingOriginal ? null : computeOrientedCanvas();
      const srcW = ed.comparingOriginal ? sourceImg.naturalWidth : base.width;
      const srcH = ed.comparingOriginal ? sourceImg.naturalHeight : base.height;

      const maxW = 740, maxH = 440;
      const scale = Math.min(maxW / srcW, maxH / srcH, 1);
      cvs.width = Math.round(srcW * scale);
      cvs.height = Math.round(srcH * scale);
      const ctx = cvs.getContext('2d');
      ctx.clearRect(0, 0, cvs.width, cvs.height);
      if (ed.comparingOriginal) {
        ctx.drawImage(sourceImg, 0, 0, cvs.width, cvs.height);
        return;
      }
      ctx.drawImage(base, 0, 0, cvs.width, cvs.height);

      // Crop dim mask + live watermark preview clipped to crop rectangle
      const cx = ed.crop.x * cvs.width;
      const cy = ed.crop.y * cvs.height;
      const cw = ed.crop.w * cvs.width;
      const ch = ed.crop.h * cvs.height;

      ctx.save();
      ctx.beginPath();
      ctx.rect(cx, cy, cw, ch);
      ctx.clip();
      drawWatermarkOnCtx(ctx, cx, cy, cw, ch);
      ctx.restore();

      ctx.fillStyle = 'rgba(0, 0, 0, 0.55)';
      ctx.fillRect(0, 0, cvs.width, cy);
      ctx.fillRect(0, cy + ch, cvs.width, cvs.height - (cy + ch));
      ctx.fillRect(0, cy, cx, ch);
      ctx.fillRect(cx + cw, cy, cvs.width - (cx + cw), ch);

      ctx.strokeStyle = '#10b981';
      ctx.lineWidth = 2;
      ctx.strokeRect(cx, cy, cw, ch);

      // Rule of thirds lines
      ctx.strokeStyle = 'rgba(255,255,255,0.35)';
      ctx.lineWidth = 1;
      for (let g = 1; g <= 2; g++) {
        ctx.beginPath(); ctx.moveTo(cx + (cw * g) / 3, cy); ctx.lineTo(cx + (cw * g) / 3, cy + ch); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(cx, cy + (ch * g) / 3); ctx.lineTo(cx + cw, cy + (ch * g) / 3); ctx.stroke();
      }

      // 8 corner & edge handles
      const handles = [
        [cx, cy], [cx + cw / 2, cy], [cx + cw, cy],
        [cx, cy + ch / 2], [cx + cw, cy + ch / 2],
        [cx, cy + ch], [cx + cw / 2, cy + ch], [cx + cw, cy + ch],
      ];
      ctx.fillStyle = '#ffffff';
      ctx.strokeStyle = '#0f172a';
      ctx.lineWidth = 1;
      handles.forEach(function (pt) {
        ctx.fillRect(pt[0] - 4, pt[1] - 4, 8, 8);
        ctx.strokeRect(pt[0] - 4, pt[1] - 4, 8, 8);
      });

      // Update dimension + est size indicator
      const infoEl = document.getElementById('stbm-ed-dims');
      if (infoEl) {
        const outW = Math.round(ed.crop.w * srcW);
        const outH = Math.round(ed.crop.h * srcH);
        const estUrl = exportCroppedDataUrl(true).dataUrl;
        const estBytes = Math.round((estUrl.length * 3) / 4);
        infoEl.textContent = outW + ' x ' + outH + 'px · Est. ' + formatBytes(estBytes);
      }
    }

    let cleanupGlobal = null;
    function closeEditorModal() {
      if (typeof cleanupGlobal === 'function') cleanupGlobal();
      root.innerHTML = '';
    }

    function renderModal() {
      if (typeof cleanupGlobal === 'function') cleanupGlobal();

      root.innerHTML =
        '<div class="stbm-modal-backdrop" id="ed-backdrop"><div class="stbm-modal wide dark">' +
        '  <div class="stbm-modal-head">' +
        '    <div><h3>Image Studio Canvas Editor</h3><div id="stbm-ed-dims" style="font-size:11.5px;color:#94a3b8">Calculating...</div></div>' +
        '    <div style="display:flex;gap:8px;flex-wrap:wrap">' +
        '      <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-undo" ' + (ed.history.length ? '' : 'disabled') + '>Undo</button>' +
        '      <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-redo" ' + (ed.redoStack.length ? '' : 'disabled') + '>Redo</button>' +
        '      <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-reset">Reset</button>' +
        '      <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-compare">Hold Compare</button>' +
        '      <button type="button" class="stbm-btn-icon" id="ed-close">&times;</button>' +
        '    </div>' +
        '  </div>' +
        '  <div class="stbm-modal-body">' +
        '    <div class="stbm-canvas-stage">' +
        '      <div class="stbm-canvas-viewport"><canvas id="stbm-ed-canvas" style="max-width:100%;cursor:crosshair;border-radius:6px;touch-action:none"></canvas></div>' +
        '      <div class="stbm-canvas-controls">' +
        '        <div class="stbm-field"><label class="stbm-field-label" style="color:#cbd5e1">Asset Name</label><input class="stbm-input" id="ed-name" value="' + A.esc(ed.name) + '"></div>' +
        '        <div class="stbm-field"><label class="stbm-field-label" style="color:#cbd5e1">Category</label><select class="stbm-select" id="ed-cat">' +
        CATEGORIES.slice(1).map(function (c) { return '<option ' + (ed.category === c ? 'selected' : '') + '>' + c + '</option>'; }).join('') +
        '        </select></div>' +
        '        <div class="stbm-field"><label class="stbm-field-label" style="color:#cbd5e1"><span>Crop Aspect Ratio</span><button type="button" class="stbm-btn stbm-btn-sm ' + (ed.locked ? 'stbm-btn-primary' : 'stbm-btn-secondary') + '" id="ed-lock">' + (ed.locked ? 'Locked' : 'Unlock') + '</button></label>' +
        '          <div style="display:flex;flex-wrap:wrap;gap:5px">' +
        RATIOS.map(function (r, idx) {
          const active = ed.aspect === r.value;
          return '<button type="button" class="stbm-btn stbm-btn-sm ' + (active ? 'stbm-btn-primary' : 'stbm-btn-secondary') + '" data-ed-ratio="' + idx + '">' + r.label + '</button>';
        }).join('') +
        '          </div>' +
        '        </div>' +
        '        <div class="stbm-field"><label class="stbm-field-label" style="color:#cbd5e1">Transform</label>' +
        '          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px">' +
        '            <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-rot-l">-90&deg;</button>' +
        '            <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-rot-r">+90&deg;</button>' +
        '            <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-flip-h">Flip H</button>' +
        '            <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ed-flip-v">Flip V</button>' +
        '          </div>' +
        '        </div>' +
        '        <div class="stbm-field"><label class="stbm-field-label" style="color:#cbd5e1">Watermark Overlay</label>' +
        '          <select class="stbm-select" id="ed-wm-type"><option value="none">None</option><option value="text">Text Watermark</option><option value="logo">Company Logo</option></select>' +
        '          <input class="stbm-input" id="ed-wm-text" style="margin-top:6px" value="' + A.esc(ed.wmText) + '" placeholder="Watermark text">' +
        '          <div style="display:grid;grid-template-columns:1fr 70px;gap:6px;margin-top:6px">' +
        '            <select class="stbm-select" id="ed-wm-pos"><option value="bottom-right">Bottom Right</option><option value="bottom-left">Bottom Left</option><option value="top-right">Top Right</option><option value="top-left">Top Left</option><option value="center">Center</option></select>' +
        '            <input type="color" id="ed-wm-color" value="' + A.esc(ed.wmColor) + '" title="Watermark Text Color" style="width:100%;height:36px;border:1px solid #475569;border-radius:8px;background:#1e293b">' +
        '          </div>' +
        '          <div style="font-size:11px;margin-top:6px;color:#cbd5e1">Opacity (<span id="ed-wm-op-val">' + Math.round(ed.wmOpacity * 100) + '%</span>): <input type="range" id="ed-wm-op" min="0.1" max="1" step="0.05" value="' + ed.wmOpacity + '" style="width:100%"></div>' +
        '          <div style="font-size:11px;margin-top:4px;color:#cbd5e1">Scale (<span id="ed-wm-sc-val">' + ed.wmScale.toFixed(1) + 'x</span>): <input type="range" id="ed-wm-scale" min="0.5" max="2.0" step="0.1" value="' + ed.wmScale + '" style="width:100%"></div>' +
        '        </div>' +
        '        <div class="stbm-field"><label class="stbm-field-label" style="color:#cbd5e1">Format &amp; Quality</label>' +
        '          <select class="stbm-select" id="ed-fmt"><option value="image/jpeg">JPEG (.jpg)</option><option value="image/png">PNG (.png)</option><option value="image/webp">WebP (.webp)</option></select>' +
        '          <div style="font-size:11px;margin-top:6px;color:#cbd5e1">Quality (<span id="ed-qual-val">' + Math.round(ed.quality * 100) + '%</span>): <input type="range" id="ed-qual" min="0.2" max="1" step="0.05" value="' + ed.quality + '" style="width:100%"></div>' +
        '        </div>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '  <div class="stbm-modal-foot">' +
        '    <button type="button" class="stbm-btn stbm-btn-secondary" id="ed-cancel">Cancel</button>' +
        '    <button type="button" class="stbm-btn stbm-btn-secondary" id="ed-download">Download Local</button>' +
        (asset.id ? '<button type="button" class="stbm-btn stbm-btn-secondary" id="ed-save-over">Overwrite Original</button>' : '') +
        '    <button type="button" class="stbm-btn stbm-btn-primary" id="ed-save-copy">Save as New WP Media Copy</button>' +
        '  </div>' +
        '</div></div>';

      document.getElementById('ed-wm-type').value = ed.wmType;
      document.getElementById('ed-wm-pos').value = ed.wmPos;
      document.getElementById('ed-fmt').value = ed.format;

      document.getElementById('ed-close').onclick = closeEditorModal;
      document.getElementById('ed-cancel').onclick = closeEditorModal;
      document.getElementById('ed-backdrop').addEventListener('click', function (ev) {
        if (ev.target === ev.currentTarget) closeEditorModal();
      });

      document.getElementById('ed-name').oninput = function (e) { ed.name = e.target.value; };
      document.getElementById('ed-cat').onchange = function (e) { ed.category = e.target.value; };
      document.getElementById('ed-lock').onclick = function () {
        pushHistory();
        ed.locked = !ed.locked;
        if (ed.locked && !ed.aspect) {
          const rot90 = (Math.abs(ed.rotation) % 180) === 90;
          const imgW = (rot90 ? sourceImg.naturalHeight : sourceImg.naturalWidth) || 800;
          const imgH = (rot90 ? sourceImg.naturalWidth : sourceImg.naturalHeight) || 600;
          ed.aspect = (ed.crop.w * imgW) / (ed.crop.h * imgH);
        }
        renderModal();
      };

      root.querySelectorAll('[data-ed-ratio]').forEach(function (b) {
        b.onclick = function () {
          pushHistory();
          const r = RATIOS[Number(b.getAttribute('data-ed-ratio'))];
          ed.aspect = r.value;
          ed.locked = Boolean(r.value);
          if (ed.aspect && sourceImg.naturalWidth) {
            const rot90 = (Math.abs(ed.rotation) % 180) === 90;
            const imgW = rot90 ? sourceImg.naturalHeight : sourceImg.naturalWidth;
            const imgH = rot90 ? sourceImg.naturalWidth : sourceImg.naturalHeight;
            const imgAspect = imgW / imgH;
            let nw = 0.85, nh = (nw * imgAspect) / ed.aspect;
            if (nh > 0.85) { nh = 0.85; nw = (nh * ed.aspect) / imgAspect; }
            ed.crop = { x: (1 - nw) / 2, y: (1 - nh) / 2, w: nw, h: nh };
          }
          renderModal();
        };
      });

      document.getElementById('ed-rot-l').onclick = function () { pushHistory(); ed.rotation = (ed.rotation - 90) % 360; renderModal(); };
      document.getElementById('ed-rot-r').onclick = function () { pushHistory(); ed.rotation = (ed.rotation + 90) % 360; renderModal(); };
      document.getElementById('ed-flip-h').onclick = function () { pushHistory(); ed.flipH = !ed.flipH; renderModal(); };
      document.getElementById('ed-flip-v').onclick = function () { pushHistory(); ed.flipV = !ed.flipV; renderModal(); };

      document.getElementById('ed-wm-type').onchange = function (e) { pushHistory(); ed.wmType = e.target.value; drawPreviewCanvas(); };
      document.getElementById('ed-wm-text').oninput = function (e) { ed.wmText = e.target.value; drawPreviewCanvas(); };
      document.getElementById('ed-wm-pos').onchange = function (e) { pushHistory(); ed.wmPos = e.target.value; drawPreviewCanvas(); };
      document.getElementById('ed-wm-color').oninput = function (e) { ed.wmColor = e.target.value; drawPreviewCanvas(); };
      document.getElementById('ed-wm-op').oninput = function (e) {
        ed.wmOpacity = Number(e.target.value);
        const el = document.getElementById('ed-wm-op-val');
        if (el) el.textContent = Math.round(ed.wmOpacity * 100) + '%';
        drawPreviewCanvas();
      };
      document.getElementById('ed-wm-scale').oninput = function (e) {
        ed.wmScale = Number(e.target.value);
        const el = document.getElementById('ed-wm-sc-val');
        if (el) el.textContent = ed.wmScale.toFixed(1) + 'x';
        drawPreviewCanvas();
      };
      document.getElementById('ed-fmt').onchange = function (e) { ed.format = e.target.value; drawPreviewCanvas(); };
      document.getElementById('ed-qual').oninput = function (e) {
        ed.quality = Number(e.target.value);
        const el = document.getElementById('ed-qual-val');
        if (el) el.textContent = Math.round(ed.quality * 100) + '%';
        drawPreviewCanvas();
      };

      const cmp = document.getElementById('ed-compare');
      cmp.onmousedown = function () { ed.comparingOriginal = true; drawPreviewCanvas(); };
      cmp.onmouseup = function () { ed.comparingOriginal = false; drawPreviewCanvas(); };
      cmp.onmouseleave = function () { ed.comparingOriginal = false; drawPreviewCanvas(); };
      cmp.ontouchstart = function () { ed.comparingOriginal = true; drawPreviewCanvas(); };
      cmp.ontouchend = function () { ed.comparingOriginal = false; drawPreviewCanvas(); };

      document.getElementById('ed-reset').onclick = function () {
        pushHistory();
        ed.rotation = 0;
        ed.flipH = false;
        ed.flipV = false;
        ed.crop = { x: 0.05, y: 0.05, w: 0.9, h: 0.9 };
        ed.aspect = null;
        ed.locked = false;
        ed.wmType = 'none';
        renderModal();
      };

      document.getElementById('ed-undo').onclick = function () {
        if (!ed.history.length) return;
        ed.redoStack.push(snapshot());
        Object.assign(ed, JSON.parse(ed.history.pop()));
        renderModal();
      };
      document.getElementById('ed-redo').onclick = function () {
        if (!ed.redoStack.length) return;
        ed.history.push(snapshot());
        Object.assign(ed, JSON.parse(ed.redoStack.pop()));
        renderModal();
      };

      // Interactive 8-Handle + Move Crop Drag (Mouse + Touch)
      const cvs = document.getElementById('stbm-ed-canvas');
      let dragging = false, dragMode = 'move', startPt = { x: 0, y: 0 }, startCrop = null, startSnap = null;

      function getClientXY(ev) {
        if (ev.touches && ev.touches.length) return { x: ev.touches[0].clientX, y: ev.touches[0].clientY };
        if (ev.changedTouches && ev.changedTouches.length) return { x: ev.changedTouches[0].clientX, y: ev.changedTouches[0].clientY };
        return { x: ev.clientX, y: ev.clientY };
      }

      function onDragStart(ev) {
        const pt = getClientXY(ev);
        const rect = cvs.getBoundingClientRect();
        if (!rect.width || !rect.height) return;
        const nx = (pt.x - rect.left) / rect.width;
        const ny = (pt.y - rect.top) / rect.height;
        const hit = hitTestCropHandle(nx, ny, ed.crop);
        if (!hit) return;
        if (ev.cancelable) ev.preventDefault();
        dragMode = hit;
        startSnap = snapshot();
        dragging = true;
        startPt = { x: nx, y: ny };
        startCrop = Object.assign({}, ed.crop);
      }

      function onDragMove(ev) {
        const pt = getClientXY(ev);
        const rect = cvs.getBoundingClientRect();
        if (!rect.width || !rect.height) return;
        const nx = (pt.x - rect.left) / rect.width;
        const ny = (pt.y - rect.top) / rect.height;

        if (!dragging) {
          const hover = hitTestCropHandle(nx, ny, ed.crop);
          const cursorMap = {
            nw: 'nwse-resize', se: 'nwse-resize',
            ne: 'nesw-resize', sw: 'nesw-resize',
            n: 'ns-resize', s: 'ns-resize',
            e: 'ew-resize', w: 'ew-resize',
            move: 'move',
          };
          cvs.style.cursor = cursorMap[hover] || 'crosshair';
          return;
        }

        if (ev.cancelable) ev.preventDefault();
        const dx = nx - startPt.x;
        const dy = ny - startPt.y;
        const rot90 = (Math.abs(ed.rotation) % 180) === 90;
        const imgW = (rot90 ? sourceImg.naturalHeight : sourceImg.naturalWidth) || 800;
        const imgH = (rot90 ? sourceImg.naturalWidth : sourceImg.naturalHeight) || 600;
        ed.crop = computeNextCrop(startCrop, dragMode, dx, dy, ed.locked ? ed.aspect : null, imgW / imgH);
        drawPreviewCanvas();
      }

      function onDragEnd() {
        if (!dragging) return;
        dragging = false;
        if (startSnap) {
          pushHistory(startSnap);
          const undoBtn = document.getElementById('ed-undo');
          const redoBtn = document.getElementById('ed-redo');
          if (undoBtn) undoBtn.disabled = false;
          if (redoBtn) redoBtn.disabled = true;
        }
        startSnap = null;
      }

      function onEscClose(ev) {
        if (ev.key === 'Escape') closeEditorModal();
      }

      cvs.addEventListener('mousedown', onDragStart);
      cvs.addEventListener('touchstart', onDragStart, { passive: false });
      window.addEventListener('mousemove', onDragMove);
      window.addEventListener('touchmove', onDragMove, { passive: false });
      window.addEventListener('mouseup', onDragEnd);
      window.addEventListener('touchend', onDragEnd);
      document.addEventListener('keydown', onEscClose);

      cleanupGlobal = function () {
        window.removeEventListener('mousemove', onDragMove);
        window.removeEventListener('touchmove', onDragMove);
        window.removeEventListener('mouseup', onDragEnd);
        window.removeEventListener('touchend', onDragEnd);
        document.removeEventListener('keydown', onEscClose);
      };

      document.getElementById('ed-download').onclick = function () {
        const exported = exportCroppedDataUrl(true);
        const ext = ed.format === 'image/png' ? 'png' : (ed.format === 'image/webp' ? 'webp' : 'jpg');
        const a = document.createElement('a');
        a.href = exported.dataUrl;
        a.download = (ed.name.trim() || 'edited-image').replace(/[^\w-]+/g, '-').toLowerCase() + '.' + ext;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
      };

      function saveAsset(overwrite) {
        const exported = exportCroppedDataUrl(true);
        const payload = {
          id: overwrite ? asset.id : undefined,
          name: ed.name.trim() || 'Edited Studio Asset',
          dataUrl: exported.dataUrl,
          width: exported.width,
          height: exported.height,
          format: ed.format,
          category: ed.category,
          isEdited: true,
          originalId: asset.id || '',
          originalName: asset.name || '',
        };
        A.ajax('stbm_save_studio_image', payload).then(function () {
          closeEditorModal();
          A.toast('Image saved to WordPress Media Library & Image Studio!', 'success');
          renderImageStudio(vp);
        }).catch(function (err) { A.toast(err.message, 'error'); });
      }

      document.getElementById('ed-save-copy').onclick = function () { saveAsset(false); };
      if (document.getElementById('ed-save-over')) {
        document.getElementById('ed-save-over').onclick = function () { saveAsset(true); };
      }
      drawPreviewCanvas();
    }

    renderModal();
    sourceImg.onload = function () { drawPreviewCanvas(); };
    sourceImg.src = asset.dataUrl;
  }

  window.STBM_MODULES.renderImageStudio = renderImageStudio;
})();
