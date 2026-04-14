/* global FormValidator, alert */

/**
 * Registration Form Validation and Submission
 */

// Select the registration form and attach an event listener to it
document.getElementById('register-form').addEventListener('submit', function (e) {
  // Prevent the form from submitting
  e.preventDefault()

  // Validate all fields
  const validation = FormValidator.validateForm('register-form')
  console.log('Validation result:', validation)

  if (!validation.valid) {
    console.error('Form validation failed:', validation.errors)
    // Errors are already displayed by markFieldError
    return
  }

  // Check if passwords match
  const password = document.getElementById('password').value
  const confirm = document.getElementById('confirm_password').value
  console.log('Password:', password, 'Confirm:', confirm)

  if (password !== confirm) {
    console.log('Passwords do not match')
    FormValidator.markFieldError('confirm_password', 'Le password non coincidono')
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
