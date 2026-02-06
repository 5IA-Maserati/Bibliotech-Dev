const fs = require('fs')
const glob = require('glob')
const cheerio = require('cheerio')

// Tags where Italian is allowed
const italianTags = ['p', 'span', 'label', 'option', 'li', 'h1', 'h2', 'h3']

// Find all PHP files, ignoring vendor folder
const phpFiles = glob.sync('**/*.php', { ignore: 'vendor/**' })

let englishText = ''
let italianText = ''

phpFiles.forEach(file => {
  let content = fs.readFileSync(file, 'utf-8')

  // Remove PHP blocks
  content = content.replace(/<\?php[\s\S]*?\?>/g, '')

  // Load HTML with cheerio
  const $ = cheerio.load(content)

  // Extract Italian text
  italianTags.forEach(tag => {
    $(tag).each((i, el) => {
      italianText += $(el).text().trim() + '\n'
    })
  })

  $('[alt]').each((i, el) => {
    italianText += $(el).attr('alt').trim() + '\n'
  })

  $('[aria-label]').each((i, el) => {
    italianText += $(el).attr('aria-label').trim() + '\n'
  })

  // Extract English text: everything else
  italianTags.forEach(tag => $(tag).remove())
  $('[alt]').removeAttr('alt')
  $('[aria-label]').removeAttr('aria-label')

  englishText += $.root().text().trim() + '\n'
})

// Save both files
fs.writeFileSync('italian-to-check.txt', italianText)
fs.writeFileSync('english-html-to-check.txt', englishText)

console.log('Extraction complete: italian-to-check.txt & english-html-to-check.txt')
