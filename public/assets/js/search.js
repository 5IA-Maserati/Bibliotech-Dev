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

    // TODO: Replace this with actual search logic
    // Example: searchAPI(sanitizedSearch, genre, sort)
    alert('Ricerca completata!')

    // Clear search field after successful search
    searchInput.value = ''
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
