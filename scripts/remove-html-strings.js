const fs = require('fs')
const glob = require('glob')
const cheerio = require('cheerio')

const FILES = [
  'public/**/*.html',
  'public/**/*.php',
  'src/**/*.php'
]

const SKIP_TAGS = new Set([
  'style',
  'code',
  'pre',
  'noscript'
])

const ATTRS_TO_STRIP = [
  'alt',
  'title',
  'placeholder',
  'aria-label',
  'aria-valuetext',
  'aria-placeholder',
  'aria-details'
]

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

function stripAlerts (scriptContent, removedTexts) {
  return scriptContent.replace(
    /(alert|confirm|prompt)\s*\(\s*(['"`])([\s\S]*?)\2\s*\)/g,
    (_, fn, quote, msg) => {
      if (msg.trim()) {
        removedTexts.push(`${fn.toUpperCase()}: "${msg}"`)
      }
      return `${fn}("")`
    }
  )
}

for (const pattern of FILES) {
  const files = glob.sync(pattern, { nodir: true })

  for (const file of files) {
    const html = fs.readFileSync(file, 'utf8')

    const $ = cheerio.load(html, {
      decodeEntities: false,
      xmlMode: false
    })

    const removedTexts = []

    $('*').contents().each((_, node) => {
      if (node.type === 'text' && !isInsideSkippedTag(node)) {
        const text = node.data ? node.data.trim() : ''

        if (text) {
          removedTexts.push(`TEXT: "${text}"`)
          node.data = ''
        }
      }
    })

    $('*').each((_, elem) => {
      if (!elem.attribs) return

      ATTRS_TO_STRIP.forEach(attr => {
        if (elem.attribs[attr]) {
          const value = elem.attribs[attr].trim()

          if (value) {
            removedTexts.push(`ATTR(${attr}): "${value}"`)
            elem.attribs[attr] = ''
          }
        }
      })

      Object.keys(elem.attribs).forEach(attrName => {
        const val = elem.attribs[attrName]

        if (val && /(alert|confirm|prompt)\s*\(/.test(val)) {
          elem.attribs[attrName] = val.replace(
            /(alert|confirm|prompt)\s*\(\s*(['"`])([\s\S]*?)\2\s*\)/g,
            (_, fn, quote, msg) => {
              if (msg.trim()) {
                removedTexts.push(`${fn.toUpperCase()} INLINE: "${msg}"`)
              }
              return `${fn}("")`
            }
          )
        }
      })
    })

    $('script').each((_, elem) => {
      if (elem.children && elem.children.length > 0) {
        elem.children.forEach(child => {
          if (child.type === 'text' && child.data) {
            child.data = stripAlerts(child.data, removedTexts)
          }
        })
      }
    })

    const strippedHtml = $.html()

    fs.writeFileSync(file, strippedHtml)

    console.log(`\n📄 File: ${file}`)

    if (removedTexts.length > 0) {
      console.log('  ❌ Removed text:')
      removedTexts.forEach(t => console.log(`    ${t}`))
    } else {
      console.log('  ✅ No text removed')
    }

    console.log('\n  📑 Full stripped content:')
    console.log(strippedHtml)
  }
}

console.log('\n✔ Visible HTML text, attributes, and alert/confirm/prompt strings stripped (CI-only)')
