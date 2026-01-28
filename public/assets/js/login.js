/**
 * Login Form Validation and Submission
 */

// Select the login form and attach event listener
document.getElementById('login-form').addEventListener('submit', function (e) {
  // Prevent default form submission
  e.preventDefault()

  // Validate all fields
  const validation = FormValidator.validateForm('login-form')

  if (!validation.valid) {
    console.error('Form validation failed:', validation.errors)
    // Errors are already displayed by markFieldError
    return
  }

  // Sanitize all inputs
  const sanitizedData = FormValidator.sanitizeForm('login-form')

  // Show success message (in production, send to server)
  console.log('Sanitized form data:', sanitizedData)
  alert('Login completato!')
})

// Enable real-time validation on blur
FormValidator.enableRealTimeValidation('login-form')

