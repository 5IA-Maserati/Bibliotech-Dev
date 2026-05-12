import FormValidator from './form-validator.js'

document.addEventListener('DOMContentLoaded', function () {
  const bookingForm = document.getElementById('booking-form')
  const suggestionsSection = document.getElementById('book-suggestions-section')
  const suggestionsList = document.getElementById('suggestions-list')
  const bookIdInput = document.getElementById('book-id')
  const selectedBookTitle = document.getElementById('selected-book-title')
  const bookingDateInput = document.getElementById('booking-date')

  function validateReturnDate (dateString) {
    if (!dateString) return { valid: false, message: 'Seleziona una data di restituzione.' }

    const returnDate = new Date(dateString)
    const today = new Date()
    const oneYearFromNow = new Date()
    oneYearFromNow.setFullYear(today.getFullYear() + 1)

    // Reset time to compare dates only
    today.setHours(0, 0, 0, 0)
    returnDate.setHours(0, 0, 0, 0)
    oneYearFromNow.setHours(23, 59, 59, 999)

    if (isNaN(returnDate.getTime())) {
      return { valid: false, message: 'Data non valida.' }
    }

    if (returnDate < today) {
      return { valid: false, message: 'La data di restituzione non può essere nel passato.' }
    }

    if (returnDate > oneYearFromNow) {
      return { valid: false, message: 'La data di restituzione non può essere oltre un anno da oggi.' }
    }

    return { valid: true }
  }

  function selectBook (bookId, bookTitle) {
    if (!bookIdInput || !selectedBookTitle || !bookingForm) return

    bookIdInput.value = bookId
    selectedBookTitle.textContent = bookTitle
    bookingForm.classList.remove('hidden')
    bookingForm.classList.add('block')
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

  // Set minimum date to today and maximum to one year from now
  if (bookingDateInput) {
    const today = new Date().toISOString().split('T')[0]
    const oneYearFromNow = new Date()
    oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() + 1)
    const maxDate = oneYearFromNow.toISOString().split('T')[0]

    bookingDateInput.setAttribute('min', today)
    bookingDateInput.setAttribute('max', maxDate)

    // Add real-time validation for return date
    bookingDateInput.addEventListener('change', function () {
      const validation = validateReturnDate(this.value)
      if (!validation.valid) {
        this.setCustomValidity(validation.message)
        this.reportValidity()
      } else {
        this.setCustomValidity('')
      }
    })

    bookingDateInput.addEventListener('input', function () {
      const validation = validateReturnDate(this.value)
      if (!validation.valid) {
        this.setCustomValidity(validation.message)
      } else {
        this.setCustomValidity('')
      }
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

      // Additional date validation
      if (bookingDateInput) {
        const dateValidation = validateReturnDate(bookingDateInput.value)
        if (!dateValidation.valid) {
          e.preventDefault()
          alert(dateValidation.message)
        }
      }
    })

    FormValidator.enableRealTimeValidation('booking-form')
  }
})
