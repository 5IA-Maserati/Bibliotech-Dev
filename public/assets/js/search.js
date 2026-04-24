/* eslint-env browser */
/* global FormValidator */

const searchInput = document.getElementById('q')
const searchButton = document.querySelector('.btn-search')

const genreFilter = document.getElementById('genre')
const sortFilter = document.getElementById('sort')

if (searchInput && searchButton) {
  searchButton.addEventListener('click', function (e) {
    e.preventDefault()

    const searchValue = searchInput.value.trim()

    if (searchValue === '') {
      FormValidator.markFieldError('q', 'Inserisci un termine di ricerca')
      return
    }

    const validation = FormValidator.validateField('q')
    if (!validation.valid) {
      FormValidator.markFieldError('q', validation.message)
      return
    }

    const sanitizedSearch = FormValidator.sanitize(searchValue)

    const genre = genreFilter ? genreFilter.value : ''
    const sort = sortFilter ? sortFilter.value : ''

    FormValidator.clearFieldError('q')

    searchAPI(sanitizedSearch, genre, sort)
  })

  searchInput.addEventListener('input', function () {
    if (searchInput.classList.contains('error')) {
      FormValidator.clearFieldError('q')
    }
  })

  searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      searchButton.click()
    }
  })

  if (genreFilter) {
    genreFilter.addEventListener('change', () => searchButton.click())
  }
  if (sortFilter) {
    sortFilter.addEventListener('change', () => searchButton.click())
  }
}

/**
 * SEARCH API 
 */
function searchAPI (query, genre, sort) {
  const resultsDiv = document.getElementById('results')
  const countSpan = document.getElementById('count')

  resultsDiv.innerHTML = '<p>Ricerca in corso...</p>'

  const params = new URLSearchParams({
    q: query,
    genre,
    sort
  })

  fetch(`/pages/search.php?${params}`)
    .then(response => response.json())
    .then(data => {
      if (!data.success || !data.books || data.books.length === 0) {
        resultsDiv.innerHTML = '<div class="no-results">Nessun libro trovato.</div>'
        countSpan.textContent = '0'
        return
      }

      resultsDiv.innerHTML = data.books.map(book => `
        <div class="book-card" data-id="${book.id}">
          <div class="book-cover">${book.title.charAt(0)}</div>
          <div class="book-info">
            <h3 class="book-title">${book.title}</h3>
            <p class="book-author">di ${book.author}</p>
            <p class="book-year">${book.publication_year || 'N/A'}</p>
            <p class="book-isbn">ISBN: ${book.isbn || 'N/A'}</p>
            ${book.genres ? `<p class="book-genres">${book.genres}</p>` : ''}
          </div>
        </div>
      `).join('')

      countSpan.textContent = data.books.length

      document.querySelectorAll('.book-card').forEach(card => {
        card.addEventListener('click', () => {
          const bookId = card.dataset.id
          window.location.href = `/pages/books_details.php?id=${bookId}`
        })
      })
    })
    .catch(error => {
      console.error('Search error:', error)
      resultsDiv.innerHTML = '<div class="no-results">Errore nella ricerca.</div>'
    })
}
