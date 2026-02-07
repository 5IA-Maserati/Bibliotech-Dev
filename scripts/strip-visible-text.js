import * as fs from 'fs'
import * as glob from 'glob'
import * as cheerio from 'cheerio'

// File patterns to process
const FILES = [
  'public/**/*.html',
  'public/**/*.php',
  'src/**/*.php'
]

// Tags to skip in HTML (text inside these tags is preserved)
const SKIP_TAGS = new Set([
  'script',
  'style',
  'code',
  'pre',
  'noscript'
])

// HTML attributes that may contain visible text
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

// Remove all string literals from PHP code block
function removePhpStrings(phpCode) {
  // Matches single, double, and backtick strings, including multiline
  const stringRegex = /(['"`])(?:\\.|(?!\1)[^\\\n\r])*?\1/g
  return phpCode.replace(stringRegex, (match, quote) => quote + quote)
}

// Process each file pattern
for (const pattern of FILES) {
  const files = glob.sync(pattern, { nodir: true })

  for (const file of files) {
    let content = fs.readFileSync(file, 'utf8')
    const removedTexts = []

    if (file.endsWith('.html')) {
      // --- HTML FILE: original Cheerio logic ---
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
    } else if (file.endsWith('.php')) {
      // --- PHP FILE: remove strings only inside <?php ... ?> blocks ---
      content = content.replace(/<\?php([\s\S]*?)\?>/g, (match, phpBlock) => {
        const strippedBlock = removePhpStrings(phpBlock)
        // Optionally collect removed strings for debug
        const strings = phpBlock.match(/(['"`])(?:\\.|(?!\1)[^\\\n\r])*?\1/g)
        if (strings) {
          strings.forEach(str => removedTexts.push(`PHP STRING: ${str}`))
        }
        return `<?php${strippedBlock}?>`
      })
    }

    // Write back modified file
    fs.writeFileSync(file, content, 'utf8')

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
    console.log(content)
  }
}

console.log('\n✔ Finished processing HTML and PHP files.')
