// BRUTAL VERSION: removes ALL string literals from ALL JS files

import fs from 'fs'
import path from 'path'

// Recursively get all JS files in a directory
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

// This replaces every string literal with ""
function removeAllStrings (jsContent) {
  return jsContent.replace(/(['"`])(?:\\.|(?!\1).)*\1/g, '""')
}

// Gather EVERY JS found in the codebase
const allJsFiles = getAllJsFiles(process.cwd())

// Exclude the script itself for security reasons
const SCRIPT_PATH = path.resolve(process.argv[1])

const filesToProcess = allJsFiles.filter(filePath => {
  const normalized = filePath.replace(/\\/g, '/')

  return (
    normalized.endsWith('.js') &&
    filePath !== SCRIPT_PATH &&
    !normalized.includes('node_modules')
  )
})

if (filesToProcess.length === 0) {
  console.log('No JS files found.')
} else {
  filesToProcess.forEach(filePath => {
    const content = fs.readFileSync(filePath, 'utf-8')

    const cleaned = removeAllStrings(content)

    fs.writeFileSync(filePath, cleaned, 'utf-8')

    console.log(`Processed: ${filePath}`)
  })
}
