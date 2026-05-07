import FormValidator from './form-validator.js'

document.addEventListener('DOMContentLoaded', function () {
  const bookingForm = document.getElementById('booking-form')
  const suggestionsSection = document.getElementById('book-suggestions-section')
  const suggestionsList = document.getElementById('suggestions-list')
  const bookIdInput = document.getElementById('book-id')
  const selectedBookTitle = document.getElementById('selected-book-title')

  function selectBook(bookId, bookTitle) {
    if (!bookIdInput || !selectedBookTitle || !bookingForm) return

    bookIdInput.value = bookId
    selectedBookTitle.textContent = bookTitle
    bookingForm.style.display = 'block'
    if (suggestionsSection) {
      suggestionsSection.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }

  if (suggestionsList) {
    suggestionsList.addEventListener('click', (event) => {
      const button = event.target.closest('button[data-book-id]')
      if (!button) return

      const bookId = button.dataset.bookId
      const bookTitle = button.dataset.bookTitle
      if (!bookId || !bookTitle) return

      selectBook(bookId, bookTitle)
    })
  }

  if (bookingForm && typeof FormValidator !== 'undefined') {
    bookingForm.addEventListener('submit', function (e) {
      const validation = FormValidator.validateForm('booking-form')
      if (!validation.valid) {
        e.preventDefault()
        console.error('Form validation failed:', validation.errors)
        return
      }
    })

    FormValidator.enableRealTimeValidation('booking-form')
  }
})
