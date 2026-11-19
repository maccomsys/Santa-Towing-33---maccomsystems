import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const { chromium } = require('/usr/local/share/bun-global/node_modules/playwright-core');

const ROOT = process.cwd();
const CONV_DIR = path.join(ROOT, 'Santa WordPress Website Conversions');
const QA_DIR = path.join(CONV_DIR, 'qa');
const SCREENSHOTS_DIR = path.join(QA_DIR, 'screenshots');

const VIEWPORTS = [
  { name: '1920', width: 1920, height: 1080 },
  { name: '1440', width: 1440, height: 900 },
  { name: '1280', width: 1280, height: 800 },
  { name: '1024', width: 1024, height: 768 },
  { name: '768', width: 768, height: 1024 },
  { name: '390', width: 390, height: 844 },
  { name: '360', width: 360, height: 780 },
];

const ROUTES = [
  { key: 'home', path: '/', group: 'pilot' },
  { key: 'services', path: '/services', group: 'pilot' },
  { key: 'fleet', path: '/fleet', group: 'pilot' },
  { key: 'privacy-policy', path: '/privacy-policy', group: 'pilot' },
  { key: 'service-area-accra', path: '/service-areas/accra', group: 'pilot' },
  { key: 'sales', path: '/sales', group: 'template-group' },
  { key: 'sales-detail-1', path: '/sales/1', group: 'template-group' },
  { key: 'rental', path: '/rental', group: 'template-group' },
  { key: 'rental-detail-1', path: '/rental/1', group: 'template-group' },
  { key: 'parts', path: '/parts', group: 'template-group' },
  { key: 'parts-detail-1', path: '/parts/1', group: 'template-group' },
  { key: 'blog', path: '/blog', group: 'template-group' },
  { key: 'blog-detail-1', path: '/blog/1', group: 'template-group' },
  { key: 'service-area-corridor', path: '/service-areas/accra-to-kumasi', group: 'template-group' },
  { key: 'service-area-subservice', path: '/service-areas/accra/emergency-towing', group: 'template-group' },
  { key: 'terms-conditions', path: '/terms-conditions', group: 'template-group' },
  { key: 'search', path: '/search?q=towing', group: 'template-group' },
  { key: 'sitemap', path: '/sitemap', group: 'template-group' },
  { key: '404', path: '/non-existent-qa-route', group: 'template-group' },
  { key: 'towing', path: '/towing', group: 'unique' },
  { key: 'haulage', path: '/haulage', group: 'unique' },
  { key: 'repairs', path: '/repairs', group: 'unique' },
  { key: 'body-works', path: '/body-works', group: 'unique' },
  { key: 'contact', path: '/contact', group: 'unique' },
  { key: 'careers', path: '/careers', group: 'unique' },
  { key: 'faqs', path: '/faqs', group: 'unique' },
  { key: 'gallery', path: '/gallery', group: 'unique' },
  { key: 'service-areas', path: '/service-areas', group: 'unique' },
];

fs.mkdirSync(SCREENSHOTS_DIR, { recursive: true });

