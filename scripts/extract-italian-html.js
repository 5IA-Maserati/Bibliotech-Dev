const fs = require('fs');
const path = require('path');
const glob = require('glob');
const cheerio = require('cheerio');

// Config: HTML tags where italian is allowed
const allowedTags = ['p','span','label','option','li','h1','h2','h3'];

// Find any PHP file
const phpFiles = glob.sync('**/*.php', { ignore: 'vendor/**' }); // ignora eventuali librerie

let italianText = '';

phpFiles.forEach(file => {
  let content = fs.readFileSync(file, 'utf-8');

  // Remove PHP blocks
  content = content.replace(/<\?php[\s\S]*?\?>/g, '');

  // Load the residue in cheerio
  const $ = cheerio.load(content);

  // Extract text from the allowed tags
  allowedTags.forEach(tag => {
    $(tag).each((i, el) => {
      italianText += $(el).text().trim() + '\n';
    });
  });

  // Extract alt and aria-label attributes
  $('[alt]').each((i, el) => {
    italianText += $(el).attr('alt').trim() + '\n';
  });
  $('[aria-label]').each((i, el) => {
    italianText += $(el).attr('aria-label').trim() + '\n';
  });
});
// Save the extracted text in a tmp file for the check
fs.writeFileSync('italian-to-check.txt', italianText);

console.log('Testo italiano estratto in italian-to-check.txt');
