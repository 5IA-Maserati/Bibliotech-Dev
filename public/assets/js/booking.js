document.addEventListener('DOMContentLoaded', function () {
  const bookingForm = document.getElementById('booking-form')
  if (!bookingForm || typeof FormValidator === 'undefined') return

  bookingForm.addEventListener('submit', function (e) {
    e.preventDefault()

    // Validate all fields
    const validation = FormValidator.validateForm('booking-form')

    if (!validation.valid) {
      console.error('Form validation failed:', validation.errors)
      return
    }

    // Sanitize all inputs
    const sanitizedData = FormValidator.sanitizeForm('booking-form')

    // Success feedback
    console.log('Sanitized booking data:', sanitizedData)
    alert('Prenotazione completata!')

    // Clear all form fields after successful submission
    bookingForm.reset()

    // Clear any remaining error messages
    const inputs = bookingForm.querySelectorAll('input')
    inputs.forEach(input => {
      FormValidator.clearFieldError(input.id)
    })
  })

  // Enable real-time validation on blur
  FormValidator.enableRealTimeValidation('booking-form')
})
