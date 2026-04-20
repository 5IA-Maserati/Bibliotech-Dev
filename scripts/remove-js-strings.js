// Safely removes user-facing string literals from selected JS files
// Handles message properties, alert() calls, and FormValidator.markFieldError()
// Overwrites files and prints cleaned content
// Files are selected based on filename only

import fs from 'fs'
import path from 'path'

/**
 * Recursively get all JS files in a directory
 * @param {string} dir - starting directory
 * @returns {string[]} - array of file paths
 */
function getAllJsFiles (dir) {
  let results = []
  const list = fs.readdirSync(dir, { withFileTypes: true })

  for (const item of list) {
    const fullPath = path.join(dir, item.name)
    if (item.isDirectory()) {
      results = results.concat(getAllJsFiles(fullPath))
    } else if (item.isFile() && fullPath.endsWith('.js')) {
      results.push(fullPath)
    }
  }
  return results
}

/**
 * Remove only user-facing string literals
 * - `message: '...'` in validators
 * - `alert('...')` calls
 * - `FormValidator.markFieldError(id, '...')` messages
 * Preserves regex, patterns, comments, and code structure
 *
 * @param {string} jsContent - Original JS file content
 * @returns {string} - JS content with user strings emptied
 */
function removeUserStrings (jsContent) {
  let cleaned = jsContent

  // 1️⃣ Remove message strings in validators
  cleaned = cleaned.replace(/(message\s*:\s*)['"`][\s\S]*?['"`]/g, '$1""')

  // 2️⃣ Remove entire alert(...) calls
  cleaned = cleaned.replace(/alert\s*\(\s*(['"`])[\s\S]*?\1\s*\)\s*;?/g, '')

  // 3️⃣ Remove second argument of FormValidator.markFieldError(id, '...')
  cleaned = cleaned.replace(
    /FormValidator\.markFieldError\s*\(\s*([^,]+)\s*,\s*(['"`])[\s\S]*?\2\s*\)/g,
    'FormValidator.markFieldError($1, "")'
  )

  return cleaned
}

// Gather all js files
const allJsFiles = getAllJsFiles(process.cwd())

// Exclude the script itself
const SCRIPT_PATH = path.resolve(process.argv[1])

const filesToProcess = allJsFiles.filter(filePath => {
  const normalized = filePath.replace(/\\/g, '/')

  return (
    normalized.endsWith('.js') &&
    filePath !== SCRIPT_PATH &&
    !normalized.includes('node_modules') &&
    !normalized.includes('dist') &&
    !normalized.includes('build')
  )
})

if (filesToProcess.length === 0) {
  console.log('No matching files found to process.')
} else {
  filesToProcess.forEach((filePath) => {
    const content = fs.readFileSync(filePath, 'utf-8')
    const cleanedContent = removeUserStrings(content)

    // Overwrite the original file
    fs.writeFileSync(filePath, cleanedContent, 'utf-8')

    console.log(`\nProcessed and overwritten file: ${filePath}`)
    console.log('--- Cleaned content start ---')
    console.log(cleanedContent)
    console.log('--- Cleaned content end ---\n')
  })
}
