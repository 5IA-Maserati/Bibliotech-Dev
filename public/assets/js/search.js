/* eslint-env browser */
/* global FormValidator */

/**
 * Search Form Validation and Functionality
 */

const searchInput = document.getElementById('q')
const searchButton = document.querySelector('.btn-search')

// Filters (currently expected)
const _genreFilter = document.getElementById('genre')
const _sortFilter = document.getElementById('sort')

if (searchInput && searchButton) {
  // Add search validation on button click
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

    // Get filter values
    const genre = _genreFilter ? _genreFilter.value : ''
    const sort = _sortFilter ? _sortFilter.value : ''

    console.log('Performing search for:', sanitizedSearch)
    console.log('Selected genre:', genre)
    console.log('Selected sort:', sort)

    FormValidator.clearFieldError('q')

    // Call search API
    searchAPI(sanitizedSearch, genre, sort)
  })

  // Real-time validation: clear error if user types
  searchInput.addEventListener('input', function () {
    if (searchInput.classList.contains('error')) {
      FormValidator.clearFieldError('q')
    }
  })

  // Trigger search on Enter key
  searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      searchButton.click()
    }
  })

  // Optional: trigger search when filters change
  if (_genreFilter) {
    _genreFilter.addEventListener('change', () => searchButton.click())
  }
  if (_sortFilter) {
    _sortFilter.addEventListener('change', () => searchButton.click())
  }
}

/**
 * Search API Function
 */
function searchAPI(query, genre, sort) {
  const resultsDiv = document.getElementById('results')
  const countSpan = document.getElementById('count')

  resultsDiv.innerHTML = '<p>Ricerca in corso...</p>'

  const params = new URLSearchParams({
    q: query,
    genre: genre,
    sort: sort
  })

  fetch(`/api/books/search.php?${params}`)
    .then(response => response.json())
    .then(data => {
      if (data.error || !data.books || data.books.length === 0) {
        resultsDiv.innerHTML = '<div class="no-results">Nessun libro trovato.</div>'
        countSpan.textContent = '0'
        return
      }

      resultsDiv.innerHTML = data.books
        .map(book => `
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
        `)
        .join('')

      countSpan.textContent = data.books.length

      // Add click handlers to book cards
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
