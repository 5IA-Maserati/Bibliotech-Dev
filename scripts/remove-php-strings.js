import fs from 'fs'
import { glob } from 'glob'

/** File patterns to process */
const FILES = ['public/**/*.php']

/**
 * Remove all quoted strings (single & double) from PHP code
 * @param {string} content
 * @returns {{ stripped: string, removed: string[] }}
 */
function removePhpStrings(content) {
  const removed = []

  // Matches single or double quoted strings with escaped chars
  const stringRegex = /'([^'\\]|\\.)*'|"([^"\\]|\\.)*"/g

  const stripped = content.replace(stringRegex, (match) => {
    removed.push(match)

    // Preserve quote type but empty content
    const quote = match[0]
    return quote + quote
  })

  return { stripped, removed }
}

/**
 * Process a single PHP file
 */
function processPhpFile(filePath) {
  const originalContent = fs.readFileSync(filePath, 'utf8')

  const { stripped, removed } = removePhpStrings(originalContent)

  // Backup file
  fs.writeFileSync(filePath + '.bak', originalContent, 'utf8')

  // Overwrite with stripped content
  fs.writeFileSync(filePath, stripped, 'utf8')

  return removed
}

// MAIN
async function main() {
  try {
    const allFiles = await glob(FILES, { nodir: true })

    console.log(`Found ${allFiles.length} PHP files.\n`)

    for (const file of allFiles) {
      const removed = processPhpFile(file)

      console.log(`📄 Processed file: ${file}`)

      if (removed.length > 0) {
        console.log(`  ❌ Removed strings (${removed.length}):`)
        removed.forEach((str) => console.log(`    ${str}`))
      } else {
        console.log('  ✅ No strings removed')
      }

      console.log('')
    }

    console.log('✔ All PHP files processed.')
  } catch (err) {
    console.error('❌ Error:', err)
  }
}

main()
