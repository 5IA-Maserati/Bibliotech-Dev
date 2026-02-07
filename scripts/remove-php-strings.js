import * as fs from 'fs';
import * as path from 'path';
import * as glob from 'glob';

/**
 * File patterns to process
 */
const FILES = [
  'public/**/*.php'
];

/**
 * Remove all string literals from a PHP code block
 * @param {string} phpCode
 * @returns {{ stripped: string, removed: string[] }}
 */
function removePhpStrings(phpCode) {
  const removed = [];

  // Correct regex for single or double quoted strings, handling escapes
  const stringRegex = /(['"])(?:\\.|(?!\1).)*?\1/g;

  const stripped = phpCode.replace(stringRegex, (match) => {
    removed.push(match);
    return match[0] + match[0]; // replace with empty quotes of same type
  });

  return { stripped, removed };
}

/**
 * Process a single PHP file
 * Splits PHP and HTML, removes strings from PHP blocks only
 */
function processPhpFile(filePath) {
  const content = fs.readFileSync(filePath, 'utf8');
  const removedStrings = [];

  // Split into PHP blocks (<?php ... ?> or <?= ... ?>) and the rest
  const parts = content.split(/(<\?(?:php|=)[\s\S]*?\?>)/gi);

  const processed = parts.map((part) => {
    if (/^<\?(php|=)/i.test(part)) {
      const { stripped, removed } = removePhpStrings(part);
      removedStrings.push(...removed);
      return stripped;
    } else {
      return part; // HTML/text outside PHP remains intact
    }
  }).join('');

  // Overwrite the file
  fs.writeFileSync(filePath, processed, 'utf8');

  return removedStrings;
}

// Find all files
const allFiles = FILES.flatMap(pattern => glob.sync(pattern, { nodir: true }));

console.log(`Found ${allFiles.length} PHP files.\n`);

allFiles.forEach(file => {
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
