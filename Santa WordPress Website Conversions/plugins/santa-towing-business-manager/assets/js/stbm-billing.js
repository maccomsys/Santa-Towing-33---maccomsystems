/**
 * Santa Towing Business Manager — Billing, CRM, Invoices, Receipts & Document Settings Modules
 * Implements Customers CRM, Pro Forma Invoice Split Customizer, Official Receipts, Doc Settings, & A4 Print/PDF Preview.
 */
(function () {
  'use strict';

  window.STBM_MODULES = window.STBM_MODULES || {};

  function app() {
    return window.STBM;
  }

  const SERVICE_PRESETS = [
    { label: 'Emergency Flatbed Towing (Greater Accra)', unitPrice: 850 },
    { label: 'Heavy Duty 30-Ton Low-Bed Machinery Haulage', unitPrice: 4500 },
    { label: 'Inter-City Highway Recovery (Accra - Kumasi)', unitPrice: 6800 },
    { label: 'Accident Scene Winching & Crane Assistance', unitPrice: 1200 },
    { label: 'Computerized Vehicle Diagnostic Scan', unitPrice: 350 },
    { label: 'Roadside Jump-Start & Tyre Assistance', unitPrice: 250 },
  ];

  const DEFAULT_SECTION_ORDER = ['billTo', 'items', 'totals', 'instructions', 'terms', 'notes'];

  const DEFAULT_SECTION_LABELS = {
    billTo: 'BILL TO / CLIENT DETAILS',
    items: 'SERVICE / ITEM DESCRIPTION',
    totals: 'TOTALS SUMMARY',
    instructions: 'PAYMENT & REMITTANCE INSTRUCTIONS',
    terms: 'TERMS & CONDITIONS',
    notes: 'NOTES',
  };

  const HIDDEN_ALIASES = {
    billTo: ['billTo', 'customer'],
    customer: ['billTo', 'customer'],
    company: ['company', 'customerCompany'],
    customerCompany: ['company', 'customerCompany'],
    email: ['email', 'customerEmail'],
    customerEmail: ['email', 'customerEmail'],
    address: ['address', 'customerAddress'],
    customerAddress: ['address', 'customerAddress'],
    items: ['items'],
    totals: ['totals', 'calculations'],
    calculations: ['totals', 'calculations'],
    instructions: ['instructions', 'payment', 'paymentSection', 'paymentInstructions'],
    payment: ['instructions', 'payment', 'paymentSection', 'paymentInstructions'],
    paymentSection: ['instructions', 'payment', 'paymentSection', 'paymentInstructions'],
    paymentInstructions: ['instructions', 'payment', 'paymentSection', 'paymentInstructions'],
    terms: ['terms', 'termsSection', 'termsAndConditions'],
    termsSection: ['terms', 'termsSection', 'termsAndConditions'],
    termsAndConditions: ['terms', 'termsSection', 'termsAndConditions'],
    notes: ['notes', 'notesSection'],
    notesSection: ['notes', 'notesSection'],
  };

  function normalizeSectionKey(key) {
    if (key === 'customer') return 'billTo';
    if (key === 'calculations') return 'totals';
    if (key === 'payment') return 'instructions';
    return key;
  }

  function isFieldVisible(hiddenFields, key) {
    const arr = Array.isArray(hiddenFields) ? hiddenFields : [];
    const aliases = HIDDEN_ALIASES[key] || [key];
    for (let i = 0; i < aliases.length; i++) {
      if (arr.indexOf(aliases[i]) !== -1) return false;
    }
    return true;
  }

  function toggleHiddenAlias(hiddenFields, key) {
    const arr = Array.isArray(hiddenFields) ? hiddenFields.slice() : [];
    const aliases = HIDDEN_ALIASES[key] || [key];
    const currentlyHidden = !isFieldVisible(arr, key);
    const cleaned = arr.filter(function (k) { return aliases.indexOf(k) === -1; });
    if (!currentlyHidden) {
      cleaned.push(aliases[0]);
    }
    return cleaned;
  }

  function bindModalDismiss(root, onClose) {
    const A = app();
    if (A && typeof A.bindModalDismiss === 'function') {
      return A.bindModalDismiss(onClose);
    }
    function dismiss() {
      root.innerHTML = '';
      if (typeof onClose === 'function') onClose();
    }
    return dismiss;
  }

  function calcInvoiceLocal(inv) {
    const round = function (n) { return Math.round((Number(n) || 0) * 100) / 100; };
    const items = (inv.items || []).map(function (it) {
      const q = Math.max(0, Number(it.quantity || 0));
      const p = Math.max(0, Number(it.unitPrice || 0));
      return { id: it.id, description: it.description || '', quantity: q, unitPrice: p, total: round(q * p) };
    });
    const subtotal = round(items.reduce(function (s, i) { return s + i.total; }, 0));
    const discEnabled = inv.discountEnabled !== false;
    const discVal = Math.max(0, Number(inv.discountValue || 0));
    let discAmt = 0;
    if (discEnabled && discVal > 0) {
      discAmt = inv.discountType === 'fixed' ? discVal : subtotal * (discVal / 100);
    }
    discAmt = round(Math.min(subtotal, discAmt));
    const afterDisc = Math.max(0, subtotal - discAmt);
    const taxEnabled = inv.taxEnabled !== false;
    const taxRate = Math.max(0, Math.min(100, Number(inv.taxRate || 0)));
    const taxAmt = taxEnabled ? round(afterDisc * (taxRate / 100)) : 0;
    const shipAmt = inv.shippingEnabled ? round(Math.max(0, Number(inv.shippingAmount || 0))) : 0;
    const extraSum = round((inv.additionalCharges || []).reduce(function (s, ch) {
      return ch.enabled !== false ? s + round(Math.max(0, Number(ch.amount || 0))) : s;
    }, 0));
    const grandTotal = round(afterDisc + taxAmt + shipAmt + extraSum);
    const amountPaid = round(Number(inv.amountPaid || 0));
    const balanceDue = round(Math.max(0, grandTotal - amountPaid));
    return { items, subtotal, discountAmount: discAmt, taxAmount: taxAmt, shippingAmount: shipAmt, additionalChargesTotal: extraSum, grandTotal, amountPaid, balanceDue };
  }

  function statusBadge(st) {
    const A = app();
    const cls = st === 'Accepted' || st === 'Issued' ? (st === 'Accepted' ? 'stbm-badge-green' : 'stbm-badge-blue') :
                st === 'Corrected' ? 'stbm-badge-amber' :
                st === 'Cancelled' ? 'stbm-badge-red' : 'stbm-badge-gray';
    return '<span class="stbm-badge ' + cls + '">' + A.esc(st) + '</span>';
  }

  function buildInvoiceBodyHtml(doc) {
    const A = app();
    const s = A.state.docSettings || {};
    const primary = s.primaryColor || '#015557';
    const calc = calcInvoiceLocal(doc);
    const hidden = doc.hiddenFields || [];
    const secTitles = doc.sectionTitles || {};
    const rawOrder = (doc.sectionOrder && doc.sectionOrder.length) ? doc.sectionOrder : DEFAULT_SECTION_ORDER;

    function titleFor(canon, fallback) {
      if (canon === 'billTo' && secTitles.customer && (!secTitles.billTo || secTitles.billTo === DEFAULT_SECTION_LABELS.billTo)) return secTitles.customer;
      if (canon === 'totals' && secTitles.calculations && (!secTitles.totals || secTitles.totals === DEFAULT_SECTION_LABELS.totals)) return secTitles.calculations;
      if (canon === 'instructions' && secTitles.payment && (!secTitles.instructions || secTitles.instructions === DEFAULT_SECTION_LABELS.instructions)) return secTitles.payment;
      return secTitles[canon] || fallback;
    }

    const customTotalsTitle = titleFor('totals', '');
    const showTotalsHeading = customTotalsTitle && customTotalsTitle !== DEFAULT_SECTION_LABELS.totals;

    const parts = {
      billTo: !isFieldVisible(hidden, 'billTo') ? '' :
        '<div style="display:grid;grid-template-columns:1.25fr 0.75fr;gap:16px;margin:14px 0">' +
        '  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px">' +
        '    <div style="font-size:10px;font-weight:800;color:' + A.esc(primary) + ';letter-spacing:0.06em;margin-bottom:4px">' + A.esc(titleFor('billTo', DEFAULT_SECTION_LABELS.billTo)) + '</div>' +
        '    <div style="font-size:14px;font-weight:800">' + A.esc(doc.customerName || 'Client Name') + '</div>' +
        (doc.customerCompany && isFieldVisible(hidden, 'company') ? '<div style="font-size:12px">' + A.esc(doc.customerCompany) + '</div>' : '') +
        '<div style="font-size:11.5px;color:#475569">' + A.esc(doc.customerPhone) + (doc.customerEmail && isFieldVisible(hidden, 'email') ? ' · ' + A.esc(doc.customerEmail) : '') + '</div>' +
        (doc.customerAddress && isFieldVisible(hidden, 'address') ? '<div style="font-size:11.5px;color:#64748b">' + A.esc(doc.customerAddress) + '</div>' : '') +
        '  </div>' +
        '  <div style="background:' + A.esc(primary) + ';color:#fff;border-radius:10px;padding:12px">' +
        '    <div style="font-size:10px;font-weight:700;opacity:0.88">ESTIMATED TOTAL DUE</div>' +
        '    <div style="font-size:20px;font-weight:900;margin:4px 0">' + A.esc(A.money(calc.grandTotal)) + '</div>' +
        '    <div style="font-size:11px;opacity:0.92">Paid: ' + A.esc(A.money(calc.amountPaid)) + '</div>' +
        '    <div style="font-size:11px;font-weight:700">Balance Due: ' + A.esc(A.money(calc.balanceDue)) + '</div>' +
        '  </div>' +
        '</div>',
      items: !isFieldVisible(hidden, 'items') ? '' :
        '<table style="width:100%;border-collapse:collapse;margin:12px 0;font-size:11.5px">' +
        '  <thead><tr style="background:' + A.esc(primary) + ';color:#fff">' +
        '    <th style="padding:7px 9px;text-align:left">#</th><th style="padding:7px 9px;text-align:left">' + A.esc(titleFor('items', DEFAULT_SECTION_LABELS.items)) + '</th>' +
        '    <th style="padding:7px 9px;text-align:right">Qty</th><th style="padding:7px 9px;text-align:right">Unit Price</th><th style="padding:7px 9px;text-align:right">Total</th>' +
        '  </tr></thead><tbody>' +
        calc.items.map(function (it, idx) {
          return '<tr style="border-bottom:1px solid #e2e8f0;background:' + (idx % 2 === 1 ? '#f8fafc' : '#fff') + '">' +
            '<td style="padding:7px 9px">' + (idx + 1) + '</td><td style="padding:7px 9px">' + A.esc(it.description || '-') + '</td>' +
            '<td style="padding:7px 9px;text-align:right">' + it.quantity + '</td><td style="padding:7px 9px;text-align:right">' + A.esc(A.money(it.unitPrice)) + '</td>' +
            '<td style="padding:7px 9px;text-align:right;font-weight:700">' + A.esc(A.money(it.total)) + '</td></tr>';
        }).join('') +
        '  </tbody></table>',
      totals: !isFieldVisible(hidden, 'totals') ? '' :
        '<div style="display:flex;justify-content:flex-end;margin:10px 0">' +
        '  <div style="width:270px;font-size:12px">' +
        (showTotalsHeading ? '<div style="font-size:10px;font-weight:800;color:' + A.esc(primary) + ';margin-bottom:4px;text-align:right">' + A.esc(customTotalsTitle) + '</div>' : '') +
        '    <div style="display:flex;justify-content:space-between;padding:3px 0"><span>Subtotal</span><strong>' + A.esc(A.money(calc.subtotal)) + '</strong></div>' +
        (calc.discountAmount > 0 ? '<div style="display:flex;justify-content:space-between;padding:3px 0;color:#dc2626"><span>' + A.esc(doc.discountLabel || 'Discount') + '</span><span>-' + A.esc(A.money(calc.discountAmount)) + '</span></div>' : '') +
        (calc.taxAmount > 0 ? '<div style="display:flex;justify-content:space-between;padding:3px 0"><span>' + A.esc(doc.taxLabel || 'Tax / VAT') + ' (' + (doc.taxRate || 0) + '%)</span><span>' + A.esc(A.money(calc.taxAmount)) + '</span></div>' : '') +
        (calc.shippingAmount > 0 ? '<div style="display:flex;justify-content:space-between;padding:3px 0"><span>' + A.esc(doc.shippingLabel || 'Dispatch / Logistics Fee') + '</span><span>' + A.esc(A.money(calc.shippingAmount)) + '</span></div>' : '') +
        (doc.additionalCharges || []).filter(function (c) { return c.enabled !== false && Number(c.amount) > 0; }).map(function (c) {
          return '<div style="display:flex;justify-content:space-between;padding:3px 0"><span>' + A.esc(c.label || 'Additional Charge') + '</span><span>' + A.esc(A.money(c.amount)) + '</span></div>';
        }).join('') +
        '    <div style="display:flex;justify-content:space-between;padding:7px 10px;margin-top:6px;background:' + A.esc(primary) + ';color:#fff;border-radius:8px;font-weight:800"><span>GRAND TOTAL</span><span>' + A.esc(A.money(calc.grandTotal)) + '</span></div>' +
        (calc.amountPaid > 0
          ? '<div style="display:flex;justify-content:space-between;padding:5px 2px 2px;color:#059669;font-weight:700"><span>Amount Paid</span><span>-' + A.esc(A.money(calc.amountPaid)) + '</span></div>' +
            '<div style="display:flex;justify-content:space-between;padding:2px;color:' + A.esc(primary) + ';font-weight:800"><span>Balance Due</span><span>' + A.esc(A.money(calc.balanceDue)) + '</span></div>'
          : '') +
        '  </div>' +
        '</div>',
      instructions: (doc.paymentInstructions && isFieldVisible(hidden, 'instructions'))
        ? '<div style="margin-top:10px"><div style="font-size:10.5px;font-weight:800;color:' + A.esc(primary) + '">' + A.esc(titleFor('instructions', DEFAULT_SECTION_LABELS.instructions)) + '</div><div style="font-size:11px;color:#475569;white-space:pre-line">' + A.esc(doc.paymentInstructions) + '</div></div>'
        : '',
      terms: (doc.termsAndConditions && isFieldVisible(hidden, 'terms'))
        ? '<div style="margin-top:10px"><div style="font-size:10.5px;font-weight:800;color:' + A.esc(primary) + '">' + A.esc(titleFor('terms', DEFAULT_SECTION_LABELS.terms)) + '</div><div style="font-size:10.5px;color:#475569;white-space:pre-line">' + A.esc(doc.termsAndConditions) + '</div></div>'
        : '',
      notes: (doc.notes && isFieldVisible(hidden, 'notes'))
        ? '<div style="margin-top:10px"><div style="font-size:10.5px;font-weight:800;color:' + A.esc(primary) + '">' + A.esc(titleFor('notes', DEFAULT_SECTION_LABELS.notes)) + '</div><div style="font-size:11px;color:#475569;white-space:pre-line">' + A.esc(doc.notes) + '</div></div>'
        : '',
    };

    const customFieldsHtml = (doc.customFields || []).filter(function (f) { return f.visible !== false && f.value; }).map(function (f) {
      return '<span style="background:#f1f5f9;padding:4px 10px;border-radius:6px;font-size:11px"><strong>' + A.esc(f.label) + ':</strong> ' + A.esc(f.value) + '</span>';
    }).join(' ');
    const customSecsHtml = (doc.customSections || []).filter(function (cs) { return cs.visible !== false && cs.content; }).map(function (cs) {
      return '<div style="margin-top:10px"><div style="font-size:10.5px;font-weight:800;color:' + A.esc(primary) + '">' + A.esc(cs.title) + '</div><div style="font-size:11px;color:#475569;white-space:pre-line">' + A.esc(cs.content) + '</div></div>';
    }).join('');

    const seen = {};
    const orderedHtml = rawOrder.map(function (k) {
      const canon = normalizeSectionKey(k);
      if (seen[canon]) return '';
      seen[canon] = true;
      return parts[canon] || '';
    }).join('');

    return orderedHtml +
      (customFieldsHtml ? '<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px">' + customFieldsHtml + '</div>' : '') +
      customSecsHtml;
  }

  function renderInvoicePreviewHtml(doc) {
    const A = app();
    const s = A.state.docSettings || {};
    const primary = s.primaryColor || '#015557';
    const phones = (s.phone || '') + (s.phoneSecondary ? ' / ' + s.phoneSecondary : '');
    return '<div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid ' + A.esc(primary) + ';padding-bottom:12px">' +
      '  <div>' +
      (s.logoUrl ? '<img src="' + A.esc(s.logoUrl) + '" style="height:40px;object-fit:contain;margin-bottom:6px">' : '') +
      '    <div style="font-size:14.5px;font-weight:900;color:' + A.esc(primary) + '">' + A.esc(s.companyName) + '</div>' +
      '    <div style="font-size:10.5px;color:#64748b">' + A.esc(s.address) + '</div>' +
      '    <div style="font-size:10.5px;color:#475569">Tel: ' + A.esc(phones) + (s.email ? ' · ' + A.esc(s.email) : '') + (s.taxRegistrationNumber ? ' · ' + A.esc(s.taxRegistrationNumber) : '') + '</div>' +
      '  </div>' +
      '  <div style="text-align:right">' +
      '    <div style="font-size:17px;font-weight:900;color:' + A.esc(primary) + '">' + A.esc((doc.docTitle || 'PRO FORMA INVOICE').toUpperCase()) + '</div>' +
      '    <div style="font-size:12px;font-weight:800">' + A.esc(doc.docNumber || 'AUTO-NUMBER') + '</div>' +
      '    <div style="font-size:10.5px;color:#64748b">Date: ' + A.esc(doc.issueDate) + '</div>' +
      '    <div style="font-size:10.5px;color:#64748b">Valid Until: ' + A.esc(doc.validUntil) + '</div>' +
      '    <div style="margin-top:4px">' + statusBadge(doc.status || 'Draft') + '</div>' +
      '  </div>' +
      '</div>' +
      buildInvoiceBodyHtml(doc) +
      '<div style="display:flex;justify-content:space-between;margin-top:26px;padding-top:14px">' +
      '  <div style="border-top:1px solid #94a3b8;width:190px;padding-top:4px;font-size:10px;color:#64748b">Authorized Management Signature</div>' +
      '  <div style="border-top:1px solid #94a3b8;width:190px;padding-top:4px;font-size:10px;color:#64748b">Client Acceptance / Official Stamp</div>' +
      '</div>' +
      '<div style="text-align:center;margin-top:16px;padding-top:8px;border-top:1px solid #e2e8f0;font-size:10px;color:#64748b">' + A.esc(s.footerText) + '</div>';
  }

  /* =====================================================================
   * A4 DOCUMENT PREVIEW & PRINT / PURE-PHP PDF MODAL
   * ===================================================================== */
  function openDocumentPreviewModal(type, doc) {
    const A = app();
    const s = A.state.docSettings || {};
    const root = document.getElementById('stbm-modal-root');
    const primary = s.primaryColor || '#015557';
    const pdfHref = A.exportUrl('stbm_download_pdf', { type: type, uid: doc.id });
    const phones = (s.phone || '') + (s.phoneSecondary ? ' / ' + s.phoneSecondary : '');

    let innerHtml = '';
    if (type === 'invoice') {
      innerHtml = renderInvoicePreviewHtml(doc);
    } else {
      const bodyHtml =
        '<div style="display:grid;grid-template-columns:1.25fr 0.75fr;gap:16px;margin:16px 0">' +
        '  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px">' +
        '    <div style="font-size:10px;font-weight:800;color:' + A.esc(primary) + ';margin-bottom:4px">RECEIVED WITH THANKS FROM</div>' +
        '    <div style="font-size:15px;font-weight:800">' + A.esc(doc.customerName) + '</div>' +
        '    <div style="font-size:11.5px;color:#475569">Phone: ' + A.esc(doc.customerPhone) + '</div>' +
        (doc.customerEmail ? '<div style="font-size:11.5px;color:#64748b">' + A.esc(doc.customerEmail) + '</div>' : '') +
        (doc.customerAddress ? '<div style="font-size:11.5px;color:#64748b">' + A.esc(doc.customerAddress) + '</div>' : '') +
        '  </div>' +
        '  <div style="background:' + A.esc(primary) + ';color:#fff;border-radius:10px;padding:14px">' +
        '    <div style="font-size:10px;font-weight:700;opacity:0.88">AMOUNT RECEIVED</div>' +
        '    <div style="font-size:21px;font-weight:900;margin:4px 0">' + A.esc(A.money(doc.amountReceived)) + '</div>' +
        '    <div style="font-size:11px">Method: ' + A.esc(doc.paymentMethod) + '</div>' +
        '  </div>' +
        '</div>' +
        '<table style="width:100%;border-collapse:collapse;margin:14px 0;font-size:12px">' +
        '  <tr><td style="padding:9px;border-bottom:1px solid #e2e8f0;color:#64748b">Payment Method</td><td style="padding:9px;border-bottom:1px solid #e2e8f0;text-align:right;font-weight:700">' + A.esc(doc.paymentMethod) + '</td></tr>' +
        '  <tr><td style="padding:9px;border-bottom:1px solid #e2e8f0;color:#64748b">Reference / Transaction ID</td><td style="padding:9px;border-bottom:1px solid #e2e8f0;text-align:right;font-weight:700">' + A.esc(doc.paymentReference || '-') + '</td></tr>' +
        '  <tr><td style="padding:9px;border-bottom:1px solid #e2e8f0;color:#64748b">Linked Pro Forma Invoice</td><td style="padding:9px;border-bottom:1px solid #e2e8f0;text-align:right;font-weight:700">' + A.esc(doc.invoiceNumber || 'Direct Payment') + '</td></tr>' +
        '  <tr><td style="padding:9px;border-bottom:1px solid #e2e8f0;color:#64748b">Remaining Invoice Balance</td><td style="padding:9px;border-bottom:1px solid #e2e8f0;text-align:right;font-weight:700">' + A.esc(A.money(doc.remainingBalance || 0)) + '</td></tr>' +
        '</table>' +
        (doc.notes ? '<div style="margin-top:10px;font-size:12px;color:#475569"><strong>Remarks:</strong> ' + A.esc(doc.notes) + '</div>' : '');

      innerHtml =
        '<div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid ' + A.esc(primary) + ';padding-bottom:14px">' +
        '  <div>' +
        (s.logoUrl ? '<img src="' + A.esc(s.logoUrl) + '" style="height:44px;object-fit:contain;margin-bottom:6px">' : '') +
        '    <div style="font-size:15px;font-weight:900;color:' + A.esc(primary) + '">' + A.esc(s.companyName) + '</div>' +
        '    <div style="font-size:11px;color:#64748b">' + A.esc(s.address) + '</div>' +
        '    <div style="font-size:11px;color:#475569">Tel: ' + A.esc(phones) + ' · ' + A.esc(s.email) + ' · ' + A.esc(s.taxRegistrationNumber) + '</div>' +
        '  </div>' +
        '  <div style="text-align:right">' +
        '    <div style="font-size:18px;font-weight:900;color:' + A.esc(primary) + '">OFFICIAL RECEIPT</div>' +
        '    <div style="font-size:12.5px;font-weight:800">' + A.esc(doc.receiptNumber) + '</div>' +
        '    <div style="font-size:11px;color:#64748b">Date: ' + A.esc(doc.receiptDate) + '</div>' +
        '    <div style="margin-top:4px">' + statusBadge(doc.status) + '</div>' +
        '  </div>' +
        '</div>' +
        bodyHtml +
        '<div style="display:flex;justify-content:space-between;margin-top:34px;padding-top:16px">' +
        '  <div style="border-top:1px solid #94a3b8;width:210px;padding-top:4px;font-size:10.5px;color:#64748b">Authorized Management Signature</div>' +
        '  <div style="border-top:1px solid #94a3b8;width:210px;padding-top:4px;font-size:10.5px;color:#64748b">Client Acceptance / Official Stamp</div>' +
        '</div>' +
        '<div style="text-align:center;margin-top:20px;padding-top:10px;border-top:1px solid #e2e8f0;font-size:10.5px;color:#64748b">' + A.esc(s.footerText) + '</div>';
    }

    root.innerHTML =
      '<div class="stbm-modal-backdrop">' +
      '  <div class="stbm-modal" style="max-width:860px">' +
      '    <div class="stbm-modal-head">' +
      '      <h3>' + (type === 'receipt' ? 'Official Receipt ' + A.esc(doc.receiptNumber) : 'Pro Forma Invoice ' + A.esc(doc.docNumber)) + '</h3>' +
      '      <div style="display:flex;gap:8px">' +
      '        <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="stbm-doc-print">Print A4</button>' +
      '        <a href="' + A.esc(pdfHref) + '" class="stbm-btn stbm-btn-primary stbm-btn-sm">Download PDF</a>' +
      '        <button type="button" class="stbm-btn-icon" id="stbm-doc-close">&times;</button>' +
      '      </div>' +
      '    </div>' +
      '    <div class="stbm-modal-body" style="background:#f1f5f9">' +
      '      <div id="printable-document-root" class="stbm-a4-preview">' +
      '        <div class="stbm-a4-accent-bar" style="background:' + A.esc(primary) + '"></div>' +
      '        <div class="stbm-a4-inner">' + innerHtml + '</div>' +
      '      </div>' +
      '    </div>' +
      '  </div>' +
      '</div>';

    const dismiss = bindModalDismiss(root);
    document.getElementById('stbm-doc-close').onclick = dismiss;
    document.getElementById('stbm-doc-print').onclick = function () { window.print(); };
  }

  /* =====================================================================
   * MODULE 2: CUSTOMERS CRM (`AdminCustomers`)
   * ===================================================================== */
  let custSearch = '';
  function renderCustomers(vp) {
    const A = app();
    const list = A.state.customers.filter(function (c) {
      if (!custSearch) return true;
      const t = (c.name + ' ' + c.company + ' ' + c.phone + ' ' + c.email).toLowerCase();
      return t.indexOf(custSearch.toLowerCase()) !== -1;
    });

    const rowsHtml = list.map(function (c) {
      const invs = A.state.invoices.filter(function (i) { return i.customerId === c.id; });
      const recs = A.state.receipts.filter(function (r) { return r.customerId === c.id && r.status !== 'Cancelled'; });
      const paid = recs.reduce(function (s, r) { return s + Number(r.amountReceived || 0); }, 0);
      return '<tr>' +
        '<td><strong>' + A.esc(c.name) + '</strong><div style="font-size:11.5px;color:#64748b">' + A.esc(c.company || 'Individual Client') + '</div></td>' +
        '<td><div>' + A.esc(c.phone) + '</div><div style="font-size:11.5px;color:#64748b">' + A.esc(c.email || '-') + '</div></td>' +
        '<td><span class="stbm-badge stbm-badge-blue">' + invs.length + ' Invoices</span> <span class="stbm-badge stbm-badge-green">' + A.esc(A.money(paid)) + ' Paid</span></td>' +
        '<td style="text-align:right;white-space:nowrap">' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-cust-hist="' + A.esc(c.id) + '">History</button> ' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-cust-edit="' + A.esc(c.id) + '">Edit</button> ' +
        '  <button type="button" class="stbm-btn-icon danger" data-cust-del="' + A.esc(c.id) + '">&times;</button>' +
        '</td></tr>';
    }).join('') || '<tr><td colspan="4" style="text-align:center;padding:28px;color:#64748b">No customers found.</td></tr>';

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Customers &amp; Client Directory</h2><p>Manage towing, haulage, rental, and fleet clients with full invoice/receipt history.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <a class="stbm-btn stbm-btn-secondary" href="' + A.esc(A.exportUrl('stbm_export_csv', { dataset: 'customers' })) + '">Export CSV</a>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-cust-add">+ Add Customer</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-card"><div class="stbm-card-body">' +
      '  <div class="stbm-toolbar"><div class="stbm-search-box">' + A.svg('search') + '<input class="stbm-input" id="stbm-cust-search" placeholder="Search name, company, phone..." value="' + A.esc(custSearch) + '"></div></div>' +
      '  <div class="stbm-table-wrap"><table class="stbm-table"><thead><tr><th>Customer</th><th>Contact</th><th>Billing Summary</th><th style="text-align:right">Actions</th></tr></thead><tbody>' + rowsHtml + '</tbody></table></div>' +
      '</div></div>';

    document.getElementById('stbm-cust-search').oninput = function (e) {
      custSearch = e.target.value;
      renderCustomers(vp);
      const inp = document.getElementById('stbm-cust-search');
      if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
    };
    document.getElementById('stbm-cust-add').onclick = function () { openCustomerModal(null, vp); };
    vp.querySelectorAll('[data-cust-edit]').forEach(function (b) {
      b.onclick = function () {
        const c = A.state.customers.find(function (x) { return x.id === b.getAttribute('data-cust-edit'); });
        openCustomerModal(c, vp);
      };
    });
    vp.querySelectorAll('[data-cust-hist]').forEach(function (b) {
      b.onclick = function () {
        const c = A.state.customers.find(function (x) { return x.id === b.getAttribute('data-cust-hist'); });
        if (c) openCustomerHistoryModal(c);
      };
    });
    vp.querySelectorAll('[data-cust-del]').forEach(function (b) {
      b.onclick = function () {
        const id = b.getAttribute('data-cust-del');
        A.confirmDialog({
          title: 'Delete Customer Record?',
          description: 'Customers with linked invoices or receipts cannot be deleted for audit safety.',
          confirmLabel: 'Delete Customer',
          isDestructive: true,
          onConfirm: function () {
            A.ajax('stbm_delete_customer', { id: id }).then(function () {
              A.toast('Customer removed.', 'info');
              renderCustomers(vp);
            }).catch(function (err) { A.toast(err.message, 'error'); });
          },
        });
      };
    });
  }

  function openCustomerModal(existing, vp) {
    const A = app();
    const c = existing ? Object.assign({}, existing) : { name: '', company: '', phone: '', email: '', address: '', notes: '' };
    const root = document.getElementById('stbm-modal-root');
    root.innerHTML =
      '<div class="stbm-modal-backdrop"><div class="stbm-modal">' +
      '  <div class="stbm-modal-head"><h3>' + (existing ? 'Edit Customer' : 'Add New Customer') + '</h3><button type="button" class="stbm-btn-icon" id="cm-close">&times;</button></div>' +
      '  <div class="stbm-modal-body">' +
      '    <div class="stbm-grid stbm-grid-2">' +
      '      <div class="stbm-field"><label class="stbm-field-label">Full Name *</label><input class="stbm-input" id="cm-name" value="' + A.esc(c.name) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Company / Organization</label><input class="stbm-input" id="cm-comp" value="' + A.esc(c.company) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Phone Number *</label><input class="stbm-input" id="cm-phone" value="' + A.esc(c.phone) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Email</label><input class="stbm-input" id="cm-email" value="' + A.esc(c.email) + '"></div>' +
      '    </div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Address</label><input class="stbm-input" id="cm-addr" value="' + A.esc(c.address) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Internal Notes</label><textarea class="stbm-textarea" id="cm-notes">' + A.esc(c.notes) + '</textarea></div>' +
      '  </div>' +
      '  <div class="stbm-modal-foot"><button type="button" class="stbm-btn stbm-btn-secondary" id="cm-cancel">Cancel</button><button type="button" class="stbm-btn stbm-btn-primary" id="cm-save">Save Customer</button></div>' +
      '</div></div>';

    const dismiss = bindModalDismiss(root);
    document.getElementById('cm-close').onclick = dismiss;
    document.getElementById('cm-cancel').onclick = dismiss;
    document.getElementById('cm-save').onclick = function () {
      c.name = document.getElementById('cm-name').value.trim();
      c.company = document.getElementById('cm-comp').value.trim();
      c.phone = document.getElementById('cm-phone').value.trim();
      c.email = document.getElementById('cm-email').value.trim();
      c.address = document.getElementById('cm-addr').value.trim();
      c.notes = document.getElementById('cm-notes').value.trim();
      if (!c.name || !c.phone) { A.toast('Name and phone are required.', 'error'); return; }
      A.ajax('stbm_save_customer', c).then(function () {
        dismiss();
        A.toast('Customer saved!', 'success');
        renderCustomers(vp);
      }).catch(function (err) { A.toast(err.message, 'error'); });
    };
  }

  function openCustomerHistoryModal(c) {
    const A = app();
    const invs = A.state.invoices.filter(function (i) { return i.customerId === c.id; });
    const recs = A.state.receipts.filter(function (r) { return r.customerId === c.id; });
    const root = document.getElementById('stbm-modal-root');
    root.innerHTML =
      '<div class="stbm-modal-backdrop"><div class="stbm-modal" style="max-width:740px">' +
      '  <div class="stbm-modal-head"><h3>Billing History: ' + A.esc(c.name) + '</h3><button type="button" class="stbm-btn-icon" id="ch-close">&times;</button></div>' +
      '  <div class="stbm-modal-body">' +
      '    <h4 style="margin:0 0 8px">Pro Forma Invoices (' + invs.length + ')</h4>' +
      (invs.length ? '<table class="stbm-table" style="margin-bottom:18px"><thead><tr><th>Number</th><th>Date</th><th>Total</th><th>Status</th></tr></thead><tbody>' +
        invs.map(function (i) { return '<tr><td><strong>' + A.esc(i.docNumber) + '</strong></td><td>' + A.esc(i.issueDate) + '</td><td>' + A.esc(A.money(i.grandTotal)) + '</td><td>' + statusBadge(i.status) + '</td></tr>'; }).join('') +
        '</tbody></table>' : '<p style="color:#64748b">No invoices yet.</p>') +
      '    <h4 style="margin:14px 0 8px">Official Receipts (' + recs.length + ')</h4>' +
      (recs.length ? '<table class="stbm-table"><thead><tr><th>Receipt #</th><th>Date</th><th>Amount</th><th>Method</th></tr></thead><tbody>' +
        recs.map(function (r) { return '<tr><td><strong>' + A.esc(r.receiptNumber) + '</strong></td><td>' + A.esc(r.receiptDate) + '</td><td>' + A.esc(A.money(r.amountReceived)) + '</td><td>' + A.esc(r.paymentMethod) + '</td></tr>'; }).join('') +
        '</tbody></table>' : '<p style="color:#64748b">No receipts recorded.</p>') +
      '  </div>' +
      '  <div class="stbm-modal-foot"><button type="button" class="stbm-btn stbm-btn-secondary" id="ch-ok">Close</button></div>' +
      '</div></div>';
    const dismiss = bindModalDismiss(root);
    document.getElementById('ch-close').onclick = dismiss;
    document.getElementById('ch-ok').onclick = dismiss;
  }

  /* =====================================================================
   * MODULE 4: PRO FORMA INVOICES + CUSTOMIZABLE SPLIT MODAL
   * ===================================================================== */
  let invTab = 'All', invSearch = '';
  function renderInvoices(vp) {
    const A = app();
    const statuses = ['All', 'Draft', 'Issued', 'Accepted', 'Cancelled'];
    const filtered = A.state.invoices.filter(function (inv) {
      if (invTab !== 'All' && inv.status !== invTab) return false;
      if (invSearch) {
        const txt = (inv.docNumber + ' ' + inv.customerName + ' ' + inv.customerCompany + ' ' + inv.customerPhone).toLowerCase();
        if (txt.indexOf(invSearch.toLowerCase()) === -1) return false;
      }
      return true;
    });

    const rowsHtml = filtered.map(function (inv) {
      return '<tr>' +
        '<td><strong>' + A.esc(inv.docNumber) + '</strong><div style="font-size:11px;color:#64748b">Issued: ' + A.esc(inv.issueDate) + '</div></td>' +
        '<td><strong>' + A.esc(inv.customerName) + '</strong><div style="font-size:11.5px;color:#64748b">' + A.esc(inv.customerCompany || inv.customerPhone) + '</div></td>' +
        '<td><strong>' + A.esc(A.money(inv.grandTotal)) + '</strong><div style="font-size:11px;color:#64748b">Paid: ' + A.esc(A.money(inv.amountPaid)) + ' · Bal: ' + A.esc(A.money(inv.balanceDue)) + '</div></td>' +
        '<td><select class="stbm-select" style="padding:5px 8px;min-height:30px;font-size:12px;width:auto" data-inv-status="' + A.esc(inv.id) + '">' +
        ['Draft', 'Issued', 'Accepted', 'Cancelled'].map(function (st) { return '<option value="' + st + '" ' + (inv.status === st ? 'selected' : '') + '>' + st + '</option>'; }).join('') +
        '</select></td>' +
        '<td style="text-align:right;white-space:nowrap">' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-inv-prev="' + A.esc(inv.id) + '">View / PDF</button> ' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-inv-edit="' + A.esc(inv.id) + '">Customize</button> ' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-inv-dup="' + A.esc(inv.id) + '">Duplicate</button> ' +
        '  <button type="button" class="stbm-btn-icon danger" data-inv-del="' + A.esc(inv.id) + '">&times;</button>' +
        '</td></tr>';
    }).join('') || '<tr><td colspan="5" style="text-align:center;padding:28px;color:#64748b">No invoices match this filter.</td></tr>';

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Pro Forma Invoices</h2><p>Create customizable quotes, adjust taxes/charges/sections, duplicate drafts, and download A4 PDFs.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <a class="stbm-btn stbm-btn-secondary" href="' + A.esc(A.exportUrl('stbm_export_csv', { dataset: 'invoices' })) + '">Export CSV</a>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-inv-new">+ New Pro Forma Invoice</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-tabs">' +
      statuses.map(function (s) {
        const cnt = s === 'All' ? A.state.invoices.length : A.state.invoices.filter(function (i) { return i.status === s; }).length;
        return '<button type="button" class="stbm-tab ' + (invTab === s ? 'active' : '') + '" data-invs-tab="' + s + '">' + s + ' <span class="stbm-badge stbm-badge-gray">' + cnt + '</span></button>';
      }).join('') +
      '</div>' +
      '<div class="stbm-card"><div class="stbm-card-body">' +
      '  <div class="stbm-toolbar"><div class="stbm-search-box">' + A.svg('search') + '<input class="stbm-input" id="stbm-invs-search" placeholder="Search invoice #, client, company..." value="' + A.esc(invSearch) + '"></div></div>' +
      '  <div class="stbm-table-wrap"><table class="stbm-table"><thead><tr><th>Document #</th><th>Client</th><th>Financials</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead><tbody>' + rowsHtml + '</tbody></table></div>' +
      '</div></div>';

    vp.querySelectorAll('[data-invs-tab]').forEach(function (b) {
      b.onclick = function () { invTab = b.getAttribute('data-invs-tab'); renderInvoices(vp); };
    });
    document.getElementById('stbm-invs-search').oninput = function (e) {
      invSearch = e.target.value;
      renderInvoices(vp);
      const inp = document.getElementById('stbm-invs-search');
      if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
    };
    document.getElementById('stbm-inv-new').onclick = function () { openInvoiceEditorModal(null, vp); };
    vp.querySelectorAll('[data-inv-status]').forEach(function (sel) {
      sel.onchange = function () {
        A.ajax('stbm_update_invoice_status', { id: sel.getAttribute('data-inv-status'), status: sel.value }).then(function () {
          A.toast('Invoice status updated.', 'success');
          renderInvoices(vp);
        }).catch(function (err) { A.toast(err.message, 'error'); });
      };
    });
    vp.querySelectorAll('[data-inv-prev]').forEach(function (b) {
      b.onclick = function () {
        const inv = A.state.invoices.find(function (i) { return i.id === b.getAttribute('data-inv-prev'); });
        if (inv) openDocumentPreviewModal('invoice', inv);
      };
    });
    vp.querySelectorAll('[data-inv-edit]').forEach(function (b) {
      b.onclick = function () {
        const inv = A.state.invoices.find(function (i) { return i.id === b.getAttribute('data-inv-edit'); });
        if (inv) openInvoiceEditorModal(inv, vp);
      };
    });
    vp.querySelectorAll('[data-inv-dup]').forEach(function (b) {
      b.onclick = function () {
        A.ajax('stbm_duplicate_invoice', { id: b.getAttribute('data-inv-dup') }).then(function (data) {
          A.toast('Duplicated as ' + (data.invoice ? data.invoice.docNumber : 'Draft'), 'success');
          renderInvoices(vp);
        }).catch(function (err) { A.toast(err.message, 'error'); });
      };
    });
    vp.querySelectorAll('[data-inv-del]').forEach(function (b) {
      b.onclick = function () {
        const id = b.getAttribute('data-inv-del');
        A.confirmDialog({
          title: 'Delete Pro Forma Invoice?',
          description: 'Invoices linked to existing Official Receipts are protected from deletion.',
          confirmLabel: 'Delete Invoice',
          isDestructive: true,
          onConfirm: function () {
            A.ajax('stbm_delete_invoice', { id: id }).then(function () {
              A.toast('Invoice deleted.', 'info');
              renderInvoices(vp);
            }).catch(function (err) { A.toast(err.message, 'error'); });
          },
        });
      };
    });
  }

  function openInvoiceEditorModal(existing, vp) {
    const A = app();
    const s = A.state.docSettings || {};
    const today = new Date().toISOString().slice(0, 10);
    const validDt = new Date(Date.now() + Number(s.defaultValidityDays || 14) * 86400000).toISOString().slice(0, 10);

    const inv = existing ? JSON.parse(JSON.stringify(existing)) : {
      docNumber: '',
      docTitle: 'Pro Forma Invoice',
      issueDate: today,
      validUntil: validDt,
      customerId: '',
      customerName: '',
      customerCompany: '',
      customerPhone: '',
      customerEmail: '',
      customerAddress: '',
      items: [{ id: 'it-1', description: 'Flatbed Emergency Towing Service', quantity: 1, unitPrice: 850, total: 850 }],
      discountEnabled: true, discountType: 'percentage', discountValue: 0, discountLabel: 'Discount',
      taxEnabled: true, taxLabel: 'Tax / VAT', taxRate: Number(s.defaultTaxRate || 15),
      shippingEnabled: false, shippingLabel: 'Dispatch / Logistics Fee', shippingAmount: 0,
      additionalCharges: [],
      customFields: [],
      customSections: [],
      hiddenFields: [],
      sectionTitles: Object.assign({}, DEFAULT_SECTION_LABELS),
      sectionOrder: DEFAULT_SECTION_ORDER.slice(),
      paymentInstructions: s.defaultPaymentInstructions || '',
      termsAndConditions: s.defaultTerms || '',
      notes: s.defaultNotes || '',
      status: 'Draft',
    };
    if (!Array.isArray(inv.sectionOrder) || !inv.sectionOrder.length) {
      inv.sectionOrder = DEFAULT_SECTION_ORDER.slice();
    } else {
      const seen = {};
      inv.sectionOrder = inv.sectionOrder.map(normalizeSectionKey).filter(function (k) {
        if (!DEFAULT_SECTION_LABELS[k] || seen[k]) return false;
        seen[k] = true;
        return true;
      });
      DEFAULT_SECTION_ORDER.forEach(function (k) { if (!seen[k]) inv.sectionOrder.push(k); });
    }
    inv.sectionTitles = Object.assign({}, DEFAULT_SECTION_LABELS, inv.sectionTitles || {});
    inv.additionalCharges = Array.isArray(inv.additionalCharges) ? inv.additionalCharges : [];
    inv.customFields = Array.isArray(inv.customFields) ? inv.customFields : [];
    inv.customSections = Array.isArray(inv.customSections) ? inv.customSections : [];
    inv.hiddenFields = Array.isArray(inv.hiddenFields) ? inv.hiddenFields : [];

    let saveAsNewCustomer = false;
    const root = document.getElementById('stbm-modal-root');

    function updatePreview() {
      const mount = document.getElementById('stbm-inv-preview-mount');
      if (mount) mount.innerHTML = renderInvoicePreviewHtml(inv);
    }

    function syncInputs() {
      const g = function (id) { return document.getElementById(id); };
      if (g('ie-docnum')) inv.docNumber = g('ie-docnum').value.trim();
      if (g('ie-title')) inv.docTitle = g('ie-title').value;
      if (g('ie-date')) inv.issueDate = g('ie-date').value;
      if (g('ie-valid')) inv.validUntil = g('ie-valid').value;
      if (g('ie-cname')) inv.customerName = g('ie-cname').value;
      if (g('ie-ccomp')) inv.customerCompany = g('ie-ccomp').value;
      if (g('ie-cphone')) inv.customerPhone = g('ie-cphone').value;
      if (g('ie-cemail')) inv.customerEmail = g('ie-cemail').value;
      if (g('ie-caddr')) inv.customerAddress = g('ie-caddr').value;
      if (g('ie-save-cust')) saveAsNewCustomer = g('ie-save-cust').checked;
      if (g('ie-disc-on')) inv.discountEnabled = g('ie-disc-on').checked;
      if (g('ie-disc-lbl')) inv.discountLabel = g('ie-disc-lbl').value;
      if (g('ie-disc-type')) inv.discountType = g('ie-disc-type').value;
      if (g('ie-disc-val')) inv.discountValue = Number(g('ie-disc-val').value || 0);
      if (g('ie-tax-on')) inv.taxEnabled = g('ie-tax-on').checked;
      if (g('ie-tax-lbl')) inv.taxLabel = g('ie-tax-lbl').value;
      if (g('ie-tax-rate')) inv.taxRate = Number(g('ie-tax-rate').value || 0);
      if (g('ie-ship-on')) inv.shippingEnabled = g('ie-ship-on').checked;
      if (g('ie-ship-lbl')) inv.shippingLabel = g('ie-ship-lbl').value;
      if (g('ie-ship-amt')) inv.shippingAmount = Number(g('ie-ship-amt').value || 0);
      if (g('ie-remit')) inv.paymentInstructions = g('ie-remit').value;
      if (g('ie-terms')) inv.termsAndConditions = g('ie-terms').value;
      if (g('ie-notes')) inv.notes = g('ie-notes').value;
      if (g('ie-status')) inv.status = g('ie-status').value;

      root.querySelectorAll('[data-it-desc]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-it-desc'));
        if (inv.items[idx]) inv.items[idx].description = inp.value;
      });
      root.querySelectorAll('[data-it-qty]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-it-qty'));
        if (inv.items[idx]) inv.items[idx].quantity = Number(inp.value || 0);
      });
      root.querySelectorAll('[data-it-price]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-it-price'));
        if (inv.items[idx]) inv.items[idx].unitPrice = Number(inp.value || 0);
      });
      root.querySelectorAll('[data-ch-lbl]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-ch-lbl'));
        if (inv.additionalCharges[idx]) inv.additionalCharges[idx].label = inp.value;
      });
      root.querySelectorAll('[data-ch-amt]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-ch-amt'));
        if (inv.additionalCharges[idx]) inv.additionalCharges[idx].amount = Number(inp.value || 0);
      });
      root.querySelectorAll('[data-ch-on]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-ch-on'));
        if (inv.additionalCharges[idx]) inv.additionalCharges[idx].enabled = inp.checked;
      });
      root.querySelectorAll('[data-cf-lbl]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-cf-lbl'));
        if (inv.customFields[idx]) inv.customFields[idx].label = inp.value;
      });
      root.querySelectorAll('[data-cf-val]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-cf-val'));
        if (inv.customFields[idx]) inv.customFields[idx].value = inp.value;
      });
      root.querySelectorAll('[data-cf-show]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-cf-show'));
        if (inv.customFields[idx]) inv.customFields[idx].visible = inp.checked;
      });
      root.querySelectorAll('[data-cs-title]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-cs-title'));
        if (inv.customSections[idx]) inv.customSections[idx].title = inp.value;
      });
      root.querySelectorAll('[data-cs-body]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-cs-body'));
        if (inv.customSections[idx]) inv.customSections[idx].content = inp.value;
      });
      root.querySelectorAll('[data-cs-show]').forEach(function (inp) {
        const idx = Number(inp.getAttribute('data-cs-show'));
        if (inv.customSections[idx]) inv.customSections[idx].visible = inp.checked;
      });
      root.querySelectorAll('[data-sec-title]').forEach(function (inp) {
        const key = inp.getAttribute('data-sec-title');
        if (key) inv.sectionTitles[key] = inp.value;
      });
    }

    function draw() {
      const custOptions = '<option value="">-- Select Existing CRM Customer --</option>' + A.state.customers.map(function (c) {
        return '<option value="' + A.esc(c.id) + '" ' + (inv.customerId === c.id ? 'selected' : '') + '>' + A.esc(c.name) + (c.company ? ' (' + A.esc(c.company) + ')' : '') + '</option>';
      }).join('');

      root.innerHTML =
        '<div class="stbm-modal-backdrop"><div class="stbm-modal wide">' +
        '  <div class="stbm-modal-head">' +
        '    <h3>' + (existing ? 'Customize ' + A.esc(inv.docNumber) : 'New Pro Forma Invoice') + '</h3>' +
        '    <div style="display:flex;gap:8px"><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ie-cancel-top">Close</button></div>' +
        '  </div>' +
        '  <div class="stbm-modal-body">' +
        '    <div class="stbm-invoice-split">' +
        '      <div>' +
        '        <div class="stbm-grid stbm-grid-2">' +
        '          <div class="stbm-field"><label class="stbm-field-label">Document Title</label><input class="stbm-input" id="ie-title" value="' + A.esc(inv.docTitle) + '"></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label">Document Number</label><input class="stbm-input" id="ie-docnum" value="' + A.esc(inv.docNumber || '') + '" placeholder="Auto-generated if blank"></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label">Issue Date</label><input type="date" class="stbm-input" id="ie-date" value="' + A.esc(inv.issueDate) + '"></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label">Valid Until</label><input type="date" class="stbm-input" id="ie-valid" value="' + A.esc(inv.validUntil) + '"></div>' +
        '        </div>' +
        '        <div class="stbm-field"><label class="stbm-field-label">CRM Client Auto-Fill</label><select class="stbm-select" id="ie-cust-pick">' + custOptions + '</select></div>' +
        '        <div class="stbm-grid stbm-grid-2">' +
        '          <div class="stbm-field"><label class="stbm-field-label">Client Name *</label><input class="stbm-input" id="ie-cname" value="' + A.esc(inv.customerName) + '"></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label"><span>Company</span><label style="font-weight:400;font-size:11px"><input type="checkbox" data-sub-vis="company" ' + (isFieldVisible(inv.hiddenFields, 'company') ? 'checked' : '') + '> Show</label></label><input class="stbm-input" id="ie-ccomp" value="' + A.esc(inv.customerCompany) + '"></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label">Phone *</label><input class="stbm-input" id="ie-cphone" value="' + A.esc(inv.customerPhone) + '"></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label"><span>Email</span><label style="font-weight:400;font-size:11px"><input type="checkbox" data-sub-vis="email" ' + (isFieldVisible(inv.hiddenFields, 'email') ? 'checked' : '') + '> Show</label></label><input class="stbm-input" id="ie-cemail" value="' + A.esc(inv.customerEmail) + '"></div>' +
        '        </div>' +
        '        <div class="stbm-field"><label class="stbm-field-label"><span>Client Address</span><label style="font-weight:400;font-size:11px"><input type="checkbox" data-sub-vis="address" ' + (isFieldVisible(inv.hiddenFields, 'address') ? 'checked' : '') + '> Show</label></label><input class="stbm-input" id="ie-caddr" value="' + A.esc(inv.customerAddress) + '"></div>' +
        (!inv.customerId ? '<div class="stbm-field"><label style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:#015557"><input type="checkbox" id="ie-save-cust" ' + (saveAsNewCustomer ? 'checked' : '') + '> Also save client to CRM Directory</label></div>' : '') +
        '        <div class="stbm-field"><label class="stbm-field-label">Quick Service Presets</label><div style="display:flex;gap:8px"><select class="stbm-select" id="ie-preset">' +
        SERVICE_PRESETS.map(function (p, i) { return '<option value="' + i + '">' + A.esc(p.label) + ' (' + A.esc(A.money(p.unitPrice)) + ')</option>'; }).join('') +
        '        </select><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ie-add-preset">+ Add Preset</button></div></div>' +
        '        <div class="stbm-field"><div class="stbm-field-label"><span>Line Items</span><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ie-add-item">+ Add Row</button></div>' +
        (inv.items || []).map(function (it, idx, arr) {
          return '<div style="display:grid;grid-template-columns:1fr 64px 96px 26px 26px 30px;gap:4px;margin-bottom:6px;align-items:center">' +
            '<input class="stbm-input" data-it-desc="' + idx + '" value="' + A.esc(it.description) + '" placeholder="Description">' +
            '<input type="number" step="0.5" class="stbm-input" data-it-qty="' + idx + '" value="' + it.quantity + '">' +
            '<input type="number" step="0.01" class="stbm-input" data-it-price="' + idx + '" value="' + it.unitPrice + '">' +
            '<button type="button" class="stbm-btn-icon" data-it-up="' + idx + '" ' + (idx === 0 ? 'disabled' : '') + ' title="Move Up">&uarr;</button>' +
            '<button type="button" class="stbm-btn-icon" data-it-dn="' + idx + '" ' + (idx === arr.length - 1 ? 'disabled' : '') + ' title="Move Down">&darr;</button>' +
            '<button type="button" class="stbm-btn-icon danger" data-it-del="' + idx + '" ' + (arr.length <= 1 ? 'disabled' : '') + '>&times;</button></div>';
        }).join('') +
        '        </div>' +
        '        <div class="stbm-grid stbm-grid-3">' +
        '          <div class="stbm-field"><label class="stbm-field-label"><input type="checkbox" id="ie-disc-on" ' + (inv.discountEnabled !== false ? 'checked' : '') + '> Discount</label><input class="stbm-input" id="ie-disc-lbl" value="' + A.esc(inv.discountLabel || 'Discount') + '" placeholder="Label" style="margin-bottom:4px"><div style="display:flex;gap:4px"><select class="stbm-select" id="ie-disc-type" style="width:70px"><option value="percentage" ' + (inv.discountType === 'percentage' ? 'selected' : '') + '>%</option><option value="fixed" ' + (inv.discountType === 'fixed' ? 'selected' : '') + '>Fixed</option></select><input type="number" class="stbm-input" id="ie-disc-val" value="' + (inv.discountValue || 0) + '"></div></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label"><input type="checkbox" id="ie-tax-on" ' + (inv.taxEnabled !== false ? 'checked' : '') + '> Tax %</label><input class="stbm-input" id="ie-tax-lbl" value="' + A.esc(inv.taxLabel || 'Tax / VAT') + '" placeholder="Tax Label" style="margin-bottom:4px"><input type="number" step="0.1" class="stbm-input" id="ie-tax-rate" value="' + (inv.taxRate || 0) + '"></div>' +
        '          <div class="stbm-field"><label class="stbm-field-label"><input type="checkbox" id="ie-ship-on" ' + (inv.shippingEnabled ? 'checked' : '') + '> Dispatch Fee</label><input class="stbm-input" id="ie-ship-lbl" value="' + A.esc(inv.shippingLabel || 'Dispatch / Logistics Fee') + '" placeholder="Fee Label" style="margin-bottom:4px"><input type="number" step="0.01" class="stbm-input" id="ie-ship-amt" value="' + (inv.shippingAmount || 0) + '"></div>' +
        '        </div>' +
        '        <div class="stbm-field"><div class="stbm-field-label"><span>Additional Charges / Levies</span><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ie-add-charge">+ Add Fee</button></div>' +
        (inv.additionalCharges || []).map(function (ch, i) {
          return '<div style="display:grid;grid-template-columns:24px 1fr 110px 30px;gap:6px;margin-bottom:6px;align-items:center">' +
            '<input type="checkbox" data-ch-on="' + i + '" ' + (ch.enabled !== false ? 'checked' : '') + '>' +
            '<input class="stbm-input" data-ch-lbl="' + i + '" value="' + A.esc(ch.label) + '" placeholder="Charge label">' +
            '<input type="number" step="0.01" class="stbm-input" data-ch-amt="' + i + '" value="' + (ch.amount || 0) + '">' +
            '<button type="button" class="stbm-btn-icon danger" data-ch-del="' + i + '">&times;</button></div>';
        }).join('') +
        '        </div>' +
        '        <div class="stbm-field"><div class="stbm-field-label"><span>Custom Fields</span><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ie-add-cf">+ Add Custom Field</button></div>' +
        (inv.customFields || []).map(function (cf, i) {
          return '<div style="display:grid;grid-template-columns:24px 1fr 1fr 30px;gap:6px;margin-bottom:6px;align-items:center">' +
            '<input type="checkbox" data-cf-show="' + i + '" ' + (cf.visible !== false ? 'checked' : '') + '>' +
            '<input class="stbm-input" data-cf-lbl="' + i + '" value="' + A.esc(cf.label) + '" placeholder="Label (e.g. Vehicle Reg)">' +
            '<input class="stbm-input" data-cf-val="' + i + '" value="' + A.esc(cf.value) + '" placeholder="Value (e.g. GR-4421-24)">' +
            '<button type="button" class="stbm-btn-icon danger" data-cf-del="' + i + '">&times;</button></div>';
        }).join('') +
        '        </div>' +
        '        <div class="stbm-field"><div class="stbm-field-label"><span>Custom Text Sections</span><button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" id="ie-add-csec">+ Add Section</button></div>' +
        (inv.customSections || []).map(function (cs, i) {
          return '<div style="border:1px solid #e2e8f0;border-radius:8px;padding:8px;margin-bottom:6px;background:#f8fafc">' +
            '<div style="display:grid;grid-template-columns:24px 1fr 30px;gap:6px;align-items:center;margin-bottom:6px">' +
            '  <input type="checkbox" data-cs-show="' + i + '" ' + (cs.visible !== false ? 'checked' : '') + '>' +
            '  <input class="stbm-input" data-cs-title="' + i + '" value="' + A.esc(cs.title) + '" placeholder="Section Title">' +
            '  <button type="button" class="stbm-btn-icon danger" data-cs-del="' + i + '">&times;</button>' +
            '</div>' +
            '<textarea class="stbm-textarea" data-cs-body="' + i + '" placeholder="Section Content" style="min-height:54px">' + A.esc(cs.content) + '</textarea>' +
            '</div>';
        }).join('') +
        '        </div>' +
        '        <div class="stbm-field"><label class="stbm-field-label">Section Order, Headings &amp; Visibility</label><div style="display:flex;flex-direction:column;gap:6px">' +
        (inv.sectionOrder || DEFAULT_SECTION_ORDER).map(function (sec, idx, arr) {
          const isVis = isFieldVisible(inv.hiddenFields, sec);
          return '<div style="display:grid;grid-template-columns:1fr 28px 28px 54px;gap:6px;align-items:center;background:#f8fafc;padding:6px 8px;border-radius:8px;border:1px solid #e2e8f0">' +
            '<input class="stbm-input" data-sec-title="' + A.esc(sec) + '" value="' + A.esc((inv.sectionTitles && inv.sectionTitles[sec]) || DEFAULT_SECTION_LABELS[sec] || sec) + '" style="padding:4px 8px;min-height:28px;font-size:12px">' +
            '<button type="button" class="stbm-btn-icon" data-sec-up="' + idx + '" ' + (idx === 0 ? 'disabled' : '') + '>&uarr;</button>' +
            '<button type="button" class="stbm-btn-icon" data-sec-dn="' + idx + '" ' + (idx === arr.length - 1 ? 'disabled' : '') + '>&darr;</button>' +
            '<button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-sec-vis="' + A.esc(sec) + '">' + (isVis ? 'Hide' : 'Show') + '</button></div>';
        }).join('') +
        '        </div></div>' +
        '        <div class="stbm-field"><label class="stbm-field-label">Payment &amp; Remittance Instructions</label><textarea class="stbm-textarea" id="ie-remit">' + A.esc(inv.paymentInstructions) + '</textarea></div>' +
        '        <div class="stbm-field"><label class="stbm-field-label">Terms &amp; Conditions</label><textarea class="stbm-textarea" id="ie-terms">' + A.esc(inv.termsAndConditions) + '</textarea></div>' +
        '        <div class="stbm-field"><label class="stbm-field-label">Notes</label><textarea class="stbm-textarea" id="ie-notes">' + A.esc(inv.notes) + '</textarea></div>' +
        '      </div>' +
        '      <div class="stbm-a4-preview" style="position:sticky;top:0">' +
        '        <div class="stbm-a4-accent-bar" style="background:' + A.esc(s.primaryColor || '#015557') + '"></div>' +
        '        <div class="stbm-a4-inner" id="stbm-inv-preview-mount">' + renderInvoicePreviewHtml(inv) + '</div>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '  <div class="stbm-modal-foot">' +
        '    <select class="stbm-select" id="ie-status" style="width:auto"><option value="Draft" ' + (inv.status === 'Draft' ? 'selected' : '') + '>Save as Draft</option><option value="Issued" ' + (inv.status === 'Issued' ? 'selected' : '') + '>Issue Invoice</option><option value="Accepted" ' + (inv.status === 'Accepted' ? 'selected' : '') + '>Mark Accepted</option><option value="Cancelled" ' + (inv.status === 'Cancelled' ? 'selected' : '') + '>Cancelled</option></select>' +
        '    <button type="button" class="stbm-btn stbm-btn-primary" id="ie-save">Save Pro Forma Invoice</button>' +
        '  </div>' +
        '</div></div>';

      const dismiss = bindModalDismiss(root);
      document.getElementById('ie-cancel-top').onclick = dismiss;

      // Live preview update without replacing form inputs (preserves cursor focus)
      const liveIds = [
        'ie-docnum', 'ie-title', 'ie-date', 'ie-valid',
        'ie-cname', 'ie-ccomp', 'ie-cphone', 'ie-cemail', 'ie-caddr', 'ie-save-cust',
        'ie-disc-on', 'ie-disc-lbl', 'ie-disc-type', 'ie-disc-val',
        'ie-tax-on', 'ie-tax-lbl', 'ie-tax-rate',
        'ie-ship-on', 'ie-ship-lbl', 'ie-ship-amt',
        'ie-remit', 'ie-terms', 'ie-notes', 'ie-status',
      ];
      liveIds.forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        const evName = (el.type === 'checkbox' || el.tagName === 'SELECT') ? 'change' : 'input';
        el['on' + evName] = function () { syncInputs(); updatePreview(); };
      });
      root.querySelectorAll('[data-it-desc],[data-it-qty],[data-it-price],[data-ch-lbl],[data-ch-amt],[data-cf-lbl],[data-cf-val],[data-cs-title],[data-cs-body],[data-sec-title]').forEach(function (el) {
        el.oninput = function () { syncInputs(); updatePreview(); };
      });
      root.querySelectorAll('[data-ch-on],[data-cf-show],[data-cs-show]').forEach(function (el) {
        el.onchange = function () { syncInputs(); updatePreview(); };
      });
      root.querySelectorAll('[data-sub-vis]').forEach(function (el) {
        el.onchange = function () {
          syncInputs();
          inv.hiddenFields = toggleHiddenAlias(inv.hiddenFields, el.getAttribute('data-sub-vis'));
          updatePreview();
        };
      });

      document.getElementById('ie-cust-pick').onchange = function (e) {
        syncInputs();
        const c = A.state.customers.find(function (x) { return x.id === e.target.value; });
        if (c) {
          inv.customerId = c.id;
          inv.customerName = c.name;
          inv.customerCompany = c.company || '';
          inv.customerPhone = c.phone;
          inv.customerEmail = c.email || '';
          inv.customerAddress = c.address || '';
          saveAsNewCustomer = false;
        } else {
          inv.customerId = '';
        }
        draw();
      };
      document.getElementById('ie-add-preset').onclick = function () {
        syncInputs();
        const p = SERVICE_PRESETS[Number(document.getElementById('ie-preset').value)];
        if (p) inv.items.push({ id: 'it-' + Date.now(), description: p.label, quantity: 1, unitPrice: p.unitPrice, total: p.unitPrice });
        draw();
      };
      document.getElementById('ie-add-item').onclick = function () {
        syncInputs();
        inv.items.push({ id: 'it-' + Date.now(), description: '', quantity: 1, unitPrice: 0, total: 0 });
        draw();
      };
      root.querySelectorAll('[data-it-up],[data-it-dn]').forEach(function (b) {
        b.onclick = function () {
          syncInputs();
          const up = b.hasAttribute('data-it-up');
          const idx = Number(b.getAttribute(up ? 'data-it-up' : 'data-it-dn'));
          const swap = up ? idx - 1 : idx + 1;
          if (swap >= 0 && swap < inv.items.length) {
            const tmp = inv.items[idx]; inv.items[idx] = inv.items[swap]; inv.items[swap] = tmp;
            draw();
          }
        };
      });
      root.querySelectorAll('[data-it-del]').forEach(function (b) {
        b.onclick = function () {
          syncInputs();
          if (inv.items.length > 1) {
            inv.items.splice(Number(b.getAttribute('data-it-del')), 1);
            draw();
          }
        };
      });
      document.getElementById('ie-add-charge').onclick = function () {
        syncInputs();
        inv.additionalCharges.push({ id: 'ch-' + Date.now(), label: 'Hydraulic Recovery Surcharge', amount: 150, enabled: true });
        draw();
      };
      root.querySelectorAll('[data-ch-del]').forEach(function (b) {
        b.onclick = function () { syncInputs(); inv.additionalCharges.splice(Number(b.getAttribute('data-ch-del')), 1); draw(); };
      });
      document.getElementById('ie-add-cf').onclick = function () {
        syncInputs();
        inv.customFields.push({ id: 'cf-' + Date.now(), label: '', value: '', visible: true });
        draw();
      };
      root.querySelectorAll('[data-cf-del]').forEach(function (b) {
        b.onclick = function () { syncInputs(); inv.customFields.splice(Number(b.getAttribute('data-cf-del')), 1); draw(); };
      });
      document.getElementById('ie-add-csec').onclick = function () {
        syncInputs();
        inv.customSections.push({ id: 'cs-' + Date.now(), title: 'Additional Logistics Section', content: '', visible: true });
        draw();
      };
      root.querySelectorAll('[data-cs-del]').forEach(function (b) {
        b.onclick = function () { syncInputs(); inv.customSections.splice(Number(b.getAttribute('data-cs-del')), 1); draw(); };
      });
      root.querySelectorAll('[data-sec-up],[data-sec-dn],[data-sec-vis]').forEach(function (b) {
        b.onclick = function () {
          syncInputs();
          if (b.hasAttribute('data-sec-vis')) {
            inv.hiddenFields = toggleHiddenAlias(inv.hiddenFields, b.getAttribute('data-sec-vis'));
          } else {
            const up = b.hasAttribute('data-sec-up');
            const idx = Number(b.getAttribute(up ? 'data-sec-up' : 'data-sec-dn'));
            const swap = up ? idx - 1 : idx + 1;
            const tmp = inv.sectionOrder[idx]; inv.sectionOrder[idx] = inv.sectionOrder[swap]; inv.sectionOrder[swap] = tmp;
          }
          draw();
        };
      });

      document.getElementById('ie-save').onclick = function () {
        syncInputs();
        if (!inv.customerName.trim() || !inv.customerPhone.trim()) {
          A.toast('Client name and phone are required.', 'error');
          return;
        }
        const saveInvoiceNow = function () {
          return A.ajax('stbm_save_invoice', inv).then(function () {
            dismiss();
            A.toast('Pro Forma Invoice saved!', 'success');
            A.renderShell();
          });
        };
        const p = (saveAsNewCustomer && !inv.customerId)
          ? A.ajax('stbm_save_customer', {
              name: inv.customerName.trim(),
              company: inv.customerCompany.trim(),
              phone: inv.customerPhone.trim(),
              email: inv.customerEmail.trim(),
              address: inv.customerAddress.trim(),
            }).then(function (data) {
              if (data && data.customer && data.customer.id) inv.customerId = data.customer.id;
              return saveInvoiceNow();
            })
          : saveInvoiceNow();
        p.catch(function (err) { A.toast(err.message, 'error'); });
      };
    }
    draw();
  }

  /* =====================================================================
   * MODULE 5: OFFICIAL RECEIPTS (`AdminReceipts`)
   * ===================================================================== */
  let recTab = 'All', recMethod = 'All', recSearch = '';
  function renderReceipts(vp) {
    const A = app();
    const methods = ['All', 'Cash', 'Bank Transfer', 'Mobile Money (MoMo)', 'Cheque', 'Credit/Debit Card', 'Other'];
    const filtered = A.state.receipts.filter(function (r) {
      if (recTab !== 'All' && r.status !== recTab) return false;
      if (recMethod !== 'All' && r.paymentMethod !== recMethod) return false;
      if (recSearch) {
        const t = (r.receiptNumber + ' ' + r.customerName + ' ' + r.paymentReference + ' ' + (r.invoiceNumber || '')).toLowerCase();
        if (t.indexOf(recSearch.toLowerCase()) === -1) return false;
      }
      return true;
    });

    const rowsHtml = filtered.map(function (r) {
      return '<tr>' +
        '<td><strong>' + A.esc(r.receiptNumber) + '</strong><div style="font-size:11px;color:#64748b">' + A.esc(r.receiptDate) + '</div></td>' +
        '<td><strong>' + A.esc(r.customerName) + '</strong><div style="font-size:11px;color:#64748b">' + (r.invoiceNumber ? 'Invoice: ' + A.esc(r.invoiceNumber) : 'Direct Payment') + '</div></td>' +
        '<td><strong>' + A.esc(A.money(r.amountReceived)) + '</strong><div style="font-size:11px;color:#64748b">Remaining Bal: ' + A.esc(A.money(r.remainingBalance || 0)) + '</div></td>' +
        '<td>' + A.esc(r.paymentMethod) + '<div style="font-size:11px;color:#64748b">' + A.esc(r.paymentReference) + '</div></td>' +
        '<td>' + statusBadge(r.status) + '</td>' +
        '<td style="text-align:right;white-space:nowrap">' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-rec-prev="' + A.esc(r.id) + '">View / PDF</button> ' +
        '  <button type="button" class="stbm-btn stbm-btn-secondary stbm-btn-sm" data-rec-edit="' + A.esc(r.id) + '">Edit</button> ' +
        '  <button type="button" class="stbm-btn-icon danger" data-rec-del="' + A.esc(r.id) + '">&times;</button>' +
        '</td></tr>';
    }).join('') || '<tr><td colspan="6" style="text-align:center;padding:28px;color:#64748b">No receipts match this filter.</td></tr>';

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Official Receipts</h2><p>Issue stamped payment receipts with automatic Pro Forma Invoice balance reconciliation.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <a class="stbm-btn stbm-btn-secondary" href="' + A.esc(A.exportUrl('stbm_export_csv', { dataset: 'receipts' })) + '">Export CSV</a>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="stbm-rec-new">+ Issue Official Receipt</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-tabs">' +
      ['All', 'Issued', 'Corrected', 'Cancelled'].map(function (st) {
        return '<button type="button" class="stbm-tab ' + (recTab === st ? 'active' : '') + '" data-recs-tab="' + st + '">' + st + '</button>';
      }).join('') +
      '</div>' +
      '<div class="stbm-card"><div class="stbm-card-body">' +
      '  <div class="stbm-toolbar">' +
      '    <div class="stbm-search-box">' + A.svg('search') + '<input class="stbm-input" id="stbm-rec-search" placeholder="Search receipt #, client, reference..." value="' + A.esc(recSearch) + '"></div>' +
      '    <select class="stbm-select" id="stbm-rec-meth" style="width:auto">' + methods.map(function (m) { return '<option value="' + A.esc(m) + '" ' + (recMethod === m ? 'selected' : '') + '>' + A.esc(m) + '</option>'; }).join('') + '</select>' +
      '  </div>' +
      '  <div class="stbm-table-wrap"><table class="stbm-table"><thead><tr><th>Receipt #</th><th>Client / Link</th><th>Amount</th><th>Method &amp; Ref</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead><tbody>' + rowsHtml + '</tbody></table></div>' +
      '</div></div>';

    vp.querySelectorAll('[data-recs-tab]').forEach(function (b) {
      b.onclick = function () { recTab = b.getAttribute('data-recs-tab'); renderReceipts(vp); };
    });
    document.getElementById('stbm-rec-meth').onchange = function (e) { recMethod = e.target.value; renderReceipts(vp); };
    document.getElementById('stbm-rec-search').oninput = function (e) {
      recSearch = e.target.value;
      renderReceipts(vp);
      const inp = document.getElementById('stbm-rec-search');
      if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
    };
    document.getElementById('stbm-rec-new').onclick = function () { openReceiptModal(null, vp); };
    vp.querySelectorAll('[data-rec-prev]').forEach(function (b) {
      b.onclick = function () {
        const rec = A.state.receipts.find(function (r) { return r.id === b.getAttribute('data-rec-prev'); });
        if (rec) openDocumentPreviewModal('receipt', rec);
      };
    });
    vp.querySelectorAll('[data-rec-edit]').forEach(function (b) {
      b.onclick = function () {
        const rec = A.state.receipts.find(function (r) { return r.id === b.getAttribute('data-rec-edit'); });
        if (rec) openReceiptModal(rec, vp);
      };
    });
    vp.querySelectorAll('[data-rec-del]').forEach(function (b) {
      b.onclick = function () {
        const id = b.getAttribute('data-rec-del');
        A.confirmDialog({
          title: 'Delete Official Receipt?',
          description: 'Any linked Pro Forma Invoice paid amount & balance due will be recalculated automatically.',
          confirmLabel: 'Delete Receipt',
          isDestructive: true,
          onConfirm: function () {
            A.ajax('stbm_delete_receipt', { id: id }).then(function () {
              A.toast('Receipt deleted & linked invoice balance recalculated.', 'info');
              A.renderShell();
            }).catch(function (err) { A.toast(err.message, 'error'); });
          },
        });
      };
    });
  }

  function openReceiptModal(existing, vp) {
    const A = app();
    const rec = existing ? Object.assign({}, existing) : {
      receiptDate: new Date().toISOString().slice(0, 10),
      invoiceId: '', invoiceNumber: '',
      customerId: '', customerName: '', customerPhone: '', customerEmail: '', customerAddress: '',
      amountReceived: 0, paymentMethod: 'Mobile Money (MoMo)', paymentReference: '', notes: '', status: 'Issued',
    };
    const root = document.getElementById('stbm-modal-root');

    function draw() {
      const invOpts = '<option value="">-- Direct Payment (No Linked Invoice) --</option>' +
        A.state.invoices.filter(function (i) { return i.status !== 'Cancelled'; }).map(function (i) {
          return '<option value="' + A.esc(i.id) + '" ' + (rec.invoiceId === i.id ? 'selected' : '') + '>' + A.esc(i.docNumber) + ' — ' + A.esc(i.customerName) + ' (Bal: ' + A.esc(A.money(i.balanceDue)) + ')</option>';
        }).join('');

      root.innerHTML =
        '<div class="stbm-modal-backdrop"><div class="stbm-modal" style="max-width:640px">' +
        '  <div class="stbm-modal-head"><h3>' + (existing ? 'Edit Receipt ' + A.esc(rec.receiptNumber) : 'Issue Official Receipt') + '</h3><button type="button" class="stbm-btn-icon" id="rm-close">&times;</button></div>' +
        '  <div class="stbm-modal-body">' +
        '    <div class="stbm-field"><label class="stbm-field-label">Link Pro Forma Invoice (Auto-Fills Client &amp; Balance)</label><select class="stbm-select" id="rm-inv">' + invOpts + '</select></div>' +
        '    <div class="stbm-grid stbm-grid-2">' +
        '      <div class="stbm-field"><label class="stbm-field-label">Receipt Date</label><input type="date" class="stbm-input" id="rm-date" value="' + A.esc(rec.receiptDate) + '"></div>' +
        '      <div class="stbm-field"><label class="stbm-field-label">Status</label><select class="stbm-select" id="rm-status"><option>Issued</option><option>Corrected</option><option>Cancelled</option></select></div>' +
        '      <div class="stbm-field"><label class="stbm-field-label">Client Name *</label><input class="stbm-input" id="rm-cname" value="' + A.esc(rec.customerName) + '"></div>' +
        '      <div class="stbm-field"><label class="stbm-field-label">Client Phone *</label><input class="stbm-input" id="rm-cphone" value="' + A.esc(rec.customerPhone) + '"></div>' +
        '      <div class="stbm-field"><label class="stbm-field-label">Amount Received *</label><input type="number" step="0.01" class="stbm-input" id="rm-amt" value="' + (rec.amountReceived || 0) + '"></div>' +
        '      <div class="stbm-field"><label class="stbm-field-label">Payment Method</label><select class="stbm-select" id="rm-meth"><option>Mobile Money (MoMo)</option><option>Bank Transfer</option><option>Cash</option><option>Cheque</option><option>Credit/Debit Card</option><option>Other</option></select></div>' +
        '    </div>' +
        '    <div class="stbm-field"><label class="stbm-field-label">Transaction / Reference ID</label><input class="stbm-input" id="rm-ref" value="' + A.esc(rec.paymentReference) + '" placeholder="e.g. MM-ID-9938271"></div>' +
        '    <div class="stbm-field"><label class="stbm-field-label">Remarks / Notes</label><textarea class="stbm-textarea" id="rm-notes">' + A.esc(rec.notes) + '</textarea></div>' +
        '  </div>' +
        '  <div class="stbm-modal-foot"><button type="button" class="stbm-btn stbm-btn-secondary" id="rm-cancel">Cancel</button><button type="button" class="stbm-btn stbm-btn-primary" id="rm-save">Save Official Receipt</button></div>' +
        '</div></div>';

      const dismiss = bindModalDismiss(root);
      document.getElementById('rm-status').value = rec.status;
      document.getElementById('rm-meth').value = rec.paymentMethod;
      document.getElementById('rm-close').onclick = dismiss;
      document.getElementById('rm-cancel').onclick = dismiss;

      document.getElementById('rm-inv').onchange = function (e) {
        const inv = A.state.invoices.find(function (i) { return i.id === e.target.value; });
        rec.invoiceId = e.target.value;
        if (inv) {
          rec.invoiceNumber = inv.docNumber;
          rec.customerId = inv.customerId || '';
          rec.customerName = inv.customerName;
          rec.customerPhone = inv.customerPhone;
          rec.customerEmail = inv.customerEmail || '';
          rec.customerAddress = inv.customerAddress || '';
          if (!existing || Number(rec.amountReceived) === 0) rec.amountReceived = inv.balanceDue;
        }
        draw();
      };

      document.getElementById('rm-save').onclick = function () {
        rec.receiptDate = document.getElementById('rm-date').value;
        rec.status = document.getElementById('rm-status').value;
        rec.customerName = document.getElementById('rm-cname').value.trim();
        rec.customerPhone = document.getElementById('rm-cphone').value.trim();
        rec.amountReceived = Number(document.getElementById('rm-amt').value || 0);
        rec.paymentMethod = document.getElementById('rm-meth').value;
        rec.paymentReference = document.getElementById('rm-ref').value.trim();
        rec.notes = document.getElementById('rm-notes').value.trim();
        if (!rec.customerName || rec.amountReceived <= 0) {
          A.toast('Client name and positive amount received are required.', 'error');
          return;
        }
        A.ajax('stbm_save_receipt', rec).then(function () {
          dismiss();
          A.toast('Receipt saved & invoice balances updated!', 'success');
          A.renderShell();
        }).catch(function (err) { A.toast(err.message, 'error'); });
      };
    }
    draw();
  }

  /* =====================================================================
   * MODULE 7: DOCUMENT SETTINGS (`AdminDocSettings`)
   * ===================================================================== */
  function renderDocSettings(vp) {
    const A = app();
    const s = Object.assign({}, A.state.docSettings || {});

    function renderPreviewCard() {
      const mount = document.getElementById('ds-preview-mount');
      if (!mount) return;
      const col = s.primaryColor || '#015557';
      mount.innerHTML =
        '<div class="stbm-a4-accent-bar" style="background:' + A.esc(col) + '"></div>' +
        '<div class="stbm-a4-inner">' +
        '  <div style="display:flex;justify-content:space-between;border-bottom:2px solid ' + A.esc(col) + ';padding-bottom:12px">' +
        '    <div>' + (s.logoUrl ? '<img src="' + A.esc(s.logoUrl) + '" style="height:38px;margin-bottom:6px">' : '') +
        '      <div style="font-weight:900;color:' + A.esc(col) + '">' + A.esc(s.companyName) + '</div>' +
        '      <div style="font-size:10.5px;color:#64748b">' + A.esc(s.address) + '</div></div>' +
        '    <div style="text-align:right"><strong style="color:' + A.esc(col) + '">PRO FORMA INVOICE</strong><div style="font-size:10.5px;color:#64748b">' + A.esc(s.taxRegistrationNumber) + '</div></div>' +
        '  </div>' +
        '  <div style="padding:14px 0;font-size:11px;color:#475569;white-space:pre-line"><strong>Payment Instructions Preview:</strong>\n' + A.esc(s.defaultPaymentInstructions) + '</div>' +
        '  <div style="border-top:1px solid #e2e8f0;padding-top:8px;font-size:10px;color:#64748b;text-align:center">' + A.esc(s.footerText) + '</div>' +
        '</div>';
    }

    vp.innerHTML =
      '<div class="stbm-page-head">' +
      '  <div><h2>Document &amp; Letterhead Settings</h2><p>Configure Pro Forma Invoice &amp; Official Receipt branding, TIN, currency, tax rate, and remittance instructions.</p></div>' +
      '  <div class="stbm-head-actions">' +
      '    <button type="button" class="stbm-btn stbm-btn-secondary" id="ds-reset">Reset Defaults</button>' +
      '    <button type="button" class="stbm-btn stbm-btn-primary" id="ds-save">Save Document Settings</button>' +
      '  </div>' +
      '</div>' +
      '<div class="stbm-split-screen">' +
      '  <div class="stbm-card"><div class="stbm-card-head"><h3>Company Letterhead &amp; Billing Defaults</h3></div><div class="stbm-card-body">' +
      '    <div class="stbm-grid stbm-grid-2">' +
      '      <div class="stbm-field"><label class="stbm-field-label">Company Legal Name</label><input class="stbm-input" id="ds-cname" value="' + A.esc(s.companyName) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Tax Registration (TIN)</label><input class="stbm-input" id="ds-tin" value="' + A.esc(s.taxRegistrationNumber) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Primary Phone</label><input class="stbm-input" id="ds-phone" value="' + A.esc(s.phone) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Billing Email</label><input class="stbm-input" id="ds-email" value="' + A.esc(s.email) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Currency Symbol</label><input class="stbm-input" id="ds-curr" value="' + A.esc(s.currency) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Default Tax / VAT Rate (%)</label><input type="number" step="0.1" class="stbm-input" id="ds-tax" value="' + A.esc(s.defaultTaxRate) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Validity Window (Days)</label><input type="number" class="stbm-input" id="ds-days" value="' + A.esc(s.defaultValidityDays) + '"></div>' +
      '      <div class="stbm-field"><label class="stbm-field-label">Accent Hex Color</label><input type="color" id="ds-col" value="' + A.esc(s.primaryColor || '#015557') + '" style="width:100%;height:38px;border:1px solid #cbd5e1;border-radius:10px"></div>' +
      '    </div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Letterhead Address</label><input class="stbm-input" id="ds-addr" value="' + A.esc(s.address) + '"></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Letterhead Logo URL</label><div style="display:flex;gap:8px"><input class="stbm-input" id="ds-logo" value="' + A.esc(s.logoUrl) + '"><button type="button" class="stbm-btn stbm-btn-secondary" id="ds-pick-logo">Media</button></div></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Default Remittance Instructions</label><textarea class="stbm-textarea" id="ds-remit">' + A.esc(s.defaultPaymentInstructions) + '</textarea></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">Default Terms &amp; Conditions</label><textarea class="stbm-textarea" id="ds-terms">' + A.esc(s.defaultTerms) + '</textarea></div>' +
      '    <div class="stbm-field"><label class="stbm-field-label">A4 Footer Strip Text</label><input class="stbm-input" id="ds-footer" value="' + A.esc(s.footerText) + '"></div>' +
      '  </div></div>' +
      '  <div class="stbm-preview-sticky"><div class="stbm-a4-preview" id="ds-preview-mount"></div></div>' +
      '</div>';

    renderPreviewCard();

    function readSettingsPayload() {
      s.companyName = document.getElementById('ds-cname').value.trim();
      s.taxRegistrationNumber = document.getElementById('ds-tin').value.trim();
      s.phone = document.getElementById('ds-phone').value.trim();
      s.email = document.getElementById('ds-email').value.trim();
      s.currency = document.getElementById('ds-curr').value.trim();
      s.defaultTaxRate = Number(document.getElementById('ds-tax').value || 15);
      s.defaultValidityDays = Number(document.getElementById('ds-days').value || 14);
      s.primaryColor = document.getElementById('ds-col').value;
      s.address = document.getElementById('ds-addr').value.trim();
      s.logoUrl = document.getElementById('ds-logo').value.trim();
      s.defaultPaymentInstructions = document.getElementById('ds-remit').value;
      s.defaultTerms = document.getElementById('ds-terms').value;
      s.footerText = document.getElementById('ds-footer').value.trim();
      return s;
    }

    ['ds-cname', 'ds-tin', 'ds-phone', 'ds-email', 'ds-curr', 'ds-tax', 'ds-days', 'ds-col', 'ds-addr', 'ds-logo', 'ds-remit', 'ds-terms', 'ds-footer'].forEach(function (id) {
      const el = document.getElementById(id);
      if (el) {
        el.oninput = function () {
          readSettingsPayload();
          renderPreviewCard();
        };
      }
    });

    document.getElementById('ds-pick-logo').onclick = function () {
      A.openWpMediaPicker(function (u) {
        document.getElementById('ds-logo').value = u;
        readSettingsPayload();
        renderPreviewCard();
      });
    };
    document.getElementById('ds-save').onclick = function () {
      A.ajax('stbm_save_doc_settings', readSettingsPayload()).then(function () {
        A.toast('Document settings saved!', 'success');
        renderDocSettings(vp);
      }).catch(function (err) { A.toast(err.message, 'error'); });
    };
    document.getElementById('ds-reset').onclick = function () {
      A.confirmDialog({
        title: 'Reset Document Settings?',
        description: 'Restore default Santa Towing company letterhead, 15% VAT rate, and remittance instructions.',
        confirmLabel: 'Reset Defaults',
        onConfirm: function () {
          A.ajax('stbm_reset_doc_settings', {}).then(function () {
            A.toast('Document settings reset.', 'info');
            renderDocSettings(vp);
          }).catch(function (err) { A.toast(err.message, 'error'); });
        },
      });
    };
  }

  window.STBM_MODULES.renderCustomers = renderCustomers;
  window.STBM_MODULES.renderInvoices = renderInvoices;
  window.STBM_MODULES.renderReceipts = renderReceipts;
  window.STBM_MODULES.renderDocSettings = renderDocSettings;
  window.STBM_MODULES._billingHelpers = {
    calcInvoiceLocal: calcInvoiceLocal,
    isFieldVisible: isFieldVisible,
    normalizeSectionKey: normalizeSectionKey,
    renderInvoicePreviewHtml: renderInvoicePreviewHtml,
  };
})();
