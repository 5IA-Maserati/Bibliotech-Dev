import * as fs from 'fs'
import * as glob from 'glob'
import * as cheerio from 'cheerio'

// Files to process
const FILES = [
  'public/**/*.html',
  'public/**/*.php',
  'src/**/*.php'
]

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
function isInsideSkippedTag (elem) {
  let current = elem.parent
  while (current) {
    if (current.tagName && SKIP_TAGS.has(current.tagName.toLowerCase())) {
      return true
    }
    current = current.parent
  }
  return false
}

// Process each file pattern
for (const pattern of FILES) {
  const files = glob.sync(pattern, { nodir: true })

  for (const file of files) {
    const html = fs.readFileSync(file, 'utf8')
    const $ = cheerio.load(html, { decodeEntities: false, xmlMode: false })

    const removedTexts = []

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

    const strippedHtml = $.html()

    // Write back modified file (CI-only)
    fs.writeFileSync(file, strippedHtml)

    // Debug output
    console.log(`\n📄 File: ${file}`)
    if (removedTexts.length > 0) {
      console.log('  ❌ Removed text:')
      removedTexts.forEach(t => console.log(`    ${t}`))
    } else {
      console.log('  ✅ No text removed')
    }

    // Print entire stripped content
    console.log('\n  📑 Full stripped content:')
    console.log(strippedHtml)
  }
}

console.log('\n✔ Visible HTML text and attributes stripped (CI-only)')
