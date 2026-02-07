// spellcheck-prep.js
// Safely removes user-facing string literals (e.g., validation messages) from selected JS files
// Overwrites files and prints cleaned content
// Files are selected based on filename only, ignoring folder structure

import fs from 'fs';
import path from 'path';

/**
 * List of filenames to process (just the filename, no path)
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

  for (const item of list) {
    const fullPath = path.join(dir, item.name);
    if (item.isDirectory()) {
      results = results.concat(getAllJsFiles(fullPath));
    } else if (item.isFile() && fullPath.endsWith('.js')) {
      results.push(fullPath);
    }
  }
  return results;
}

/**
 * Remove only user-facing string literals (e.g., `message` properties)
 * Preserves regex, patterns, comments, and code structure
 *
 * @param {string} jsContent - Original JS file content
 * @returns {string} - JS content with message strings emptied
 */
function removeUserStrings(jsContent) {
  // Match lines like: message: 'some text' or message: "some text"
  return jsContent.replace(/(message\s*:\s*)['"`][\s\S]*?['"`]/g, '$1""');
}

// Get all JS files in the current project
const allJsFiles = getAllJsFiles(process.cwd());

// Filter files by filename only
const filesToProcess = allJsFiles.filter((filePath) =>
  filenamesToProcess.includes(path.basename(filePath))
);

if (filesToProcess.length === 0) {
  console.log('No matching files found to process.');
} else {
  filesToProcess.forEach((filePath) => {
    const content = fs.readFileSync(filePath, 'utf-8');
    const cleanedContent = removeUserStrings(content);

    // Overwrite the original file
    fs.writeFileSync(filePath, cleanedContent, 'utf-8');

    console.log(`\nProcessed and overwritten file: ${filePath}`);
    console.log('--- Cleaned content start ---');
    console.log(cleanedContent);
    console.log('--- Cleaned content end ---\n');
  });
}
