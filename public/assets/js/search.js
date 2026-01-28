/**
 * Search Form Validation and Functionality
 */

// Get search input and button elements
const searchInput = document.getElementById('q');
const searchButton = document.querySelector('.btn-search');
const genreFilter = document.getElementById('genre');
const sortFilter = document.getElementById('sort');

// Add search validation on button click
searchButton.addEventListener('click', function (e) {
  e.preventDefault();

  // Validate search input
  const searchValue = searchInput.value.trim();

  if (searchValue === '') {
    FormValidator.markFieldError('q', 'Inserisci un termine di ricerca');
    return;
  }

  // Validate search input against pattern
  const validation = FormValidator.validateField('q');

  if (!validation.valid) {
    FormValidator.markFieldError('q', validation.message);
    return;
  }

  // Sanitize search input
  const sanitizedSearch = FormValidator.sanitize(searchValue);
  console.log('Performing search for:', sanitizedSearch);

  // Clear error on successful validation
  FormValidator.clearFieldError('q');

  // TODO: Perform actual search operation here
  // For now, just log the sanitized search term
});

// Real-time validation and error clearing on input
searchInput.addEventListener('input', function () {
  if (searchInput.classList.contains('error')) {
    FormValidator.clearFieldError('q');
  }
});

// Validate on Enter key press
searchInput.addEventListener('keypress', function (e) {
  if (e.key === 'Enter') {
    searchButton.click();
  }
});
