import * as fs from 'fs'
import * as path from 'path'
import * as glob from 'glob'

/** File patterns to process */
const FILES = ['public/**/*.php']

/**
 * Remove all strings inside a PHP block
 * @param {string} phpCode
 * @returns {{ stripped: string, removed: string[] }}
 */
function removePhpStrings (phpCode) {
  const removed = []
  // Correct regex: either a single-quoted string or a double-quoted string,
  // each allowing escaped chars like \' or \"
  const stringRegex = /'([^'\\]|\\.)*'|"([^"\\]|\\.)*"/g

  const stripped = phpCode.replace(stringRegex, (match) => {
    removed.push(match)
    // Return empty quotes of the same type (e.g. '' or "")
    return match[0] + match[0]
  })

  return { stripped, removed }
}

/**
 * Process a single PHP file: split PHP blocks vs non-PHP and remove strings inside PHP only
 */
function processPhpFile (filePath) {
  const originalContent = fs.readFileSync(filePath, 'utf8')
  const removedStrings = []

  // Split content into PHP blocks (keeps delimiters) and non-PHP parts
  // Matches <? ... ?>, <?php ... ?> and <?= ... ?>
  const parts = originalContent.split(/(<\?(?:php|=)?[\s\S]*?\?>)/gi)

  const processed = parts
    .map((part) => {
      if (/^<\?(?:php|=)/i.test(part)) {
        // this is a PHP block
        const { stripped, removed } = removePhpStrings(part)
        removedStrings.push(...removed)
        return stripped
      } else {
        // HTML/text outside PHP — leave unchanged
        return part
      }
    })
    .join('')

  // Overwrite original file (make backup first if preferisci)
  fs.writeFileSync(filePath, processed, 'utf8')

  return removedStrings
}

// Main
const allFiles = FILES.flatMap((pattern) =>
  glob.sync(pattern, { nodir: true })
)

console.log(`Found ${allFiles.length} PHP files.\n`)

allFiles.forEach((file) => {
  const removed = processPhpFile(file)
  console.log(`📄 Processed file: ${file}`)
  if (removed.length > 0) {
    console.log(`  ❌ Removed PHP strings (${removed.length}):`)
    removed.forEach((str) => console.log(`    ${str}`))
  } else {
    console.log('  ✅ No PHP strings removed')
  }
  console.log('')
})

console.log('✔ All PHP files processed.')
