/* eslint-env browser */
/* global FormValidator */

/**
 * Search Form Validation and Functionality
 */

const searchInput = document.getElementById('q')
const searchButton = document.querySelector('.btn-search')

// Filtri (attualmente non usati, ma previsti)
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
    console.log('Performing search for:', sanitizedSearch)

    FormValidator.clearFieldError('q')
    // TODO: search reale
  })

  // Real-time validation
  searchInput.addEventListener('input', function () {
    if (searchInput.classList.contains('error')) {
      FormValidator.clearFieldError('q')
    }
  })

  // Validate on Enter
  searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      searchButton.click()
    }
  })
}
