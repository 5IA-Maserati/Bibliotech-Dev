const fs = require('fs');
const glob = require('glob');
const cheerio = require('cheerio');

// Files to process
const FILES = [
  'public/**/*.html',
  'public/**/*.php',
  'src/**/*.php'
];

// Tags to skip (text inside these tags is preserved)
const SKIP_TAGS = new Set([
  'style',
  'code',
  'pre',
  'noscript'
]);

// Attributes that may contain visible text we want to clear
const ATTRS_TO_STRIP = [
  'alt',
  'title',
  'placeholder',
  'aria-label',
  'aria-labelledby',
  'aria-describedby',
  'aria-valuetext',
  'aria-placeholder',
  'aria-details'
];

// Helper: check if a node is inside a skipped tag
function isInsideSkippedTag(elem) {
  let current = elem.parent;
  while (current) {
    if (current.tagName && SKIP_TAGS.has(current.tagName.toLowerCase())) {
      return true;
    }
    current = current.parent;
  }
  return false;
}

// Remove alert(), confirm(), prompt() strings inside <script>
function stripAlerts(scriptContent, removedTexts) {
  return scriptContent.replace(/(alert|confirm|prompt)\s*\(\s*(['"`])([\s\S]*?)\2\s*\)/g, function(_, fn, quote, msg) {
    if (msg.trim()) {
      removedTexts.push(`${fn.toUpperCase()}: "${msg}"`);
    }
    return `${fn}("")`;
  });
}

// Process each file pattern
for (const pattern of FILES) {
  const files = glob.sync(pattern, { nodir: true });

  for (const file of files) {
    const html = fs.readFileSync(file, 'utf8');
    const $ = cheerio.load(html, { decodeEntities: false, xmlMode: false });

    const removedTexts = [];

    // Remove visible text nodes not in skipped tags
    $('*').contents().each((_, node) => {
      if (node.type === 'text' && !isInsideSkippedTag(node)) {
        const text = node.data ? node.data.trim() : '';
        if (text) {
          removedTexts.push(`TEXT: "${text}"`);
          node.data = '';
        }
      }
    });

    // Remove text from ATTRS_TO_STRIP
    $('*').each((_, elem) => {
      if (!elem.attribs) return;

      ATTRS_TO_STRIP.forEach(attr => {
        if (elem.attribs[attr]) {
          const value = elem.attribs[attr].trim();
          if (value) {
            removedTexts.push(`ATTR(${attr}): "${value}"`);
            elem.attribs[attr] = '';
          }
        }
      });

      // Inline JS: alert, confirm, prompt
      Object.keys(elem.attribs).forEach(attrName => {
        const val = elem.attribs[attrName];
        if (val && /(alert|confirm|prompt)\s*\(/.test(val)) {
          elem.attribs[attrName] = val.replace(/(alert|confirm|prompt)\s*\(\s*(['"`])([\s\S]*?)\2\s*\)/g, function(_, fn, quote, msg) {
            if (msg.trim()) {
              removedTexts.push(`${fn.toUpperCase()} INLINE: "${msg}"`);
            }
            return `${fn}("")`;
          });
        }
      });
    });

    // Strip alert/confirm/prompt content inside <script>
    $('script').each((_, elem) => {
      if (elem.children && elem.children.length > 0) {
        elem.children.forEach(child => {
          if (child.type === 'text' && child.data) {
            child.data = stripAlerts(child.data, removedTexts);
          }
        });
      }
    });

    const strippedHtml = $.html();

    // Write back modified file
    fs.writeFileSync(file, strippedHtml);

    // Debug output
    console.log(`\n📄 File: ${file}`);
    if (removedTexts.length > 0) {
      console.log('  ❌ Removed text:');
      removedTexts.forEach(t => console.log(`    ${t}`));
    } else {
      console.log('  ✅ No text removed');
    }

    console.log('\n  📑 Full stripped content:');
    console.log(strippedHtml);
  }
}

console.log('\n✔ Visible HTML text, attributes, and alert/confirm/prompt strings stripped (CI-only)');