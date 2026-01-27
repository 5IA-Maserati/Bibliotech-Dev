/**
 * Booking Form Validation and Submission
 */

// Select the booking form and attach event listener
document.getElementById('booking-form').addEventListener('submit', function (e) {
  // Prevent default form submission
  e.preventDefault()

  // Validate all fields
  const validation = FormValidator.validateForm('booking-form')
  
  if (!validation.valid) {
    console.error('Form validation failed:', validation.errors)
    // Errors are already displayed by markFieldError
    return
  }

  // Sanitize all inputs
  const sanitizedData = FormValidator.sanitizeForm('booking-form')
  
  // Show success message (in production, send to server)
  console.log('Sanitized booking data:', sanitizedData)
  alert('Prenotazione completata!')
})

// Enable real-time validation on blur
FormValidator.enableRealTimeValidation('booking-form')
