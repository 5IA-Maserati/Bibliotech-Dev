/* eslint-env browser */
/* global FormValidator */

const searchInput = document.getElementById('q')
const searchButton = document.querySelector('.btn-search')

const genreFilter = document.getElementById('genre')
const sortFilter = document.getElementById('sort')

const placeholderCoverUrl = (window.BookCovers && window.BookCovers.placeholderCoverUrl) || '/assets/img/common/default_cover.png'

const loadBookCoverImage = (imgElement, isbn) => {
  if (window.BookCovers && typeof window.BookCovers.loadBookCoverImage === 'function') {
    return window.BookCovers.loadBookCoverImage(imgElement, isbn)
  }
  return Promise.resolve()
}

// Pagination elements
const paginationDiv = document.getElementById('pagination')
const prevButton = document.getElementById('prev-page')
const nextButton = document.getElementById('next-page')
const pageInfo = document.getElementById('page-info')
const totalSpan = document.getElementById('total')

// Pagination state
let currentPage = 1
const itemsPerPage = 11
let totalPages = 1
let totalItems = 0
let currentQuery = ''
let currentGenre = ''
let currentSort = 'title'

function renderBookResults (books, title, pagination) {
  const resultsDiv = document.getElementById('results')
  const countSpan = document.getElementById('count')

  if (!books || books.length === 0) {
    resultsDiv.innerHTML = '<div class="no-results">Nessun libro trovato.</div>'
    countSpan.textContent = '0'
    paginationDiv.style.display = 'none'
    return
  }

  resultsDiv.innerHTML = `
    <h3 class="section-title">${title}</h3>
    ${books.map(book => {
      const coverUrl = placeholderCoverUrl
      return `
      <div class="book-card" data-id="${book.id}">
        <img class="book-cover" data-isbn="${book.isbn || ''}" src="${coverUrl}" alt="Cover of ${book.title}">
        <div class="book-info">
          <h3 class="book-title">${book.title}</h3>
          ${book.favorite ? '<span class="favorite-badge">Preferito</span>' : ''}
          <p class="book-author">di ${book.author}</p>
          <p class="book-year">${book.publication_year || 'N/A'}</p>
          <p class="book-isbn">ISBN: ${book.isbn || 'N/A'}</p>
        </div>
      </div>
      `
    }).join('')}
  `

  resultsDiv.querySelectorAll('.book-cover[data-isbn]').forEach(img => {
    const isbn = img.dataset.isbn
    if (isbn) {
      loadBookCoverImage(img, isbn)
    }
  })

  countSpan.textContent = books.length

  // Update pagination if provided
  if (pagination) {
    updatePaginationControls(pagination)
  }

  document.querySelectorAll('.book-card').forEach(card => {
    card.addEventListener('click', () => {
      const bookId = card.dataset.id
      window.location.href = `/pages/books_details.php?id=${bookId}`
    })
  })
}

function updatePaginationControls (pagination) {
  currentPage = pagination.page
  totalPages = pagination.totalPages
  totalItems = pagination.total

  totalSpan.textContent = totalItems

  if (totalPages > 1) {
    paginationDiv.style.display = 'flex'
    pageInfo.textContent = `Pagina ${currentPage} di ${totalPages}`

    prevButton.disabled = currentPage <= 1
    nextButton.disabled = currentPage >= totalPages
  } else {
    paginationDiv.style.display = 'none'
  }
}

function initSearchPage () {
  const genre = genreFilter ? genreFilter.value : ''
  const sort = sortFilter ? sortFilter.value : ''

  // Always use API for loading books with pagination
  searchAPI('', genre, sort, 1)
}

window.addEventListener('DOMContentLoaded', initSearchPage)

if (searchInput && searchButton) {
  searchButton.addEventListener('click', function (e) {
    e.preventDefault()

    const searchValue = searchInput.value.trim()

    // Allow empty search to show all books
    const sanitizedSearch = searchValue

    const genre = genreFilter ? genreFilter.value : ''
    const sort = sortFilter ? sortFilter.value : ''

    FormValidator.clearFieldError('q')

    searchAPI(sanitizedSearch, genre, sort, 1) // Reset to page 1 on new search
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
    genreFilter.addEventListener('change', () => searchAPI(searchInput.value.trim(), genreFilter.value, sortFilter.value, 1))
  }
  if (sortFilter) {
    sortFilter.addEventListener('change', () => searchAPI(searchInput.value.trim(), genreFilter.value, sortFilter.value, 1))
  }
}

/**
 * SEARCH API
 */
function searchAPI (query, genre, sort, page = 1) {
  const resultsDiv = document.getElementById('results')
  const countSpan = document.getElementById('count')

  resultsDiv.innerHTML = '<p>Ricerca in corso...</p>'

  // Update current state
  currentQuery = query
  currentGenre = genre
  currentSort = sort

  const params = new URLSearchParams({
    q: query,
    genre,
    sort,
    page: page.toString(),
    limit: itemsPerPage.toString()
  })

  fetch(`/api/books/search.php?${params}`)
    .then(response => response.json())
    .then(data => {
      if (!data.success || !data.books || data.books.length === 0) {
        resultsDiv.innerHTML = '<div class="no-results">Nessun libro trovato.</div>'
        countSpan.textContent = '0'
        paginationDiv.style.display = 'none'
        return
      }

      const title = query ? `Risultati per "${query}"` : 'Tutti i libri'
      renderBookResults(data.books, title, data.pagination)
    })
    .catch(error => {
      console.error('Search error:', error)
      resultsDiv.innerHTML = '<div class="no-results">Errore nella ricerca.</div>'
    })
}

// Pagination event listeners
if (prevButton) {
  prevButton.addEventListener('click', () => {
    if (currentPage > 1) {
      searchAPI(currentQuery, currentGenre, currentSort, currentPage - 1)
    }
  })
}

if (nextButton) {
  nextButton.addEventListener('click', () => {
    if (currentPage < totalPages) {
      searchAPI(currentQuery, currentGenre, currentSort, currentPage + 1)
    }
  })
}
