/**
 * Registration Form Validation and Submission
 */

// Select the registration form and attach an event listener to it
document.getElementById('register-form').addEventListener('submit', function (e) {
  // Prevent the form from submitting
  e.preventDefault()

  // Validate all fields
  const validation = FormValidator.validateForm('register-form')
  
  if (!validation.valid) {
    console.error('Form validation failed:', validation.errors)
    // Errors are already displayed by markFieldError
    return
  }

  // Check if passwords match
  const password = document.getElementById('password').value
  const confirm = document.getElementById('confirm-password').value

  if (password !== confirm) {
    FormValidator.markFieldError('confirm-password', 'Le password non coincidono')
    return
  }

  // Sanitize all inputs
  const sanitizedData = FormValidator.sanitizeForm('register-form')
  
  // Show success message (in production, send to server)
  console.log('Sanitized form data:', sanitizedData)
  alert('Registrazione completata!')
})

// Enable real-time validation on blur
FormValidator.enableRealTimeValidation('register-form')
