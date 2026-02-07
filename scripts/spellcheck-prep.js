// spellcheck-prep.js
// Overwrites selected JS files by removing all string literals
// Files are selected based on their filename only, ignoring folder structure

import fs from 'fs';
import path from 'path';

/**
 * List of filenames to process (just the filename, no path)
 * Example: ['validator.js', 'form.js']
 */
const filenamesToProcess = [
  'form-validator.js'
];

/**
 * Recursively get all JS files in a directory
 * @param {string} dir - starting directory
 * @returns {string[]} - array of file paths
 */
function getAllJsFiles(dir) {
  let results = [];
  const list = fs.readdirSync(dir, { withFileTypes: true });

  list.forEach((item) => {
    const fullPath = path.join(dir, item.name);
    if (item.isDirectory()) {
      results = results.concat(getAllJsFiles(fullPath));
    } else if (item.isFile() && fullPath.endsWith('.js')) {
      results.push(fullPath);
    }
  });

  return results;
}

/**
 * Remove all string literals from JS content
 * Preserves comments and code structure intact
 *
 * @param {string} jsContent - Original JS file content
 * @returns {string} - JS content with all string literals replaced by empty strings
 */
function removeAllStrings(jsContent) {
  const stringRegex = /(['"`])([\s\S]*?)\1/g;
  return jsContent.replace(stringRegex, (match, quote) => quote + quote);
}

// Get all JS files in the current project
const allJsFiles = getAllJsFiles(process.cwd());

// Filter files by filename
const filesToProcess = allJsFiles.filter((filePath) =>
  filenamesToProcess.includes(path.basename(filePath))
);

if (filesToProcess.length === 0) {
  console.log('No matching files found to process.');
} else {
  filesToProcess.forEach((filePath) => {
    const content = fs.readFileSync(filePath, 'utf-8');
    const cleanedContent = removeAllStrings(content);
    fs.writeFileSync(filePath, cleanedContent, 'utf-8');
    console.log(`Processed and overwritten file: ${filePath}`);
  });
}