async function inspectPage(page, url, vp) {
  await page.setViewportSize({ width: vp.width, height: vp.height });
  const consoleErrors = [];
  const pageErrors = [];
  const brokenImages = [];

  const onConsole = (msg) => {
    if (msg.type() === 'error') consoleErrors.push(msg.text());
  };
  const onPageError = (err) => pageErrors.push(String(err));
  page.on('console', onConsole);
  page.on('pageerror', onPageError);

  await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 }).catch(() => {});
  await page.waitForTimeout(450);

  // Trigger scroll reveals cleanly
  await page.evaluate(async () => {
    window.scrollTo(0, 0);
  });

  const metrics = await page.evaluate(() => {
    const doc = document.documentElement;
    const body = document.body;
    const header = document.querySelector('header');
    const h1 = document.querySelector('h1');
    const footer = document.querySelector('footer');
    const firstSection = document.querySelector('main section, main > div');
    const imgs = Array.from(document.querySelectorAll('img'));
    const broken = imgs
      .filter((img) => {
        const st = getComputedStyle(img);
        return st.display !== 'none' && (!img.complete || img.naturalWidth === 0);
      })
      .map((img) => img.getAttribute('src') || img.alt || 'unknown');

    const title = document.title;
    const descEl = document.querySelector('meta[name="description"]');
    const robotsEl = document.querySelector('meta[name="robots"]');
    const canonEl = document.querySelector('link[rel="canonical"]');

    const h1Style = h1 ? getComputedStyle(h1) : null;
    const bodyStyle = getComputedStyle(body);
    const headerRect = header ? header.getBoundingClientRect() : null;
    const footerRect = footer ? footer.getBoundingClientRect() : null;
    const sectionRect = firstSection ? firstSection.getBoundingClientRect() : null;

    return {
      overflowX: doc.scrollWidth > doc.clientWidth,
      scrollWidth: doc.scrollWidth,
      clientWidth: doc.clientWidth,
      scrollHeight: doc.scrollHeight,
      title,
      description: descEl ? descEl.getAttribute('content') : null,
      robots: robotsEl ? robotsEl.getAttribute('content') : null,
      canonical: canonEl ? canonEl.getAttribute('href') : null,
      headerHeight: headerRect ? Math.round(headerRect.height) : null,
      headerTop: headerRect ? Math.round(headerRect.top) : null,
      firstSectionTop: sectionRect ? Math.round(sectionRect.top) : null,
      h1Text: h1 ? h1.textContent.trim().slice(0, 80) : null,
      h1Font: h1Style ? `${h1Style.fontSize} / ${h1Style.fontWeight} / ${h1Style.fontFamily.split(',')[0]}` : null,
      bodyFont: `${bodyStyle.fontSize} / ${bodyStyle.fontFamily.split(',')[0]}`,
      footerHeight: footerRect ? Math.round(footerRect.height) : null,
      imgCount: imgs.length,
      brokenImages: broken,
    };
  });

  page.off('console', onConsole);
  page.off('pageerror', onPageError);

  return {
    ...metrics,
    consoleErrors,
    pageErrors,
  };
}

async function run() {
  const mode = process.argv.includes('--baseline') ? 'baseline' : 'full';
  const candidatePath = '/ms-playwright/chromium_headless_shell-1247/chrome-headless-shell-linux64/chrome-headless-shell';
  const browser = await chromium.launch({
    headless: true,
    ...(fs.existsSync(candidatePath) ? { executablePath: candidatePath } : {}),
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  const context = await browser.newContext();
  const page = await context.newPage();

  const reactBase = 'http://127.0.0.1:3000';
  const report = { generatedAt: new Date().toISOString(), routes: {} };

  for (const r of ROUTES) {
    const viewportsToRun = r.group === 'pilot' || ['sales', 'rental', 'parts', 'blog', 'towing', 'contact'].includes(r.key)
      ? VIEWPORTS
      : [VIEWPORTS[1], VIEWPORTS[5]]; // 1440 + 390 for secondary routes

    report.routes[r.key] = { path: r.path, group: r.group, viewports: {} };
    for (const vp of viewportsToRun) {
      const m = await inspectPage(page, `${reactBase}${r.path}`, vp);
      report.routes[r.key].viewports[vp.name] = m;
      if (r.group === 'pilot' && (vp.name === '1440' || vp.name === '390')) {
        await page.screenshot({
          path: path.join(SCREENSHOTS_DIR, `react-${r.key}-${vp.name}.png`),
          fullPage: false,
        });
      }
    }
    const r1440 = report.routes[r.key].viewports['1440'];
    console.log(
      `[REACT] ${r.path.padEnd(36)} title="${(r1440.title || '').slice(0, 42)}" hdr=${r1440.headerTop}:${r1440.headerHeight} sec1=${r1440.firstSectionTop} h1="${(r1440.h1Font || '').slice(0, 26)}" broken=${r1440.brokenImages.length} ovf=${r1440.overflowX}`
    );
  }

  fs.writeFileSync(path.join(QA_DIR, 'react-baseline.json'), JSON.stringify(report, null, 2));
  await browser.close();
  console.log(`Baseline written (${mode}).`);
}

run().catch((err) => {
  console.error(err);
  process.exit(1);
});
