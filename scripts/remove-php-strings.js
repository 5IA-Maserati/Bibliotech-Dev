import * as fs from 'fs'
import * as path from 'path'
import * as glob from 'glob'

// --- CONFIG ---
// Directory to scan
const PHP_DIR = 'src' // change this to your project folder
const PHP_GLOB = `${PHP_DIR}/**/*.php`

// --- Helper: remove strings in a PHP block ---
function removePhpStrings(phpCode) {
  const removed = []
  const stringRegex = /(['"`])(?:\\.|(?!\1)[^\\\n\r])*?\1/g

  const stripped = phpCode.replace(stringRegex, function(match, quote) {
    removed.push(match)
    return quote + quote // replace with empty string of same quote
  })

  return { stripped, removed }
}

// --- Process single PHP file ---
function processPhpFile(filePath) {
  const content = fs.readFileSync(filePath, 'utf8')
  let output = ''
  const removedStrings = []

  // Regex to find PHP blocks
  const phpBlockRegex = /<\?php([\s\S]*?)\?>/g
  let lastIndex = 0
  let match

  while ((match = phpBlockRegex.exec(content)) !== null) {
    const phpStart = match.index
    const phpEnd = phpBlockRegex.lastIndex

    // Append HTML before this PHP block
    output += content.slice(lastIndex, phpStart)

    // Process the PHP block
    const phpBlock = match[1]
    const result = removePhpStrings(phpBlock)
    removedStrings.push(...result.removed)

    // Add back stripped PHP block
    output += `<?php${result.stripped}?>`

    lastIndex = phpEnd
  }

  // Append remaining HTML
  output += content.slice(lastIndex)

  // Write back file
  fs.writeFileSync(filePath, output, 'utf8')

  // Debug output
  console.log(`\n📄 Processed file: ${filePath}`)
  if (removedStrings.length > 0) {
    console.log('  ❌ Removed PHP strings:')
    removedStrings.forEach(str => console.log(`    ${str}`))
  } else {
    console.log('  ✅ No PHP strings removed')
  }

  console.log('\n  📑 Full stripped content:')
  console.log(output)
}

// --- Main: process all PHP files ---
function processAllPhpFiles() {
  const files = glob.sync(PHP_GLOB, { nodir: true })
  if (files.length === 0) {
    console.log('No PHP files found.')
    return
  }

  console.log(`Found ${files.length} PHP files.`)

  files.forEach(file => {
    processPhpFile(file)
  })

  console.log('\n✔ All PHP files processed.')
}

// Run
processAllPhpFiles()
