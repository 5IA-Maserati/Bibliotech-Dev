import * as fs from 'fs'
import * as glob from 'glob'
import * as cheerio from 'cheerio'

// ------------------------------
// FILE PATTERNS TO PROCESS
// ------------------------------
const FILES = [
  'public/**/*.html',
  'public/**/*.php',
  'src/**/*.php'
]

// ------------------------------
// HTML SETTINGS
// ------------------------------

// Tags to skip (text inside these tags is preserved)
const SKIP_TAGS = new Set([
  'script',
  'style',
  'code',
  'pre',
  'noscript'
])

// Attributes that may contain visible text we want to clear
const ATTRS_TO_STRIP = ['alt', 'aria-label', 'title']

// Helper: check if a node is inside a skipped tag
function isInsideSkippedTag(elem) {
  let current = elem.parent
  while (current) {
    if (current.tagName && SKIP_TAGS.has(current.tagName.toLowerCase())) {
      return true
    }
    current = current.parent
  }
  return false
}

// ------------------------------
// Helper: remove all string literals from PHP or JS
// ------------------------------
function removeAllStrings(content) {
  // Matches single-quoted, double-quoted, and backtick strings
  const stringRegex = /(['"`])(?:\\.|(?!\1).)*\1/g
  return content.replace(stringRegex, match => match[0] + match[0])
}

// ------------------------------
// MAIN PROCESS
// ------------------------------
for (const pattern of FILES) {
  const files = glob.sync(pattern, { nodir: true })

  for (const file of files) {
    let content = fs.readFileSync(file, 'utf8')
    const removedTexts = []

    if (file.endsWith('.html')) {
      // ------------------------------
      // HTML/PHP AS HTML PART
      // ------------------------------
      const $ = cheerio.load(content, { decodeEntities: false, xmlMode: false })

      // Remove visible text nodes not in skipped tags
      $('*').contents().each((_, node) => {
        if (node.type === 'text' && !isInsideSkippedTag(node)) {
          const text = node.data?.trim()
          if (text) {
            removedTexts.push(`TEXT: "${text}"`)
            node.data = ''
          }
        }
      })

      // Remove text from specified attributes
      $('*').each((_, elem) => {
        for (const attr of ATTRS_TO_STRIP) {
          if (elem.attribs && elem.attribs[attr]) {
            const value = elem.attribs[attr].trim()
            if (value) {
              removedTexts.push(`ATTR(${attr}): "${value}"`)
              elem.attribs[attr] = ''
            }
          }
        }
      })

      content = $.html()
    }

    // ------------------------------
    // REMOVE ALL STRINGS (PHP/JS)
    // ------------------------------
    content = removeAllStrings(content)

    // Write back modified file (CI-only)
    fs.writeFileSync(file, content)

    // ------------------------------
    // DEBUG OUTPUT
    // ------------------------------
    console.log(`\n📄 File: ${file}`)
    if (removedTexts.length > 0) {
      console.log('  ❌ Removed visible HTML text:')
      removedTexts.forEach(t => console.log(`    ${t}`))
    } else {
      console.log('  ✅ No visible HTML text removed')
    }

    console.log('\n  📑 Full stripped content:')
    console.log(content)
  }
}

console.log('\n✔ All string literals and visible HTML text stripped (CI-only)')
