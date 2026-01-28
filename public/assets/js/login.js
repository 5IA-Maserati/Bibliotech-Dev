/* eslint-env browser */
/* global FormValidator */

/**
 * Login Form Validation and Submission
 */

const loginForm = document.getElementById('login-form')

if (loginForm) {
  loginForm.addEventListener('submit', function (e) {
    e.preventDefault()

    // Validate all fields
    const validation = FormValidator.validateForm('login-form')

    if (!validation.valid) {
      console.error('Form validation failed:', validation.errors)
      return
    }

    // Sanitize all inputs
    const sanitizedData = FormValidator.sanitizeForm('login-form')
    console.log('Sanitized form data:', sanitizedData)

    alert('Login completato!')
  })

  // Enable real-time validation on blur
  FormValidator.enableRealTimeValidation('login-form')
}
