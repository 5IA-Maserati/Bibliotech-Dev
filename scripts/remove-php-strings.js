import * as fs from 'fs';
import * as path from 'path';
import * as glob from 'glob';

/**
 * File patterns to process
 * You can add any folders/files you want
 */
const FILES = [
  'public/**/*.php'
];

/**
 * Remove all strings inside a PHP block
 * @param {string} phpCode
 * @returns {{ stripped: string, removed: string[] }}
 */
function removePhpStrings(phpCode) {
  const removed = [];
  // Regex for strings enclosed in single or double quotes
  const stringRegex = /(['"])(?:\\.|[^\1\\])*?\1/g;

  const stripped = phpCode.replace(stringRegex, (match) => {
    removed.push(match);
    // Return empty quotes of the same type
    return match[0] + match[0];
  });

  return { stripped, removed };
}

/**
 * Process a single PHP file
 * Splits PHP blocks and HTML, removes strings from PHP blocks only
 */
function processPhpFile(filePath) {
  const originalContent = fs.readFileSync(filePath, 'utf8');
  const removedStrings = [];

  // Split content into PHP blocks and everything else (HTML/text)
  // Keeps the PHP delimiters <?php ... ?> or <?= ... ?>
  const parts = originalContent.split(/(<\?(?:php|=)?[\s\S]*?\?>)/gi);

  const processed = parts.map((part) => {
    if (part.match(/^<\?(?:php|=)/i)) {
      // This is a PHP block
      const { stripped, removed } = removePhpStrings(part);
      removedStrings.push(...removed);
      return stripped;
    } else {
      // HTML or text outside PHP blocks, leave unchanged
      return part;
    }
  }).join('');

  // Overwrite the original file
  fs.writeFileSync(filePath, processed, 'utf8');

  return removedStrings;
}

// Find all files matching the patterns
const allFiles = FILES.flatMap(pattern => glob.sync(pattern, { nodir: true }));

console.log(`Found ${allFiles.length} PHP files.\n`);

allFiles.forEach((file) => {
  const removed = processPhpFile(file);
  console.log(`📄 Processed file: ${file}`);
  if (removed.length > 0) {
    console.log(`  ❌ Removed PHP strings (${removed.length}):`);
    removed.forEach(str => console.log(`    ${str}`));
  } else {
    console.log('  ✅ No PHP strings removed');
  }
  console.log('');
});

console.log('✔ All PHP files processed.');
